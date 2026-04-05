<?php
require_once "config.php";

// Generate JWT token
function generateJWT($userId, $email, $role) {
    $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
    $payload = json_encode([
        'user_id' => $userId,
        'email' => $email,
        'role' => $role,
        'iat' => time(),
        'exp' => time() + (60 * 60 * 24) // 24 hours
    ]);
    
    $base64UrlHeader = base64UrlEncode($header);
    $base64UrlPayload = base64UrlEncode($payload);
    
    $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, JWT_SECRET, true);
    $base64UrlSignature = base64UrlEncode($signature);
    
    return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
}

// Verify JWT token
function verifyJWT($token) {
    if (!$token) {
        return false;
    }
    
    $tokenParts = explode('.', $token);
    if (count($tokenParts) !== 3) {
        return false;
    }
    
    $header = base64_decode($tokenParts[0]);
    $payload = base64_decode($tokenParts[1]);
    $signatureProvided = $tokenParts[2];
    
    $base64UrlHeader = base64UrlEncode($header);
    $base64UrlPayload = base64UrlEncode($payload);
    
    $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, JWT_SECRET, true);
    $base64UrlSignature = base64UrlEncode($signature);
    
    if ($base64UrlSignature !== $signatureProvided) {
        return false;
    }
    
    $payloadData = json_decode($payload, true);
    
    // Check expiration
    if (isset($payloadData['exp']) && $payloadData['exp'] < time()) {
        return false;
    }
    
    return $payloadData;
}

// Get token from Authorization header
function getBearerToken() {
    $headers = getallheaders();
    
    if (isset($headers['Authorization'])) {
        $matches = [];
        preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches);
        if (isset($matches[1])) {
            return $matches[1];
        }
    }
    
    return null;
}

// Base64 URL encode
function base64UrlEncode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

// Require authentication
function requireAuth() {
    $token = getBearerToken();
    $payload = verifyJWT($token);
    
    if (!$payload) {
        sendResponse(401, ['error' => 'Unauthorized', 'message' => 'Invalid or expired token']);
    }

    // Reject blacklisted tokens (logged-out sessions)
    $check = $pdo->prepare("SELECT 1 FROM token_blacklist WHERE token = :token LIMIT 1");
    $check->bindParam(':token', $token, PDO::PARAM_STR);
    $check->execute();
    if ($check->fetch()) {
        sendResponse(401, ['error' => 'Unauthorized', 'message' => 'Token has been invalidated. Please log in again']);
    }
    
    return $payload;
}

// Require admin role
function requireAdmin() {
    $user = requireAuth();
    
    if ($user['role'] !== 'admin') {
        sendResponse(403, ['error' => 'Forbidden', 'message' => 'Admin access required']);
    }
    
    return $user;
}
?>
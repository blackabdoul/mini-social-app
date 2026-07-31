-- ============================================================
-- MiniSocial — Full Database Schema
-- ============================================================
-- Verified against the live `users` table via SHOW CREATE TABLE.
-- All foreign key columns match users.id exactly: INT(11), signed.
-- ============================================================


-- ------------------------------------------------------------
-- 1. users  (already exists — shown here for reference only)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id                      INT(11)      AUTO_INCREMENT PRIMARY KEY,
    email                   VARCHAR(255) NOT NULL UNIQUE,
    password                VARCHAR(255) NOT NULL,
    full_name               VARCHAR(255) NULL,
    phone                   VARCHAR(20)  NULL,
    bio                     TEXT         NULL,
    location                VARCHAR(255) NULL,
    dob                     DATE         NULL,
    role                    VARCHAR(20)  NOT NULL DEFAULT 'user',
    is_verified             TINYINT(1)   NOT NULL DEFAULT 0,
    verification_token      VARCHAR(64)  NULL,
    token_expires_at        DATETIME     NULL,
    reset_token             VARCHAR(64)  NULL,
    reset_token_expires_at  DATETIME     NULL,
    created_at              TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    updated_at              TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ------------------------------------------------------------
-- 2. token_blacklist
-- ------------------------------------------------------------
-- Stores JWTs that have been logged out. requireAuth() checks
-- this table and rejects any token found here, even if it hasn't
-- naturally expired yet.
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS token_blacklist (
    id          INT UNSIGNED  AUTO_INCREMENT PRIMARY KEY,
    token       VARCHAR(512)  NOT NULL,
    expires_at  DATETIME      NOT NULL,
    created_at  DATETIME      DEFAULT NOW(),
    UNIQUE INDEX idx_token      (token(64)),
    INDEX        idx_expires_at (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Optional cleanup job (cron or scheduled event) — removes tokens
-- that have already expired naturally and no longer need blacklisting:
-- DELETE FROM token_blacklist WHERE expires_at < NOW();


-- ------------------------------------------------------------
-- 3. posts
-- ------------------------------------------------------------
-- user_id is INT(11) to match users.id exactly (not UNSIGNED).
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS posts (
    id          INT UNSIGNED    AUTO_INCREMENT PRIMARY KEY,
    user_id     INT(11)         NOT NULL,
    content     TEXT            NULL,
    image_path  VARCHAR(255)    NULL,
    created_at  DATETIME        DEFAULT NOW(),
    updated_at  DATETIME        DEFAULT NOW() ON UPDATE NOW(),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id    (user_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ------------------------------------------------------------
-- 4. likes
-- ------------------------------------------------------------
-- UNIQUE KEY on (user_id, post_id) makes double-liking impossible
-- at the database level, regardless of application logic.
-- user_id is INT(11) to match users.id exactly.
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS likes (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     INT(11)      NOT NULL,
    post_id     INT UNSIGNED NOT NULL,
    created_at  DATETIME     DEFAULT NOW(),
    UNIQUE KEY unique_like (user_id, post_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ------------------------------------------------------------
-- 5. comments
-- ------------------------------------------------------------
-- user_id is INT(11) to match users.id exactly.
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS comments (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    post_id     INT UNSIGNED NOT NULL,
    user_id     INT(11)      NOT NULL,
    content     VARCHAR(500) NOT NULL,
    created_at  DATETIME     DEFAULT NOW(),
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_post_id    (post_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
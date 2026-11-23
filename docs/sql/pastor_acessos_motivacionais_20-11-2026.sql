CREATE TABLE IF NOT EXISTS pastor_acessos_motivacionais (
    login_id CHAR(36) NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL,
    PRIMARY KEY (login_id),
    KEY pastor_  acessos_motivacionais_user_id_index (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
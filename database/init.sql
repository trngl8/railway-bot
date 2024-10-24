DROP TABLE IF EXISTS app_users;
CREATE TABLE app_users (
    id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    phone VARCHAR(255) NOT NULL,
    profile_id INTEGER NOT NULL,
    key TEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX phone_idx ON app_users (phone);

DROP TABLE IF EXISTS app_messages;
CREATE TABLE app_messages (
    id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    chat_id INTEGER NOT NULL,
    user_id INTEGER NOT NULL,
    body TEXT NOT NULL,
    step INTEGER NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX chat_id_idx ON app_messages (chat_id);

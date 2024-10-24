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

CREATE INDEX user_id_idx ON app_messages (user_id);
CREATE INDEX chat_id_idx ON app_messages (chat_id);

DROP TABLE IF EXISTS app_stations;
CREATE TABLE app_stations (
    id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    code INTEGER NOT NULL,
);

DROP TABLE IF EXISTS app_subscriptions;
CREATE TABLE app_subscriptions (
    id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    station_departure_id INTEGER NOT NULL,
    station_arrival_id INTEGER NOT NULL,
    departure_date DATE NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

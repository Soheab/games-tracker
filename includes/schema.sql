CREATE DATABASE IF NOT EXISTS games_tracker;
USE games_tracker;


CREATE TABLE IF NOT EXISTS users
(
    id         INT AUTO_INCREMENT PRIMARY KEY,
    username   VARCHAR(50)  NOT NULL UNIQUE,
    email      VARCHAR(100) NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    role       INT       DEFAULT 0 -- 0: user, 1: admin
);

CREATE TABLE IF NOT EXISTS games
(
    id           INT AUTO_INCREMENT PRIMARY KEY,
    title        VARCHAR(100) NOT NULL,
    genre        VARCHAR(50),
    platform     VARCHAR(50),
    release_year YEAR,
    added_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS collections
(
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    description TEXT,
    image_file  VARCHAR(255),
    user_id     INT          NOT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS collection_game
(
    id            INT AUTO_INCREMENT PRIMARY KEY,
    collection_id INT NOT NULL,
    game_id       INT NOT NULL,
    status        ENUM ('wishlist','playing','completed','dropped') DEFAULT 'playing',
    added_at      TIMESTAMP                                         DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (collection_id) REFERENCES collections (id) ON DELETE CASCADE,
    FOREIGN KEY (game_id) REFERENCES games (id) ON DELETE CASCADE
);

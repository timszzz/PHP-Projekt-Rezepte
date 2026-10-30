-- schema.sql: legt die Datenbank und alle Tabellen neu an
-- Kann mehrfach ausgeführt werden, vorhandene Daten gehen dabei verloren

CREATE DATABASE IF NOT EXISTS rezepte
  CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE rezepte;

-- Erst die Tabelle mit Fremdschlüsseln löschen, dann die referenzierten
DROP TABLE IF EXISTS main_recipe;
DROP TABLE IF EXISTS main_category;
DROP TABLE IF EXISTS main_user;

CREATE TABLE main_category (
    id   INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE main_user (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    username      VARCHAR(50)  NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,          -- Ergebnis von password_hash(), nie Klartext
    created_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE main_recipe (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(150) NOT NULL,
    ingredients TEXT         NOT NULL,             -- eine Zutat pro Zeile
    preparation TEXT         NOT NULL,
    category_id INT          NOT NULL,
    user_id     INT          NULL,                 -- Ersteller, darf leer sein
    created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME     NULL ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES main_category(id),
    FOREIGN KEY (user_id)     REFERENCES main_user(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
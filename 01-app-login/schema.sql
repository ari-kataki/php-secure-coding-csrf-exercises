-- ricostruito dalle query del codice (il dump originale non c'e')
CREATE DATABASE dbsam;
USE dbsam;

CREATE TABLE users (
    id INT NOT NULL AUTO_INCREMENT,
    user VARCHAR(40) NOT NULL,
    password VARCHAR(72) NOT NULL,
    ruolo ENUM('AUTHOR','EDITOR','ADMIN') NOT NULL,
    PRIMARY KEY (id)
);

CREATE TABLE messages (
    id INT NOT NULL AUTO_INCREMENT,
    text TEXT NOT NULL,
    sending_date DATETIME NOT NULL,
    user_id INT NOT NULL,
    deleted TINYINT NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

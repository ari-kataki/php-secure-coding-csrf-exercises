-- ricostruito dalle query del codice (il dump originale non c'e')
-- delection_date = '2038-01-19 03:14:07' vuol dire "messaggio non cancellato"
CREATE DATABASE bacheca;
USE bacheca;

CREATE TABLE users (
    users_id INT NOT NULL AUTO_INCREMENT,
    username VARCHAR(40) NOT NULL,
    password VARCHAR(72) NOT NULL,
    role VARCHAR(10) NOT NULL,
    PRIMARY KEY (users_id)
);

CREATE TABLE messages (
    id INT NOT NULL AUTO_INCREMENT,
    text_message TEXT NOT NULL,
    date_message DATETIME NOT NULL,
    id_users INT NOT NULL,
    delection_date DATETIME NOT NULL DEFAULT '2038-01-19 03:14:07',
    status VARCHAR(10) NOT NULL DEFAULT 'published',
    PRIMARY KEY (id),
    FOREIGN KEY (id_users) REFERENCES users(users_id)
);

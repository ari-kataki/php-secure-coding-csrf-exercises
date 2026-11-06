-- ricostruito dalle query del codice (il dump originale non c'e')
-- il codice usa lo stesso nome di database della cartella 02 (bacheca) ma con tabelle diverse:
-- non usarli insieme nello stesso database
CREATE DATABASE bacheca;
USE bacheca;

CREATE TABLE users (
    users_id INT NOT NULL AUTO_INCREMENT,
    username VARCHAR(40) NOT NULL,
    password VARCHAR(72) NOT NULL,
    PRIMARY KEY (users_id)
);

CREATE TABLE messages (
    id INT NOT NULL AUTO_INCREMENT,
    text_message TEXT NOT NULL,
    date_message DATETIME NOT NULL,
    id_users INT NOT NULL,
    status VARCHAR(10) NOT NULL DEFAULT 'publish',
    PRIMARY KEY (id),
    FOREIGN KEY (id_users) REFERENCES users(users_id)
);

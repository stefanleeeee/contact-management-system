CREATE DATABASE IF NOT EXISTS Vova;

USE Vova;

CREATE TABLE IF NOT EXISTS Contacts (
   id INT PRIMARY KEY AUTO_INCREMENT,
   telephone_number VARCHAR(50),
   firstname VARCHAR(50),
   lastname VARCHAR(50),
   description VARCHAR(255)
);

DROP TABLE contacts;

DELETE FROM contacts;

SELECT * FROM contacts;

INSERT INTO contacts (telephone_number, firstname, lastname, DESCRIPTION) VALUES ('svbncxdfs', 'afsdfdssdf', 'sghjkjhyjksdf', 'qweqsdf');
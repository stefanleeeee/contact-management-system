CREATE DATABASE IF NOT EXISTS Vova;

USE Vova;

CREATE TABLE IF NOT EXISTS Contacts (
   id INT PRIMARY KEY AUTO_INCREMENT,
   telephone_number VARCHAR(20),
   firstname VARCHAR(50),
   lastname VARCHAR(50),
   description VARCHAR(255),
   contactphoto BLOB
);
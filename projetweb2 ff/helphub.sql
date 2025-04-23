-- Base de données HelpHub
CREATE DATABASE IF NOT EXISTS helphub;
USE helphub;

CREATE TABLE associations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100),
    adresse VARCHAR(255),
    email VARCHAR(100),
    cin CHAR(8),
    identifiant_fiscal VARCHAR(10),
    logo VARCHAR(255),
    pseudo VARCHAR(50),
    mot_de_passe VARCHAR(255)
);

CREATE TABLE donateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100),
    prenom VARCHAR(100),
    email VARCHAR(100),
    cin CHAR(8),
    pseudo VARCHAR(50),
    mot_de_passe VARCHAR(255)
);

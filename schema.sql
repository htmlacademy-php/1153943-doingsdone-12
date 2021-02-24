CREATE DATABASE `schema`
    DEFAULT CHARACTER SET utf8
    DEFAULT COLLATE utf8_general_ci;

USE `schema`;

CREATE TABLE `list` (
    `id` INT(255) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `title` text(50) NOT NULL
);

CREATE TABLE `users` (
    `id` INT(255) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `email` varchar(50) NOT NULL UNIQUE,
    `password` varchar(255) NOT NULL,
    `name` varchar(20) NOT NULL
);

CREATE TABLE `tasks` (
    `id` INT(255) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `list_id` INT NOT NULL,
    `title` text(50) NOT NULL,
    `period` text(255),
    `file` varchar(255) DEFAULT NULL,
    `date_deadline` DATETIME,
    `is_done` tinyint(1) default 0
);

CREATE FULLTEXT INDEX task_search ON tasks(title);

-- database.sql 
-- task_management
CREATE DATABASE IF NOT EXISTS task_management;
USE task_management;

CREATE TABLE IF NOT EXISTS tarefas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL UNIQUE,
    custo DECIMAL(10,2) NOT NULL,
    data_limite DATE NOT NULL,
    ordem INT NOT NULL UNIQUE
);

select nome, custo, data_limite, ordem, id from tarefas;
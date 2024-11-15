<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'task_management');

try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die("Conexão falhou: " . $conn->connect_error);
    }
    $conn->set_charset("utf8");
} catch (mysqli_sql_exception $e) {
    die("Erro de conexão: " . $e->getMessage());
}

// Criar o banco de dados e a tabela se não existirem
$sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
if ($conn->query($sql) === TRUE) {
    $conn->select_db(DB_NAME);
    
    $sql = "CREATE TABLE IF NOT EXISTS tarefas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(255) NOT NULL UNIQUE,
        custo DECIMAL(10,2) NOT NULL,
        data_limite DATE NOT NULL,
        ordem INT NOT NULL UNIQUE
    )";
    
    if (!$conn->query($sql)) {
        die("Erro ao criar tabela: " . $conn->error);
    }
}
?>
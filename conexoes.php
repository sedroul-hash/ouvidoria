<?php
$host = "127.0.0.1";
$user = "root";
$pass = "";
$db = "dbouvidoria";
$porta = 3306; // Tente mudar de 3307 para 3306

$conn = new mysqli($host, $user, $pass, $db, $porta);

// Verificação de erro para versões recentes do PHP (estilo exception)
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}
?>
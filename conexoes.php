<?php
$host = "127.0.0.1";
$usuario = "root";
$senha = "";
$banco = "dbouvidoria";
$porta = "3307";

$conn = new mysqli($host, $usuario, $senha, $banco);

// Verifica se deu erro
if ($conn->connect_error) {
    die("Erro ao conectar: " . $conn->connect_error);
}
?>

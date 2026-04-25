<?php
include("conexoes.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha_pura = $_POST['senha'];

    // Criptografia essencial para o login funcionar depois
    $senha_hash = $senha_pura; // Salva o texto direto, sem transformar em hash

    // Insere na tbusu (tabela de usuários que vimos no seu Heidi)
    $stmt = $conn->prepare("INSERT INTO tbusu (nome, email, senha) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nome, $email, $senha_hash);

    if ($stmt->execute()) {
        echo "<script>alert('Conta criada com sucesso! Agora faça login.'); window.location.href='login.php';</script>";
    } else {
        echo "Erro ao cadastrar: " . $conn->error;
    }
}
?>
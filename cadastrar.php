<?php
include("conexoes.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Aplicando trim() em tudo, inclusive na senha
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha_pura = trim($_POST['senha']); 

    // Campos obrigatórios vazios?
    if (empty($nome) || empty($email) || empty($senha_pura)) {
        echo "<script>alert('Por favor, preencha todos os campos!'); window.history.back();</script>";
        exit();
    }

    // 1. VALIDAÇÃO DO FORMATO DO E-MAIL
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Por favor, insira um e-mail válido.'); window.history.back();</script>";
        exit();
    }

    // 2. VALIDAÇÃO DE DUPLICIDADE DE E-MAIL (Bloqueia e-mails repetidos)
    $stmt_check = $conn->prepare("SELECT EMAIL FROM tbusuarios WHERE EMAIL = ?");
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        echo "<script>alert('Este e-mail já está cadastrado em nosso sistema!'); window.history.back();</script>";
        $stmt_check->close();
        exit();
    }
    $stmt_check->close();

    // 3. VALIDAÇÃO DE TAMANHO MÍNIMO DA SENHA
    if (strlen($senha_pura) < 8) {
        echo "<script>alert('A senha deve ter pelo menos 8 caracteres válidos.'); window.history.back();</script>";
        exit();
    }

    // 4. VALIDAÇÃO DE COMPLEXIDADE DA SENHA
    if (!preg_match('/[A-Z]/', $senha_pura) || !preg_match('/[a-z]/', $senha_pura) || !preg_match('/[0-9]/', $senha_pura)) {
        echo "<script>alert('Sua senha deve conter pelo menos uma letra maiúscula, uma letra minúscula e um número.'); window.history.back();</script>";
        exit();
    }

    // Se passou em tudo, gera o hash seguro
    $senha_hash = password_hash($senha_pura, PASSWORD_DEFAULT); 

    // Inserção no banco de dados
    $stmt = $conn->prepare("INSERT INTO tbusuarios (NOME, EMAIL, SENHA) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nome, $email, $senha_hash);

    if ($stmt->execute()) {
        $stmt->close();
        echo "<script>alert('Conta criada com sucesso! Agora faça login.'); window.location.href='login.php';</script>";
        exit();
    } else {
        echo "Erro ao cadastrar: " . $conn->error;
    }
}
?>
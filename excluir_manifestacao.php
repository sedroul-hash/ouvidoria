<?php
session_start();

// VALIDAÇÃO DE SEGURANÇA: Só permite o acesso se for um administrador logado
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true || !isset($_SESSION['nivel']) || $_SESSION['nivel'] !== 'admin') {
    header("Location: login.php"); 
    exit();
}

// Importa a conexão com o banco de dados
include 'conexoes.php'; 

if (isset($_GET['id'])) {
    // intval() garante que o ID recebido seja puramente um número inteiro (proteção extra)
    $id = intval($_GET['id']);

    if ($id > 0) {
        // Tenta deletar usando a nomenclatura em MAIÚSCULO (Padrão do banco principal)
        $stmt = $conn->prepare("DELETE FROM TBMANIFEST WHERE IDMANIFEST = ?");
        if ($stmt) {
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
        } else {
            // Plano B: Tenta deletar usando letras minúsculas (caso use banco local de testes em minúsculo)
            $stmt2 = $conn->prepare("DELETE FROM tbmanifest WHERE idmanifest = ?");
            if ($stmt2) {
                $stmt2->bind_param("i", $id);
                $stmt2->execute();
                $stmt2->close();
            }
        }
    }
}

// Redireciona de volta para o painel de administração atualizado
header("Location: adm.php");
exit();
?>
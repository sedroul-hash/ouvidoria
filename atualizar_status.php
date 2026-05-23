<?php
session_start();

// VALIDAÇÃO DE SEGURANÇA: Só permite o acesso se for um administrador logado
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true || !isset($_SESSION['nivel']) || $_SESSION['nivel'] !== 'admin') {
    header("Location: login.php"); 
    exit();
}

// Importa a conexão com o banco de dados
include 'conexoes.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id']) && isset($_POST['status'])) {
    $id = intval($_POST['id']);
    $novo_status = trim($_POST['status']);

    // Lista de status válidos aceitos pelo sistema para evitar dados corrompidos
    $status_permitidos = ["Pendente", "Em análise", "Resolvido"];

    if ($id > 0 && in_array($novo_status, $status_permitidos)) {
        
        // Tenta atualizar usando a nomenclatura em MAIÚSCULO (Padrão do banco principal)
        $stmt = $conn->prepare("UPDATE TBMANIFEST SET STATUS = ? WHERE IDMANIFEST = ?");
        if ($stmt) {
            $stmt->bind_param("si", $novo_status, $id);
            $stmt->execute();
            $stmt->close();
        } else {
            // Plano B: Tenta atualizar usando letras minúsculas
            $stmt2 = $conn->prepare("UPDATE tbmanifest SET status = ? WHERE idmanifest = ?");
            if ($stmt2) {
                $stmt2->bind_param("si", $novo_status, $id);
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
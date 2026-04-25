<?php
include("conexao.php"); // Chama a conexão

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Pega o que foi digitado no formulário
    $tipo_texto = $_POST['tipo']; 
    $mensagem = $_POST['mensagem'];

    // 1. Primeiro buscamos o ID do tipo selecionado
    $sql_tipo = "SELECT idtipo FROM TBTIPO WHERE tipo = '$tipo_texto'";
    $res_tipo = $conn->query($sql_tipo);
    $dados_tipo = $res_tipo->fetch_assoc();
    $idtipo = $dados_tipo['idtipo'];

    // 2. Inserimos na tabela de manifestações
    $sql = "INSERT INTO TBMANIFEST (idtipo, manifest, status) 
            VALUES ('$idtipo', '$mensagem', 'Em análise')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Enviado com sucesso!'); window.location.href='principal.php';</script>";
    } else {
        echo "Erro: " . $conn->error;
    }
}
?>

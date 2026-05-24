<?php
include("conexoes.php"); // Chama a conexão

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Pega o que foi digitado no formulário
    $tipo_texto = $_POST['tipo']; 
    $mensagem = $_POST['mensagem'];

    // 1. Buscamos o ID do tipo selecionado
    $stmt_tipo = $conn->prepare("SELECT idtipo FROM tbtipo WHERE tipo = ?");
    $stmt_tipo->bind_param("s", $tipo_texto);
    $stmt_tipo->execute();
    $res_tipo = $stmt_tipo->get_result();

    if ($res_tipo->num_rows > 0) {
        $dados_tipo = $res_tipo->fetch_assoc();
        $idtipo = $dados_tipo['idtipo'];

        // 2. Inserimos na tabela de manifestações
        $status = 'Em análise';
        
        $stmt_manifest = $conn->prepare("INSERT INTO tbmanifest (idtipo, manifest, status) VALUES (?, ?, ?)");
        $stmt_manifest->bind_param("iss", $idtipo, $mensagem, $status);

        if ($stmt_manifest->execute()) {
            // RETORNO SIMPLES: O JavaScript do 'anonimo.php' vai ler isso e gerar o protocolo na tela
            echo "SUCESSO"; 
        } else {
            echo "Erro ao salvar manifestação: " . $stmt_manifest->error;
        }
        
        $stmt_manifest->close();
    } else {
        echo "Erro: O tipo de manifestação ('" . htmlspecialchars($tipo_texto) . "') não foi encontrado no banco de dados.";
    }

    $stmt_tipo->close();
}
?>
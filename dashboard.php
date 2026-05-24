<?php
session_start();

if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: login.php");
    exit();
}

$nome_usuario = $_SESSION['usuario'];
$nivel_acesso = $_SESSION['nivel']; 
$idusu = isset($_SESSION['idusuario']) ? intval($_SESSION['idusuario']) : 0; 

include("conexoes.php");

$erro = ""; 
$sucesso = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['assunto']) && isset($_POST['mensagem']) && isset($_POST['tipo'])) {
        
        $assunto = trim($_POST['assunto']); 
        $tipo = intval($_POST['tipo']);
        $mensagem = trim($_POST['mensagem']);
        $status = "Pendente";
        $idadm = null; 

        // Unificando o assunto e a mensagem para salvar na coluna MANIFEST do seu banco
        $manifestacao_completa = "[Assunto: " . $assunto . "] " . $mensagem;

        if (empty($tipo) || empty($assunto) || empty($mensagem)) {
            $erro = "Por favor, preencha todos os campos.";
        } else {
            // Ajustado para refletir a sua estrutura real: idusu, idtipo, idadm, manifest, status
            $stmt_manifest = $conn->prepare("INSERT INTO tbmanifest (IDUSU, IDTIPO, IDADM, MANIFEST, STATUS) VALUES (?, ?, ?, ?, ?)");
            
            if ($stmt_manifest) {
                $stmt_manifest->bind_param("iiiss", $idusu, $tipo, $idadm, $manifestacao_completa, $status);
                
                if ($stmt_manifest->execute()) {
                    $stmt_manifest->close();
                    header("Location: dashboard.php?sucesso=1");
                    exit();
                } else {
                    $erro = "Erro ao enviar manifestação: " . $conn->error;
                }
            } else {
                $erro = "Erro interno no servidor de dados.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Painel - Ouvidoria Dom Walfrido</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">

  <style>
    :root {
      --verde-dw: #14462a;
      --verde-claro: #1a5c38;
      --laranja-dw: #f37021;
      --fundo: #f8fafc;
      --branco: #ffffff;
    }

    body {
      font-family: 'Outfit', sans-serif;
      background-color: var(--fundo);
      margin: 0;
    }

    /* SIDEBAR MODERNA */
    .sidebar {
      width: 260px;
      height: 100vh;
      background: var(--verde-dw);
      color: white;
      position: fixed;
      padding: 30px 20px;
      box-shadow: 4px 0 10px rgba(0,0,0,0.1);
      z-index: 1000;
    }

    .sidebar .brand {
      text-align: center;
      margin-bottom: 40px;
      border-bottom: 1px solid rgba(255,255,255,0.1);
      padding-bottom: 20px;
    }

    .sidebar h4 {
      font-weight: 700;
      font-size: 1.2rem;
      margin: 0;
      letter-spacing: 1px;
    }

    .sidebar p {
      font-size: 0.75rem;
      opacity: 0.7;
      text-transform: uppercase;
      margin: 5px 0 0;
    }

    .sidebar-logo {
      width: 60px;      
      height: auto;
      margin-bottom: 15px;
    }

    .nav-btn {
      width: 100%;
      margin-top: 12px;
      border: none;
      padding: 12px 15px;
      border-radius: 12px;
      background: transparent;
      color: rgba(255,255,255,0.8);
      text-align: left;
      font-weight: 500;
      transition: 0.3s;
      text-decoration: none;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .nav-btn:hover {
      background: rgba(255,255,255,0.1);
      color: white;
    }

    /* CONTEÚDO PRINCIPAL */
    .content {
      margin-left: 260px;
      padding: 40px;
    }

    .welcome-text {
      margin-bottom: 30px;
    }

    .welcome-text h2 {
      color: var(--verde-dw);
      font-weight: 700;
    }

    /* CARDS DE STATUS */
    .status-card {
      padding: 25px;
      border-radius: 20px;
      color: white;
      border: none;
      box-shadow: 0 8px 20px rgba(0,0,0,0.05);
      transition: 0.3s;
    }

    .status-card:hover { transform: translateY(-5px); }

    .bg-dw-verde { background: linear-gradient(135deg, #1a5c38, #2e7d32); }
    .bg-dw-laranja { background: linear-gradient(135deg, #f37021, #ff9800); }
    .bg-dw-azul { background: linear-gradient(135deg, #14462a, #0f8b8d); }

    .status-card h5 { font-size: 0.9rem; opacity: 0.9; text-transform: uppercase; font-weight: 600; }
    .status-card h2 { font-size: 2.5rem; font-weight: 700; margin: 0; }

    /* BOXES DE CONTEÚDO */
    .card-box {
      background: var(--branco);
      padding: 30px;
      border-radius: 20px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.04);
      margin-bottom: 30px;
      border: 1px solid rgba(0,0,0,0.02);
    }

    .card-box h4 {
      color: var(--verde-dw);
      font-weight: 700;
      margin-bottom: 20px;
      font-size: 1.25rem;
    }

    .form-control {
      border-radius: 10px;
      border: 1px solid #e2e8f0;
      padding: 12px;
      margin-bottom: 15px;
    }

    .form-control:focus {
      border-color: var(--laranja-dw);
      box-shadow: 0 0 0 0.25rem rgba(243, 112, 33, 0.1);
    }

    .btn-enviar {
      background: var(--verde-dw);
      color: white;
      border-radius: 10px;
      border: none;
      padding: 12px;
      font-weight: 600;
      transition: 0.3s;
      width: 100%;
    }

    .btn-enviar:hover {
      background: var(--verde-claro);
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    /* LISTA DE SOLICITAÇÕES */
    .solicitacao-item {
      padding: 15px;
      border-radius: 12px;
      background: #f8fafc;
      margin-bottom: 10px;
      border-left: 5px solid var(--laranja-dw);
      transition: 0.2s;
    }

    .solicitacao-item:hover { background: #f1f5f9; }

    .badge-status {
      background: var(--verde-dw);
      color: white;
      padding: 4px 10px;
      border-radius: 20px;
      font-size: 0.7rem;
      text-transform: uppercase;
    }

    .badge-tipo {
      background: #e2e8f0;
      color: #475569;
      padding: 3px 8px;
      border-radius: 6px;
      font-size: 0.75rem;
      font-weight: 500;
    }

    /* RESPONSIVO */
    @media(max-width: 768px){
      .sidebar { width: 100%; height: auto; position: relative; padding: 20px; }
      .content { margin-left: 0; padding: 20px; }
    }
  </style>
</head>

<body>

<div class="sidebar">
  <div class="brand">
    <img src="logo_dw.png" alt="Dom Walfrido" class="sidebar-logo">
    <h4>DOM WALFRIDO</h4>
    <p>Ouvidoria Digital</p>
  </div>
  <a href="#novo" class="nav-btn">Nova Manifestação</a>
  <a href="#lista-hist" class="nav-btn">Minhas Solicitações</a>
  <div style="height: 20vh;"></div>
  <button class="nav-btn" onclick="logout()" style="color: #ff9b9b;">Sair</button>
</div>

<div class="content">
  <div class="welcome-text">
    <h2>Olá, <?php echo htmlspecialchars($nome_usuario, ENT_QUOTES, 'UTF-8'); ?>!</h2>
    <p class="text-muted">Acompanhe suas interações com a nossa instituição</p>
  </div>

  <div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="status-card bg-dw-verde">
            <h5>Resolvidas</h5>
            <?php
                $stmt1 = $conn->prepare("SELECT COUNT(*) as total FROM tbmanifest WHERE idusu = ? AND status = 'Resolvido'");
                $stmt1->bind_param("i", $idusu);
                $stmt1->execute();
                $res1 = $stmt1->get_result()->fetch_assoc();
                echo "<h2>" . $res1['total'] . "</h2>";
                $stmt1->close();
            ?>
        </div>
    </div>
    <div class="col-md-4">
        <div class="status-card bg-dw-laranja">
            <h5>Em análise / Pendente</h5>
            <?php
                $stmt2 = $conn->prepare("SELECT COUNT(*) as total FROM tbmanifest WHERE idusu = ? AND status != 'Resolvido'");
                $stmt2->bind_param("i", $idusu);
                $stmt2->execute();
                $res2 = $stmt2->get_result()->fetch_assoc();
                echo "<h2>" . $res2['total'] . "</h2>";
                $stmt2->close();
            ?>
        </div>
    </div>
    <div class="col-md-4">
        <div class="status-card bg-dw-azul">
            <h5>Total enviado</h5>
            <?php
                $stmt3 = $conn->prepare("SELECT COUNT(*) as total FROM tbmanifest WHERE idusu = ?");
                $stmt3->bind_param("i", $idusu);
                $stmt3->execute();
                $res3 = $stmt3->get_result()->fetch_assoc();
                echo "<h2>" . $res3['total'] . "</h2>";
                $stmt3->close();
            ?>
        </div>
    </div>
  </div>

  <form method="POST" id="novo" action="dashboard.php">
    <div class="card-box">
        <h4>Nova Manifestação</h4>
        <?php 
        if(!empty($erro)) echo '<div class="alert alert-danger">'. htmlspecialchars($erro) .'</div>';
        if(isset($_GET['sucesso'])) echo '<div class="alert alert-success">Manifestação enviada com sucesso!</div>'; 
        ?>
        <input name="assunto" class="form-control" placeholder="Assunto da mensagem" required>
        <select name="tipo" class="form-control" required>
          <option value="">Selecione o Tipo</option>
          <option value="1">Reclamação</option>
          <option value="2">Sugestão</option>
          <option value="3">Elogio</option>
          <option value="4">Denúncia</option>
        </select>
        <textarea name="mensagem" class="form-control" rows="4" placeholder="Descreva detalhadamente..." required></textarea>
        <button type="submit" class="btn-enviar">Enviar Manifestação</button>
    </div>
  </form>

  <div class="card-box" id="lista-hist">
    <h4>Histórico Recente</h4>
    <div id="lista">
        <?php
        $stmt_lista = $conn->prepare("SELECT idmanifest, idtipo, manifest, status, data_envio FROM tbmanifest WHERE idusu = ? ORDER BY idmanifest DESC");
        $stmt_lista->bind_param("i", $idusu);
        $stmt_lista->execute();
        $result = $stmt_lista->get_result();

        $tipos_nome = [1 => "Reclamação", 2 => "Sugestão", 3 => "Elogio", 4 => "Denúncia"];

        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $data_formatada = (!empty($row['data_envio'])) ? date('d/m/Y H:i', strtotime($row['data_envio'])) : 'Não informada';
                $status_atual = (!empty($row['status'])) ? $row['status'] : 'Pendente';
                
                $tipo_id = intval($row['idtipo']);
                $tipo_texto = isset($tipos_nome[$tipo_id]) ? $tipos_nome[$tipo_id] : "Outros";

                $cor_status = "var(--laranja-dw)";
                if($status_atual == "Resolvido") { $cor_status = "#2e7d32"; }
                elseif($status_atual == "Pendente") { $cor_status = "#475569"; }

                echo '
                <div class="solicitacao-item d-flex justify-content-between align-items-center">
                    <div>
                        <div class="d-flex gap-2 align-items-center mb-2">
                            <span class="badge-status">#'. $row['idmanifest'] .'</span>
                            <span class="badge-tipo">'. $tipo_texto .'</span>
                        </div>
                        <p class="text-dark mb-1">'. nl2br(htmlspecialchars($row['manifest'], ENT_QUOTES, 'UTF-8')) .'</p>
                        <small class="text-muted">Enviado em: '. $data_formatada .'</small>
                    </div>
                    <div class="text-end">
                        <span style="color: '. $cor_status .'; font-weight: 700; font-size: 0.95rem;">'. htmlspecialchars($status_atual) .'</span>
                    </div>
                </div>';
            }
        } else {
            echo '<p class="text-muted text-center py-4">Nenhuma solicitação enviada ainda.</p>';
        }
        $stmt_lista->close();
        ?>
    </div>
  </div>
</div>

<script>
function logout(){
  if(confirm("Deseja realmente sair?")) { window.location.href = "logout.php"; }
}
</script>
</body>
</html>
<?php
// 1. Ativa a exibição de erros ocultos do PHP para diagnóstico imediato
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. Inicia a sessão de forma segura
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 3. VALIDAÇÃO DE SEGURANÇA: Se não for admin, cancela o acesso
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true || !isset($_SESSION['nivel']) || $_SESSION['nivel'] !== 'admin') {
    header("Location: login.php"); 
    exit();
}

// 4. Importa a conexão com o banco de dados
include 'conexoes.php'; 

// Identificação flexível das tabelas (Aceita maiúsculas e minúsculas)
$tabela_manifest = "tbmanifest";
$coluna_id = "idmanifest";

// Valida nome exato da tabela no banco
$check_table = $conn->query("SHOW TABLES LIKE 'TBMANIFEST'");
if ($check_table && $check_table->num_rows > 0) {
    $tabela_manifest = "TBMANIFEST";
    $coluna_id = "IDMANIFEST";
}

// 5. BUSCA NO BANCO: Busca todas as manifestações ordenadas pela mais recente
$resultado = $conn->query("SELECT * FROM $tabela_manifest ORDER BY $coluna_id DESC");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Painel Admin - Ouvidoria Dom Walfrido</title>

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

    /* SIDEBAR MODERNA (IGUAL À DASHBOARD) */
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
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .nav-btn:hover {
      background: rgba(255,255,255,0.1);
      color: white;
    }

    .nav-btn.active {
      background: var(--laranja-dw);
      color: white;
      box-shadow: 0 4px 12px rgba(243, 112, 33, 0.3);
    }

    /* CONTEÚDO PRINCIPAL */
    .content {
      margin-left: 260px;
      padding: 40px;
    }

    .welcome-text h2 {
      color: var(--verde-dw);
      font-weight: 700;
    }

    /* CARDS DE STATUS GRADIENTES */
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
      margin-bottom: 25px;
      font-size: 1.25rem;
    }

    /* ITENS DA LISTA */
    .solicitacao-item {
      padding: 25px;
      border-radius: 16px;
      background: #f8fafc;
      margin-bottom: 20px;
      border-left: 6px solid var(--verde-dw);
      transition: 0.3s;
    }
    
    .solicitacao-item:hover {
      background: #f1f5f9;
      transform: translateX(4px);
    }

    /* Cores de borda dinâmicas com base no status */
    .border-pendente { border-left-color: #94a3b8; }
    .border-analise { border-left-color: var(--laranja-dw); }
    .border-resolvido { border-left-color: #22c55e; }

    .badge-status-local {
      color: white;
      padding: 5px 12px;
      border-radius: 20px;
      font-size: 0.75rem;
      text-transform: uppercase;
      font-weight: 600;
    }
    .badge-p { background-color: #64748b; }
    .badge-a { background-color: var(--laranja-dw); }
    .badge-r { background-color: #22c55e; }

    .badge-anon {
      background: #334155;
      color: white;
      font-size: 10px;
      padding: 4px 10px;
      border-radius: 6px;
      text-transform: uppercase;
    }

    .status-select {
      padding: 8px 14px;
      border-radius: 10px;
      border: 1px solid #e2e8f0;
      font-size: 0.85rem;
      font-weight: 500;
      background-color: white;
      outline: none;
      color: #334155;
      cursor: pointer;
    }

    .status-select:focus {
      border-color: var(--laranja-dw);
    }

    .btn-delete {
      background: #fee2e2;
      color: #b91c1c;
      border: none;
      padding: 8px 14px;
      border-radius: 10px;
      font-size: 0.85rem;
      font-weight: 600;
      transition: 0.3s;
      text-decoration: none;
    }

    .btn-delete:hover { 
      background: #fca5a5; 
    }

    @media(max-width: 768px){
      .sidebar { width: 100%; height: auto; position: relative; padding: 20px; }
      .content { margin-left: 0; padding: 20px; }
    }
  </style>
</head>

<body>

<div class="sidebar">
  <div class="brand text-center mb-4">
    <img src="logo_dw.png" alt="Dom Walfrido" class="sidebar-logo">
    <h4>PAINEL ADMIN</h4>
    <p>Controle de Ouvidoria</p>
  </div>
  <button id="btn-todas" class="nav-btn active" onclick="filtrar('todas')">Todas Recebidas</button>
  <button id="btn-pendente" class="nav-btn" onclick="filtrar('Pendente')">Pendentes</button>
  <button id="btn-analise" class="nav-btn" onclick="filtrar('Em análise')">Em análise</button>
  <button id="btn-resolvido" class="nav-btn" onclick="filtrar('Resolvido')">Resolvidas</button>
  <div style="height: 30vh;"></div>
  <button class="nav-btn" onclick="logout()" style="color: #ff9b9b;">Sair do Painel</button>
</div>

<div class="content">
  <div class="welcome-text mb-4 d-flex justify-content-between align-items-center">
    <div>
        <h2>Gestão de Manifestações</h2>
        <p class="text-muted">Painel geral de auditoria e resposta a solicitações.</p>
    </div>
    <span class="text-muted fs-6 mb-3">Admin logado: <strong style="color: var(--verde-dw);"><?php echo htmlspecialchars($_SESSION['usuario']); ?></strong></span>
  </div>

  <div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="status-card bg-dw-azul">
            <h5>Pendentes Globais</h5>
            <?php
                $res = $conn->query("SELECT COUNT(*) as total FROM $tabela_manifest WHERE status = 'Pendente' OR status = 'Aberto'");
                $row = $res->fetch_assoc();
                echo "<h2>" . ($row ? $row['total'] : 0) . "</h2>";
            ?>
        </div>
    </div>
    <div class="col-md-4">
        <div class="status-card bg-dw-laranja">
            <h5>Em análise</h5>
            <?php
                $res = $conn->query("SELECT COUNT(*) as total FROM $tabela_manifest WHERE status = 'Em análise'");
                $row = $res->fetch_assoc();
                echo "<h2>" . ($row ? $row['total'] : 0) . "</h2>";
            ?>
        </div>
    </div>
    <div class="col-md-4">
        <div class="status-card bg-dw-verde">
            <h5>Resolvidos</h5>
            <?php
                $res = $conn->query("SELECT COUNT(*) as total FROM $tabela_manifest WHERE status = 'Resolvido'");
                $row = $res->fetch_assoc();
                echo "<h2>" . ($row ? $row['total'] : 0) . "</h2>";
            ?>
        </div>
    </div>
  </div>

  <div class="card-box">
    <h4>Histórico de Ocorrências</h4>
    <div id="lista">
      
      <?php 
      if ($resultado && $resultado->num_rows > 0):
          while($row = $resultado->fetch_assoc()): 
              $id_manifest = isset($row['IDMANIFEST']) ? $row['IDMANIFEST'] : $row['idmanifest'];
              $manifest_text = isset($row['MANIFEST']) ? $row['MANIFEST'] : $row['manifest'];
              $status_atual = isset($row['STATUS']) ? $row['STATUS'] : $row['status'];
              $id_usuario = isset($row['IDUSU']) ? $row['IDUSU'] : (isset($row['idusu']) ? $row['idusu'] : null);

              // Compatibiliza o status padrão 'Aberto' vindo da dashboard como 'Pendente'
              if(strtolower($status_atual) == 'aberto') { $status_atual = 'Pendente'; }

              // Configurações visuais dinâmicas baseadas na Dashboard
              $classe_borda = "border-pendente";
              $classe_badge = "badge-p";
              if ($status_atual == "Em análise") { $classe_borda = "border-analise"; $classe_badge = "badge-a"; }
              if ($status_atual == "Resolvido") { $classe_borda = "border-resolvido"; $classe_badge = "badge-r"; }
      ?>
      
      <div class="solicitacao-item <?php echo $classe_borda; ?>" data-status="<?php echo htmlspecialchars($status_atual); ?>">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <span>
              <span class="badge bg-secondary me-2" style="font-size: 11px; padding: 4px 8px;">#<?php echo $id_manifest; ?></span>
              <?php if (empty($id_usuario) || $id_usuario == 0): ?>
                  <span class="badge-anon">Anônimo</span>
              <?php else: ?>
                  <span class="badge bg-dark text-white" style="font-size: 10px; text-transform: uppercase;">Usuário Identificado</span>
              <?php endif; ?>
          </span>
          <span class="badge-status-local <?php echo $classe_badge; ?>"><?php echo htmlspecialchars($status_atual); ?></span>
        </div>
        
        <div class="bg-white p-3 rounded-3 my-3 border" style="background-color: #ffffff !important;">
          <p class="text-dark m-0" style="font-size: 0.95rem; line-height: 1.5; color: #1e293b !important;">
              <?php echo nl2br(htmlspecialchars($manifest_text, ENT_QUOTES, 'UTF-8')); ?>
          </p>
        </div>

        <div class="d-flex align-items-center justify-content-between mt-3">
          <form action="atualizar_status.php" method="POST" class="d-inline">
             <input type="hidden" name="id" value="<?php echo $id_manifest; ?>">
             <div class="d-flex align-items-center gap-2">
                 <small class="text-muted fw-semibold">Alterar Status:</small>
                 <select name="status" class="status-select" onchange="this.form.submit()">
                    <option value="Pendente" <?php echo ($status_atual == 'Pendente') ? 'selected' : ''; ?>>Pendente</option>
                    <option value="Em análise" <?php echo ($status_atual == 'Em análise') ? 'selected' : ''; ?>>Em análise</option>
                    <option value="Resolvido" <?php echo ($status_atual == 'Resolvido') ? 'selected' : ''; ?>>Resolvido</option>
                 </select>
             </div>
          </form>

          <a href="excluir_manifestacao.php?id=<?php echo $id_manifest; ?>" 
             class="btn-delete" 
             onclick="return confirm('Tem certeza absoluta que deseja apagar permanentemente esta manifestação?')">
             Excluir Registro
          </a>
        </div>
      </div>

      <?php 
          endwhile; 
      else: 
      ?>
          <p class="text-muted text-center py-5">Nenhuma manifestação encontrada no sistema até o momento.</p>
      <?php endif; ?>

    </div>
  </div>
</div>

<script>
function filtrar(statusAlvo) {
  document.querySelectorAll('.sidebar .nav-btn').forEach(btn => btn.classList.remove('active'));
  
  if (statusAlvo === 'todas') document.getElementById('btn-todas').classList.add('active');
  if (statusAlvo === 'Pendente') document.getElementById('btn-pendente').classList.add('active');
  if (statusAlvo === 'Em análise') document.getElementById('btn-analise').classList.add('active');
  if (statusAlvo === 'Resolvido') document.getElementById('btn-resolvido').classList.add('active');

  let itens = document.querySelectorAll('#lista .solicitacao-item');
  
  itens.forEach(item => {
    let statusItem = item.getAttribute('data-status');
    if (statusAlvo === 'todas' || statusItem === statusAlvo) {
      item.style.display = 'block';
    } else {
      item.style.display = 'none';
    }
  });
}

function logout(){
  if(confirm("Deseja realmente sair do painel administrativo?")) {
    window.location.href = "logout.php";
  }
}
</script>

</body>
</html>
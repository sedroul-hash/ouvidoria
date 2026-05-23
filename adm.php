<?php
// 1. Ativa a exibição de erros ocultos do PHP para diagnóstico imediato
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. Inicia a sessão de forma segura
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 3. VALIDAÇÃO DE SEGURANÇA AJUSTADA: Se não for admin, cancela o acesso
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true || !isset($_SESSION['nivel']) || $_SESSION['nivel'] !== 'admin') {
    // Redireciona de forma limpa para a tela de login
    header("Location: login.php"); 
    exit();
}

// 4. Importa a conexão com o banco de dados
include 'conexoes.php'; 

// 5. BUSCA NO BANCO: Busca todas as manifestações ordenadas pela mais recente
$resultado = $conn->query("SELECT * FROM TBMANIFEST ORDER BY IDMANIFEST DESC");

if (!$resultado) {
    // Plano B automático caso o banco utilize letras minúsculas
    $resultado = $conn->query("SELECT * FROM tbmanifest ORDER BY idmanifest DESC");
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin - Gestão Dom Walfrido</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">

<style>
:root {
  --verde-dw: #14462a;
  --verde-claro: #1a5c38;
  --laranja-dw: #f37021;
  --fundo: #f1f5f9;
  --branco: #ffffff;
}

body {
  font-family: 'Outfit', sans-serif;
  background: var(--fundo);
  margin: 0;
}

/* SIDEBAR ADMIN */
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
  margin-bottom: 35px;
  border-bottom: 1px solid rgba(255,255,255,0.1);
  padding-bottom: 20px;
}

.sidebar h4 {
  font-weight: 700;
  font-size: 1.1rem;
  letter-spacing: 1px;
}

.sidebar p {
  font-size: 0.7rem;
  text-transform: uppercase;
  margin: 0;
  color: var(--laranja-dw);
}

.nav-btn {
  width: 100%;
  margin-top: 10px;
  border: none;
  padding: 12px 15px;
  border-radius: 10px;
  background: transparent;
  color: rgba(255,255,255,0.7);
  text-align: left;
  font-weight: 500;
  transition: 0.3s;
  display: flex;
  align-items: center;
  gap: 12px;
}

.nav-btn:hover {
  background: rgba(255,255,255,0.1);
  color: white;
}

.nav-btn.active {
  background: var(--laranja-dw);
  color: white;
}

/* CONTEÚDO */
.content {
  margin-left: 260px;
  padding: 40px;
}

.header-title {
  color: var(--verde-dw);
  font-weight: 700;
  margin-bottom: 30px;
}

/* CARDS DE MANIFESTAÇÃO */
.card-box {
  background: var(--branco);
  padding: 25px;
  border-radius: 15px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.05);
  margin-bottom: 20px;
}

.item {
  padding: 20px;
  border-bottom: 1px solid #f0f0f0;
  transition: 0.3s;
  border-radius: 10px;
}

.item:hover {
  background: #f8fafc;
}

.item:last-child { border-bottom: none; }

/* BADGES E STATUS */
.badge-anon {
  background: #334155;
  color: white;
  font-size: 10px;
  padding: 3px 8px;
  border-radius: 5px;
  text-transform: uppercase;
}

.status-select {
  padding: 6px 12px;
  border-radius: 8px;
  border: 1px solid #ddd;
  font-size: 0.85rem;
  outline: none;
}

.badge-status {
  padding: 5px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  display: inline-block;
}

/* Classes de cores baseadas no status real */
.status-pendente { background: #e2e8f0; color: #475569; }
.status-analise { background: #fef3c7; color: #92400e; }
.status-resolvido { background: #dcfce7; color: #166534; }

.btn-delete {
  background: #fee2e2;
  color: #b91c1c;
  border: none;
  padding: 6px 12px;
  border-radius: 8px;
  font-size: 0.85rem;
  font-weight: 600;
  transition: 0.3s;
  text-decoration: none;
  display: inline-block;
}

.btn-delete:hover { background: #fca5a5; }

@media(max-width: 768px){
  .sidebar { width: 100%; height: auto; position: relative; padding: 15px; }
  .content { margin-left: 0; padding: 20px; }
}
</style>
</head>

<body>

<div class="sidebar">
  <div class="brand">
    <img src="logo_dw.png" width="40" class="mb-2" alt="Logo">
    <h4>PAINEL ADMIN</h4>
    <p>Ouvidoria Dom Walfrido</p>
  </div>

  <button id="btn-todas" class="nav-btn active" onclick="filtrar('todas')">Todas Recebidas</button>
  <button id="btn-pendente" class="nav-btn" onclick="filtrar('Pendente')">Pendentes</button>
  <button id="btn-analise" class="nav-btn" onclick="filtrar('Em análise')">Em análise</button>
  <button id="btn-resolvido" class="nav-btn" onclick="filtrar('Resolvido')">Resolvidas</button>
  
  <div style="height: 30vh;"></div>
  
  <button class="nav-btn text-danger" onclick="logout()">Sair do Sistema</button>
</div>

<div class="content">

  <div class="header-title d-flex justify-content-between align-items-center">
    <h2>Gestão de Manifestações</h2>
    <span class="text-muted fs-6">Administrador: <strong><?php echo htmlspecialchars($_SESSION['usuario']); ?></strong></span>
  </div>

  <div class="card-box">
    <div id="lista">
      
      <?php 
      if ($resultado && $resultado->num_rows > 0):
          while($row = $resultado->fetch_assoc()): 
              // Mapeamento flexível para aceitar tanto colunas maiúsculas quanto minúsculas do banco
              $id_manifest = isset($row['IDMANIFEST']) ? $row['IDMANIFEST'] : $row['idmanifest'];
              $manifest_text = isset($row['MANIFEST']) ? $row['MANIFEST'] : $row['manifest'];
              $status_atual = isset($row['STATUS']) ? $row['STATUS'] : $row['status'];
              $id_usuario = isset($row['IDUSU']) ? $row['IDUSU'] : (isset($row['idusu']) ? $row['idusu'] : null);

              // Define a classe CSS correta da bolinha dependendo do status atual
              $classe_badge = "status-pendente";
              if ($status_atual == "Em análise") { $classe_badge = "status-analise"; }
              if ($status_atual == "Resolvido") { $classe_badge = "status-resolvido"; }
      ?>
      
      <div class="item" data-status="<?php echo htmlspecialchars($status_atual); ?>">
        <div class="d-flex justify-content-between align-items-center">
          <span>
              <strong>#<?php echo $id_manifest; ?></strong> 
              <?php if (empty($id_usuario) || $id_usuario == 0): ?>
                  <span class="badge-anon ms-2">Anônimo</span>
              <?php else: ?>
                  <span class="badge bg-secondary text-white ms-2" style="font-size: 10px;">Identificado</span>
              <?php endif; ?>
          </span>
          <span class="badge-status <?php echo $classe_badge; ?>"><?php echo htmlspecialchars($status_atual); ?></span>
        </div>
        
        <div class="mt-2">
          <p class="text-dark" style="font-size: 0.95rem;"><?php echo nl2br(htmlspecialchars($manifest_text, ENT_QUOTES, 'UTF-8')); ?></p>
        </div>

        <div class="d-flex align-items-center gap-2 mt-3">
          <form action="atualizar_status.php" method="POST" class="d-inline">
             <input type="hidden" name="id" value="<?php echo $id_manifest; ?>">
             <select name="status" class="status-select" onchange="this.form.submit()">
                <option value="Pendente" <?php echo ($status_atual == 'Pendente') ? 'selected' : ''; ?>>Pendente</option>
                <option value="Em análise" <?php echo ($status_atual == 'Em análise') ? 'selected' : ''; ?>>Em análise</option>
                <option value="Resolvido" <?php echo ($status_atual == 'Resolvido') ? 'selected' : ''; ?>>Resolvido</option>
             </select>
          </form>

          <a href="excluir_manifestacao.php?id=<?php echo $id_manifest; ?>" 
             class="btn-delete" 
             onclick="return confirm('Tem certeza absoluta que deseja apagar permanentemente esta manifestação?')">
             Excluir
          </a>
        </div>
      </div>

      <?php 
          endwhile; 
      else: 
      ?>
          <p class="text-muted text-center py-4">Nenhuma manifestação encontrada no banco de dados.</p>
      <?php endif; ?>

    </div>
  </div>

</div>

<script>
// Sistema dinâmico que filtra os cards na tela sem precisar recarregar a página
function filtrar(statusAlvo) {
  // Remove a classe ativa de todos os botões da barra lateral
  document.querySelectorAll('.sidebar .nav-btn').forEach(btn => btn.classList.remove('active'));
  
  // Adiciona a marcação laranja ao botão clicado
  if (statusAlvo === 'todas') document.getElementById('btn-todas').classList.add('active');
  if (statusAlvo === 'Pendente') document.getElementById('btn-pendente').classList.add('active');
  if (statusAlvo === 'Em análise') document.getElementById('btn-analise').classList.add('active');
  if (statusAlvo === 'Resolvido') document.getElementById('btn-resolvido').classList.add('active');

  let itens = document.querySelectorAll('#lista .item');
  
  itens.forEach(item => {
    let statusItem = item.getAttribute('data-status');
    
    if (statusAlvo === 'todas' || statusItem === statusAlvo) {
      item.style.display = 'block'; // Exibe o card
    } else {
      item.style.display = 'none';  // Oculta o card
    }
  });
}

function logout(){
  if(confirm("Deseja sair do painel administrativo?")) {
    window.location.href = "logout.php"; // Apaga a sessão com segurança
  }
}
</script>

</body>
</html>
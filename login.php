<?php
include("conexoes.php");
session_start();

$erro = ""; 
$sucesso = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // --- LÓGICA DE LOGIN ---
    if (isset($_POST['acao']) && $_POST['acao'] == 'login') {
        $email = $_POST['email'];
        $senha = $_POST['senha'];

        $stmt = $conn->prepare("SELECT idusu, nome, senha FROM tbusu WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $usuario = $result->fetch_assoc();
            
            // MUDANÇA AQUI: Comparamos o texto direto em vez de usar password_verify
            if ($senha == $usuario['senha']) { 
                $_SESSION['id_usuario'] = $usuario['idusu'];
                $_SESSION['usuario'] = $usuario['nome'];
                header("Location: dashboard.php");
                exit();
            } else {
                $erro = "Senha incorreta!";
            }
        } else {
            $erro = "Usuário não encontrado!";
        }
    }

    // --- LÓGICA DE CADASTRO ---
    if (isset($_POST['acao']) && $_POST['acao'] == 'cadastrar') {
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $senha_pura = $_POST['senha'];

        // MUDANÇA AQUI: Não usamos mais o password_hash, salvamos a senha direto
        $stmt = $conn->prepare("INSERT INTO tbusu (nome, email, senha) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nome, $email, $senha_pura);
        
        if ($stmt->execute()) {
            $sucesso = "Conta criada com sucesso! Faça login.";
        } else {
            $erro = "Erro ao cadastrar: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Acesso - Ouvidoria Dom Walfrido</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">

  <style>
    :root {
      --verde-dw: #14462a;
      --verde-claro: #1a5c38;
      --laranja-dw: #f37021;
      --gelo: #f4f7f6;
    }

    body {
      font-family: 'Outfit', sans-serif;
      background: linear-gradient(135deg, var(--verde-dw), var(--verde-claro));
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0;
    }

    .card-box {
      background: white;
      padding: 40px;
      border-radius: 24px;
      box-shadow: 0 15px 35px rgba(0,0,0,0.3);
      width: 100%;
      max-width: 400px;
      border-top: 6px solid var(--laranja-dw);
    }

    .school-logo { 
      width: 70px;
      height: 70px;
      object-fit: contain;
      margin-bottom: 15px;
      display: block;
      margin-left: auto;
      margin-right: auto;
    }

    .header-text {
      text-align: center;
      margin-bottom: 30px;
    }

    .header-text h1 {
      font-size: 1.4rem;
      font-weight: 700;
      color: var(--verde-dw);
      margin: 0;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .header-text span {
      font-size: 0.8rem;
      color: #666;
      text-transform: uppercase;
      font-weight: 400;
    }

    .title {
      text-align: center;
      font-weight: 600;
      margin-bottom: 20px;
      color: #333;
      font-size: 1.1rem;
    }

    .form-control {
      border-radius: 10px;
      padding: 12px 15px;
      border: 1px solid #ddd;
      background-color: #f9f9f9;
      margin-bottom: 15px;
    }

    .form-control:focus {
      border-color: var(--verde-dw);
      box-shadow: 0 0 0 0.25rem rgba(20, 70, 42, 0.1);
    }

    .btn-main {
      background: var(--verde-dw);
      color: white;
      border-radius: 10px;
      padding: 12px;
      width: 100%;
      border: none;
      font-weight: 600;
      transition: 0.3s;
    }

    .btn-main:hover {
      background: #0d2e1b;
      transform: translateY(-2px);
    }

    .btn-back {
      color: #777;
      text-decoration: none;
      font-size: 0.9rem;
      display: block;
      text-align: center;
      margin-top: 20px;
      transition: 0.3s;
    }

    .btn-back:hover {
      color: var(--laranja-dw);
    }

    .switch {
      text-align: center;
      margin-top: 20px;
      cursor: pointer;
      color: var(--verde-claro);
      font-weight: 500;
      font-size: 0.9rem;
    }

    .switch b {
      color: var(--laranja-dw);
    }

    .alert-msg {
      padding: 10px;
      border-radius: 8px;
      font-size: 0.85rem;
      margin-bottom: 15px;
      text-align: center;
    }
    .alert-danger { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }

    .hidden { display: none; }
  </style>
</head>

<body>
  <div class="card-box">
    <img src="logo_dw.png" alt="Dom Walfrido" class="school-logo">

    <div class="header-text">
      <h1>Dom Walfrido</h1>
      <span>Ouvidoria Digital</span>
    </div>

    <?php if($erro): ?>
        <div class="alert-msg alert-danger"><?php echo $erro; ?></div>
    <?php endif; ?>
    <?php if($sucesso): ?>
        <div class="alert-msg alert-success"><?php echo $sucesso; ?></div>
    <?php endif; ?>

    <form id="loginForm" action="login.php" method="POST">
      <input type="hidden" name="acao" value="login">
      <h3 class="title">Acesse sua conta</h3>
      <input type="email" name="email" class="form-control" placeholder="E-mail institucional" required>
      <input type="password" name="senha" class="form-control" placeholder="Senha" required>
      <button class="btn-main" type="submit">Entrar</button>
      <div class="switch" onclick="mostrarCadastro()">
        Não tem conta? <b>Cadastre-se</b>
      </div>
    </form>

    <form id="cadastroForm" class="hidden" action="login.php" method="POST">
      <input type="hidden" name="acao" value="cadastrar">
      <h3 class="title">Criar nova conta</h3>
      <input type="text" name="nome" class="form-control" placeholder="Nome completo" required>
      <input type="email" name="email" class="form-control" placeholder="E-mail" required>
      <input type="password" name="senha" class="form-control" placeholder="Crie uma senha" required>
      <button class="btn-main" type="submit" style="background: var(--laranja-dw);">Finalizar Cadastro</button>
      <div class="switch" onclick="mostrarLogin()">
        Já possui conta? <b>Fazer login</b>
      </div>
    </form>

    <a href="principal.php" class="btn-back">← Voltar para o início</a>
  </div>

  <script>
    function mostrarCadastro() {
      document.getElementById("loginForm").classList.add("hidden");
      document.getElementById("cadastroForm").classList.remove("hidden");
      // Limpa mensagens de erro ao trocar
      document.querySelectorAll('.alert-msg').forEach(el => el.style.display = 'none');
    }

    function mostrarLogin() {
      document.getElementById("cadastroForm").classList.add("hidden");
      document.getElementById("loginForm").classList.remove("hidden");
    }
  </script>
</body>
</html>
<?php
include("conexoes.php");
session_start(); // Sempre iniciado antes de qualquer saída

$erro = ""; 
$sucesso = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // --- LÓGICA DE LOGIN ---
    // CORRIGIDO: Agora verifica se a ação é 'login' (combina com o input hidden do HTML)
    if (isset($_POST['acao']) && $_POST['acao'] == 'login') {
        $email = trim($_POST['email']);
        $senha = $_POST['senha'];

        // Buscamos o usuário pelo e-mail informado
        $stmt = $conn->prepare("SELECT IDUSU, NOME, SENHA FROM tbusuarios WHERE EMAIL = ?");
        if (!$stmt) {
            die("Erro no prepare (login): " . $conn->error);
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        // Se encontrou o e-mail no banco de dados
        if ($result->num_rows > 0) {
            $usuario = $result->fetch_assoc();
            
            // CORRIGIDO: Utilizando password_verify para checar a senha criptografada
            if (password_verify($senha, $usuario['SENHA'])) { 
                $_SESSION['idusuario'] = $usuario['IDUSU']; 
                $_SESSION['usuario'] = $usuario['NOME'];
                $_SESSION['logado'] = true;
                
                $stmt->close();
                header("Location: dashboard.php");
                exit();
            } else {
                $erro = "E-mail ou senha incorretos!";
            }
        } else {
            $erro = "E-mail ou senha incorretos!"; 
        }

        $stmt->close();
    }

    // --- LÓGICA DE CADASTRO ---
    if (isset($_POST['acao']) && $_POST['acao'] == 'cadastrar') {
        $nome = trim($_POST['nome']);
        $email = trim($_POST['email']);
        $senha_pura = $_POST['senha'];

        // CORRIGIDO: Segurança em primeiro lugar, gerando hash da senha
        $senha_segura = password_hash($senha_pura, PASSWORD_DEFAULT);

        // Opcional, mas altamente recomendado: verificar se o e-mail já existe
        $check = $conn->prepare("SELECT IDUSU FROM tbusuarios WHERE EMAIL = ?");
        $check->bind_param("s", $email);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $erro = "Este e-mail já está cadastrado!";
            $check->close();
        } else {
            $check->close();

            $stmt = $conn->prepare("INSERT INTO tbusuarios (NOME, EMAIL, SENHA) VALUES (?, ?, ?)");
            if (!$stmt) {
                die("Erro no prepare (cadastro): " . $conn->error);
            }
            
            // Salvando a $senha_segura (com hash)
            $stmt->bind_param("sss", $nome, $email, $senha_segura); 
            
            if ($stmt->execute()) {
                $sucesso = "Conta criada com sucesso! Faça login.";
            } else {
                $erro = "Erro ao cadastrar: " . $conn->error;
            }
            $stmt->close();
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

    /* ESTILOS DO BOTÃO VISUALIZAR SENHA */
    .password-container {
      position: relative;
      margin-bottom: 15px;
    }

    .password-container .form-control {
      margin-bottom: 0;
      padding-right: 45px; /* Abre espaço para o botão do olho */
    }

    .toggle-password {
      position: absolute;
      right: 15px;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      padding: 0;
      cursor: pointer;
      color: #777;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .toggle-password:hover {
      color: var(--laranja-dw);
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

    <!-- Formulário de Login -->
    <form id="loginForm" action="" method="POST">
      <input type="hidden" name="acao" value="login">
      <h3 class="title">Acesse sua conta</h3>
      <input type="email" name="email" class="form-control" placeholder="E-mail institucional" required>
      
      <!-- Campo Senha com Olho -->
      <div class="password-container">
        <input type="password" minlength="8" name="senha" id="senhaLogin" class="form-control" placeholder="Senha" required>
        <button type="button" class="toggle-password" onclick="togglePassword('senhaLogin', this)">
          <!-- Ícone de olho aberto por padrão -->
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
            <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z"/>
            <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/>
          </svg>
        </button>
      </div>

      <button class="btn-main" type="submit">Entrar</button>
      <div class="switch" onclick="mostrarCadastro()">
        Não tem conta? <b>Cadastre-se</b>
      </div>
    </form>

    <!-- Formulário de Cadastro -->
    <form id="cadastroForm" class="hidden" action="" method="POST">
      <input type="hidden" name="acao" value="cadastrar">
      <h3 class="title">Criar nova conta</h3>
      <input type="text" name="nome" class="form-control" placeholder="Nome completo" required>
      <input type="email" name="email" class="form-control" placeholder="E-mail" required>
      
      <!-- Campo Senha com Olho -->
      <div class="password-container">
        <input type="password" minlength="8" name="senha" id="senhaCadastro" class="form-control" placeholder="Crie uma senha" required>
        <button type="button" class="toggle-password" onclick="togglePassword('senhaCadastro', this)">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
            <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z"/>
            <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/>
          </svg>
        </button>
      </div>

      <button class="btn-main" type="submit" style="background: var(--laranja-dw);">Finalizar Cadastro</button>
      <div class="switch" onclick="mostrarLogin()">
        Já possui conta? <b>Fazer login</b>
      </div>
    </form>

    <a href="index.php" class="btn-back">← Voltar para o início</a>
  </div>

  <script>
    function mostrarCadastro() {
      document.getElementById("loginForm").classList.add("hidden");
      document.getElementById("cadastroForm").classList.remove("hidden");
      document.querySelectorAll('.alert-msg').forEach(el => el.style.display = 'none');
    }

    function mostrarLogin() {
      document.getElementById("cadastroForm").classList.add("hidden");
      document.getElementById("loginForm").classList.remove("hidden");
    }

    // NOVA FUNÇÃO: Alternar visualização da senha e alterar o ícone SVG dinamicamente
    function togglePassword(inputId, button) {
      const input = document.getElementById(inputId);
      
      // Definição dos dois ícones (Olho Aberto e Olho Fechado)
      const eyeOpenSvg = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16"><path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z"/><path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/></svg>`;
      const eyeClosedSvg = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16"><path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a8.8 8.8 0 0 0-2.79.444l1.259 1.259C7.16 4.07 7.58 4 8 4c2.12 0 3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.181.26-.422.634-.736 1.01z"/><path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829"/><path d="M3.35 5.47q-.27.24-.518.487A13 13 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7 7 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12z"/></svg>`;

      if (input.type === "password") {
        input.type = "text";
        button.innerHTML = eyeClosedSvg;
      } else {
        input.type = "password";
        button.innerHTML = eyeOpenSvg;
      }
    }
  </script>
</body>
</html>
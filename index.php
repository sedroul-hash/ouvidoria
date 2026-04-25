<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ouvidoria Digital | Dom Walfrido</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">

  <style>
    :root {
      --verde-institucional: #14462a; /* Verde escuro de peso */
      --verde-gradiente: #1a5c38;
      --laranja-dw: #f37021; /* Laranja harmônico */
      --laranja-hover: #d65a12;
      --branco: #ffffff;
      --gelo: #f8fafc;
    }

    body {
      font-family: 'Outfit', sans-serif;
      margin: 0;
      background: var(--gelo);
      color: #333;
    }

    /* HEADER */
    .topbar {
      background: var(--verde-gradiente);
      padding: 12px 40px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      position: sticky;
      top: 0;
      z-index: 999;
    }

    .logo {
      color: var(--branco);
      font-weight: 700;
      font-size: 18px;
      display: flex;
      align-items: center;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }


    .nav-link {
      color: rgba(255,255,255,0.8) !important;
      margin-right: 25px;
      font-weight: 400;
      transition: 0.3s;
      font-size: 0.95rem;
    }

    .nav-link:hover {
      color: var(--laranja-dw) !important;
      opacity: 1;
    }

    .btn-login-top {
      background: var(--laranja-dw);
      color: white;
      border-radius: 50px;
      padding: 6px 20px;
      border: none;
      font-weight: 600;
      transition: 0.3s;
    }

    .btn-login-top:hover {
      background: var(--laranja-hover);
      box-shadow: 0 4px 12px rgba(243, 112, 33, 0.3);
    }

    /* HERO SECTION */
    .hero {
      background: linear-gradient(135deg, var(--verde-institucional), var(--verde-gradiente));
      padding: 100px 0;
      color: white;
      position: relative;
      overflow: hidden;
    }

    /* Detalhe estético no fundo */
    .hero::after {
      content: "";
      position: absolute;
      bottom: -50px;
      right: -50px;
      width: 300px;
      height: 300px;
      background: var(--laranja-dw);
      opacity: 0.05;
      border-radius: 50%;
    }

    .hero h1 {
      font-size: 52px;
      font-weight: 800;
      line-height: 1.1;
      margin-bottom: 20px;
    }

    .hero h2 {
      font-size: 22px;
      font-weight: 300;
      color: var(--laranja-dw);
      margin-bottom: 25px;
      text-transform: uppercase;
      letter-spacing: 2px;
    }

    .hero p {
      max-width: 520px;
      font-size: 1.1rem;
      opacity: 0.85;
      line-height: 1.6;
      margin-bottom: 40px;
    }

    /* BOTÕES PRINCIPAIS */
    .btn-container {
      display: flex;
      gap: 15px;
    }

    .btn-main {
      background: var(--laranja-dw);
      color: white;
      padding: 14px 32px;
      border-radius: 50px;
      border: none;
      font-weight: 700;
      transition: 0.3s;
      box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }

    .btn-main:hover {
      background: var(--laranja-hover);
      transform: translateY(-3px);
      box-shadow: 0 6px 20px rgba(0,0,0,0.3);
    }

    .btn-secondary-outline {
      background: transparent;
      color: white;
      padding: 14px 32px;
      border-radius: 50px;
      border: 2px solid rgba(255,255,255,0.3);
      font-weight: 600;
      transition: 0.3s;
    }

    .btn-secondary-outline:hover {
      background: rgba(255,255,255,0.1);
      border-color: white;
      transform: translateY(-3px);
    }

    /* CAIXA DE IMAGEM */
    .image-box {
      background: rgba(255, 255, 255, 0.05);
      backdrop-filter: blur(5px);
      padding: 40px;
      border-radius: 30px;
      border: 1px solid rgba(255,255,255,0.1);
      display: inline-block;
      transition: 0.5s;
    }

    .image-box:hover {
      transform: rotate(2deg) scale(1.02);
      border-color: var(--laranja-dw);
    }

    .illustration {
      max-width: 280px;
      filter: drop-shadow(0 10px 20px rgba(0,0,0,0.2));
    }

    /* RESPONSIVIDADE */
    @media(max-width: 768px){
      .hero { text-align: center; padding: 60px 20px; }
      .hero h1 { font-size: 36px; }
      .btn-container { justify-content: center; flex-direction: column; }
      .image-box { margin-top: 50px; }
      .topbar { padding: 15px 20px; }
      .nav-links-wrapper { display: none; } /* Esconde links no mobile para limpar */
    }
  </style>
</head>

<body>

  <header class="topbar d-flex justify-content-between align-items-center">
    <div class="logo">
      <img src="logo_dw.png" width="35" class="me-2" alt="Logo">
      Dom Walfrido Teixeira Vieira
    </div>

    <div class="d-flex align-items-center">
      <div class="nav-links-wrapper d-none d-md-block">
        <a href="#" class="nav-link d-inline">Início</a>
        <a href="#" class="nav-link d-inline">Institucional</a>
      </div>
      <button class="btn-login-top" onclick="login()">Login</button>
    </div>
  </header>

  <section class="hero">
    <div class="container">
      <div class="row align-items-center">

        <div class="col-md-7">
          <h2>Ouvidoria Dom Walfrido</h2>
          <h1>Sua voz constrói nosso futuro.</h1>

          <p>
            Um canal direto, seguro e eficiente para fortalecer nossa comunidade escolar. 
            Escolha como deseja prosseguir e ajude o <strong>Dom Walfrido</strong> a evoluir.
          </p>

          <div class="btn-container">
            <button class="btn-main" onclick="login()">
              Fazer Login
            </button>

            <button class="btn-secondary-outline" onclick="anonimo()">
              Manifestação Anônima
            </button>
          </div>
        </div>

        <div class="col-md-5 text-center">
          <div class="image-box">
            <img src="https://cdn-icons-png.flaticon.com/512/3062/3062634.png" 
                 class="illustration" alt="Ilustração Ouvidoria">
          </div>
        </div>

      </div>
    </div>
  </section>

  <script>
    // Se o arquivo agora é PHP, o link deve terminar em .php
    function login() { window.location.href = "login.php"; } 
    function anonimo() { window.location.href = "anonimo.php"; }
</script>

</body>
</html>
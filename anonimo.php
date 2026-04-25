<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manifestação Anônima - Dom Walfrido</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">

  <style>
    :root {
      --verde-dw: #14462a;
      --verde-claro: #1a5c38;
      --laranja-dw: #f37021;
      --laranja-hover: #d65a12;
      --fundo: #f4f7f6;
    }

    body {
      font-family: 'Outfit', sans-serif;
      background: linear-gradient(135deg, var(--verde-dw), var(--verde-claro));
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      margin: 0;
      padding: 20px;
    }

    /* CARD */
    .box {
      background: white;
      padding: 40px;
      border-radius: 24px;
      width: 100%;
      max-width: 480px;
      box-shadow: 0 20px 40px rgba(0,0,0,0.3);
      animation: fadeIn 0.5s ease-out;
      border-top: 8px solid var(--laranja-dw);
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px);}
      to { opacity: 1; transform: translateY(0);}
    }

    .title {
      font-weight: 700;
      color: var(--verde-dw);
      text-align: center;
      margin-bottom: 5px;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
    }

    .subtitle {
      font-size: 0.95rem;
      text-align: center;
      color: #666;
      margin-bottom: 25px;
    }

    /* ALERTA DE ANONIMATO */
    .alert-anon {
      background: #fff8f1;
      border: 1px solid #ffe7d3;
      border-left: 4px solid var(--laranja-dw);
      border-radius: 12px;
      padding: 12px 15px;
      font-size: 0.85rem;
      color: #854d0e;
      margin-bottom: 20px;
      display: flex;
      gap: 10px;
      align-items: center;
    }

    /* FORMULÁRIO */
    .form-control {
      border-radius: 12px;
      padding: 12px 15px;
      border: 1px solid #e2e8f0;
      background-color: #f8fafc;
      margin-bottom: 15px;
    }

    .form-control:focus {
      border-color: var(--verde-dw);
      box-shadow: 0 0 0 0.25rem rgba(20, 70, 42, 0.1);
    }

    /* BOTÃO PRINCIPAL */
    .btn-main {
      background: var(--verde-dw);
      color: white;
      border-radius: 12px;
      border: none;
      padding: 14px;
      font-weight: 600;
      transition: 0.3s;
      width: 100%;
      margin-top: 10px;
    }

    .btn-main:hover {
      background: #0d2e1b;
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    /* PROTOCOLO RESULTADO */
    .protocolo-box {
      background: #f0fdf4;
      border: 1px solid #dcfce7;
      border-radius: 12px;
      padding: 20px;
      margin-top: 20px;
      display: none;
      text-align: center;
    }

    .protocolo-box strong {
      color: var(--verde-dw);
      font-size: 1.2rem;
    }

    /* VOLTAR */
    .btn-back {
      color: var(--laranja-dw);
      text-decoration: none;
      font-size: 0.9rem;
      display: block;
      text-align: center;
      margin-top: 25px;
      font-weight: 500;
      transition: 0.3s;
    }

    .btn-back:hover {
      color: var(--laranja-hover);
      text-decoration: underline;
    }
  </style>
</head>

<body>

<div class="box">

  <h4 class="title">Manifestação Anônima</h4>
  <p class="subtitle">Sua identidade será preservada.</p>

  <div class="alert-anon">
    <span>⚠️</span>
    <span><strong>Atenção:</strong> Não informe nomes, números ou dados que possam te identificar se deseja total anonimato.</span>
  </div>

  <form id="anonForm">
    <select id="tipo" class="form-control" required>
      <option value="">Selecione o tipo de registro</option>
      <option>Denúncia</option>
      <option>Reclamação</option>
      <option>Sugestão</option>
      <option>Elogio</option>
    </select>

    <input id="assunto" class="form-control" placeholder="Assunto (ex: Infraestrutura, Merenda...)" required>

    <textarea id="mensagem" class="form-control" rows="4" placeholder="Descreva sua manifestação com o máximo de detalhes possível..." required></textarea>

    <button type="button" class="btn-main" onclick="enviarAnonimo()">Enviar agora</button>
  </form>

  <div id="protocoloBox" class="protocolo-box">
    </div>

  <a href="javascript:void(0)" class="btn-back" onclick="voltar()">← Voltar para a página inicial</a>

</div>

<script>
function enviarAnonimo() {
  const tipo = document.getElementById("tipo").value;
  const assunto = document.getElementById("assunto").value;
  const mensagem = document.getElementById("mensagem").value;

  if(!tipo || !assunto || !mensagem){
    alert("Por favor, preencha todos os campos antes de enviar.");
    return;
  }

  // Simulação de geração de protocolo
  const protocolo = "ANON-" + Math.floor(Math.random() * 900000 + 100000);

  const box = document.getElementById("protocoloBox");
  const form = document.getElementById("anonForm");
  
  form.style.display = "none"; // Esconde o formulário após envio
  box.style.display = "block";
  box.innerHTML = `
    <div style="font-size: 2rem; margin-bottom: 10px;">✅</div>
    <p style="margin-bottom: 5px; color: #166534; font-weight: 600;">Enviado com sucesso!</p>
    <small style="color: #666;">Guarde seu número de protocolo:</small><br>
    <strong>${protocolo}</strong>
  `;
}

function voltar(){
  window.location.href = "principal.html";
}
</script>

</body>
</html>
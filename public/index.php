<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>DevVerse • Soluções Digitais para Restaurantes</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;900&display=swap" rel="stylesheet">
  <style>
    :root{
      --bg:#0f0f12;
      --card:#16161c;
      --primary:#E5D9C4;
      --accent:#8b7a5a;
      --text:#f2f2f2;
      --muted:#b8b8b8;
    }
    *{box-sizing:border-box;margin:0;padding:0;font-family:'Inter',sans-serif}
    body{background:var(--bg);color:var(--text);line-height:1.6}
    a{text-decoration:none;color:inherit}

    header{position:sticky;top:0;background:rgba(15,15,18,.9);backdrop-filter:blur(10px);z-index:10}
    .nav{max-width:1200px;margin:auto;display:flex;justify-content:space-between;align-items:center;padding:20px}
    .logo{font-weight:900;font-size:24px;letter-spacing:1px;color:var(--primary)}
    .menu a{margin-left:24px;color:var(--muted)}
    .menu a:hover{color:var(--primary)}

    .hero{max-width:1200px;margin:100px auto;padding:0 20px;display:grid;grid-template-columns:1.2fr 1fr;gap:60px}
    .hero h1{font-size:52px;line-height:1.1}
    .hero span{color:var(--primary)}
    .hero p{margin:24px 0;color:var(--muted);font-size:18px}
    .btn{display:inline-block;padding:14px 26px;border-radius:10px;background:var(--primary);color:#000;font-weight:600}
    .btn.outline{background:none;border:1px solid var(--primary);color:var(--primary);margin-left:14px}

    .card{background:var(--card);border-radius:18px;padding:30px}
    .features{max-width:1200px;margin:120px auto;padding:0 20px;display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:30px}
    .card h3{margin-bottom:10px;color:var(--primary)}
    .card p{color:var(--muted)}

    .saas{max-width:1200px;margin:120px auto;padding:0 20px;display:grid;grid-template-columns:1fr 1fr;gap:50px}

    .steps{max-width:1200px;margin:120px auto;padding:0 20px}
    .steps-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:30px;margin-top:40px}

    .cta{max-width:1200px;margin:120px auto;padding:60px 40px;background:linear-gradient(135deg,var(--card),#1f1f27);border-radius:30px;text-align:center}
    .cta h2{font-size:38px}
    .cta p{margin:20px 0;color:var(--muted)}

    footer{margin-top:120px;padding:40px;text-align:center;color:var(--muted);border-top:1px solid #222}

    @media(max-width:900px){
      .hero,.saas{grid-template-columns:1fr}
      .hero h1{font-size:40px}
    }
  </style>
</head>
<body>

<header>
  <div class="nav">
    <div class="logo">DevVerse</div>
    <nav class="menu">
      <a href="#servicos">Serviços</a>
      <a href="#saas">SaaS</a>
      <a href="#como-funciona">Como funciona</a>
      <a href="../auth/login.php">Login</a>
      <a href="cadastrar.php">Cadastrar</a>
    </nav>
  </div>
</header>

<section class="hero">
  <div>
    <h1>Transforme seu restaurante com <span>tecnologia inteligente</span></h1>
    <p>A DevVerse cria soluções digitais para restaurantes venderem mais, atenderem melhor e operarem com eficiência usando Cardápio QR Code e automação.</p>
    <a class="btn" href="../public/cadastrar.php">Começar agora</a>
    <a class="btn outline" href="#saas">Conhecer o sistema</a>
  </div>
  <div class="card">
    <h3>🚀 Cardápio QR Code</h3>
    <p>Pedidos direto da mesa, sem aplicativos, sem complicação.</p>
    <h3 style="margin-top:20px">💳 Pagamento via Pix</h3>
    <p>Pix direto na conta do restaurante, simples e rápido.</p>
  </div>
</section>

<section id="servicos" class="features">
  <div class="card"><h3>Digitalização</h3><p>Leve seu restaurante para o digital com soluções modernas.</p></div>
  <div class="card"><h3>Automação</h3><p>Menos papel, menos erros, mais produtividade.</p></div>
  <div class="card"><h3>Gestão</h3><p>Controle produtos, pedidos e mesas em um painel único.</p></div>
  <div class="card"><h3>Escalabilidade</h3><p>Sistema SaaS pronto para crescer junto com seu negócio.</p></div>
</section>

<section id="saas" class="saas">
  <div>
    <h2>Nosso SaaS: Cardápio QR Code</h2>
    <p>Uma plataforma completa onde cada restaurante tem seu próprio painel para gerenciar produtos, pedidos, mesas e Pix.</p>
    <p>Sem taxas por venda. Você paga apenas a mensalidade.</p>
  </div>
  <div class="card">
    <ul>
      <li>✔ Painel exclusivo do restaurante</li>
      <li>✔ Produtos e categorias ilimitados</li>
      <li>✔ Pedidos em tempo real</li>
      <li>✔ Pix visível no cardápio da mesa</li>
      <li>✔ QR Code por mesa</li>
    </ul>
  </div>
</section>

<section id="como-funciona" class="steps">
  <h2>Como funciona</h2>
  <div class="steps-grid">
    <div class="card"><h3>1️⃣ Cadastro</h3><p>O restaurante cria sua conta na DevVerse.</p></div>
    <div class="card"><h3>2️⃣ Configuração</h3><p>Cadastra produtos, mesas e chave Pix.</p></div>
    <div class="card"><h3>3️⃣ QR Code</h3><p>Imprime os QR Codes das mesas.</p></div>
    <div class="card"><h3>4️⃣ Vendas</h3><p>Clientes pedem e pagam direto da mesa.</p></div>
  </div>
</section>

<section class="cta">
  <h2>Pronto para digitalizar seu restaurante?</h2>
  <p>Crie sua conta agora e comece a usar o Cardápio QR Code.</p>
  <a class="btn" href="../public/cadastrar.php">Cadastrar restaurante</a>
</section>

<footer>
  © <?php echo date('Y'); ?> DevVerse • Tecnologia para Restaurantes
</footer>

</body>
</html>
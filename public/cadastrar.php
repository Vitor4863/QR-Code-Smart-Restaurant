<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Cadastrar Restaurante • DevVerse</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;900&display=swap" rel="stylesheet">
  <style>
    :root{
      --bg:#0f0f12;
      --card:#16161c;
      --primary:#E5D9C4;
      --text:#f2f2f2;
      --muted:#b8b8b8;
    }
    *{box-sizing:border-box;margin:0;padding:0;font-family:'Inter',sans-serif}
    body{background:var(--bg);color:var(--text);min-height:100vh;display:flex;align-items:center;justify-content:center}

    .container{width:100%;max-width:460px;padding:20px}
    .card{background:var(--card);border-radius:20px;padding:40px}

    h1{text-align:center;margin-bottom:10px}
    p{text-align:center;color:var(--muted);margin-bottom:30px}

    label{display:block;margin-bottom:6px;font-size:14px;color:var(--muted)}
    input,select{width:100%;padding:14px;border-radius:10px;border:1px solid #2a2a33;background:#0f0f12;color:var(--text);margin-bottom:18px}
    input:focus,select:focus{outline:none;border-color:var(--primary)}

    button{width:100%;padding:14px;border-radius:12px;border:none;background:var(--primary);color:#000;font-weight:700;font-size:16px;cursor:pointer}
    button:hover{opacity:.9}

    .footer{text-align:center;margin-top:20px;font-size:14px;color:var(--muted)}
    .footer a{color:var(--primary)}
  </style>
</head>
<body>

<div class="container">
  <div class="card">
    <h1>Cadastro do Restaurante</h1>
    <p>Crie sua conta e comece a usar o Cardápio QR Code</p>

    <form method="POST" action="../auth/register.php">
      <label>Nome do restaurante</label>
      <input type="text" name="nome" required>

      <label>Email</label>
      <input type="email" name="email" required>

      <label>Telefone</label>
      <input type="text" name="telefone" required>

      <label>Senha</label>
      <input type="password" name="senha" required>

      <label>Plano</label>
      <select name="plano" required>
        <option value="basico">Básico</option>
        <option value="pro">Profissional</option>
        <option value="premium">Premium</option>
      </select>

      <button type="submit">Criar conta</button>
    </form>

    <div class="footer">
      Já tem conta? <a href="../auth/login.php">Entrar</a>
    </div>
  </div>
</div>

</body>
</html>

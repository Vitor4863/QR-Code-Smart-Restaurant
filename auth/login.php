<?php
// /auth/login.php

session_start();
require __DIR__ . '/../core/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (!$email || !$senha) {
        $erro = "Preencha todos os campos.";
    } else {

        // Buscar restaurante pelo email
        $stmt = $pdo->prepare("SELECT * FROM restaurantes WHERE email = ? AND status = 'ativo'");
        $stmt->execute([$email]);
        $restaurante = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($restaurante && password_verify($senha, $restaurante['senha'])) {
            // Login correto → criar sessão
            $_SESSION['restaurante_id']   = $restaurante['id'];
            $_SESSION['restaurante_nome'] = $restaurante['nome'];
            $_SESSION['restaurante_slug'] = $restaurante['slug'];

            // Redireciona para o produtos
            header('Location: /app/produtos.php');
            exit;
        } else {
            $erro = "Email ou senha incorretos.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Login Restaurante • DevVerse</title>
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
input{width:100%;padding:14px;border-radius:10px;border:1px solid #2a2a33;background:#0f0f12;color:var(--text);margin-bottom:18px}
input:focus{outline:none;border-color:var(--primary)}

button{width:100%;padding:14px;border-radius:12px;border:none;background:var(--primary);color:#000;font-weight:700;font-size:16px;cursor:pointer}
button:hover{opacity:.9}

.footer{text-align:center;margin-top:20px;font-size:14px;color:var(--muted)}
.footer a{color:var(--primary)}

.erro{background:#8b0000;padding:12px;border-radius:10px;margin-bottom:20px;text-align:center;color:#fff;font-weight:600}
</style>
</head>
<body>

<div class="container">
  <div class="card">
    <h1>Login do Restaurante</h1>
    <p>Entre na sua conta para acessar o painel</p>

    <?php if(!empty($erro)): ?>
      <div class="erro"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
      <label>Email</label>
      <input type="email" name="email" required>

      <label>Senha</label>
      <input type="password" name="senha" required>

      <button type="submit">Entrar</button>
    </form>

    <div class="footer">
      Não tem conta? <a href="../public/cadastrar.php">Cadastrar restaurante</a>
    </div>
  </div>
</div>

</body>
</html>

<?php
// /auth/register.php

session_start();
require __DIR__ . '/../core/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /public/cadastrar.php');
    exit;
}

$nome     = trim($_POST['nome'] ?? '');
$email    = trim($_POST['email'] ?? '');
$telefone = trim($_POST['telefone'] ?? '');
$senha    = $_POST['senha'] ?? '';
$plano    = $_POST['plano'] ?? 'basico';

if (!$nome || !$email || !$senha) {
    die('Dados obrigatórios não preenchidos');
}

// Gerar slug do restaurante
function gerarSlug($string) {
    $slug = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
    $slug = preg_replace('/[^a-zA-Z0-9]/', '-', $slug);
    $slug = strtolower(trim($slug, '-'));
    return $slug;
}

$slug = gerarSlug($nome);

// Verificar se email já existe
$stmt = $pdo->prepare("SELECT id FROM restaurantes WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    die('Email já cadastrado');
}

// Criptografar senha
$senhaHash = password_hash($senha, PASSWORD_DEFAULT);

// Inserir restaurante
$stmt = $pdo->prepare("INSERT INTO restaurantes (nome, slug, email, telefone, senha, plano, status, criado_em) VALUES (?, ?, ?, ?, ?, ?, 'ativo', NOW())");
$stmt->execute([$nome, $slug, $email, $telefone, $senhaHash, $plano]);

$restauranteId = $pdo->lastInsertId();

// Criar sessão automática (login direto)
$_SESSION['restaurante_id'] = $restauranteId;
$_SESSION['restaurante_nome'] = $nome;
$_SESSION['restaurante_slug'] = $slug;

// Redirecionar para o painel do restaurante
header('Location: /admin/dashboard.php');
exit;

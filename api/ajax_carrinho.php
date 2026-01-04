<?php
session_start();
require __DIR__ . '/../core/config.php';;
header('Content-Type: application/json');

$mesa = isset($_GET['mesa']) ? (int)$_GET['mesa'] : 1;
$acao = $_GET['acao'] ?? '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if(!isset($_SESSION['carrinhos'])) $_SESSION['carrinhos'] = [];
if(!isset($_SESSION['carrinhos'][$mesa])) $_SESSION['carrinhos'][$mesa] = [];

$carrinho = &$_SESSION['carrinhos'][$mesa];

if($acao == 'add'){
    if(isset($carrinho[$id])) $carrinho[$id]++;
    else $carrinho[$id] = 1;
} elseif($acao == 'remove'){
    if(isset($carrinho[$id])){
        $carrinho[$id]--;
        if($carrinho[$id] <= 0) unset($carrinho[$id]);
    }
}

// Gera HTML do carrinho
$total = 0;
$html = '';
if(!empty($carrinho)){
    $ids = array_keys($carrinho);
    $in = str_repeat('?,', count($ids)-1) . '?';
    $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id IN ($in)");
    $stmt->execute($ids);
    $produtosCarrinho = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), null, 'id');

    foreach($carrinho as $id => $qtd){
        $prod = $produtosCarrinho[$id];
        $subtotal = $prod['preco'] * $qtd;
        $total += $subtotal;
        $html .= "<p>{$prod['nome']} x $qtd = R$ ".number_format($subtotal,2,',','.').
        " <a href='javascript:void(0)' onclick=\"atualizarCarrinho($mesa, $id, 'remove')\" class='btn-small'>Remover</a></p>";
    }
    $html .= "<p><strong>Total: R$ ".number_format($total,2,',','.')."</strong></p>";
} else $html = "<p>Carrinho vazio</p>";

echo json_encode(['html' => $html]);

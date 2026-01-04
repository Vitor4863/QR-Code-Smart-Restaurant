<?php
session_start();
require __DIR__ . '/../core/config.php';



// Mesa
$mesa = isset($_GET['mesa']) ? (int)$_GET['mesa'] : 0;
if($mesa <= 0) die("Mesa inválida! Informe ?mesa=1 no URL");

// Inicializar carrinho e status de pagamento
if(!isset($_SESSION['carrinho'][$mesa])) $_SESSION['carrinho'][$mesa] = [];
if(!isset($_SESSION['pago'][$mesa])) $_SESSION['pago'][$mesa] = false;

// Adicionar produto via AJAX
if(isset($_POST['add'])) {
    $id = (int)$_POST['add'];
    if(isset($_SESSION['carrinho'][$mesa][$id])) {
        $_SESSION['carrinho'][$mesa][$id]['qtd']++;
    } else {
        $stmt = $pdo->prepare("SELECT nome, preco, imagem, descricao FROM produtos WHERE id=?");
        $stmt->execute([$id]);
        $prod = $stmt->fetch(PDO::FETCH_ASSOC);
        if($prod) {
            $_SESSION['carrinho'][$mesa][$id] = ['nome'=>$prod['nome'],'preco'=>$prod['preco'],'qtd'=>1,'imagem'=>$prod['imagem'],'descricao'=>$prod['descricao']];
        }
    }
    $_SESSION['pago'][$mesa] = false;
    echo json_encode(['carrinho'=>$_SESSION['carrinho'][$mesa],'pago'=>$_SESSION['pago'][$mesa]]);
    exit;
}

// Remover produto via AJAX
if(isset($_POST['remove'])) {
    $id = (int)$_POST['remove'];
    if(isset($_SESSION['carrinho'][$mesa][$id])) {
        $_SESSION['carrinho'][$mesa][$id]['qtd']--;
        if($_SESSION['carrinho'][$mesa][$id]['qtd'] <= 0) unset($_SESSION['carrinho'][$mesa][$id]);
    }
    if(empty($_SESSION['carrinho'][$mesa])) $_SESSION['pago'][$mesa] = false;
    echo json_encode(['carrinho'=>$_SESSION['carrinho'][$mesa],'pago'=>$_SESSION['pago'][$mesa]]);
    exit;
}

// Finalizar pedido
if(isset($_POST['finalizar'])) {
    if(!empty($_SESSION['carrinho'][$mesa]) && $_SESSION['pago'][$mesa]) {
        $total = 0;
        foreach($_SESSION['carrinho'][$mesa] as $item) $total += $item['preco'] * $item['qtd'];
        $stmt = $pdo->prepare("INSERT INTO pedidos (mesa, total, status) VALUES (?,?,?)");
        $stmt->execute([$mesa,$total,'Pendente']);
        unset($_SESSION['carrinho'][$mesa]);
        $_SESSION['pago'][$mesa] = false;
        echo json_encode(['ok'=>true]);
    } else {
        echo json_encode(['ok'=>false,'msg'=>'Pagamento não confirmado']);
    }
    exit;
}

// Buscar produtos
$stmt = $pdo->query("SELECT * FROM produtos ORDER BY categoria, nome");
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
if(!$produtos) $produtos = [];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Cardápio - Mesa <?= $mesa ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
<style>
:root {
    --bg: #1A1A1B;
    --text: #E5D9C4;
    --gold: #CDBA96;
    --card-bg: #22222B;
    --btn-grad: linear-gradient(135deg, #CDBA96, #E5D9C4);
    --red-grad: linear-gradient(135deg, #e74c3c, #c0392b);
}

* { box-sizing: border-box; margin:0; padding:0; }

body {
    font-family: 'Montserrat', sans-serif;
    background: var(--bg);
    color: var(--text);
    padding: 20px;
}

.container {
    max-width: 1200px;
    margin: auto;
    display: flex;
    gap: 30px;
    flex-wrap: wrap;
}

/* PRODUTOS */
.produtos {
    flex: 2;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 25px;
}

.produto {
    background: var(--card-bg);
    border-radius: 2rem;
    padding: 20px;
    text-align: center;
    box-shadow: 0 6px 25px rgba(0,0,0,0.5);
    transition: all 0.4s ease;
    cursor: pointer;
    position: relative;
}
.produto:hover {
    transform: translateY(-10px) scale(1.04);
    box-shadow: 0 20px 60px rgba(205,186,150,0.6);
}

.produto img {
    width: 100%;
    height: 180px;
    object-fit: cover;
    border-radius: 1.5rem;
    margin-bottom: 15px;
    transition: transform 0.4s ease, box-shadow 0.4s ease;
}
.produto:hover img {
    transform: scale(1.08);
    box-shadow: 0 15px 45px rgba(205,186,150,0.4);
}

.produto h3 {
    font-size: 20px;
    margin-bottom: 6px;
    color: var(--text);
    text-shadow: 0 0 8px var(--gold);
}
.produto p {
    font-size: 14px;
    color: #ccc;
    margin-bottom: 10px;
}
.preco {
    font-weight: 700;
    font-size: 16px;
    color: var(--gold);
    margin-bottom: 15px;
}

/* BOTÕES */
.btn-adicionar {
    padding: 10px 20px;
    background: var(--btn-grad);
    color: var(--bg);
    border-radius: 1rem;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-block;
}
.btn-adicionar:hover {
    transform: scale(1.08);
    box-shadow: 0 10px 30px rgba(205,186,150,0.5);
}

/* CARRINHO */
.carrinho {
    flex: 1;
    background: var(--card-bg);
    border-radius: 2rem;
    padding: 25px;
    box-shadow: 0 8px 30px rgba(0,0,0,0.6);
    position: sticky;
    top: 20px;
    height: max-content;
}
.carrinho h2 {
    text-align: center;
    font-size: 26px;
    color: var(--text);
    margin-bottom: 20px;
    text-shadow: 0 0 8px var(--gold);
}
.item-carrinho {
    display: flex;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid #333;
    font-size: 15px;
    color: #ccc;
}
.item-carrinho:last-child { border-bottom: none; }

.total {
    font-weight: 700;
    font-size: 18px;
    text-align: right;
    margin-top: 15px;
    color: var(--gold);
}

/* BOTÕES CARRINHO */
.btn-remover {
    padding: 6px 12px;
    background: var(--red-grad);
    color: #fff;
    border: none;
    border-radius: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
}
.btn-remover:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 25px rgba(0,0,0,0.5);
}

.btn-finalizar {
    width: 100%;
    padding: 15px;
    background: var(--btn-grad);
    color: var(--bg);
    border: none;
    border-radius: 1.5rem;
    font-weight: 700;
    margin-top: 20px;
    cursor: pointer;
    transition: all 0.3s ease;
}
.btn-finalizar:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

/* PIX */
.pix {
    text-align: center;
    margin-top: 20px;
}
.pix p {
    margin-bottom: 10px;
    color: #ccc;
    font-size: 14px;
}
.pix img {
    border-radius: 1.5rem;
    max-width: 180px;
    box-shadow: 0 8px 30px rgba(205,186,150,0.6);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.pix img:hover {
    transform: scale(1.05);
    box-shadow: 0 10px 40px rgba(205,186,150,0.8);
}

/* RESPONSIVO */
@media(max-width:768px){
    .container{flex-direction:column;}
    .produtos{grid-template-columns:1fr;}
}
</style>
</head>
<body>
<div class="container">
<div class="produtos">
<?php foreach($produtos as $p): ?>
<div class="produto">
    <img src="<?= htmlspecialchars($p['imagem']) ?>" alt="<?= htmlspecialchars($p['nome']) ?>">
    <h3><?= htmlspecialchars($p['nome']) ?></h3>
    <p><?= htmlspecialchars($p['descricao']) ?></p>
    <p class="preco">R$ <?= number_format($p['preco'],2,',','.') ?></p>
    <a class="btn-adicionar" data-id="<?= $p['id'] ?>">Adicionar</a>
</div>
<?php endforeach; ?>
</div>

<div class="carrinho" id="carrinho">
<h2>Carrinho</h2>
<div id="itens-carrinho"></div>
<p class="total" id="total">Total: R$ 0,00</p>
<div class="pix" id="pix">
    <p>Escaneie o QR Code para pagar via Pix:</p>
    <img src="img/qr.png" alt="QR Code Pix" id="pix-img">
</div>
<button class="btn-finalizar" id="finalizar" disabled>Finalizar Pedido</button>
</div>
</div>

<script>
let carrinho = {};
let pago = false;

function atualizarCarrinho(){
    const itens = document.getElementById('itens-carrinho');
    const totalEl = document.getElementById('total');
    const pixImg = document.getElementById('pix-img');
    const finalizarBtn = document.getElementById('finalizar');
    itens.innerHTML = '';
    let total = 0;
    for(let id in carrinho){
        const item = carrinho[id];
        total += item.preco * item.qtd;
        const div = document.createElement('div');
        div.className = 'item-carrinho';
        div.innerHTML = `<span>${item.nome} x ${item.qtd} = R$ ${(item.preco*item.qtd).toFixed(2)}</span>
                         <button class="btn-remover" data-id="${id}">Remover</button>`;
        itens.appendChild(div);
    }
    totalEl.textContent = `Total: R$ ${total.toFixed(2)}`;
    pixImg.src = total>0 ? `https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=pix://BR${total.toFixed(2)}` : '';
    finalizarBtn.disabled = !pago || total===0;

    document.querySelectorAll('.btn-remover').forEach(btn=>{
        btn.addEventListener('click',function(){
            const id = this.dataset.id;
            fetch('',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'remove='+id})
            .then(r=>r.json()).then(data=>{
                carrinho = data.carrinho;
                pago = data.pago;
                atualizarCarrinho();
            });
        });
    });
}

document.querySelectorAll('.btn-adicionar').forEach(btn=>{
    btn.addEventListener('click',function(e){
        e.preventDefault();
        const id = this.dataset.id;
        fetch('',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'add='+id})
        .then(r=>r.json()).then(data=>{
            carrinho = data.carrinho;
            pago = data.pago;
            atualizarCarrinho();
        });
    });
});

document.getElementById('finalizar').addEventListener('click',function(){
    fetch('',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'finalizar=1'})
    .then(r=>r.json()).then(resp=>{
        if(resp.ok){
            alert('Pedido finalizado com sucesso!');
            carrinho = {};
            pago = false;
            atualizarCarrinho();
        } else {
            alert(resp.msg || 'Pagamento não confirmado');
        }
    });
});

atualizarCarrinho();
</script>
</body>
</html>

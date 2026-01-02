<?php
require 'config.php'; // Conexão PDO

// ==================== EXCLUSÃO DE PRODUTO ====================
if(isset($_GET['del'])){
    $id = (int)$_GET['del'];
    $stmtImg = $pdo->prepare("SELECT imagem FROM produtos WHERE id = ?");
    $stmtImg->execute([$id]);
    $imgData = $stmtImg->fetch(PDO::FETCH_ASSOC);
    if($imgData && file_exists($imgData['imagem'])){
        unlink($imgData['imagem']);
    }
    $stmt = $pdo->prepare("DELETE FROM produtos WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: produtos.php");
    exit;
}

// ==================== ADICIONAR PRODUTO ====================
if(isset($_POST['add'])){
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $preco = $_POST['preco'];
    $categoria = $_POST['categoria'];
    $imagem = '';

    if(isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0){
        $ext = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
        $imagem = 'uploads/' . uniqid() . '.' . $ext;
        if(!is_dir('uploads')) mkdir('uploads', 0755, true);
        move_uploaded_file($_FILES['imagem']['tmp_name'], $imagem);
    }

    $stmt = $pdo->prepare("INSERT INTO produtos (nome, descricao, preco, categoria, imagem) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$nome, $descricao, $preco, $categoria, $imagem]);
    header("Location: produtos.php");
    exit;
}

// ==================== BUSCAR PRODUTOS ====================
$stmt = $pdo->prepare("SELECT * FROM produtos ORDER BY id DESC");
$stmt->execute();
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
if(!$produtos) $produtos = [];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Produtos</title>
<script src="https://cdn.tailwindcss.com"></script>
<style>
:root {
    --cor-texto: #E5D9C4;
    --cor-glow: #CDBA96;
    --cor-fundo: #1A1A1B;
    --cor-overlay: rgba(0,0,0,0.6);
}

/* ==================== BODY ==================== */
body {
    background-color: var(--cor-fundo);
    color: var(--cor-texto);
    font-family: 'Montserrat', sans-serif;
    padding: 2rem;
}

/* ==================== FORMULÁRIO ==================== */
form {
    background: #22222B;
    padding: 2rem;
    border-radius: 2rem;
    box-shadow: 0 15px 40px rgba(0,0,0,0.5);
    border: 1px solid #333;
}
form input, form button { font-family: 'Montserrat', sans-serif; }
form input[type=text],
form input[type=number],
form input[type=file] {
    background: #1A1A1B;
    color: var(--cor-texto);
    border: 1px solid #333;
    padding: 0.75rem 1rem;
    border-radius: 1rem;
    width: 100%;
    margin-bottom: 1rem;
}
form input:focus {
    border-color: var(--cor-glow);
    box-shadow: 0 0 12px var(--cor-glow);
}
form button {
    background: var(--cor-glow);
    color: var(--cor-fundo);
    font-weight: 600;
    border-radius: 1rem;
    padding: 0.75rem 1rem;
    width: 100%;
    transition: all 0.3s ease;
    cursor: pointer;
}
form button:hover {
    transform: translateY(-2px) scale(1.02);
    box-shadow: 0 8px 25px rgba(205,186,150,0.5);
}

/* ==================== GRID DE PRODUTOS ==================== */
.grid {
    display: grid;
    grid-template-columns: repeat(auto-fit,minmax(300px,1fr));
    gap: 2rem;
    margin-top: 2rem;
}

/* ==================== CARD PRODUTO ==================== */
.card-hover {
    background: #22222B;
    border-radius: 2rem;
    overflow: hidden;
    position: relative;
    transition: all 0.4s ease;
    box-shadow: 0 10px 30px rgba(0,0,0,0.4);
}
.card-hover:hover {
    transform: translateY(-8px) scale(1.03);
    box-shadow: 0 20px 50px rgba(205,186,150,0.5);
}

/* ==================== IMAGEM ==================== */
.card-hover img {
    width: 100%;
    height: 250px;
    object-fit: cover;
    transition: transform 0.4s ease;
    border-bottom-left-radius: 2rem;
    border-bottom-right-radius: 2rem;
}
.card-hover:hover img { transform: scale(1.05); }

/* ==================== OVERLAY ==================== */
.overlay {
    position: absolute;
    inset: 0;
    background: var(--cor-overlay);
    backdrop-filter: blur(6px);
    opacity: 0;
    transition: opacity 0.4s ease;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 1.5rem;
    color: var(--cor-texto);
}
.card-hover:hover .overlay { opacity: 1; }
.overlay h3 {
    font-size: 1.5rem;
    font-weight: 800;
    text-shadow: 0 0 8px var(--cor-glow);
}
.overlay p { margin-top: 0.5rem; line-height: 1.4; }
.overlay span {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 999px;
    font-size: 0.75rem;
    font-weight: 600;
    background: var(--cor-glow);
    color: var(--cor-fundo);
}

/* ==================== BOTÕES ==================== */
.button-overlay {
    font-size: 0.8rem;
    font-weight: 600;
    padding: 0.5rem 1rem;
    border-radius: 1rem;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}
.button-overlay:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(205,186,150,0.4); }
.button-overlay.bg-blue { background: var(--cor-glow); color: var(--cor-fundo); }
.button-overlay.bg-red { background: #e74c3c; color: #fff; }

/* ==================== LIMITADOR DE LINHAS ==================== */
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* ==================== MODAL ==================== */
.modal-bg {
    display: none;
    position: fixed;
    inset:0;
    background: rgba(0,0,0,0.7);
    justify-content: center;
    align-items: center;
    z-index: 50;
}
.modal-content {
    background: #22222B;
    padding: 2rem;
    border-radius: 2rem;
    max-width: 500px;
    width: 90%;
    color: var(--cor-texto);
    box-shadow: 0 20px 50px rgba(0,0,0,0.7);
    animation: fadeIn 0.3s ease;
    position: relative;
}
.modal-content h2 {
    font-size: 1.8rem;
    font-weight: 800;
    text-shadow: 0 0 10px var(--cor-glow);
    margin-bottom: 1rem;
}
.modal-content p { margin: 0.5rem 0; }
.modal-content img { width: 100%; border-radius: 1.5rem; margin-bottom: 1rem; }
.modal-close {
    position: absolute;
    top: 1rem;
    right: 1rem;
    font-size: 1.2rem;
    font-weight: bold;
    cursor: pointer;
    color: var(--cor-glow);
    transition: all 0.3s ease;
}
.modal-close:hover { transform: scale(1.2); }

@keyframes fadeIn { from {opacity:0; transform: translateY(-10px);} to {opacity:1; transform: translateY(0);} }

/* ==================== RESPONSIVO ==================== */
@media(max-width:768px){ .grid { grid-template-columns: 1fr; } }
</style>
</head>
<body>

<div class="max-w-7xl mx-auto">

    <h1 class="text-4xl font-bold mb-8 text-center">Produtos</h1>

    <!-- FORMULÁRIO -->
 
    <form method="post" enctype="multipart/form-data">
        <input type="text" name="nome" placeholder="Nome" required>
        <input type="text" name="descricao" placeholder="Descrição" required>
        <input type="number" step="0.01" name="preco" placeholder="Preço" required>
        <input type="text" name="categoria" placeholder="Categoria" required>
        <input type="file" name="imagem" accept="image/*">
        <button name="add">Adicionar Produto</button>
    </form>
    <br><br>
 <h1 class="text-4xl font-bold mb-8 text-center">Estoque de Produtos</h1>
    <!-- GRID DE PRODUTOS -->
    <div class="grid">
    <?php if(count($produtos) > 0): ?>
        <?php foreach($produtos as $p): ?>
        <div class="relative card-hover">
            <img src="<?= htmlspecialchars($p['imagem']) ?>" alt="<?= htmlspecialchars($p['nome']) ?>">
            <div class="overlay">
                <div>
                    <h3><?= htmlspecialchars($p['nome']) ?></h3>
                    <span><?= htmlspecialchars($p['categoria']) ?></span>
                    <p class="line-clamp-3"><?= htmlspecialchars($p['descricao']) ?></p>
                </div>
                <div class="flex items-center justify-between mt-4">
                    <p class="font-bold text-lg">R$ <?= number_format($p['preco'],2,',','.') ?></p>
                    <div class="flex gap-2">
                        <button onclick="openModal('<?= htmlspecialchars(addslashes($p['nome'])) ?>','<?= htmlspecialchars(addslashes($p['descricao'])) ?>','<?= number_format($p['preco'],2,',','.') ?>','<?= htmlspecialchars(addslashes($p['categoria'])) ?>','<?= htmlspecialchars($p['imagem']) ?>')"
                                class="button-overlay bg-blue">Ver Mais</button>
                        <a href="?del=<?= $p['id'] ?>" onclick="return confirm('Excluir este produto?')"
                           class="button-overlay bg-red">Excluir</a>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="text-center col-span-full mt-8">Nenhum produto cadastrado.</p>
    <?php endif; ?>
    </div>
</div>

<!-- ==================== MODAL ==================== -->
<div class="modal-bg" id="modal">
    <div class="modal-content">
        <span class="modal-close" onclick="closeModal()">×</span>
        <h2 id="modal-nome"></h2>
        <img id="modal-imagem" src="" alt="">
        <p><strong>Categoria:</strong> <span id="modal-categoria"></span></p>
        <p><strong>Descrição:</strong> <span id="modal-descricao"></span></p>
        <p><strong>Preço:</strong> R$ <span id="modal-preco"></span></p>
    </div>
</div>

<script>
function openModal(nome, descricao, preco, categoria, imagem){
    document.getElementById('modal-nome').textContent = nome;
    document.getElementById('modal-descricao').textContent = descricao;
    document.getElementById('modal-preco').textContent = preco;
    document.getElementById('modal-categoria').textContent = categoria;
    document.getElementById('modal-imagem').src = imagem;
    document.getElementById('modal').style.display = 'flex';
}
function closeModal(){ document.getElementById('modal').style.display = 'none'; }
</script>

</body>
</html>

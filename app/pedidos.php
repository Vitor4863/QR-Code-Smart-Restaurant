<?php
require __DIR__ . '/../core/config.php';

// ==================== RETORNAR PEDIDOS EM JSON ====================
if (isset($_GET['action']) && $_GET['action'] === 'fetch') {
    $pedidos = $pdo->query("SELECT * FROM pedidos ORDER BY criado_em DESC")
                   ->fetchAll(PDO::FETCH_ASSOC) ?? [];

    $contagem = ['Pendente' => 0, 'Em preparo' => 0, 'Entregue' => 0];

    foreach ($pedidos as $p) {
        if (isset($contagem[$p['status']])) {
            $contagem[$p['status']]++;
        }
    }

    echo json_encode(['pedidos' => $pedidos, 'contagem' => $contagem]);
    exit;
}

// ==================== ATUALIZAR STATUS DO PEDIDO ====================
if (isset($_POST['id'], $_POST['status'])) {
    $stmt = $pdo->prepare("UPDATE pedidos SET status=? WHERE id=?");
    $stmt->execute([$_POST['status'], $_POST['id']]);
    echo json_encode(['ok' => true]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Dashboard de Pedidos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        /* ==================== RESET ==================== */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Montserrat', sans-serif; background: #1A1A1B; color: #E5D9C4; padding: 20px; }

        h1 {
            text-align: center;
            margin-bottom: 40px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 2.4rem;
            color: #E5D9C4;
            text-shadow: 0 0 15px #CDBA96;
        }

        .dashboard {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 50px;
        }
        .card {
            flex: 1;
            min-width: 180px;
            padding: 25px 20px;
            border-radius: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.4s ease;
            box-shadow: 0 6px 25px rgba(0,0,0,0.5);
            font-weight: 600;
            font-size: 1.2rem;
            background: #222236;
            position: relative;
            overflow: hidden;
            color: #E5D9C4;
        }
        .card::before {
            content:"";
            position: absolute;
            top:0; left:0; width:100%; height:100%;
            background: linear-gradient(120deg, rgba(205,186,150,0.2), transparent);
            opacity: 0;
            transition: opacity 0.4s;
            border-radius:20px;
        }
        .card:hover::before { opacity: 1; }
        .card:hover { transform: translateY(-5px) scale(1.05); box-shadow: 0 12px 35px rgba(0,0,0,0.7); }

        .card.Pendente { background: linear-gradient(135deg,#CDBA96,#CDBA96); color: #fff; }
        .card.EmPreparo { background: linear-gradient(135deg,#CDBA96,#CDBA96); color: #fff; }
        .card.Entregue { background: linear-gradient(135deg,#CDBA96,#CDBA96); color: #fff; }
        .card.Total { background: linear-gradient(135deg,#CDBA96,#CDBA96); color: #fff; }

        .card p { font-size: 1.8rem; margin-top: 10px; text-shadow: 0 0 8px rgba(0,0,0,0.5); }
        .card i { font-size: 1.8rem; display: block; margin-bottom: 8px; }

        .filters { text-align:center; margin-bottom:30px; }
        .filters button {
            padding: 10px 20px;
            border: none;
            border-radius: 25px;
            font-weight: 600;
            margin: 0 5px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            background: #333;
            color: #E5D9C4;
        }
        .filters button:hover { transform: translateY(-3px) scale(1.05); box-shadow: 0 6px 18px rgba(0,0,0,0.5); }
        .filters button.ativo { box-shadow: inset 0 0 0 3px #CDBA96; }

        .busca { margin-bottom:25px; text-align:center; }
        .busca input {
            padding: 12px 20px;
            border-radius: 25px;
            border: 1px solid #555;
            width: 250px;
            max-width: 100%;
            background: #1A1A1B;
            color: #E5D9C4;
            outline: none;
            transition: all 0.3s ease;
            font-size: 1rem;
        }
        .busca input:focus { border-color:#CDBA96; box-shadow:0 0 12px #CDBA96; }

        .pedidos-container { display: grid; grid-template-columns: repeat(auto-fit,minmax(320px,1fr)); gap: 20px; }
        .pedido { background: #2c2c3a; border-radius: 20px; padding: 25px; transition: all 0.4s ease; box-shadow: 0 6px 20px rgba(0,0,0,0.5); position: relative; color: #E5D9C4; }
        .pedido:hover { transform: translateY(-5px) scale(1.02); box-shadow: 0 12px 30px rgba(0,0,0,0.6); }
        .pedido strong { display:block; font-size:1.2rem; margin-bottom:10px; color:#E5D9C4; text-shadow:0 0 5px #000; }
        .pedido p { margin-top:6px; font-size:1rem; color:#ddd; }

        .status { display:inline-block; padding:6px 16px; border-radius:18px; font-weight:600; font-size:0.9rem; margin-top:10px; }
        .status.Pendente { background:#CDBA96; color:#1A1A1B; }
        .status.EmPreparo { background:#E5D9C4; color:#1A1A1B; }
        .status.Entregue { background:#27AE60; color:#fff; }

        .btn-status { margin:6px 5px 0 0; padding:6px 18px; border-radius:20px; border:none; color:#fff; font-weight:600; cursor:pointer; transition:all 0.3s ease; font-size:0.9rem; }
        .btn-status:hover { transform: translateY(-2px) scale(1.05); box-shadow: 0 6px 18px rgba(0,0,0,0.5); }
        .btn-status.Pendente { background:#CDBA96; color:#1A1A1B; }
        .btn-status.EmPreparo { background:#E5D9C4; color:#1A1A1B; }
        .btn-status.Entregue { background:#27AE60; }

        @keyframes novoPedidoGlow { 0%,100%{box-shadow:0 0 0 rgba(46,204,113,0);} 50%{box-shadow:0 0 30px CDBA96;} }
        .pedido.novo { animation: novoPedidoGlow 1.5s ease-in-out 3; border: 2px solid #27AE60; }

        #graficoStatus { max-width: 600px; margin: 0 auto 40px; display:block; height: 350px; }

        @media(max-width:768px){
            .dashboard { flex-direction: column; }
            .pedidos-container { grid-template-columns: 1fr; }
            .pedido { padding: 18px; font-size: 14px; }
            .btn-status { padding: 5px 12px; font-size: 12px; }
            .busca input { width: 100%; max-width: 300px; }
        }
    </style>
</head>
<body>

<h1>Dashboard de Pedidos</h1>

<div class="dashboard">
    <div class="card Pendente" id="card-pendente"><i></i>Pendentes<br><p>0</p></div>
    <div class="card EmPreparo" id="card-empreparo"><i></i>Em Preparo<br><p>0</p></div>
    <div class="card Entregue" id="card-entregue"><i></i>Entregues<br><p>0</p></div>
    <div class="card Total" id="card-total"><i></i>Total<br><p>R$ 0,00</p></div>
</div>

<canvas id="graficoStatus"></canvas>

<div class="busca">
    <input type="text" id="buscaPedido" placeholder="Buscar por mesa ou pedido...">
</div>

<div class="filters">
    <button class="Pendente ativo" onclick="filtrar('Pendente',this)">Pendentes</button>
    <button class="EmPreparo" onclick="filtrar('Em preparo',this)">Em Preparo</button>
    <button class="Entregue" onclick="filtrar('Entregue',this)">Entregues</button>
    <button class="Todos" onclick="filtrar('all',this)">Todos</button>
</div>

<div class="pedidos-container" id="pedidos-container"></div>
<audio id="somPedido" src="novo-pedido.mp3" preload="auto"></audio>

<script>
let filtroAtual = 'Pendente';
const inputBusca = document.getElementById('buscaPedido');
inputBusca.addEventListener('input', aplicarFiltro);

const grafico = new Chart(document.getElementById('graficoStatus'), {
    type:'doughnut',
    data:{
        labels:['Pendentes','Em Preparo','Entregues'],
        datasets:[{
            data:[0,0,0],
            backgroundColor:['#96290eff','#c5c47eff','#27AE60'],
            borderColor:'#1A1A1B',
            borderWidth:2
        }]
    },
    options:{
        responsive:true,
        cutout:'50%',
        plugins:{
            legend:{
                position:'bottom',
                align:'center',
                labels:{
                    color:'#E5D9C4',
                    font:{size:14, weight:'600'},
                    boxWidth:20,
                    boxHeight:20,
                    padding:15,
                    usePointStyle:true
                }
            },
            tooltip:{
                callbacks:{
                    label: function(context){
                        return context.label + ': ' + context.raw;
                    }
                }
            }
        }
    }
});

function filtrar(status, btn){
    filtroAtual = status;
    document.querySelectorAll('.filters button').forEach(b=>b.classList.remove('ativo'));
    btn.classList.add('ativo');
    aplicarFiltro();
}

function aplicarFiltro(){
    const termo = inputBusca.value.toLowerCase();
    document.querySelectorAll('.pedido').forEach(p=>{
        const correspondeFiltro = filtroAtual==='all'||p.dataset.status===filtroAtual;
        const correspondeBusca = p.dataset.mesa?.toLowerCase().includes(termo) || p.dataset.id?.toLowerCase().includes(termo);
        p.style.display = (correspondeFiltro && (termo==='' || correspondeBusca)) ? 'block':'none';
    });
}

function atualizarStatus(id, status){
    fetch('pedidos.php',{
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:`id=${id}&status=${encodeURIComponent(status)}`
    }).then(carregarPedidos);
}

function carregarPedidos(){
    fetch('pedidos.php?action=fetch')
    .then(res=>res.json())
    .then(data=>{
        const container = document.getElementById('pedidos-container');
        const pedidosExistentes = new Set([...container.querySelectorAll('.pedido')].map(p=>p.dataset.id));
        container.innerHTML = '';
        let total = 0;

        data.pedidos.forEach(p=>{
            total += parseFloat(p.total);
            const novo = !pedidosExistentes.has(String(p.id));
            const div = document.createElement('div');
            div.className = 'pedido'+(novo?' novo':'');
            div.dataset.status = p.status;
            div.dataset.id = p.id;
            div.dataset.mesa = p.mesa;
            div.innerHTML = `
                <strong>Pedido #${p.id}</strong> - Mesa ${p.mesa}
                <span class="status ${p.status.replace(' ','')}">${p.status}</span>
                <p>Total: R$ ${parseFloat(p.total).toFixed(2)}</p>
                <button class="btn-status Pendente" onclick="atualizarStatus(${p.id},'Pendente')">Pendente</button>
                <button class="btn-status EmPreparo" onclick="atualizarStatus(${p.id},'Em preparo')">Em Preparo</button>
                <button class="btn-status Entregue" onclick="atualizarStatus(${p.id},'Entregue')">Entregue</button>
            `;
            container.appendChild(div);
            if(novo) document.getElementById('somPedido').play().catch(()=>{});
        });

        document.getElementById('card-pendente').querySelector('p').textContent = data.contagem['Pendente'];
        document.getElementById('card-empreparo').querySelector('p').textContent = data.contagem['Em preparo'];
        document.getElementById('card-entregue').querySelector('p').textContent = data.contagem['Entregue'];

        document.getElementById('card-total').querySelector('p').textContent = 'R$ '+total.toFixed(2);

        grafico.data.datasets[0].data = [data.contagem['Pendente'], data.contagem['Em preparo'], data.contagem['Entregue']];
        grafico.update();

        aplicarFiltro();
    });
}

carregarPedidos();
setInterval(carregarPedidos, 5000);
</script>

</body>
</html>

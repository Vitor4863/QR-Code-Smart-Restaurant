<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>QR Codes das Mesas</title>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<style>
body {
    font-family: 'Segoe UI', sans-serif;
    background: #121212;
    color: #E5D9C4;
    text-align: center;
    padding: 30px;
}
h1 {
    margin-bottom: 40px;
    font-size: 2.2em;
    color: #E5D9C4;
    text-shadow: 0 0 10px #E5D9C4;
}

/* Container de mesas */
#mesas {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 30px;
}

/* Cartão da mesa */
.mesa {
    background: #1e1e1e;
    border-radius: 20px;
    padding: 20px;
    width: 180px;
    
    transition: all 0.3s ease;
    text-align: center;
}
.mesa:hover {
    transform: translateY(-5px) scale(1.1);
  
}

/* Número da mesa */
.mesa div.label {
    font-size: 20px;
    font-weight: bold;
    margin-bottom: 15px;
    color: #E5D9C4;

}

/* QR Code */
.mesa .qrcode {
    margin: auto;
}

/* Responsivo */
@media(max-width:600px){
    #mesas { gap: 20px; }
    .mesa { width: 140px; padding: 15px; }
    .mesa div.label { font-size: 16px; margin-bottom: 10px; }
}
</style>
</head>
<body>
<h1>QR Codes das Mesas</h1>
<div id="mesas"></div>

<script>
const totalMesas = 10;
const container = document.getElementById('mesas');

for(let i=1;i<=totalMesas;i++){
    const div = document.createElement('div');
    div.classList.add('mesa');

    const label = document.createElement('div');
    label.classList.add('label');
    label.textContent = 'Mesa ' + i;
    div.appendChild(label);

    const qrDiv = document.createElement('div');
    qrDiv.classList.add('qrcode');
    div.appendChild(qrDiv);

    const url = `${window.location.origin}/index.php?mesa=${i}`;
    new QRCode(qrDiv, { 
        text: url, 
        width: 150, 
        height: 150, 
        colorDark: "#E5D9C4", 
        colorLight: "#1e1e1e", 
        correctLevel: QRCode.CorrectLevel.H 
    });

    container.appendChild(div);
}
</script>
</body>
</html>

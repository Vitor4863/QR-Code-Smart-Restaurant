function atualizarCarrinho(mesa, id, acao){
    fetch(`ajax_carrinho.php?mesa=${mesa}&acao=${acao}&id=${id}`)
    .then(response => response.json())
    .then(data => {
        document.querySelector("#carrinho").innerHTML = data.html;
    });
}

document.getElementById("configuracoes").addEventListener("click", function() {
    window.location.href = "editar-perfil.php";
});

document.addEventListener('DOMContentLoaded', function() {
    const inputFoto = document.getElementById('foto-perfil');
    const formFoto = document.getElementById('form-foto-perfil');
    const imgPerfil = document.getElementById('foto-perfil-usuario');

    // Quando o usuário seleciona uma nova foto
    inputFoto.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();

            // Atualiza a imagem de perfil exibida
            reader.onload = function(e) {
                imgPerfil.src = e.target.result;
            };

            // Lê o arquivo selecionado
            reader.readAsDataURL(this.files[0]);

            // Envia o formulário automaticamente

            formFoto.submit();
        }
    });
});

document.getElementById('foto-perfil').addEventListener('change', function(event) {
    const file = event.target.files[0];
    if (file){
        const reader = new FileReader();
        reader.onload = function(e) {
            document.querySelector('.foto-perfil').src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
});

function marcarComoLida(id){
    fetch('marcar_notificacao.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'id' + id
    }).then(() => {
        location.reload();
    })
}

document.getElementById("btn-notificacoes").addEventListener("click", function() {
    document.getElementById("notificacoes-dropdown").classList.toggle("show");
});

async function enviarPedidoAmizade(perfilId) {
    try {
        const response = await fetch(`enviar_pedido.php?id=${perfilId}`);
        if (response.ok) {
            location.reload(); // Recarrega a página para atualizar status
        } else {
            alert("Erro ao enviar pedido");
        }
    } catch (error){
        console.error('Erro:', error);
        alert("Erro de conexão");
    }
}

async function aceitarPedido(pedidoId) {
    try {
        const response = await fetch(`aceitar_pedido.php?id=${pedidoId}`);

        if (response.ok){
            location.reload();
        } else {
            alert("Erro ao aceitar o pedido ");
        }
    } catch (error) {
        console.error('Erro:', error);
    }
}

async function recusarPedido(pedidoId, perfil_Id) {
    console.log("pedidoId:", pedidoId, "perfil_Id:", perfil_Id);
    try{
        const response = await fetch(`recusar_pedido.php?id=${pedidoId}&perfil_id=${perfil_Id}`);
        if (response.ok){
           location.reload();
        } else {
            alert("Erro ao recusar o pedido ");
        }
    } catch (error) {
        console.error('Erro:', error);
    }
    
}


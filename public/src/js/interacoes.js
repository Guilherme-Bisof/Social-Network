// interacoes.js
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM carregado, inicializando interações...');

    // Curtir publicação
    document.querySelectorAll('.curtir-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const publicacaoId = this.dataset.publicacaoId;
            console.log('Curtindo publicação:', publicacaoId);
            curtirPublicacao(publicacaoId, this);
        });
    });

    // Toggle comentários
    document.querySelectorAll('.comentar-btn, .comentarios-count').forEach(el => {
        el.addEventListener('click', function() {
            const publicacaoId = this.dataset.publicacaoId;
            console.log('Abrindo comentários:', publicacaoId);
            toggleComentarios(publicacaoId);
        });
    });

    // Enviar comentário
    document.querySelectorAll('.form-comentario').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const publicacaoId = this.dataset.publicacaoId;
            const input = this.querySelector('input[name="comentario"]');
            const comentario = input.value.trim();

            if(!comentario) {
                alert('Por favor, escreva um comentário antes de enviar.');
                return;
            }

            enviarComentario(publicacaoId, comentario, this);
        });
    });
});

// Função de curtir
function curtirPublicacao(publicacaoId, elemento) {
    fetch('../../public/actions/curtir.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'publicacao_id=' + publicacaoId
    })
    .then(r => r.json())
    .then(data => {
        if(data.status !== 'success') {
            alert('Erro: ' + data.message);
            return;
        }

        const icon = elemento.querySelector('i');
        const text = elemento.querySelector('span');

        if(data.acao === 'curtido') {
            icon.classList.replace('far','fas');
            elemento.classList.add('curtido');
            text.textContent = ' Curtido';
        } else {
            icon.classList.replace('fas','far');
            elemento.classList.remove('curtido');
            text.textContent = ' Curtir';
        }

        const curtidasCount = elemento.closest('.publicacao').querySelector('.curtidas-count');
        curtidasCount.textContent = data.total_curtidas + ' curtidas';
    })
    .catch(err => {
        console.error(err);
        alert('Erro ao curtir a publicação.');
    });
}

// Abrir/fechar comentários
function toggleComentarios(publicacaoId) {
    const container = document.getElementById('comentarios-' + publicacaoId);
    if(!container) return;

    container.classList.toggle('mostrar');

    if(container.classList.contains('mostrar')) carregarComentarios(publicacaoId);
}

// Carregar comentários via fetch
function carregarComentarios(publicacaoId) {
    const lista = document.querySelector('#comentarios-' + publicacaoId + ' .lista-comentarios');
    if(!lista) return;

    if(lista.dataset.carregado === "true") return; // já carregado

    lista.innerHTML = '<p class="carregando">Carregando comentários...</p>';

    fetch('../../public/actions/carregar_comentarios.php?publicacao_id=' + publicacaoId)
    .then(r => r.json())
    .then(data => {
        if(data.status === 'success') {
            lista.innerHTML = data.html;
            lista.dataset.carregado = "true";
        } else {
            lista.innerHTML = '<p class="erro">Erro ao carregar comentários.</p>';
        }
    })
    .catch(err => {
        console.error(err);
        lista.innerHTML = '<p class="erro">Erro ao carregar comentários.</p>';
    });
}

// Enviar comentário
function enviarComentario(publicacaoId, comentario, form) {
    fetch('../../public/actions/comentar.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'publicacao_id=' + publicacaoId + '&comentario=' + encodeURIComponent(comentario)
    })
    .then(r => r.json())
    .then(data => {
        if(data.status !== 'success') {
            alert('Erro: ' + data.message);
            return;
        }

        const lista = form.nextElementSibling;
        const semComentarios = lista.querySelector('.sem-comentarios');
        if(semComentarios) semComentarios.remove();
        const carregando = lista.querySelector('.carregando');
        if(carregando) carregando.remove();

        lista.insertAdjacentHTML('afterbegin', data.html);
        form.querySelector('input[name="comentario"]').value = '';

        const countEl = form.closest('.publicacao').querySelector('.comentarios-count');
        let current = parseInt(countEl.dataset.count) || 0;
        countEl.dataset.count = current + 1;
        countEl.textContent = (current + 1) + ' comentários';
    })
    .catch(err => {
        console.error(err);
        alert('Erro ao enviar comentário.');
    });
}

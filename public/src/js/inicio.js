// Carregar atividades ao rolar a page
let paginaAtividades = 1;
window.addEventListener('scroll', () => {
    if(window.innerHeight + window.scrollY >= document.body.offsetHeight - 100) {
        carregarMaisAtividades(paginaAtividades++);
    }
}) ;

function carregarMaisAtividades(pagina) {
    fetch(`carregar_atividades.php?pagina=${pagina}`)
        .then(response => response.text())
        .then(data => {
            document.getElementById('lista-atividades').innerHTML += data;
        });
}

// Carregar Amigos Online
document.addEventListener('DOMContentLoaded', () => {
    fetch('carregar_amigos.php')
        .then(response => response.text())
        .then(data => {
            document.getElementById('amigos-online').innerHTML = data;
        });
});
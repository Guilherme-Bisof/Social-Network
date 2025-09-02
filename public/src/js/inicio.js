document.addEventListener('DOMContentLoaded', function() {
    // Função para preview de imagem antes do upload
    document.querySelectorAll('input[type="file"][name="imagem"]').forEach(input => {
        input.addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Aqui você pode adicionar um preview da imagem se desejar
                    console.log('Imagem carregada para preview');
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
    });
    
    // Função para curtir publicações
    document.querySelectorAll('.fa-thumbs-up').forEach(icon => {
        icon.addEventListener('click', function(e) {
            e.preventDefault();
            this.classList.toggle('far');
            this.classList.toggle('fas');
            this.classList.toggle('text-primary');
        });
    });
    
    // Adicionar funcionalidade de busca
    const searchInput = document.querySelector('.search-container input');
    if (searchInput) {
        searchInput.addEventListener('keyup', function(e) {
            if (e.key === 'Enter') {
                const searchTerm = this.value.trim();
                if (searchTerm.length > 0) {
                    // Implementar lógica de busca aqui
                    console.log('Buscar por:', searchTerm);
                    // window.location.href = `buscar.php?termo=${encodeURIComponent(searchTerm)}`;
                }
            }
        });
    }
    
    // Adicionar animações de hover nas publicações
    const publicacoes = document.querySelectorAll('.custom-card');
    publicacoes.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.transition = 'transform 0.3s ease';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
    
    // Adicionar funcionalidade de carregar mais publicações
    let carregarMaisBtn = document.createElement('button');
    carregarMaisBtn.className = 'btn btn-connectu w-100 mt-3';
    carregarMaisBtn.textContent = 'Carregar Mais';
    carregarMaisBtn.addEventListener('click', function() {
        // Implementar carregamento de mais publicações aqui
        console.log('Carregar mais publicações');
    });
    
    // Adicionar o botão após as publicações
    const publicacoesContainer = document.querySelector('.main-content');
    if (publicacoesContainer) {
        publicacoesContainer.appendChild(carregarMaisBtn);
    }
});
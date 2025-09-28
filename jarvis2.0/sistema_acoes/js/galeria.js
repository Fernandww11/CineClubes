// Melhorias para a galeria de imagens
document.addEventListener('DOMContentLoaded', function() {
    
    // Configurações avançadas do Lightbox
    lightbox.option({
        'resizeDuration': 200,
        'wrapAround': true,
        'albumLabel': 'Imagem %1 de %2',
        'showImageNumberLabel': true,
        'fadeDuration': 300,
        'imageFadeDuration': 300,
        'positionFromTop': 50,
        'disableScrolling': true,
        'sanitizeTitle': false
    });
    
    // Adicionar efeitos hover nas miniaturas da galeria
    const galleryThumbs = document.querySelectorAll('.gallery-thumb');
    
    galleryThumbs.forEach(function(thumb) {
        // Adicionar efeito de zoom suave
        thumb.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.1)';
            this.style.borderColor = '#667eea';
            this.style.boxShadow = '0 4px 15px rgba(102, 126, 234, 0.3)';
        });
        
        thumb.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
            this.style.borderColor = 'transparent';
            this.style.boxShadow = 'none';
        });
        
        // Adicionar loading spinner ao clicar
        thumb.addEventListener('click', function() {
            // Criar overlay de loading
            const loadingOverlay = document.createElement('div');
            loadingOverlay.className = 'loading-overlay';
            loadingOverlay.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            
            // Adicionar estilos inline para o overlay
            loadingOverlay.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.8);
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 9999;
                color: white;
                font-size: 2rem;
            `;
            
            document.body.appendChild(loadingOverlay);
            
            // Remover loading após um tempo
            setTimeout(function() {
                if (document.body.contains(loadingOverlay)) {
                    document.body.removeChild(loadingOverlay);
                }
            }, 1000);
        });
    });
    
    // Adicionar teclas de atalho personalizadas
    document.addEventListener('keydown', function(e) {
        // Se o lightbox estiver aberto
        if (document.querySelector('.lightbox')) {
            switch(e.key) {
                case 'Home':
                    e.preventDefault();
                    // Ir para a primeira imagem
                    lightbox.changeImage(0);
                    break;
                case 'End':
                    e.preventDefault();
                    // Ir para a última imagem
                    const totalImages = lightbox.album.length - 1;
                    lightbox.changeImage(totalImages);
                    break;
                case 'r':
                case 'R':
                    e.preventDefault();
                    // Recarregar imagem atual
                    location.reload();
                    break;
            }
        }
    });
    
    // Adicionar indicador de progresso personalizado
    const originalStart = lightbox.start;
    lightbox.start = function($link) {
        originalStart.call(this, $link);
        
        // Adicionar barra de progresso
        setTimeout(function() {
            const lightboxContainer = document.querySelector('.lightbox');
            if (lightboxContainer && !document.querySelector('.progress-indicator')) {
                const progressBar = document.createElement('div');
                progressBar.className = 'progress-indicator';
                progressBar.style.cssText = `
                    position: absolute;
                    bottom: 10px;
                    left: 50%;
                    transform: translateX(-50%);
                    background: rgba(255, 255, 255, 0.2);
                    border-radius: 10px;
                    padding: 5px 15px;
                    color: white;
                    font-size: 0.9rem;
                    backdrop-filter: blur(10px);
                `;
                
                const currentIndex = lightbox.currentImageIndex + 1;
                const totalImages = lightbox.album.length;
                progressBar.textContent = `${currentIndex} de ${totalImages}`;
                
                lightboxContainer.appendChild(progressBar);
            }
        }, 100);
    };
    
    // Melhorar a experiência de carregamento das imagens
    const images = document.querySelectorAll('img[src*="uploads/"]');
    images.forEach(function(img) {
        // Adicionar lazy loading
        img.loading = 'lazy';
        
        // Adicionar placeholder enquanto carrega
        img.addEventListener('load', function() {
            this.style.opacity = '1';
        });
        
        img.addEventListener('error', function() {
            this.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgZmlsbD0iI2VlZSIvPjx0ZXh0IHg9IjUwIiB5PSI1MCIgZm9udC1mYW1pbHk9IkFyaWFsLCBzYW5zLXNlcmlmIiBmb250LXNpemU9IjE0IiBmaWxsPSIjOTk5IiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBkeT0iLjNlbSI+SW1hZ2VtIG7Do28gZW5jb250cmFkYTwvdGV4dD48L3N2Zz4=';
            this.alt = 'Imagem não encontrada';
        });
        
        // Inicializar com opacidade reduzida
        img.style.opacity = '0.7';
        img.style.transition = 'opacity 0.3s ease';
    });
    
    // Adicionar animação de entrada para os cards
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    // Observar todos os cards de ação
    const actionCards = document.querySelectorAll('.action-card');
    actionCards.forEach(function(card) {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(card);
    });
    
    // Adicionar tooltip personalizado para botões
    const tooltipElements = document.querySelectorAll('[title]');
    tooltipElements.forEach(function(element) {
        element.addEventListener('mouseenter', function() {
            const tooltip = document.createElement('div');
            tooltip.className = 'custom-tooltip';
            tooltip.textContent = this.getAttribute('title');
            tooltip.style.cssText = `
                position: absolute;
                background: rgba(0, 0, 0, 0.8);
                color: white;
                padding: 5px 10px;
                border-radius: 5px;
                font-size: 0.8rem;
                z-index: 1000;
                pointer-events: none;
                white-space: nowrap;
            `;
            
            document.body.appendChild(tooltip);
            
            const rect = this.getBoundingClientRect();
            tooltip.style.left = (rect.left + rect.width / 2 - tooltip.offsetWidth / 2) + 'px';
            tooltip.style.top = (rect.top - tooltip.offsetHeight - 5) + 'px';
            
            this.tooltipElement = tooltip;
        });
        
        element.addEventListener('mouseleave', function() {
            if (this.tooltipElement) {
                document.body.removeChild(this.tooltipElement);
                this.tooltipElement = null;
            }
        });
    });
});

// Função para confirmar exclusão (movida para cá para melhor organização)
function confirmarExclusao(idAcao) {
    // Criar modal personalizado de confirmação
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.innerHTML = `
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Confirmar Exclusão
                    </h5>
                </div>
                <div class="modal-body">
                    <p>Tem certeza que deseja excluir esta ação?</p>
                    <p class="text-muted small">
                        <i class="fas fa-info-circle me-1"></i>
                        Esta ação não pode ser desfeita e todos os arquivos relacionados serão removidos.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="button" class="btn btn-danger" onclick="executarExclusao(${idAcao})">
                        <i class="fas fa-trash me-2"></i>Excluir
                    </button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();
    
    // Remover modal após fechar
    modal.addEventListener('hidden.bs.modal', function() {
        document.body.removeChild(modal);
    });
}

function executarExclusao(idAcao) {
    // Mostrar loading
    const btnExcluir = document.querySelector('.modal .btn-danger');
    btnExcluir.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Excluindo...';
    btnExcluir.disabled = true;
    
    // Redirecionar para a página de exclusão
    setTimeout(function() {
        window.location.href = 'excluir_acao.php?id=' + idAcao;
    }, 500);
}


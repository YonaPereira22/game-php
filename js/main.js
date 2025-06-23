document.addEventListener('DOMContentLoaded', function() {
    // Sistema de votación por estrellas
    const starRatings = document.querySelectorAll('.star-rating');
    
    starRatings.forEach(rating => {
        const stars = rating.querySelectorAll('.vote-star');
        const gameId = rating.dataset.gameId;
        
        stars.forEach((star, index) => {
            star.addEventListener('mouseenter', () => {
                highlightStars(stars, index + 1);
            });
            
            star.addEventListener('mouseleave', () => {
                resetStars(stars);
                highlightVotedStars(stars);
            });
            
            star.addEventListener('click', () => {
                const rating = index + 1;
                submitVote(gameId, rating, stars);
            });
        });
    });
    
    function highlightStars(stars, count) {
        stars.forEach((star, index) => {
            if (index < count) {
                star.classList.add('hover');
            } else {
                star.classList.remove('hover');
            }
        });
    }
    
    function resetStars(stars) {
        stars.forEach(star => {
            star.classList.remove('hover');
        });
    }
    
    function highlightVotedStars(stars) {
        stars.forEach(star => {
            if (star.classList.contains('voted')) {
                star.classList.add('hover');
            }
        });
    }
    
    function submitVote(gameId, rating, stars) {
        const formData = new FormData();
        formData.append('game_id', gameId);
        formData.append('rating', rating);
        
        fetch('vote.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Actualizar estrellas votadas
                stars.forEach((star, index) => {
                    if (index < rating) {
                        star.classList.add('voted');
                    } else {
                        star.classList.remove('voted');
                    }
                });
                
                // Actualizar información de rating
                updateRatingDisplay(data.new_average, data.total_votes);
                
                // Mostrar mensaje de éxito
                showMessage('¡Voto registrado exitosamente!', 'success');
                
                // Actualizar texto de voto del usuario
                updateUserVoteInfo(rating);
            } else {
                showMessage(data.message || 'Error al procesar el voto', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('Error de conexión', 'error');
        });
    }
    
    function updateRatingDisplay(newAverage, totalVotes) {
        const currentRating = document.querySelector('.current-rating');
        if (currentRating) {
            const stars = currentRating.querySelectorAll('.fas.fa-star');
            const ratingText = currentRating.querySelector('span');
            
            stars.forEach((star, index) => {
                if (index < Math.round(newAverage)) {
                    star.classList.add('active');
                } else {
                    star.classList.remove('active');
                }
            });
            
            if (ratingText) {
                ratingText.textContent = `${newAverage} (${totalVotes} votos)`;
            }
        }
    }
    
    function updateUserVoteInfo(rating) {
        let userVoteInfo = document.querySelector('.user-vote-info');
        if (!userVoteInfo) {
            userVoteInfo = document.createElement('p');
            userVoteInfo.className = 'user-vote-info';
            document.querySelector('.vote-section').appendChild(userVoteInfo);
        }
        userVoteInfo.textContent = `Ya votaste: ${rating} estrellas`;
    }
    
    function showMessage(text, type) {
        // Remover mensajes existentes
        const existingMessages = document.querySelectorAll('.temp-message');
        existingMessages.forEach(msg => msg.remove());
        
        // Crear nuevo mensaje
        const message = document.createElement('div');
        message.className = `message ${type} temp-message`;
        message.textContent = text;
        message.style.position = 'fixed';
        message.style.top = '20px';
        message.style.right = '20px';
        message.style.zIndex = '1000';
        message.style.minWidth = '300px';
        message.style.animation = 'slideIn 0.3s ease-out';
        
        document.body.appendChild(message);
        
        // Remover después de 3 segundos
        setTimeout(() => {
            message.style.animation = 'slideOut 0.3s ease-in';
            setTimeout(() => message.remove(), 300);
        }, 3000);
    }
    
    // Validación de formulario de subida
    const uploadForm = document.querySelector('.upload-form');
    if (uploadForm) {
        uploadForm.addEventListener('submit', function(e) {
            const fileInput = document.getElementById('game_file');
            if (fileInput.files.length > 0) {
                const file = fileInput.files[0];
                if (file.size > 10 * 1024 * 1024) { // 10MB
                    e.preventDefault();
                    showMessage('El archivo es demasiado grande. Máximo 10MB.', 'error');
                    return;
                }
                
                if (!file.name.toLowerCase().endsWith('.zip')) {
                    e.preventDefault();
                    showMessage('Solo se permiten archivos ZIP.', 'error');
                    return;
                }
            }
        });
    }
    
    // Animaciones CSS adicionales
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);
});
    
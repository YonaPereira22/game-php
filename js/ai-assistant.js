document.addEventListener('DOMContentLoaded', function() {
    var dialog = document.getElementById('assistantChat');
    var openButton = document.getElementById('openAssistantChat');
    var closeButton = document.getElementById('closeAssistantChat');
    var messages = document.getElementById('assistantChatMessages');
    var form = document.getElementById('assistantChatForm');
    var input = document.getElementById('assistantChatInput');
    var suggestions = document.getElementById('assistantChatSuggestions');

    if (!dialog || !openButton || !closeButton || !messages || !form || !input || !suggestions) {
        return;
    }

    function appendMessage(text, sender) {
        var message = document.createElement('div');
        message.className = 'assistant-chat-message assistant-chat-message--' + sender;
        message.textContent = text;
        messages.appendChild(message);
        messages.scrollTop = messages.scrollHeight;
        return message;
    }

    function appendRecommendations(games) {
        if (!Array.isArray(games) || games.length === 0) {
            return;
        }

        var resultList = document.createElement('div');
        resultList.className = 'assistant-chat-results';

        games.forEach(function(game) {
            var link = document.createElement('a');
            link.className = 'assistant-chat-result';
            link.href = 'game.php?id=' + encodeURIComponent(game.id);

            if (game.preview_url) {
                var image = document.createElement('img');
                image.src = game.preview_url;
                image.alt = '';
                image.loading = 'lazy';
                image.addEventListener('error', function() {
                    var placeholder = document.createElement('span');
                    placeholder.className = 'assistant-chat-result-placeholder';
                    placeholder.textContent = '🎮';
                    image.replaceWith(placeholder);
                }, { once: true });
                link.appendChild(image);
            } else {
                var placeholder = document.createElement('span');
                placeholder.className = 'assistant-chat-result-placeholder';
                placeholder.textContent = '🎮';
                link.appendChild(placeholder);
            }

            var details = document.createElement('span');
            var title = document.createElement('strong');
            var metadata = document.createElement('small');
            title.textContent = game.title;
            metadata.textContent = [game.category, game.age_group].filter(Boolean).join(' · ');
            details.appendChild(title);
            details.appendChild(metadata);
            link.appendChild(details);
            resultList.appendChild(link);
        });

        messages.appendChild(resultList);
        messages.scrollTop = messages.scrollHeight;
    }

    function sendMessage(text) {
        var message = text.trim();
        if (!message || form.dataset.busy === 'true') {
            return;
        }

        appendMessage(message, 'user');
        input.value = '';
        form.dataset.busy = 'true';
        form.querySelector('button[type="submit"]').disabled = true;
        suggestions.hidden = true;
        var pendingMessage = appendMessage('Estamos buscando juegos para ti…', 'assistant');

        fetch('recomendaciones.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ message: message })
        })
            .then(function(response) {
                return response.json().then(function(data) {
                    if (!response.ok) {
                        throw new Error(data.error || 'No pudimos consultar el catálogo.');
                    }
                    return data;
                });
            })
            .then(function(data) {
                pendingMessage.textContent = data.reply || 'Aquí tienes algunas recomendaciones del catálogo.';
                appendRecommendations(data.games);
            })
            .catch(function(error) {
                pendingMessage.textContent = error.message || 'No pudimos consultar el catálogo. Inténtalo nuevamente.';
            })
            .finally(function() {
                form.dataset.busy = 'false';
                form.querySelector('button[type="submit"]').disabled = false;
                input.focus();
            });
    }

    appendMessage('Somos tus asistentes de juegos. Cuéntanos qué materia o tema quieres practicar y, si quieres, para qué edad.', 'assistant');

    openButton.addEventListener('click', function() {
        dialog.showModal();
        input.focus();
    });

    closeButton.addEventListener('click', function() {
        dialog.close();
    });

    dialog.addEventListener('click', function(event) {
        if (event.target === dialog) {
            dialog.close();
        }
    });

    form.addEventListener('submit', function(event) {
        event.preventDefault();
        sendMessage(input.value);
    });

    suggestions.querySelectorAll('button').forEach(function(button) {
        button.addEventListener('click', function() {
            sendMessage(button.textContent);
        });
    });
});

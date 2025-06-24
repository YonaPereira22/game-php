// Letras del alfabeto español (26 letras)
const SPANISH_LETTERS = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];

// Variables del juego
let preguntasJSON = {};
let currentTheme = '';
let currentQuestions = {};
let currentLetter = '';
let letterTimer = null;
let timeLeft = 30;
let gameStats = {
    correct: 0,
    incorrect: 0,
    remaining: 26
};
let score = 0;
let scorePorLetra = {};
let estadoPorLetra = {}; // { letra: "pendiente" | "correcto" | "incorrecto" }
let tiempoPorLetra = {}; // { letra: segundos_restantes }


// Elementos DOM
const themeSelector = document.getElementById('themeSelector');
const themeButtons = document.getElementById('themeButtons');
const roscoContainer = document.getElementById('roscoContainer');
const roscoCircle = document.getElementById('roscoCircle');
const questionPanel = document.getElementById('questionPanel');
const gameStatsElement = document.getElementById('gameStats');
const answerInput = document.getElementById('answerInput');
const finalStats = document.getElementById('finalStats');

// Inicializar el juego
function initGame() {
    fetch('preguntas.json')
        .then(r => r.json())
        .then(json => {
            preguntasJSON = json;
            generateThemeButtons();
        });
}

// Generar botones de tema
function generateThemeButtons() {
    themeButtons.innerHTML = '';
    Object.keys(preguntasJSON).forEach(theme => {
        const button = document.createElement('button');
        button.className = 'theme-btn';
        button.textContent = theme.charAt(0).toUpperCase() + theme.slice(1);
        button.onclick = () => selectTheme(theme);
        themeButtons.appendChild(button);
    });
}

// Seleccionar tema
function selectTheme(theme) {
    currentTheme = theme;
    currentQuestions = {};
    preguntasJSON[theme].forEach(question => {
        currentQuestions[question.letra] = question;
    });

    themeSelector.style.display = 'none';

    // Oculta la zona de juego
    document.querySelector('.juego-area').classList.remove('hidden');
    gameStatsElement.classList.remove('hidden');

    generateRosco();
    resetGameStats();
}

// Generar el rosco circular
function generateRosco() {
    roscoCircle.innerHTML = '';
    const radius = 200;
    const centerX = 250;
    const centerY = 250;

    SPANISH_LETTERS.forEach((letter, index) => {
        const angle = (index * 360 / SPANISH_LETTERS.length) - 90;
        const angleRad = (angle * Math.PI) / 180;

        const x = centerX + radius * Math.cos(angleRad);
        const y = centerY + radius * Math.sin(angleRad);

        const letterBtn = document.createElement('button');
        letterBtn.className = 'letter-btn';
        letterBtn.textContent = letter;
        letterBtn.style.left = `${x}px`;
        letterBtn.style.top = `${y}px`;
        letterBtn.id = `btn-${letter}`;
        //letterBtn.onclick = () => selectLetter(letter);

        // Añadir temporizador
        const timer = document.createElement('div');
        timer.className = 'timer';
        timer.textContent = '30';
        timer.id = `timer-${letter}`;
        letterBtn.appendChild(timer);

        roscoCircle.appendChild(letterBtn);
    });
}

// Seleccionar letra
function selectLetter(letter) {
    if (!currentQuestions[letter]) {
        alert(`No hay pregunta disponible para la letra ${letter}`);
        return;
    }
    document.querySelectorAll('.letter-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    const letterBtn = document.getElementById(`btn-${letter}`);
    letterBtn.classList.add('active');
    currentLetter = letter;
    showQuestion(letter);
    startLetterTimer(letter); // pasa la letra
}

// Mostrar pregunta
function showQuestion(letter) {
    const question = currentQuestions[letter];
    document.getElementById('currentLetter').textContent = `Letra ${letter}`;
    let questionText = '';
    switch (question.tipo) {
        case 'comienza':
            questionText = `Comienza con ${letter}: ${question.descripcion}`;
            break;
        case 'termina':
            questionText = `Termina con ${letter}: ${question.descripcion}`;
            break;
        case 'contiene':
            questionText = `Contiene ${letter}: ${question.descripcion}`;
            break;
    }
    document.getElementById('questionText').textContent = questionText;
    answerInput.value = '';
    answerInput.focus();

    console.log("Pendientes: ", Object.keys(estadoPorLetra).filter(l => estadoPorLetra[l] === "pendiente"));
console.log("Correctos: ", Object.keys(estadoPorLetra).filter(l => estadoPorLetra[l] === "correcto"));
console.log("Incorrectos: ", Object.keys(estadoPorLetra).filter(l => estadoPorLetra[l] === "incorrecto"));

}

// Iniciar temporizador de letra
function startLetterTimer(letter) {
    clearInterval(letterTimer);
    timeLeft = tiempoPorLetra[letter] || 30;
    updateTimerDisplay();
    letterTimer = setInterval(() => {
        timeLeft--;
        updateTimerDisplay();
        if (timeLeft <= 0) {
            clearInterval(letterTimer);
            tiempoPorLetra[letter] = 0;
            handleTimeOut();
        }
    }, 1000);
}

// Actualizar display del temporizador
function updateTimerDisplay() {
    document.getElementById('timeLeft').textContent = timeLeft;
    if (currentLetter) {
        const timer = document.getElementById(`timer-${currentLetter}`);
        if (timer) {
            timer.textContent = timeLeft;
        }
    }
}

// Manejar tiempo agotado
function handleTimeOut() {
    markIncorrect();
    moveToNextLetter();
}

// Verificar respuesta
function checkAnswer() {
    const userAnswer = answerInput.value.trim().toLowerCase();
    const correctAnswer = currentQuestions[currentLetter].palabra.toLowerCase();

    clearInterval(letterTimer);

    if (userAnswer === correctAnswer) {
        markCorrect();
    } else {
        markIncorrect();
    }

    setTimeout(() => {
        moveToNextLetter();
    }, 1200);
}

// Marcar respuesta correcta
function markCorrect() {
    const letterBtn = document.getElementById(`btn-${currentLetter}`);
    letterBtn.classList.remove('active', 'incorrect', 'pending'); // <-- QUITA 'pending'
    letterBtn.classList.add('correct');
    letterBtn.onclick = null;

    gameStats.correct++;
    gameStats.remaining--;

    estadoPorLetra[currentLetter] = "correcto";
    tiempoPorLetra[currentLetter] = timeLeft;
    scorePorLetra[currentLetter] = timeLeft;
    score += timeLeft;

    updateGameStats();
    showFeedback('¡Correcto! 🎉', 'correct');
}

// Marcar respuesta incorrecta
function markIncorrect() {
    const letterBtn = document.getElementById(`btn-${currentLetter}`);
    letterBtn.classList.remove('active', 'correct', 'pending'); // <-- QUITA 'pending'
    letterBtn.classList.add('incorrect');
    letterBtn.onclick = null;

    gameStats.incorrect++;
    gameStats.remaining--;

    estadoPorLetra[currentLetter] = "incorrecto";
    tiempoPorLetra[currentLetter] = 0;
    scorePorLetra[currentLetter] = 0;

    // Aquí fuerzas el cambio visual en el botón del rosco:
    const timerDiv = document.getElementById(`timer-${currentLetter}`);
    if (timerDiv) timerDiv.textContent = "0";

    updateGameStats();
    const correctAnswer = currentQuestions[currentLetter].palabra;
    showFeedback(`❌ Incorrecto. La respuesta era: "${correctAnswer}"`, 'incorrect');
}

// Mostrar feedback
function showFeedback(message, type) {
    const feedback = document.createElement('div');
    feedback.style.cssText = `
        position: fixed;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        padding: 15px 25px;
        border-radius: 25px;
        color: white;
        font-weight: bold;
        z-index: 1000;
        font-size: 1.1rem;
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        ${type === 'correct' ? 'background: linear-gradient(45deg, #56ab2f, #a8e6cf);' : 'background: linear-gradient(45deg, #ff416c, #ff4b2b);'}
    `;
    feedback.textContent = message;
    document.body.appendChild(feedback);

    setTimeout(() => {
        feedback.remove();
    }, 2200);
}

// Pasar pregunta
function passQuestion() {
    clearInterval(letterTimer);
    tiempoPorLetra[currentLetter] = timeLeft;  // guarda el tiempo restante
    estadoPorLetra[currentLetter] = "pendiente";
    // Marca la letra como azul ("pendiente")
    const letterBtn = document.getElementById(`btn-${currentLetter}`);
    letterBtn.classList.remove('active', 'correct', 'incorrect');
    letterBtn.classList.add('pending');
    setTimeout(() => {
        moveToNextLetter();
    }, 400);
}

// Mover a siguiente letra
function moveToNextLetter() {
    const pendientes = SPANISH_LETTERS.filter(letra =>
        estadoPorLetra[letra] === "pendiente"
    );
    if (pendientes.length === 0) {
        endGame();
        return;
    }
    // Elige la próxima pendiente, ciclo
    let idx = pendientes.indexOf(currentLetter);
    let siguienteLetra = pendientes[(idx + 1) % pendientes.length];
    selectLetter(siguienteLetra);
}

// Actualizar estadísticas del juego
function updateGameStats() {
    document.getElementById('correctCount').textContent = gameStats.correct;
    document.getElementById('incorrectCount').textContent = gameStats.incorrect;
    document.getElementById('remainingCount').textContent = gameStats.remaining;
    document.getElementById('timeLeft').textContent = timeLeft;
}

// Reiniciar estadísticas del juego
function resetGameStats() {
    gameStats = { correct: 0, incorrect: 0, remaining: 26 };
    score = 0;
    scorePorLetra = {};
    estadoPorLetra = {};
    tiempoPorLetra = {};
    SPANISH_LETTERS.forEach(letra => {
        estadoPorLetra[letra] = "pendiente";
        tiempoPorLetra[letra] = 30;
    });
    updateGameStats();
    setTimeout(() => {
        selectLetter('A');
    }, 500);
}

// Terminar juego
function endGame() {
    clearInterval(letterTimer);

    roscoContainer.style.display = 'none';
    questionPanel.style.display = 'none';
    gameStatsElement.classList.add('hidden');

    showFinalStats();
}

// Mostrar estadísticas finales
function showFinalStats() {
    const percentage = Math.round((gameStats.correct / 26) * 100);
    let performance = '';

    if (percentage >= 90) performance = '🏆 ¡Excelente!';
    else if (percentage >= 70) performance = '🥇 ¡Muy bien!';
    else if (percentage >= 50) performance = '🥈 ¡Bien!';
    else performance = '🥉 ¡Sigue practicando!';

    let scoreList = '<ul style="columns: 2; font-size:0.98em;">';
    SPANISH_LETTERS.forEach(letter => {
        if(scorePorLetra[letter] !== undefined)
            scoreList += `<li>${letter}: <strong>${scorePorLetra[letter]}</strong></li>`;
    });
    scoreList += '</ul>';

    document.getElementById('finalStatsContent').innerHTML = `
        <div style="margin-bottom: 15px;">
            <b>Puntaje total:</b> <span style="color:#ffd89b;font-size:1.3em;">${score}</span>
        </div>
        <div>${scoreList}</div>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 20px;">
            <div class="stat-card">
                <div class="stat-number" style="color: #56ab2f;">${gameStats.correct}</div>
                <div>Respuestas Correctas</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" style="color: #ff416c;">${gameStats.incorrect}</div>
                <div>Respuestas Incorrectas</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" style="color: #ffd89b;">${percentage}%</div>
                <div>Porcentaje de Aciertos</div>
            </div>
        </div>
        <h3 style="margin: 20px 0; font-size: 1.5rem;">${performance}</h3>
        <p style="font-size: 1.1rem; margin-bottom: 15px;">
            Has completado el rosco del tema <strong>${currentTheme}</strong>
        </p>
    `;
    finalStats.style.display = 'block';
}

// Reiniciar juego
function restartGame() {
    finalStats.style.display = 'none';
    themeSelector.style.display = 'block';
    currentTheme = '';
    currentQuestions = {};
    currentLetter = '';
    clearInterval(letterTimer);
    generateThemeButtons();
}

// Event listeners
document.getElementById('submitBtn').addEventListener('click', checkAnswer);
document.getElementById('passBtn').addEventListener('click', passQuestion);
document.getElementById('restartBtn').addEventListener('click', restartGame);
answerInput.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') checkAnswer();
});
answerInput.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') e.preventDefault();
});
document.addEventListener('DOMContentLoaded', initGame);

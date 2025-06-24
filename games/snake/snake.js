// Configuración del juego
const canvas = document.getElementById('gameBoard');
const ctx = canvas.getContext('2d');
const blockSize = 20;
const gridWidth = canvas.width / blockSize;
const gridHeight = canvas.height / blockSize;

// Elementos UI
const scoreElement = document.getElementById('score');
const levelElement = document.getElementById('level');
const codeEditorElement = document.getElementById('codeEditor');
const tokenInfoElement = document.getElementById('tokenInfo');
const levelInfoElement = document.getElementById('levelInfo');
const gameOverScreen = document.getElementById('gameOver');
const errorMessageElement = document.getElementById('errorMessage');
const finalScoreElement = document.getElementById('finalScore');
const startScreen = document.getElementById('startScreen');

// Botones
const resetButton = document.getElementById('resetButton');
const restartButton = document.getElementById('restartButton');
const startButton = document.getElementById('startButton');

// Estado del juego
let snake = [];
let direction = 'right';
let nextDirection = 'right';
let score = 0;
let gameSpeed = 150;
let currentLevel = 1;
let gameInterval;
let token = null;
let collectedTokens = [];
let gameActive = false;

// Niveles y metas
const levels = [
    {
        name: "Variables",
        tokens: [
            { type: 'keyword', value: 'let', description: 'Declara una variable local con ámbito de bloque.' },
            { type: 'keyword', value: 'nombre', description: 'Identificador para almacenar un valor.' },
            { type: 'operator', value: '=', description: 'Operador de asignación.' },
            { type: 'string', value: '"programador"', description: 'Valor de texto (string) entre comillas.' },
            { type: 'punctuation', value: ';', description: 'Punto y coma para finalizar una instrucción.' }
        ],
        targetCode: 'let nombre = "programador";',
        description: "Nivel 1: Debes crear una variable llamada 'nombre' con el valor 'programador'"
    },
    {
        name: "Condicionales",
        tokens: [
            { type: 'keyword', value: 'if', description: 'Estructura condicional para ejecutar código si la condición es verdadera.' },
            { type: 'punctuation', value: '(', description: 'Paréntesis de apertura para la condición.' },
            { type: 'keyword', value: 'score', description: 'Variable que almacena la puntuación.' },
            { type: 'operator', value: '>', description: 'Operador de comparación "mayor que".' },
            { type: 'number', value: '10', description: 'Valor numérico.' },
            { type: 'punctuation', value: ')', description: 'Paréntesis de cierre para la condición.' },
            { type: 'punctuation', value: '{', description: 'Llave de apertura para el bloque de código.' },
            { type: 'keyword', value: 'win', description: 'Palabra clave que representa ganar.' },
            { type: 'punctuation', value: '(', description: 'Paréntesis de apertura para la función.' },
            { type: 'punctuation', value: ')', description: 'Paréntesis de cierre para la función.' },
            { type: 'punctuation', value: ';', description: 'Punto y coma para finalizar una instrucción.' },
            { type: 'punctuation', value: '}', description: 'Llave de cierre para el bloque de código.' }
        ],
        targetCode: 'if (score > 10) {\n  win();\n}',
        description: "Nivel 2: Crea una estructura condicional que ejecute win() si la puntuación es mayor que 10"
    },
    {
        name: "Funciones",
        tokens: [
            { type: 'keyword', value: 'function', description: 'Palabra clave para declarar una función.' },
            { type: 'function', value: 'saludar', description: 'Nombre de la función.' },
            { type: 'punctuation', value: '(', description: 'Paréntesis de apertura para los argumentos.' },
            { type: 'punctuation', value: ')', description: 'Paréntesis de cierre para los argumentos.' },
            { type: 'punctuation', value: '{', description: 'Llave de apertura para el bloque de código.' },
            { type: 'keyword', value: 'return', description: 'Palabra clave para devolver un valor.' },
            { type: 'string', value: '"Hola"', description: 'Valor de texto (string) entre comillas.' },
            { type: 'punctuation', value: ';', description: 'Punto y coma para finalizar una instrucción.' },
            { type: 'punctuation', value: '}', description: 'Llave de cierre para el bloque de código.' }
        ],
        targetCode: 'function saludar() {\n  return "Hola";\n}',
        description: "Nivel 3: Define una función llamada 'saludar' que devuelva el string 'Hola'"
    }
];

// Errores para colisiones
const errors = [
    "SyntaxError: Unexpected token",
    "TypeError: Cannot read property of undefined",
    "ReferenceError: Variable not defined",
    "RangeError: Maximum call stack size exceeded",
    "TypeError: null is not a function"
];

// Inicializar juego
function initGame() {
    // Crear serpiente inicial
    snake = [
        {x: 5, y: 10},
        {x: 4, y: 10},
        {x: 3, y: 10}
    ];
    
    direction = 'right';
    nextDirection = 'right';
    score = 0;
    gameSpeed = 150;
    currentLevel = 1;
    collectedTokens = [];
    
    // Actualizar UI
    scoreElement.textContent = score;
    levelElement.textContent = currentLevel;
    updateCodeEditor();
    levelInfoElement.textContent = levels[currentLevel - 1].description;
    
    // Generar primer token
    createToken();
    
    // Iniciar bucle de juego
    if (gameInterval) clearInterval(gameInterval);
    gameInterval = setInterval(gameLoop, gameSpeed);
    gameActive = true;
}

// Bucle principal del juego
function gameLoop() {
    moveSnake();
    checkCollision();
    drawGame();
}

// Dibujar el juego
function drawGame() {
    // Limpiar canvas
    ctx.fillStyle = "#252526";
    ctx.fillRect(0, 0, canvas.width, canvas.height);
    
    // Dibujar cuadrícula
    ctx.strokeStyle = "#333333";
    ctx.lineWidth = 0.5;
    
    // Líneas horizontales
    for (let y = 0; y <= canvas.height; y += blockSize) {
        ctx.beginPath();
        ctx.moveTo(0, y);
        ctx.lineTo(canvas.width, y);
        ctx.stroke();
    }
    
    // Líneas verticales
    for (let x = 0; x <= canvas.width; x += blockSize) {
        ctx.beginPath();
        ctx.moveTo(x, 0);
        ctx.lineTo(x, canvas.height);
        ctx.stroke();
    }
    
    // Dibujar token
    if (token) {
        const tokenType = levels[currentLevel - 1].tokens[token.tokenIndex].type;
        switch(tokenType) {
            case 'keyword':
                ctx.fillStyle = "#569cd6";
                break;
            case 'operator':
                ctx.fillStyle = "#d4d4d4";
                break;
            case 'punctuation':
                ctx.fillStyle = "#d4d4d4";
                break;
            case 'string':
                ctx.fillStyle = "#ce9178";
                break;
            case 'number':
                ctx.fillStyle = "#b5cea8";
                break;
            case 'function':
                ctx.fillStyle = "#dcdcaa";
                break;
            default:
                ctx.fillStyle = "#569cd6";
        }
        
        ctx.fillRect(token.x * blockSize, token.y * blockSize, blockSize, blockSize);
        
        // Dibujar texto del token
        ctx.fillStyle = "#000000";
        ctx.font = "10px Courier New";
        const tokenText = levels[currentLevel - 1].tokens[token.tokenIndex].value.substring(0, 2);
        ctx.fillText(tokenText, token.x * blockSize + 4, token.y * blockSize + 14);
    }
    
    // Dibujar serpiente
    for (let i = 0; i < snake.length; i++) {
        // Cabeza de la serpiente
        if (i === 0) {
            ctx.fillStyle = "#569cd6"; // Azul VSCode para la cabeza
        } else {
            // Cuerpo con color gradiente
            const greenValue = Math.floor(200 - (i * 5));
            ctx.fillStyle = `rgb(50, ${Math.max(50, greenValue)}, 50)`;
        }
        
        ctx.fillRect(snake[i].x * blockSize, snake[i].y * blockSize, blockSize, blockSize);
        
        // Borde para cada segmento
        ctx.strokeStyle = "#333333";
        ctx.lineWidth = 1;
        ctx.strokeRect(snake[i].x * blockSize, snake[i].y * blockSize, blockSize, blockSize);
        
        // Si es la cabeza, dibujar "ojos"
        if (i === 0) {
            ctx.fillStyle = "#ffffff";
            
            // Ajustar posición de los ojos según la dirección
            if (direction === 'right' || direction === 'left') {
                const eyeX = direction === 'right' ? 
                    snake[i].x * blockSize + blockSize - 6 : 
                    snake[i].x * blockSize + 2;
                
                ctx.fillRect(eyeX, snake[i].y * blockSize + 5, 4, 4);
                ctx.fillRect(eyeX, snake[i].y * blockSize + blockSize - 9, 4, 4);
            } else {
                const eyeY = direction === 'down' ? 
                    snake[i].y * blockSize + blockSize - 6 : 
                    snake[i].y * blockSize + 2;
                
                ctx.fillRect(snake[i].x * blockSize + 5, eyeY, 4, 4);
                ctx.fillRect(snake[i].x * blockSize + blockSize - 9, eyeY, 4, 4);
            }
        }
    }
}

// Mover la serpiente
function moveSnake() {
    // Actualizar dirección
    direction = nextDirection;
    
    // Calcular nueva posición de la cabeza
    const head = {x: snake[0].x, y: snake[0].y};
    
    switch(direction) {
        case 'up':
            head.y--;
            break;
        case 'down':
            head.y++;
            break;
        case 'left':
            head.x--;
            break;
        case 'right':
            head.x++;
            break;
    }
    
    // Agregar nueva cabeza
    snake.unshift(head);
    
    // Comprobar si la serpiente ha comido un token
    if (token && head.x === token.x && head.y === token.y) {
        // Aumentar puntuación
        score += 10;
        scoreElement.textContent = score;
        
        // Agregar token a la colección
        const currentToken = levels[currentLevel - 1].tokens[token.tokenIndex];
        collectedTokens.push(currentToken);
        
        // Mostrar información del token
        tokenInfoElement.innerHTML = `<span class="${currentToken.type}">🍴 Comiste <span class="token">${currentToken.value}</span> → ${currentToken.description}</span>`;
        
        // Actualizar código en el editor
        updateCodeEditor();
        
        // Verificar si se completó el nivel
        checkLevelCompletion();
        
        // Crear nuevo token
        createToken();
        
        // Aumentar velocidad ligeramente
        if (gameSpeed > 70) {
            gameSpeed -= 2;
            clearInterval(gameInterval);
            gameInterval = setInterval(gameLoop, gameSpeed);
        }
    } else {
        // Si no comió un token, remover la cola
        snake.pop();
    }
}

// Comprobar colisiones
function checkCollision() {
    const head = snake[0];
    
    // Colisión con los bordes
    if (head.x < 0 || head.x >= gridWidth || head.y < 0 || head.y >= gridHeight) {
        gameOver("¡Error de límites! La variable está fuera de rango.");
        return;
    }
    
    // Colisión con la propia serpiente
    for (let i = 1; i < snake.length; i++) {
        if (head.x === snake[i].x && head.y === snake[i].y) {
            gameOver(errors[Math.floor(Math.random() * errors.length)]);
            return;
        }
    }
}

// Crear un nuevo token
function createToken() {
    // Si no hay más tokens disponibles, no crear ninguno
    const availableTokens = levels[currentLevel - 1].tokens.filter(
        (_, index) => !collectedTokens.some(t => t.value === levels[currentLevel - 1].tokens[index].value)
    );
    
    if (availableTokens.length === 0) {
        token = null;
        return;
    }
    
    // Seleccionar un token disponible al azar
    const tokenIndex = levels[currentLevel - 1].tokens.findIndex(
        t => !collectedTokens.some(ct => ct.value === t.value)
    );
    
    // Generar posición aleatoria que no esté ocupada por la serpiente
    let x, y;
    let validPosition = false;
    
    while (!validPosition) {
        x = Math.floor(Math.random() * gridWidth);
        y = Math.floor(Math.random() * gridHeight);
        
        validPosition = true;
        
        // Comprobar si la posición está ocupada por la serpiente
        for (const segment of snake) {
            if (segment.x === x && segment.y === y) {
                validPosition = false;
                break;
            }
        }
    }
    
    token = {x, y, tokenIndex};
}

// Actualizar el editor de código
function updateCodeEditor() {
    if (collectedTokens.length === 0) {
        codeEditorElement.innerHTML = "// Tu código aparecerá aquí";
        return;
    }
    
    let codeHTML = "";
    
    // Formatear el código con colores según el tipo de token
    for (const token of collectedTokens) {
        codeHTML += `<span class="${token.type}">${token.value}</span> `;
    }
    
    // Reemplazar saltos de línea específicos para mejor formato
    let formattedCode = codeHTML;
    if (currentLevel === 2) {
        // Para el condicional if
        if (codeHTML.includes('{')) {
            formattedCode = formattedCode.replace('{ ', '{\n  ');
        }
        if (codeHTML.includes('}')) {
            formattedCode = formattedCode.replace(' }', '\n}');
        }
    } else if (currentLevel === 3) {
        // Para la función
        if (codeHTML.includes('{')) {
            formattedCode = formattedCode.replace('{ ', '{\n  ');
        }
        if (codeHTML.includes('}')) {
            formattedCode = formattedCode.replace(' }', '\n}');
        }
    }
    
    codeEditorElement.innerHTML = formattedCode;
}

// Verificar si se completó el nivel
function checkLevelCompletion() {
    const currentLevelData = levels[currentLevel - 1];
    
    // Verificar si se han recogido todos los tokens del nivel
    if (collectedTokens.length === currentLevelData.tokens.length) {
        // Verificar si están en el orden correcto
        const collectedCode = collectedTokens.map(t => t.value).join(' ');
        const targetTokens = currentLevelData.tokens.map(t => t.value).join(' ');
        
        if (collectedCode === targetTokens) {
            // Nivel completado
            levelUp();
        } else {
            // Tokens en orden incorrecto
            gameOver("SyntaxError: Error de sintaxis, los tokens están en orden incorrecto.");
        }
    }
}

// Subir de nivel
function levelUp() {
    currentLevel++;
    
    if (currentLevel > levels.length) {
        // Juego completado
        clearInterval(gameInterval);
        
        // Mostrar mensaje de victoria
        gameOverScreen.style.display = "flex";
        errorMessageElement.style.color = "#4EC9B0";
        errorMessageElement.textContent = "¡Felicidades! Has completado todos los niveles";
        finalScoreElement.textContent = `Puntuación final: ${score}`;
        gameActive = false;
        return;
    }
    
    // Bonus de puntos por completar nivel
    score += 50;
    scoreElement.textContent = score;
    levelElement.textContent = currentLevel;
    
    // Actualizar información del nivel
    levelInfoElement.textContent = levels[currentLevel - 1].description;
    
    // Resetear tokens recogidos
    collectedTokens = [];
    updateCodeEditor();
    
    // Crear nuevo token
    createToken();
    
    // Mostrar mensaje de nivel completado
    tokenInfoElement.innerHTML = `<span style="color: #4EC9B0">¡Nivel completado! +50 puntos</span>`;
}

// Game Over
function gameOver(error) {
    clearInterval(gameInterval);
    gameActive = false;
    
    // Mostrar pantalla de game over
    gameOverScreen.style.display = "flex";
    errorMessageElement.textContent = error;
    finalScoreElement.textContent = `Puntuación final: ${score}`;
}

// Controles del teclado
document.addEventListener('keydown', (e) => {
    if (!gameActive) return;
    
    switch(e.key) {
        case 'ArrowUp':
            if (direction !== 'down') nextDirection = 'up';
            break;
        case 'ArrowDown':
            if (direction !== 'up') nextDirection = 'down';
            break;
        case 'ArrowLeft':
            if (direction !== 'right') nextDirection = 'left';
            break;
        case 'ArrowRight':
            if (direction !== 'left') nextDirection = 'right';
            break;
    }
});

// Eventos de los botones
resetButton.addEventListener('click', initGame);
restartButton.addEventListener('click', () => {
    gameOverScreen.style.display = "none";
    initGame();
});
startButton.addEventListener('click', () => {
    startScreen.style.display = "none";
    initGame();
});
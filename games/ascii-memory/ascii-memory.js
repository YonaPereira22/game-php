document.addEventListener('DOMContentLoaded', function() {
    const startScreen = document.getElementById('startScreen');
    const gameScreen = document.getElementById('gameScreen');
    const gameBoard = document.getElementById('gameBoard');
    const gameComplete = document.getElementById('gameComplete');
    const player1NameInput = document.getElementById('player1Name');
    const player2NameInput = document.getElementById('player2Name');
    const player1NameDisplay = document.getElementById('player1NameDisplay');
    const player2NameDisplay = document.getElementById('player2NameDisplay');
    const player1ScoreValue = document.getElementById('player1ScoreValue');
    const player2ScoreValue = document.getElementById('player2ScoreValue');
    const player1Score = document.getElementById('player1Score');
    const player2Score = document.getElementById('player2Score');
    const currentPlayerDisplay = document.getElementById('currentPlayerDisplay');
    const finalPlayer1Name = document.getElementById('finalPlayer1Name');
    const finalPlayer2Name = document.getElementById('finalPlayer2Name');
    const finalPlayer1Score = document.getElementById('finalPlayer1Score');
    const finalPlayer2Score = document.getElementById('finalPlayer2Score');
    const winnerText = document.getElementById('winnerText');
    const movesText = document.getElementById('movesText');
    const backToMenuBtn = document.getElementById('backToMenuBtn');
    const playAgainBtn = document.getElementById('playAgainBtn');
    const difficultyButtons = document.querySelectorAll('[data-difficulty]');

    let cards = [];
    let flippedCards = [];
    let player1Pairs = [];
    let player2Pairs = [];
    let currentPlayer = 1;
    let moves = 0;
    let difficulty = 'normal';
    let lockBoard = false;

    difficultyButtons.forEach(button => {
        button.addEventListener('click', function() {
            difficulty = this.dataset.difficulty;
            startGame();
        });
    });

    backToMenuBtn.addEventListener('click', showStartScreen);
    playAgainBtn.addEventListener('click', showStartScreen);

    function startGame() {
        flippedCards = [];
        player1Pairs = [];
        player2Pairs = [];
        currentPlayer = 1;
        moves = 0;
        lockBoard = false;

        const player1Name = player1NameInput.value || 'Jugador 1';
        const player2Name = player2NameInput.value || 'Jugador 2';
        player1NameDisplay.textContent = player1Name;
        player2NameDisplay.textContent = player2Name;
        finalPlayer1Name.textContent = player1Name;
        finalPlayer2Name.textContent = player2Name;

        player1Score.classList.add('active');
        player2Score.classList.remove('active');
        currentPlayerDisplay.textContent = player1Name;
        currentPlayerDisplay.className = 'current-player player1-turn';
        player1ScoreValue.textContent = '0';
        player2ScoreValue.textContent = '0';

        cards = generateCards(difficulty);

        startScreen.style.display = 'none';
        gameScreen.style.display = 'block';
        gameComplete.style.display = 'none';

        renderGameBoard();
    }

    function generateCards(level) {
        let asciiPairs = [];
        let pairsToSelect;

        if (level === 'easy') {
            for (let i = 65; i <= 90; i++) {
                asciiPairs.push({ value: i, type: 'code' });
                asciiPairs.push({ value: i, type: 'char' });
            }
            pairsToSelect = 6;
        } else if (level === 'normal') {
            for (let i = 65; i <= 90; i++) {
                asciiPairs.push({ value: i, type: 'code' });
                asciiPairs.push({ value: i, type: 'char' });
            }
            for (let i = 97; i <= 122; i++) {
                asciiPairs.push({ value: i, type: 'code' });
                asciiPairs.push({ value: i, type: 'char' });
            }
            pairsToSelect = 8;
        } else {
            for (let i = 33; i <= 126; i++) {
                asciiPairs.push({ value: i, type: 'code' });
                asciiPairs.push({ value: i, type: 'char' });
            }
            pairsToSelect = 10;
        }

        const uniqueValues = [...new Set(asciiPairs.map(card => card.value))];
        shuffleArray(uniqueValues);
        const selectedValues = uniqueValues.slice(0, pairsToSelect);

        const selectedPairs = [];
        selectedValues.forEach(value => {
            selectedPairs.push({ value, type: 'code' });
            selectedPairs.push({ value, type: 'char' });
        });

        shuffleArray(selectedPairs);

        return selectedPairs.map((card, index) => ({
            ...card,
            id: index,
            isFlipped: false,
            isMatched: false,
            matchedBy: null
        }));
    }

    function renderGameBoard() {
        gameBoard.innerHTML = '';

        cards.forEach(card => {
            const cardElement = document.createElement('div');
            cardElement.className = 'card';
            cardElement.dataset.id = card.id;

            const cardContent = document.createElement('div');
            cardContent.className = 'card-content';

            if (card.isMatched) {
                cardElement.className = `card ${card.matchedBy === 1 ? 'card-matched-player1' : 'card-matched-player2'}`;
                cardContent.textContent = card.type === 'code' ? card.value : String.fromCharCode(card.value);
            } else if (card.isFlipped) {
                cardElement.className = `card ${card.type === 'code' ? 'card-front-code' : 'card-front-char'}`;
                cardContent.textContent = card.type === 'code' ? card.value : String.fromCharCode(card.value);
            } else {
                cardContent.className = 'card-back';
                cardContent.textContent = '?';
            }

            cardElement.appendChild(cardContent);
            cardElement.addEventListener('click', () => flipCard(card.id));
            gameBoard.appendChild(cardElement);
        });
    }

    function flipCard(id) {
        if (lockBoard) return;

        const cardIndex = cards.findIndex(card => card.id === id);
        const card = cards[cardIndex];

        if (card.isFlipped || card.isMatched) return;
        if (flippedCards.length === 2) return;

        card.isFlipped = true;
        flippedCards.push(card);
        renderGameBoard();

        if (flippedCards.length === 2) {
            lockBoard = true;
            moves++;

            const [first, second] = flippedCards;

            if (first.value === second.value && first.type !== second.type) {
                setTimeout(() => {
                    const firstIndex = cards.findIndex(c => c.id === first.id);
                    const secondIndex = cards.findIndex(c => c.id === second.id);

                    cards[firstIndex].isMatched = true;
                    cards[secondIndex].isMatched = true;
                    cards[firstIndex].matchedBy = currentPlayer;
                    cards[secondIndex].matchedBy = currentPlayer;

                    if (currentPlayer === 1) {
                        player1Pairs.push(first.value);
                        player1ScoreValue.textContent = player1Pairs.length;
                    } else {
                        player2Pairs.push(first.value);
                        player2ScoreValue.textContent = player2Pairs.length;
                    }

                    flippedCards = [];
                    renderGameBoard();

                    const totalPairsFound = player1Pairs.length + player2Pairs.length;
                    if (totalPairsFound === cards.length / 2) {
                        showGameComplete();
                    } else {
                        lockBoard = false;
                    }
                }, 500);
            } else {
                setTimeout(() => {
                    cards.forEach(c => {
                        if (c.id === first.id || c.id === second.id) {
                            c.isFlipped = false;
                        }
                    });

                    flippedCards = [];
                    switchPlayer();
                    renderGameBoard();
                    lockBoard = false;
                }, 1000);
            }
        }
    }

    function switchPlayer() {
        currentPlayer = currentPlayer === 1 ? 2 : 1;

        if (currentPlayer === 1) {
            player1Score.classList.add('active');
            player2Score.classList.remove('active');
            currentPlayerDisplay.textContent = player1NameDisplay.textContent;
            currentPlayerDisplay.className = 'current-player player1-turn';
        } else {
            player1Score.classList.remove('active');
            player2Score.classList.add('active');
            currentPlayerDisplay.textContent = player2NameDisplay.textContent;
            currentPlayerDisplay.className = 'current-player player2-turn';
        }
    }

    function showGameComplete() {
        gameComplete.style.display = 'block';

        finalPlayer1Score.textContent = player1Pairs.length;
        finalPlayer2Score.textContent = player2Pairs.length;

        if (player1Pairs.length > player2Pairs.length) {
            winnerText.textContent = `¡${player1NameDisplay.textContent} ha ganado!`;
        } else if (player2Pairs.length > player1Pairs.length) {
            winnerText.textContent = `¡${player2NameDisplay.textContent} ha ganado!`;
        } else {
            winnerText.textContent = '¡Empate!';
        }

        movesText.textContent = `Total de movimientos: ${moves}`;
    }

    function showStartScreen() {
        startScreen.style.display = 'flex';
        gameScreen.style.display = 'none';
    }

    function shuffleArray(array) {
        for (let i = array.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [array[i], array[j]] = [array[j], array[i]];
        }
        return array;
    }
});

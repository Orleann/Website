<div class="modern-card">
    <h3><i class="fas fa-gamepad"></i> Snake</h3>
    <p>Steruj strzałkami na klawiaturze. Zbieraj jedzenie i rosnij! Unikaj zderzeń ze ścianami i własnym ciałem.</p>
    
    <div style="text-align: center; margin: 20px 0;">
        <div style="display: inline-block; background: rgba(102, 126, 234, 0.1); padding: 15px 25px; border-radius: 10px; margin: 10px;">
            <strong>Punkty:</strong> <span id="score">0</span>
        </div>
        <div style="display: inline-block; background: rgba(102, 126, 234, 0.1); padding: 15px 25px; border-radius: 10px; margin: 10px;">
            <strong>Rekord:</strong> <span id="highScore">0</span>
        </div>
        <button id="startBtn" class="btn-modern" style="margin: 10px;">
            <i class="fas fa-play"></i> Start
        </button>
        <button id="resetBtn" class="btn-modern" style="margin: 10px;">
            <i class="fas fa-redo"></i> Reset
        </button>
    </div>
    
    <div style="text-align: center; margin: 20px 0;">
        <canvas id="gameCanvas" style="border: 3px solid rgba(102, 126, 234, 0.3); border-radius: 10px; background: #1a1a2e; max-width: 100%;"></canvas>
    </div>
    
    <div id="gameOver" style="display: none; text-align: center; padding: 20px; background: rgba(102, 126, 234, 0.1); border-radius: 10px; margin-top: 20px;">
        <h3 id="gameOverText"></h3>
        <p id="finalScore"></p>
        <button class="btn-modern" onclick="resetGame()">
            <i class="fas fa-redo"></i> Zagraj Ponownie
        </button>
    </div>
</div>

<style>
    #gameCanvas {
        display: block;
        margin: 0 auto;
        box-shadow: 0 8px 32px rgba(0,0,0,0.3);
    }
</style>

<script>
// Konfiguracja gry
const GRID_SIZE = 20;
const CANVAS_WIDTH = 600;
const CANVAS_HEIGHT = 600;
const GRID_COLS = CANVAS_WIDTH / GRID_SIZE;
const GRID_ROWS = CANVAS_HEIGHT / GRID_SIZE;

// Stan gry
let gameState = {
    score: 0,
    highScore: 0,
    gameRunning: false,
    gameOver: false
};

// Wąż
let snake = [
    {x: 10, y: 10}
];

// Kierunek węża (0=stop, 1=up, 2=right, 3=down, 4=left)
let direction = 2; // Start w prawo
let nextDirection = 2;

// Jedzenie
let food = {x: 15, y: 15};

let canvas, ctx;
let animationFrame;
let lastUpdateTime = 0;
let gameSpeed = 150; // ms między ruchami

// Audio Context
let audioContext;

function initAudio() {
    try {
        audioContext = new (window.AudioContext || window.webkitAudioContext)();
    } catch (e) {
        console.log('Web Audio API nie jest wspierane');
    }
}

function playSound(frequency, duration, type = 'sine') {
    if (!audioContext) return;
    
    const oscillator = audioContext.createOscillator();
    const gainNode = audioContext.createGain();
    
    oscillator.connect(gainNode);
    gainNode.connect(audioContext.destination);
    
    oscillator.frequency.value = frequency;
    oscillator.type = type;
    gainNode.gain.setValueAtTime(0.2, audioContext.currentTime);
    gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + duration);
    oscillator.start(audioContext.currentTime);
    oscillator.stop(audioContext.currentTime + duration);
}

// Inicjalizacja
window.addEventListener('DOMContentLoaded', function() {
    canvas = document.getElementById('gameCanvas');
    ctx = canvas.getContext('2d');
    canvas.width = CANVAS_WIDTH;
    canvas.height = CANVAS_HEIGHT;
    
    // Inicjalizuj audio
    initAudio();
    
    // Wczytaj rekord z localStorage
    const savedHighScore = localStorage.getItem('snakeHighScore');
    if (savedHighScore) {
        gameState.highScore = parseInt(savedHighScore);
        document.getElementById('highScore').textContent = gameState.highScore;
    }
    
    // Rysuj początkowy stan
    draw();
    
    // Event listeners
    document.getElementById('startBtn').addEventListener('click', startGame);
    document.getElementById('resetBtn').addEventListener('click', resetGame);
    document.addEventListener('keydown', handleKeyPress);
    
    // Aktywuj audio context przy pierwszym interakcji
    document.addEventListener('click', function() {
        if (audioContext && audioContext.state === 'suspended') {
            audioContext.resume();
        }
    }, { once: true });
});

function startGame() {
    if (!gameState.gameRunning && !gameState.gameOver) {
        gameState.gameRunning = true;
        gameState.gameOver = false;
        lastUpdateTime = Date.now();
        document.getElementById('gameOver').style.display = 'none';
        gameLoop();
    }
}

function resetGame() {
    gameState.score = 0;
    gameState.gameRunning = false;
    gameState.gameOver = false;
    
    // Reset węża
    snake = [{x: 10, y: 10}];
    direction = 2;
    nextDirection = 2;
    
    // Losowe jedzenie
    generateFood();
    
    // Reset UI
    updateUI();
    document.getElementById('gameOver').style.display = 'none';
    draw();
}

function handleKeyPress(e) {
    if (!gameState.gameRunning) return;
    
    // Zapobiegaj odwróceniu węża w przeciwnym kierunku
    switch(e.key) {
        case 'ArrowUp':
            if (direction !== 3) nextDirection = 1;
            break;
        case 'ArrowRight':
            if (direction !== 4) nextDirection = 2;
            break;
        case 'ArrowDown':
            if (direction !== 1) nextDirection = 3;
            break;
        case 'ArrowLeft':
            if (direction !== 2) nextDirection = 4;
            break;
    }
}

function generateFood() {
    let newFood;
    let onSnake;
    
    do {
        newFood = {
            x: Math.floor(Math.random() * GRID_COLS),
            y: Math.floor(Math.random() * GRID_ROWS)
        };
        
        // Sprawdź czy jedzenie nie jest na wężu
        onSnake = snake.some(segment => segment.x === newFood.x && segment.y === newFood.y);
    } while (onSnake);
    
    food = newFood;
}

function moveSnake() {
    direction = nextDirection;
    
    // Oblicz nową głowę
    const head = {...snake[0]};
    
    switch(direction) {
        case 1: // up
            head.y--;
            break;
        case 2: // right
            head.x++;
            break;
        case 3: // down
            head.y++;
            break;
        case 4: // left
            head.x--;
            break;
    }
    
    // Sprawdź kolizje ze ścianami
    if (head.x < 0 || head.x >= GRID_COLS || head.y < 0 || head.y >= GRID_ROWS) {
        endGame('Zderzenie ze ścianą!');
        return;
    }
    
    // Sprawdź kolizję z własnym ciałem
    if (snake.some(segment => segment.x === head.x && segment.y === head.y)) {
        endGame('Zderzenie z własnym ciałem!');
        return;
    }
    
    // Dodaj nową głowę
    snake.unshift(head);
    
    // Sprawdź czy zjedzono jedzenie
    if (head.x === food.x && head.y === food.y) {
        gameState.score += 10;
        playSound(600, 0.1, 'sine'); // Dźwięk zjedzenia
        generateFood();
        
        // Zwiększ prędkość co 50 punktów
        if (gameState.score % 50 === 0 && gameSpeed > 80) {
            gameSpeed -= 5;
        }
    } else {
        // Usuń ogon jeśli nie zjedzono jedzenia
        snake.pop();
    }
}

function endGame(message) {
    gameState.gameRunning = false;
    gameState.gameOver = true;
    
    // Zaktualizuj rekord
    if (gameState.score > gameState.highScore) {
        gameState.highScore = gameState.score;
        localStorage.setItem('snakeHighScore', gameState.highScore);
        document.getElementById('gameOverText').textContent = 'Nowy Rekord! ' + message;
        playSound(800, 0.3, 'sine'); // Dźwięk rekordu
    } else {
        document.getElementById('gameOverText').textContent = message;
        playSound(300, 0.2, 'sine'); // Dźwięk przegranej
    }
    
    document.getElementById('finalScore').textContent = 'Twój wynik: ' + gameState.score;
    document.getElementById('gameOver').style.display = 'block';
    updateUI();
}

function updateUI() {
    document.getElementById('score').textContent = gameState.score;
    document.getElementById('highScore').textContent = gameState.highScore;
}

function draw() {
    // Tło
    ctx.fillStyle = '#1a1a2e';
    ctx.fillRect(0, 0, CANVAS_WIDTH, CANVAS_HEIGHT);
    
    // Siatka (opcjonalna)
    ctx.strokeStyle = 'rgba(102, 126, 234, 0.1)';
    ctx.lineWidth = 1;
    for (let x = 0; x <= GRID_COLS; x++) {
        ctx.beginPath();
        ctx.moveTo(x * GRID_SIZE, 0);
        ctx.lineTo(x * GRID_SIZE, CANVAS_HEIGHT);
        ctx.stroke();
    }
    for (let y = 0; y <= GRID_ROWS; y++) {
        ctx.beginPath();
        ctx.moveTo(0, y * GRID_SIZE);
        ctx.lineTo(CANVAS_WIDTH, y * GRID_SIZE);
        ctx.stroke();
    }
    
    // Jedzenie
    ctx.fillStyle = '#ff6b6b';
    ctx.beginPath();
    ctx.arc(
        food.x * GRID_SIZE + GRID_SIZE / 2,
        food.y * GRID_SIZE + GRID_SIZE / 2,
        GRID_SIZE / 2 - 2,
        0,
        Math.PI * 2
    );
    ctx.fill();
    
    // Wąż
    snake.forEach((segment, index) => {
        const x = segment.x * GRID_SIZE + 2;
        const y = segment.y * GRID_SIZE + 2;
        const size = GRID_SIZE - 4;
        
        if (index === 0) {
            // Głowa - jaśniejszy kolor
            ctx.fillStyle = '#4ecdc4';
        } else {
            // Ciało - gradient koloru
            const brightness = 1 - (index / snake.length) * 0.3;
            ctx.fillStyle = `rgb(${Math.floor(78 * brightness)}, ${Math.floor(205 * brightness)}, ${Math.floor(196 * brightness)})`;
        }
        
        // Rysuj zaokrąglony prostokąt
        ctx.beginPath();
        const radius = 4;
        ctx.moveTo(x + radius, y);
        ctx.lineTo(x + size - radius, y);
        ctx.quadraticCurveTo(x + size, y, x + size, y + radius);
        ctx.lineTo(x + size, y + size - radius);
        ctx.quadraticCurveTo(x + size, y + size, x + size - radius, y + size);
        ctx.lineTo(x + radius, y + size);
        ctx.quadraticCurveTo(x, y + size, x, y + size - radius);
        ctx.lineTo(x, y + radius);
        ctx.quadraticCurveTo(x, y, x + radius, y);
        ctx.closePath();
        ctx.fill();
    });
    
    // Oczy na głowie
    if (snake.length > 0) {
        const head = snake[0];
        ctx.fillStyle = '#fff';
        const eyeSize = 3;
        const eyeOffset = 6;
        
        switch(direction) {
            case 1: // up
                ctx.beginPath();
                ctx.arc(head.x * GRID_SIZE + GRID_SIZE / 2 - eyeOffset, head.y * GRID_SIZE + GRID_SIZE / 2 - 2, eyeSize, 0, Math.PI * 2);
                ctx.arc(head.x * GRID_SIZE + GRID_SIZE / 2 + eyeOffset, head.y * GRID_SIZE + GRID_SIZE / 2 - 2, eyeSize, 0, Math.PI * 2);
                ctx.fill();
                break;
            case 2: // right
                ctx.beginPath();
                ctx.arc(head.x * GRID_SIZE + GRID_SIZE / 2 + 2, head.y * GRID_SIZE + GRID_SIZE / 2 - eyeOffset, eyeSize, 0, Math.PI * 2);
                ctx.arc(head.x * GRID_SIZE + GRID_SIZE / 2 + 2, head.y * GRID_SIZE + GRID_SIZE / 2 + eyeOffset, eyeSize, 0, Math.PI * 2);
                ctx.fill();
                break;
            case 3: // down
                ctx.beginPath();
                ctx.arc(head.x * GRID_SIZE + GRID_SIZE / 2 - eyeOffset, head.y * GRID_SIZE + GRID_SIZE / 2 + 2, eyeSize, 0, Math.PI * 2);
                ctx.arc(head.x * GRID_SIZE + GRID_SIZE / 2 + eyeOffset, head.y * GRID_SIZE + GRID_SIZE / 2 + 2, eyeSize, 0, Math.PI * 2);
                ctx.fill();
                break;
            case 4: // left
                ctx.beginPath();
                ctx.arc(head.x * GRID_SIZE + GRID_SIZE / 2 - 2, head.y * GRID_SIZE + GRID_SIZE / 2 - eyeOffset, eyeSize, 0, Math.PI * 2);
                ctx.arc(head.x * GRID_SIZE + GRID_SIZE / 2 - 2, head.y * GRID_SIZE + GRID_SIZE / 2 + eyeOffset, eyeSize, 0, Math.PI * 2);
                ctx.fill();
                break;
        }
    }
}

function gameLoop() {
    if (!gameState.gameRunning) return;
    
    const currentTime = Date.now();
    
    // Ruch węża w określonych odstępach czasu
    if (currentTime - lastUpdateTime >= gameSpeed) {
        moveSnake();
        lastUpdateTime = currentTime;
    }
    
    // Rysowanie w każdej klatce dla płynności
    draw();
    updateUI();
    
    if (gameState.gameRunning) {
        animationFrame = requestAnimationFrame(gameLoop);
    }
}
</script>

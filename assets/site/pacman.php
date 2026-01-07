<div class="modern-card">
    <h3><i class="fas fa-gamepad"></i> Pac-Man</h3>
    <p>Steruj strzałkami na klawiaturze. Zbierz wszystkie kropki, aby wygrać!</p>
    
    <div style="text-align: center; margin: 20px 0;">
        <div style="display: inline-block; background: rgba(102, 126, 234, 0.1); padding: 15px 25px; border-radius: 10px; margin: 10px;">
            <strong>Punkty:</strong> <span id="score">0</span>
        </div>
        <div style="display: inline-block; background: rgba(102, 126, 234, 0.1); padding: 15px 25px; border-radius: 10px; margin: 10px;">
            <strong>Życia:</strong> <span id="lives">3</span>
        </div>
        <button id="startBtn" class="btn-modern" style="margin: 10px;">
            <i class="fas fa-play"></i> Start
        </button>
        <button id="resetBtn" class="btn-modern" style="margin: 10px;">
            <i class="fas fa-redo"></i> Reset
        </button>
    </div>
    
    <div style="text-align: center; margin: 20px 0;">
        <canvas id="gameCanvas" style="border: 3px solid rgba(102, 126, 234, 0.3); border-radius: 10px; background: #000; max-width: 100%;"></canvas>
    </div>
    
    <div id="gameOver" style="display: none; text-align: center; padding: 20px; background: rgba(102, 126, 234, 0.1); border-radius: 10px; margin-top: 20px;">
        <h3 id="gameOverText"></h3>
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
const TILE_SIZE = 20;
const CANVAS_WIDTH = 600;
const CANVAS_HEIGHT = 600;
const COLS = CANVAS_WIDTH / TILE_SIZE;
const ROWS = CANVAS_HEIGHT / TILE_SIZE;

// Stan gry
let gameState = {
    score: 0,
    lives: 3,
    gameRunning: false,
    gameWon: false,
    gameLost: false
};

// Labirynt początkowy (1 = ściana, 0 = puste, 2 = kropka, 3 = duża kropka)
// Centrum spawnu jest zamknięte na początku
let initialMaze = [
    [1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1],
    [1,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1,1,2,2,2,2,2,2,2,2,2,2,2,2,1],
    [1,2,1,1,1,1,2,1,1,1,1,1,1,1,2,1,1,2,1,1,1,1,1,1,1,2,1,1,2,1],
    [1,3,1,1,1,1,2,1,1,1,1,1,1,1,2,1,1,2,1,1,1,1,1,1,1,2,1,1,3,1],
    [1,2,1,1,1,1,2,1,1,1,1,1,1,1,2,1,1,2,1,1,1,1,1,1,1,2,1,1,2,1],
    [1,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1],
    [1,2,1,1,1,1,2,1,1,2,1,1,1,1,1,1,1,1,1,1,1,1,2,1,1,2,1,1,2,1],
    [1,2,1,1,1,1,2,1,1,2,1,1,1,1,1,1,1,1,1,1,1,1,2,1,1,2,1,1,2,1],
    [1,2,2,2,2,2,2,1,1,2,2,2,2,1,1,1,1,1,1,2,2,2,2,1,1,2,2,2,2,1],
    [1,1,1,1,1,1,2,1,1,1,1,1,0,1,1,1,1,1,1,0,1,1,1,1,1,2,1,1,1,1],
    [0,0,0,0,0,1,2,1,1,1,1,1,0,1,1,1,1,1,1,0,1,1,1,1,1,2,1,0,0,0],
    [0,0,0,0,0,1,2,1,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,1,2,1,0,0,0],
    [0,0,0,0,0,1,2,1,1,0,1,1,1,1,1,1,1,1,1,1,1,1,0,1,1,2,1,0,0,0],
    [1,1,1,1,1,1,2,1,1,0,1,1,1,1,1,1,1,1,1,1,1,1,0,1,1,2,1,1,1,1],
    [0,0,0,0,0,0,2,0,0,0,1,1,1,1,1,1,1,1,1,1,1,1,0,0,0,2,0,0,0,0],
    [1,1,1,1,1,1,2,1,1,0,1,0,0,0,0,0,0,0,0,0,0,1,0,1,1,2,1,1,1,1],
    [0,0,0,0,0,1,2,1,1,0,1,1,1,1,1,1,1,1,1,1,1,1,0,1,1,2,1,0,0,0],
    [0,0,0,0,0,1,2,1,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,1,2,1,0,0,0],
    [0,0,0,0,0,1,2,1,1,0,1,1,1,1,1,1,1,1,1,1,1,1,0,1,1,2,1,0,0,0],
    [1,1,1,1,1,1,2,1,1,0,1,1,1,1,1,1,1,1,1,1,1,1,0,1,1,2,1,1,1,1],
    [1,2,2,2,2,2,2,2,2,2,2,2,2,1,1,1,1,1,1,2,2,2,2,2,2,2,2,2,2,1],
    [1,2,1,1,1,1,2,1,1,1,1,1,2,1,1,1,1,1,1,2,1,1,1,1,1,2,1,1,2,1],
    [1,2,1,1,1,1,2,1,1,1,1,1,2,1,1,1,1,1,1,2,1,1,1,1,1,2,1,1,2,1],
    [1,3,2,2,1,1,2,2,2,2,2,2,2,0,0,0,0,0,0,2,2,2,2,2,2,2,1,1,3,1],
    [1,1,1,2,1,1,2,1,1,2,1,1,1,1,1,1,1,1,1,1,1,1,2,1,1,2,1,1,1,1],
    [1,1,1,2,1,1,2,1,1,2,1,1,1,1,1,1,1,1,1,1,1,1,2,1,1,2,1,1,1,1],
    [1,2,2,2,2,2,2,1,1,2,2,2,2,1,1,1,1,1,1,2,2,2,2,1,1,2,2,2,2,1],
    [1,2,1,1,1,1,1,1,1,1,1,1,2,1,1,1,1,1,1,2,1,1,1,1,1,1,1,1,2,1],
    [1,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1],
    [1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1]
];

// Labirynt aktywny (będzie modyfikowany)
let maze = [];

// Pozycja Pac-Mana (z interpolacją dla płynności)
let pacman = {
    x: 14,
    y: 23,
    pixelX: 14 * TILE_SIZE,
    pixelY: 23 * TILE_SIZE,
    direction: 0, // 0=stop, 1=up, 2=right, 3=down, 4=left
    nextDirection: 0,
    mouthOpen: true,
    mouthAngle: 0,
    moveProgress: 0 // 0-1, postęp ruchu między kafelkami
};

// Duchy - wszystkie w centrum spawnu (z interpolacją)
let ghosts = [
    {x: 14, y: 14, pixelX: 14 * TILE_SIZE, pixelY: 14 * TILE_SIZE, direction: 2, color: '#ff0000', name: 'Blinky', moveProgress: 0},
    {x: 13, y: 14, pixelX: 13 * TILE_SIZE, pixelY: 14 * TILE_SIZE, direction: 2, color: '#ffb8ff', name: 'Pinky', moveProgress: 0},
    {x: 15, y: 14, pixelX: 15 * TILE_SIZE, pixelY: 14 * TILE_SIZE, direction: 4, color: '#00ffff', name: 'Inky', moveProgress: 0},
    {x: 14, y: 15, pixelX: 14 * TILE_SIZE, pixelY: 15 * TILE_SIZE, direction: 1, color: '#ffb851', name: 'Clyde', moveProgress: 0}
];

let canvas, ctx;
let animationFrame;
let dotsRemaining = 0;
let frameCount = 0;
let audioContext;

// Inicjalizacja Audio Context
function initAudio() {
    try {
        audioContext = new (window.AudioContext || window.webkitAudioContext)();
    } catch (e) {
        console.log('Web Audio API nie jest wspierane');
    }
}

// Funkcja do odtwarzania dźwięku zbierania punktów
function playDotSound(type = 'small') {
    if (!audioContext) return;
    
    const oscillator = audioContext.createOscillator();
    const gainNode = audioContext.createGain();
    
    oscillator.connect(gainNode);
    gainNode.connect(audioContext.destination);
    
    if (type === 'small') {
        // Dźwięk dla małej kropki - krótki, wysoki
        oscillator.frequency.value = 800;
        oscillator.type = 'sine';
        gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.1);
        oscillator.start(audioContext.currentTime);
        oscillator.stop(audioContext.currentTime + 0.1);
    } else if (type === 'large') {
        // Dźwięk dla dużej kropki - dłuższy, niższy
        oscillator.frequency.value = 400;
        oscillator.type = 'sine';
        gainNode.gain.setValueAtTime(0.4, audioContext.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.3);
        oscillator.start(audioContext.currentTime);
        oscillator.stop(audioContext.currentTime + 0.3);
    }
}

// Inicjalizacja
window.addEventListener('DOMContentLoaded', function() {
    canvas = document.getElementById('gameCanvas');
    ctx = canvas.getContext('2d');
    canvas.width = CANVAS_WIDTH;
    canvas.height = CANVAS_HEIGHT;
    
    // Inicjalizuj audio
    initAudio();
    
    // Skopiuj początkowy labirynt
    maze = initialMaze.map(row => [...row]);
    
    // Licz kropki
    countDots();
    
    // Rysuj początkowy stan
    draw();
    
    // Event listeners
    document.getElementById('startBtn').addEventListener('click', startGame);
    document.getElementById('resetBtn').addEventListener('click', resetGame);
    document.addEventListener('keydown', handleKeyPress);
    
    // Aktywuj audio context przy pierwszym interakcji użytkownika
    document.addEventListener('click', function() {
        if (audioContext && audioContext.state === 'suspended') {
            audioContext.resume();
        }
    }, { once: true });
});

function countDots() {
    dotsRemaining = 0;
    for (let y = 0; y < ROWS; y++) {
        for (let x = 0; x < COLS; x++) {
            if (maze[y][x] === 2 || maze[y][x] === 3) {
                dotsRemaining++;
            }
        }
    }
}

function openSpawnGate() {
    // Otwórz przejście w centrum spawnu (linia 14, kolumny 11-18)
    // Zmień ściany (1) na puste (0) w przejściu
    for (let x = 11; x <= 18; x++) {
        maze[14][x] = 0;
    }
    // Otwórz też górną część przejścia (linia 13)
    for (let x = 11; x <= 18; x++) {
        if (maze[13][x] === 1) {
            maze[13][x] = 0;
        }
    }
}

function startGame() {
    if (!gameState.gameRunning && !gameState.gameWon && !gameState.gameLost) {
        // Otwórz przejście z respa
        openSpawnGate();
        gameState.gameRunning = true;
        gameLoop();
    }
}

function resetGame() {
    gameState.score = 0;
    gameState.lives = 3;
    gameState.gameRunning = false;
    gameState.gameWon = false;
    gameState.gameLost = false;
    
    // Reset labiryntu do początkowego stanu (zamknięty spawn)
    maze = initialMaze.map(row => [...row]);
    
    // Reset pozycji
    pacman.x = 14;
    pacman.y = 23;
    pacman.pixelX = 14 * TILE_SIZE;
    pacman.pixelY = 23 * TILE_SIZE;
    pacman.direction = 0;
    pacman.nextDirection = 0;
    pacman.moveProgress = 0;
    
    ghosts = [
        {x: 14, y: 14, pixelX: 14 * TILE_SIZE, pixelY: 14 * TILE_SIZE, direction: 2, color: '#ff0000', name: 'Blinky', moveProgress: 0},
        {x: 13, y: 14, pixelX: 13 * TILE_SIZE, pixelY: 14 * TILE_SIZE, direction: 2, color: '#ffb8ff', name: 'Pinky', moveProgress: 0},
        {x: 15, y: 14, pixelX: 15 * TILE_SIZE, pixelY: 14 * TILE_SIZE, direction: 4, color: '#00ffff', name: 'Inky', moveProgress: 0},
        {x: 14, y: 15, pixelX: 14 * TILE_SIZE, pixelY: 15 * TILE_SIZE, direction: 1, color: '#ffb851', name: 'Clyde', moveProgress: 0}
    ];
    
    frameCount = 0;
    
    // Reset labiryntu
    maze = [
        [1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1],
        [1,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1,1,2,2,2,2,2,2,2,2,2,2,2,2,1],
        [1,2,1,1,1,1,2,1,1,1,1,1,1,1,2,1,1,2,1,1,1,1,1,1,1,2,1,1,2,1],
        [1,3,1,1,1,1,2,1,1,1,1,1,1,1,2,1,1,2,1,1,1,1,1,1,1,2,1,1,3,1],
        [1,2,1,1,1,1,2,1,1,1,1,1,1,1,2,1,1,2,1,1,1,1,1,1,1,2,1,1,2,1],
        [1,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1],
        [1,2,1,1,1,1,2,1,1,2,1,1,1,1,1,1,1,1,1,1,1,1,2,1,1,2,1,1,2,1],
        [1,2,1,1,1,1,2,1,1,2,1,1,1,1,1,1,1,1,1,1,1,1,2,1,1,2,1,1,2,1],
        [1,2,2,2,2,2,2,1,1,2,2,2,2,1,1,1,1,1,1,2,2,2,2,1,1,2,2,2,2,1],
        [1,1,1,1,1,1,2,1,1,1,1,1,0,1,1,1,1,1,1,0,1,1,1,1,1,2,1,1,1,1],
        [0,0,0,0,0,1,2,1,1,1,1,1,0,1,1,1,1,1,1,0,1,1,1,1,1,2,1,0,0,0],
        [0,0,0,0,0,1,2,1,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,1,2,1,0,0,0],
        [0,0,0,0,0,1,2,1,1,0,1,1,1,1,1,1,1,1,1,1,1,1,0,1,1,2,1,0,0,0],
        [1,1,1,1,1,1,2,1,1,0,1,0,0,0,0,0,0,0,0,0,0,1,0,1,1,2,1,1,1,1],
        [0,0,0,0,0,0,2,0,0,0,1,0,0,0,0,0,0,0,0,0,0,1,0,0,0,2,0,0,0,0],
        [1,1,1,1,1,1,2,1,1,0,1,0,0,0,0,0,0,0,0,0,0,1,0,1,1,2,1,1,1,1],
        [0,0,0,0,0,1,2,1,1,0,1,1,1,1,1,1,1,1,1,1,1,1,0,1,1,2,1,0,0,0],
        [0,0,0,0,0,1,2,1,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,1,2,1,0,0,0],
        [0,0,0,0,0,1,2,1,1,0,1,1,1,1,1,1,1,1,1,1,1,1,0,1,1,2,1,0,0,0],
        [1,1,1,1,1,1,2,1,1,0,1,1,1,1,1,1,1,1,1,1,1,1,0,1,1,2,1,1,1,1],
        [1,2,2,2,2,2,2,2,2,2,2,2,2,1,1,1,1,1,1,2,2,2,2,2,2,2,2,2,2,1],
        [1,2,1,1,1,1,2,1,1,1,1,1,2,1,1,1,1,1,1,2,1,1,1,1,1,2,1,1,2,1],
        [1,2,1,1,1,1,2,1,1,1,1,1,2,1,1,1,1,1,1,2,1,1,1,1,1,2,1,1,2,1],
        [1,3,2,2,1,1,2,2,2,2,2,2,2,0,0,0,0,0,0,2,2,2,2,2,2,2,1,1,3,1],
        [1,1,1,2,1,1,2,1,1,2,1,1,1,1,1,1,1,1,1,1,1,1,2,1,1,2,1,1,1,1],
        [1,1,1,2,1,1,2,1,1,2,1,1,1,1,1,1,1,1,1,1,1,1,2,1,1,2,1,1,1,1],
        [1,2,2,2,2,2,2,1,1,2,2,2,2,1,1,1,1,1,1,2,2,2,2,1,1,2,2,2,2,1],
        [1,2,1,1,1,1,1,1,1,1,1,1,2,1,1,1,1,1,1,2,1,1,1,1,1,1,1,1,2,1],
        [1,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1],
        [1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1]
    ];
    
    countDots();
    updateUI();
    document.getElementById('gameOver').style.display = 'none';
    draw();
}

function handleKeyPress(e) {
    if (!gameState.gameRunning) return;
    
    switch(e.key) {
        case 'ArrowUp':
            pacman.nextDirection = 1;
            break;
        case 'ArrowRight':
            pacman.nextDirection = 2;
            break;
        case 'ArrowDown':
            pacman.nextDirection = 3;
            break;
        case 'ArrowLeft':
            pacman.nextDirection = 4;
            break;
    }
}

function canMove(x, y, direction) {
    let newX = x;
    let newY = y;
    
    switch(direction) {
        case 1: newY--; break; // up
        case 2: newX++; break; // right
        case 3: newY++; break; // down
        case 4: newX--; break; // left
    }
    
    // Teleportacja przez boki
    if (newX < 0) newX = COLS - 1;
    if (newX >= COLS) newX = 0;
    
    if (newY < 0 || newY >= ROWS) return false;
    if (maze[newY][newX] === 1) return false;
    
    return true;
}

function movePacman() {
    // Spróbuj zmienić kierunek
    if (pacman.nextDirection !== 0 && canMove(pacman.x, pacman.y, pacman.nextDirection)) {
        pacman.direction = pacman.nextDirection;
    }
    
    // Jeśli jest w trakcie ruchu, kontynuuj animację
    if (pacman.moveProgress > 0 && pacman.moveProgress < 1) {
        pacman.moveProgress += 0.25; // Szybkość animacji
        if (pacman.moveProgress >= 1) {
            pacman.moveProgress = 0;
            // Zbierz kropkę gdy dotrze do środka kafelka
            if (maze[pacman.y][pacman.x] === 2) {
                maze[pacman.y][pacman.x] = 0;
                gameState.score += 10;
                dotsRemaining--;
                playDotSound('small'); // Dźwięk małej kropki
            } else if (maze[pacman.y][pacman.x] === 3) {
                maze[pacman.y][pacman.x] = 0;
                gameState.score += 50;
                dotsRemaining--;
                playDotSound('large'); // Dźwięk dużej kropki
            }
        } else {
            // Aktualizuj pozycję pikselową dla interpolacji
            updatePacmanPixelPosition();
        }
        return;
    }
    
    // Ruch - tylko gdy nie jest w trakcie animacji
    if (pacman.direction !== 0 && canMove(pacman.x, pacman.y, pacman.direction)) {
        let newX = pacman.x;
        let newY = pacman.y;
        
        switch(pacman.direction) {
            case 1: newY--; break;
            case 2: newX++; break;
            case 3: newY++; break;
            case 4: newX--; break;
        }
        
        // Teleportacja
        if (newX < 0) newX = COLS - 1;
        if (newX >= COLS) newX = 0;
        
        // Rozpocznij ruch - zachowaj starą pozycję dla interpolacji
        pacman.x = newX;
        pacman.y = newY;
        pacman.moveProgress = 0.1; // Rozpocznij animację
        updatePacmanPixelPosition();
        
        // Animacja ust
        pacman.mouthAngle += 0.3;
        pacman.mouthOpen = Math.sin(pacman.mouthAngle) > 0;
    }
}

function updatePacmanPixelPosition() {
    if (pacman.direction === 0 || pacman.moveProgress === 0) {
        // Bez ruchu - pozycja dokładnie na kafelku
        pacman.pixelX = pacman.x * TILE_SIZE;
        pacman.pixelY = pacman.y * TILE_SIZE;
        return;
    }
    
    // Oblicz poprzednią pozycję (skąd startujemy)
    let prevX = pacman.x;
    let prevY = pacman.y;
    
    switch(pacman.direction) {
        case 1: prevY++; break; // up - wracamy w dół
        case 2: prevX--; break; // right - wracamy w lewo
        case 3: prevY--; break; // down - wracamy w górę
        case 4: prevX++; break; // left - wracamy w prawo
    }
    
    // Teleportacja dla poprzedniej pozycji
    if (prevX < 0) prevX = COLS - 1;
    if (prevX >= COLS) prevX = 0;
    
    // Interpoluj między poprzednią a obecną pozycją
    let startX = prevX * TILE_SIZE;
    let startY = prevY * TILE_SIZE;
    let targetX = pacman.x * TILE_SIZE;
    let targetY = pacman.y * TILE_SIZE;
    
    pacman.pixelX = startX + (targetX - startX) * pacman.moveProgress;
    pacman.pixelY = startY + (targetY - startY) * pacman.moveProgress;
}

function moveGhost(ghost) {
    // Jeśli jest w trakcie ruchu, kontynuuj animację
    if (ghost.moveProgress > 0 && ghost.moveProgress < 1) {
        ghost.moveProgress += 0.15; // Szybkość animacji (trochę wolniej niż Pac-Man)
        if (ghost.moveProgress >= 1) {
            ghost.moveProgress = 0;
        } else {
            updateGhostPixelPosition(ghost);
            return;
        }
    }
    
    // Prosta AI - losowy kierunek jeśli zablokowany
    if (Math.random() < 0.3 || !canMove(ghost.x, ghost.y, ghost.direction)) {
        const directions = [1, 2, 3, 4];
        const possible = directions.filter(d => canMove(ghost.x, ghost.y, d));
        if (possible.length > 0) {
            ghost.direction = possible[Math.floor(Math.random() * possible.length)];
        }
    }
    
    if (canMove(ghost.x, ghost.y, ghost.direction)) {
        let newX = ghost.x;
        let newY = ghost.y;
        
        switch(ghost.direction) {
            case 1: newY--; break;
            case 2: newX++; break;
            case 3: newY++; break;
            case 4: newX--; break;
        }
        
        // Teleportacja
        if (newX < 0) newX = COLS - 1;
        if (newX >= COLS) newX = 0;
        
        ghost.x = newX;
        ghost.y = newY;
        ghost.moveProgress = 0.05;
        updateGhostPixelPosition(ghost);
    }
}

function updateGhostPixelPosition(ghost) {
    if (ghost.direction === 0 || ghost.moveProgress === 0) {
        // Bez ruchu - pozycja dokładnie na kafelku
        ghost.pixelX = ghost.x * TILE_SIZE;
        ghost.pixelY = ghost.y * TILE_SIZE;
        return;
    }
    
    // Oblicz poprzednią pozycję (skąd startujemy)
    let prevX = ghost.x;
    let prevY = ghost.y;
    
    switch(ghost.direction) {
        case 1: prevY++; break; // up - wracamy w dół
        case 2: prevX--; break; // right - wracamy w lewo
        case 3: prevY--; break; // down - wracamy w górę
        case 4: prevX++; break; // left - wracamy w prawo
    }
    
    // Teleportacja dla poprzedniej pozycji
    if (prevX < 0) prevX = COLS - 1;
    if (prevX >= COLS) prevX = 0;
    
    // Interpoluj między poprzednią a obecną pozycją
    let startX = prevX * TILE_SIZE;
    let startY = prevY * TILE_SIZE;
    let targetX = ghost.x * TILE_SIZE;
    let targetY = ghost.y * TILE_SIZE;
    
    ghost.pixelX = startX + (targetX - startX) * ghost.moveProgress;
    ghost.pixelY = startY + (targetY - startY) * ghost.moveProgress;
}

function checkCollisions() {
    for (let ghost of ghosts) {
        // Sprawdź kolizję na podstawie pozycji pikselowych (z tolerancją)
        const distance = Math.sqrt(
            Math.pow(ghost.pixelX - pacman.pixelX, 2) + 
            Math.pow(ghost.pixelY - pacman.pixelY, 2)
        );
        
        if (distance < TILE_SIZE * 0.7) { // Kolizja gdy są blisko siebie
            gameState.lives--;
            if (gameState.lives <= 0) {
                gameState.gameLost = true;
                gameState.gameRunning = false;
                endGame('Przegrałeś! Spróbuj ponownie.');
            } else {
                // Reset pozycji
                pacman.x = 14;
                pacman.y = 23;
                pacman.pixelX = 14 * TILE_SIZE;
                pacman.pixelY = 23 * TILE_SIZE;
                pacman.direction = 0;
                pacman.nextDirection = 0;
                pacman.moveProgress = 0;
            }
        }
    }
    
    if (dotsRemaining === 0) {
        gameState.gameWon = true;
        gameState.gameRunning = false;
        endGame('Wygrałeś! Gratulacje!');
    }
}

function endGame(message) {
    document.getElementById('gameOverText').textContent = message;
    document.getElementById('gameOver').style.display = 'block';
}

function updateUI() {
    document.getElementById('score').textContent = gameState.score;
    document.getElementById('lives').textContent = gameState.lives;
}

function draw() {
    // Tło
    ctx.fillStyle = '#000';
    ctx.fillRect(0, 0, CANVAS_WIDTH, CANVAS_HEIGHT);
    
    // Labirynt
    for (let y = 0; y < ROWS; y++) {
        for (let x = 0; x < COLS; x++) {
            const tile = maze[y][x];
            const px = x * TILE_SIZE;
            const py = y * TILE_SIZE;
            
            if (tile === 1) {
                // Ściana
                ctx.fillStyle = '#2121de';
                ctx.fillRect(px, py, TILE_SIZE, TILE_SIZE);
                ctx.fillStyle = '#0000ff';
                ctx.fillRect(px + 2, py + 2, TILE_SIZE - 4, TILE_SIZE - 4);
            } else if (tile === 2) {
                // Mała kropka
                ctx.fillStyle = '#ffb897';
                ctx.beginPath();
                ctx.arc(px + TILE_SIZE/2, py + TILE_SIZE/2, 2, 0, Math.PI * 2);
                ctx.fill();
            } else if (tile === 3) {
                // Duża kropka
                ctx.fillStyle = '#ffb897';
                ctx.beginPath();
                ctx.arc(px + TILE_SIZE/2, py + TILE_SIZE/2, 5, 0, Math.PI * 2);
                ctx.fill();
            }
        }
    }
    
    // Duchy
    for (let ghost of ghosts) {
        const px = ghost.pixelX;
        const py = ghost.pixelY;
        
        // Ciało
        ctx.fillStyle = ghost.color;
        ctx.beginPath();
        ctx.arc(px + TILE_SIZE/2, py + TILE_SIZE/2, TILE_SIZE/2 - 2, 0, Math.PI * 2);
        ctx.fill();
        
        // Oczy
        ctx.fillStyle = '#fff';
        ctx.beginPath();
        ctx.arc(px + TILE_SIZE/2 - 4, py + TILE_SIZE/2 - 2, 2, 0, Math.PI * 2);
        ctx.arc(px + TILE_SIZE/2 + 4, py + TILE_SIZE/2 - 2, 2, 0, Math.PI * 2);
        ctx.fill();
        
        ctx.fillStyle = '#000';
        ctx.beginPath();
        ctx.arc(px + TILE_SIZE/2 - 4, py + TILE_SIZE/2 - 2, 1, 0, Math.PI * 2);
        ctx.arc(px + TILE_SIZE/2 + 4, py + TILE_SIZE/2 - 2, 1, 0, Math.PI * 2);
        ctx.fill();
    }
    
    // Pac-Man
    const px = pacman.pixelX;
    const py = pacman.pixelY;
    ctx.fillStyle = '#ffff00';
    ctx.beginPath();
    
    let startAngle = 0;
    let endAngle = Math.PI * 2;
    
    if (pacman.mouthOpen) {
        const mouthAngle = Math.PI / 6;
        switch(pacman.direction) {
            case 1: // up
                startAngle = -Math.PI / 2 + mouthAngle;
                endAngle = -Math.PI / 2 - mouthAngle + Math.PI * 2;
                break;
            case 2: // right
                startAngle = mouthAngle;
                endAngle = -mouthAngle + Math.PI * 2;
                break;
            case 3: // down
                startAngle = Math.PI / 2 + mouthAngle;
                endAngle = Math.PI / 2 - mouthAngle;
                break;
            case 4: // left
                startAngle = Math.PI + mouthAngle;
                endAngle = Math.PI - mouthAngle;
                break;
        }
    }
    
    ctx.arc(px + TILE_SIZE/2, py + TILE_SIZE/2, TILE_SIZE/2 - 2, startAngle, endAngle);
    if (pacman.mouthOpen) {
        ctx.lineTo(px + TILE_SIZE/2, py + TILE_SIZE/2);
    }
    ctx.fill();
}

function gameLoop() {
    if (!gameState.gameRunning) return;
    
    frameCount++;
    
    // Ruch Pac-Mana co 4 klatki (szybszy, ale z interpolacją)
    if (frameCount % 4 === 0) {
        movePacman();
    } else {
        // Kontynuuj animację nawet gdy nie ma nowego ruchu
        if (pacman.moveProgress > 0 && pacman.moveProgress < 1) {
            pacman.moveProgress += 0.25;
            updatePacmanPixelPosition();
        }
    }
    
    // Ruch duchów co 6 klatek (wolniej niż Pac-Man)
    if (frameCount % 6 === 0) {
        for (let ghost of ghosts) {
            moveGhost(ghost);
        }
    } else {
        // Kontynuuj animację duchów
        for (let ghost of ghosts) {
            if (ghost.moveProgress > 0 && ghost.moveProgress < 1) {
                ghost.moveProgress += 0.2;
                updateGhostPixelPosition(ghost);
            }
        }
    }
    
    // Sprawdzanie kolizji i aktualizacja UI w każdej klatce
    checkCollisions();
    updateUI();
    
    // Rysowanie w każdej klatce dla płynnej animacji
    draw();
    
    if (gameState.gameRunning) {
        animationFrame = requestAnimationFrame(gameLoop);
    }
}
</script>

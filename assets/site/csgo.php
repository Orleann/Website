<div class="modern-card">
    <h3><i class="fas fa-crosshairs"></i> Counter-Strike Style FPS</h3>
    <p><strong>Sterowanie:</strong> WASD - poruszanie się | Spacja - skok | Myszka - celowanie | Lewy przycisk myszy - strzał</p>
    
    <div style="text-align: center; margin: 20px 0;">
        <div style="display: inline-block; background: rgba(102, 126, 234, 0.1); padding: 15px 25px; border-radius: 10px; margin: 10px;">
            <strong>Punkty:</strong> <span id="score">0</span>
        </div>
        <div style="display: inline-block; background: rgba(102, 126, 234, 0.1); padding: 15px 25px; border-radius: 10px; margin: 10px;">
            <strong>Celne strzały:</strong> <span id="hits">0</span>/<span id="shots">0</span>
        </div>
        <div style="display: inline-block; background: rgba(102, 126, 234, 0.1); padding: 15px 25px; border-radius: 10px; margin: 10px;">
            <strong>Pozostało celów:</strong> <span id="targetsLeft">10</span>
        </div>
        <button id="startBtn" class="btn-modern" style="margin: 10px;">
            <i class="fas fa-play"></i> Start
        </button>
        <button id="resetBtn" class="btn-modern" style="margin: 10px;">
            <i class="fas fa-redo"></i> Reset
        </button>
    </div>
    
    <div style="text-align: center; margin: 20px 0; position: relative;">
        <div id="gameContainer" style="position: relative; display: inline-block;">
            <canvas id="gameCanvas" style="border: 3px solid rgba(102, 126, 234, 0.3); border-radius: 10px; max-width: 100%; display: block;"></canvas>
            <img id="weaponImage" src="assets/img/ak-47-gun-weapon-png-1.png" style="position: absolute; bottom: 0; right: 0; width: 400px; height: auto; pointer-events: none; z-index: 10; transform: translateX(50px); opacity: 0.95; image-rendering: crisp-edges;" alt="Weapon">
            <div id="crosshair" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); pointer-events: none; z-index: 100;">
                <div style="width: 30px; height: 30px; border: 2px solid rgba(255, 255, 255, 0.8); border-radius: 50%; position: relative;">
                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 4px; height: 4px; background: rgba(255, 255, 255, 0.9); border-radius: 50%;"></div>
                </div>
            </div>
        </div>
    </div>
    
    <div id="gameOver" style="display: none; text-align: center; padding: 20px; background: rgba(102, 126, 234, 0.1); border-radius: 10px; margin-top: 20px;">
        <h3 id="gameOverText"></h3>
        <p id="finalStats"></p>
        <button class="btn-modern" onclick="resetGame()">
            <i class="fas fa-redo"></i> Zagraj Ponownie
        </button>
    </div>
</div>

<style>
    #gameContainer {
        box-shadow: 0 8px 32px rgba(0,0,0,0.3);
    }
    
    #crosshair {
        user-select: none;
    }
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script>
// Konfiguracja gry
const CANVAS_WIDTH = 800;
const CANVAS_HEIGHT = 600;

// Stan gry
let gameState = {
    score: 0,
    hits: 0,
    shots: 0,
    targetsLeft: 10,
    gameRunning: false,
    gameOver: false
};

// Three.js zmienne
let scene, camera, renderer;
let targets = [];
let walls = [];
let raycaster, mouse;
let controls = {
    yaw: 0,
    pitch: 0
};

// Model broni (obrazek 2D)
let weaponImage = null;
let weaponRecoil = 0;
let weaponRecoilTarget = 0;
let weaponSway = 0;

// Sterowanie klawiaturą
let keys = {
    w: false,
    a: false,
    s: false,
    d: false,
    space: false
};

const moveSpeed = 0.1;
const jumpHeight = 0.3;
const gravity = 0.01;
let velocityY = 0;
let isOnGround = true;

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
    gainNode.gain.setValueAtTime(0.15, audioContext.currentTime);
    gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + duration);
    oscillator.start(audioContext.currentTime);
    oscillator.stop(audioContext.currentTime + duration);
}

function playGunshot() {
    if (!audioContext) return;
    
    const now = audioContext.currentTime;
    
    // Główny dźwięk strzału - krótki, głośny "bang"
    const mainOsc = audioContext.createOscillator();
    const mainGain = audioContext.createGain();
    
    mainOsc.type = 'sawtooth';
    mainOsc.frequency.setValueAtTime(200, now);
    mainOsc.frequency.exponentialRampToValueAtTime(50, now + 0.05);
    
    mainGain.gain.setValueAtTime(0.4, now);
    mainGain.gain.exponentialRampToValueAtTime(0.01, now + 0.1);
    
    mainOsc.connect(mainGain);
    mainGain.connect(audioContext.destination);
    
    mainOsc.start(now);
    mainOsc.stop(now + 0.1);
    
    // Dodatkowy wysokotonowy "crack" dla realizmu
    const crackOsc = audioContext.createOscillator();
    const crackGain = audioContext.createGain();
    
    crackOsc.type = 'square';
    crackOsc.frequency.setValueAtTime(2000, now);
    crackOsc.frequency.exponentialRampToValueAtTime(500, now + 0.03);
    
    crackGain.gain.setValueAtTime(0.2, now);
    crackGain.gain.exponentialRampToValueAtTime(0.01, now + 0.05);
    
    crackOsc.connect(crackGain);
    crackGain.connect(audioContext.destination);
    
    crackOsc.start(now);
    crackOsc.stop(now + 0.05);
    
    // Niski ton dla "thump"
    const thumpOsc = audioContext.createOscillator();
    const thumpGain = audioContext.createGain();
    
    thumpOsc.type = 'sine';
    thumpOsc.frequency.setValueAtTime(80, now);
    thumpOsc.frequency.exponentialRampToValueAtTime(40, now + 0.08);
    
    thumpGain.gain.setValueAtTime(0.15, now);
    thumpGain.gain.exponentialRampToValueAtTime(0.01, now + 0.12);
    
    thumpOsc.connect(thumpGain);
    thumpGain.connect(audioContext.destination);
    
    thumpOsc.start(now);
    thumpOsc.stop(now + 0.12);
}

// Inicjalizacja
window.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('gameCanvas');
    
    // Inicjalizuj audio
    initAudio();
    
    // Utwórz scenę
    scene = new THREE.Scene();
    scene.background = new THREE.Color(0x87ceeb); // Niebieskie niebo
    scene.fog = new THREE.Fog(0x87ceeb, 10, 50);
    
    // Kamera (perspektywa FPS)
    camera = new THREE.PerspectiveCamera(75, CANVAS_WIDTH / CANVAS_HEIGHT, 0.1, 1000);
    camera.position.set(0, 1.6, 0); // Wysokość oczu gracza
    
    // Renderer
    renderer = new THREE.WebGLRenderer({ canvas: canvas, antialias: true });
    renderer.setSize(CANVAS_WIDTH, CANVAS_HEIGHT);
    renderer.shadowMap.enabled = true;
    renderer.shadowMap.type = THREE.PCFSoftShadowMap;
    
    // Oświetlenie
    const ambientLight = new THREE.AmbientLight(0xffffff, 0.6);
    scene.add(ambientLight);
    
    const directionalLight = new THREE.DirectionalLight(0xffffff, 0.8);
    directionalLight.position.set(10, 10, 5);
    directionalLight.castShadow = true;
    directionalLight.shadow.mapSize.width = 2048;
    directionalLight.shadow.mapSize.height = 2048;
    scene.add(directionalLight);
    
    // Raycaster dla strzelania
    raycaster = new THREE.Raycaster();
    mouse = new THREE.Vector2();
    
    // Utwórz środowisko
    createEnvironment();
    
    // Event listeners
    document.getElementById('startBtn').addEventListener('click', startGame);
    document.getElementById('resetBtn').addEventListener('click', resetGame);
    
    canvas.addEventListener('mousemove', onMouseMove);
    canvas.addEventListener('click', onMouseClick);
    canvas.addEventListener('contextmenu', (e) => e.preventDefault());
    
    // Blokuj kursor w canvasie
    canvas.addEventListener('mousedown', () => {
        if (gameState.gameRunning) {
            canvas.requestPointerLock();
        }
    });
    
    document.addEventListener('pointerlockchange', onPointerLockChange);
    
    // Sterowanie klawiaturą
    document.addEventListener('keydown', onKeyDown);
    document.addEventListener('keyup', onKeyUp);
    
    // Aktywuj audio context
    document.addEventListener('click', function() {
        if (audioContext && audioContext.state === 'suspended') {
            audioContext.resume();
        }
    }, { once: true });
    
    // Renderuj początkowy stan
    animate();
});

function createEnvironment() {
    // Podłoga
    const floorGeometry = new THREE.PlaneGeometry(50, 50);
    const floorMaterial = new THREE.MeshStandardMaterial({ 
        color: 0x4a4a4a,
        roughness: 0.8,
        metalness: 0.2
    });
    const floor = new THREE.Mesh(floorGeometry, floorMaterial);
    floor.rotation.x = -Math.PI / 2;
    floor.receiveShadow = true;
    scene.add(floor);
    
    // Ściany
    const wallMaterial = new THREE.MeshStandardMaterial({ 
        color: 0x8b7355,
        roughness: 0.7,
        metalness: 0.1
    });
    
    // Ściana 1 (przód)
    const wall1 = new THREE.Mesh(
        new THREE.BoxGeometry(20, 5, 0.5),
        wallMaterial
    );
    wall1.position.set(0, 2.5, -10);
    wall1.castShadow = true;
    wall1.receiveShadow = true;
    scene.add(wall1);
    walls.push(wall1);
    
    // Ściana 2 (tył)
    const wall2 = new THREE.Mesh(
        new THREE.BoxGeometry(20, 5, 0.5),
        wallMaterial
    );
    wall2.position.set(0, 2.5, 10);
    wall2.castShadow = true;
    wall2.receiveShadow = true;
    scene.add(wall2);
    walls.push(wall2);
    
    // Ściana 3 (lewo)
    const wall3 = new THREE.Mesh(
        new THREE.BoxGeometry(0.5, 5, 20),
        wallMaterial
    );
    wall3.position.set(-10, 2.5, 0);
    wall3.castShadow = true;
    wall3.receiveShadow = true;
    scene.add(wall3);
    walls.push(wall3);
    
    // Ściana 4 (prawo)
    const wall4 = new THREE.Mesh(
        new THREE.BoxGeometry(0.5, 5, 20),
        wallMaterial
    );
    wall4.position.set(10, 2.5, 0);
    wall4.castShadow = true;
    wall4.receiveShadow = true;
    scene.add(wall4);
    walls.push(wall4);
    
    // Dodaj cele
    createTargets();
    
    // Inicjalizuj obrazek broni
    initWeaponImage();
}

function createTargets() {
    // Usuń stare cele
    targets.forEach(target => scene.remove(target));
    targets = [];
    
    // Utwórz nowe cele
    for (let i = 0; i < gameState.targetsLeft; i++) {
        const targetGeometry = new THREE.CylinderGeometry(0.5, 0.5, 0.1, 16);
        const targetMaterial = new THREE.MeshStandardMaterial({ 
            color: 0xff0000,
            emissive: 0x330000,
            roughness: 0.5,
            metalness: 0.3
        });
        const target = new THREE.Mesh(targetGeometry, targetMaterial);
        
        // Losowa pozycja
        target.position.set(
            (Math.random() - 0.5) * 15,
            1.5 + Math.random() * 2,
            -8 + Math.random() * 12
        );
        
        target.rotation.x = Math.PI / 2;
        target.castShadow = true;
        target.receiveShadow = true;
        target.userData.isTarget = true;
        
        scene.add(target);
        targets.push(target);
    }
}

function initWeaponImage() {
    weaponImage = document.getElementById('weaponImage');
    
    // Animuj broń (obrazek 2D)
    function animateWeapon() {
        if (weaponImage && gameState.gameRunning) {
            // Odrzut broni (recoil)
            weaponRecoil += (weaponRecoilTarget - weaponRecoil) * 0.2;
            weaponRecoilTarget *= 0.9; // Zmniejsz odrzut
            
            // Delikatna animacja (drganie)
            weaponSway = Math.sin(Date.now() * 0.01) * 3;
            const verticalSway = Math.sin(Date.now() * 0.005) * 2;
            
            // Zastosuj transformacje
            const translateX = 50 + weaponSway - weaponRecoil * 20;
            const translateY = -weaponRecoil * 15 - verticalSway;
            const rotate = weaponRecoil * 2;
            
            weaponImage.style.transform = `translateX(${translateX}px) translateY(${translateY}px) rotate(${rotate}deg)`;
        } else if (weaponImage && !gameState.gameRunning) {
            // Reset pozycji gdy gra nie działa
            weaponImage.style.transform = 'translateX(50px)';
        }
        
        requestAnimationFrame(animateWeapon);
    }
    
    animateWeapon();
}

function startGame() {
    if (!gameState.gameRunning && !gameState.gameOver) {
        gameState.gameRunning = true;
        gameState.gameOver = false;
        gameState.score = 0;
        gameState.hits = 0;
        gameState.shots = 0;
        gameState.targetsLeft = 10;
        
        createTargets();
        updateUI();
        document.getElementById('gameOver').style.display = 'none';
        
        const canvas = document.getElementById('gameCanvas');
        canvas.requestPointerLock();
    }
}

function resetGame() {
    gameState.score = 0;
    gameState.hits = 0;
    gameState.shots = 0;
    gameState.targetsLeft = 10;
    gameState.gameRunning = false;
    gameState.gameOver = false;
    
    controls.yaw = 0;
    controls.pitch = 0;
    
    // Reset pozycji gracza
    camera.position.set(0, 1.6, 0);
    
    // Reset fizyki skoku
    velocityY = 0;
    isOnGround = true;
    
    // Reset odrzutu broni
    weaponRecoil = 0;
    weaponRecoilTarget = 0;
    weaponSway = 0;
    
    // Reset pozycji obrazka broni
    if (weaponImage) {
        weaponImage.style.transform = 'translateX(50px)';
    }
    
    // Reset klawiszy
    keys.w = false;
    keys.a = false;
    keys.s = false;
    keys.d = false;
    keys.space = false;
    
    createTargets();
    updateUI();
    document.getElementById('gameOver').style.display = 'none';
    
    document.exitPointerLock();
}

function onPointerLockChange() {
    const canvas = document.getElementById('gameCanvas');
    if (document.pointerLockElement === canvas) {
        document.addEventListener('mousemove', onMouseMove);
    } else {
        document.removeEventListener('mousemove', onMouseMove);
    }
}

function onKeyDown(event) {
    if (!gameState.gameRunning) return;
    
    switch(event.key.toLowerCase()) {
        case 'w':
            keys.w = true;
            break;
        case 'a':
            keys.a = true;
            break;
        case 's':
            keys.s = true;
            break;
        case 'd':
            keys.d = true;
            break;
        case ' ':
            event.preventDefault(); // Zapobiegaj przewijaniu strony
            if (isOnGround) {
                keys.space = true;
                velocityY = jumpHeight;
                isOnGround = false;
            }
            break;
    }
}

function onKeyUp(event) {
    switch(event.key.toLowerCase()) {
        case 'w':
            keys.w = false;
            break;
        case 'a':
            keys.a = false;
            break;
        case 's':
            keys.s = false;
            break;
        case 'd':
            keys.d = false;
            break;
        case ' ':
            keys.space = false;
            break;
    }
}

function movePlayer() {
    if (!gameState.gameRunning) return;
    
    // Oblicz kierunek ruchu na podstawie rotacji kamery
    const direction = new THREE.Vector3();
    camera.getWorldDirection(direction);
    direction.y = 0; // Nie poruszaj się w górę/dół
    direction.normalize();
    
    // Wektor prostopadły do kierunku (dla ruchu w bok)
    const right = new THREE.Vector3();
    right.crossVectors(direction, new THREE.Vector3(0, 1, 0));
    right.normalize();
    
    const newPosition = camera.position.clone();
    
    // Ruch do przodu/tyłu
    if (keys.w) {
        newPosition.add(direction.clone().multiplyScalar(moveSpeed));
    }
    if (keys.s) {
        newPosition.add(direction.clone().multiplyScalar(-moveSpeed));
    }
    
    // Ruch w lewo/prawo
    if (keys.a) {
        newPosition.add(right.clone().multiplyScalar(-moveSpeed));
    }
    if (keys.d) {
        newPosition.add(right.clone().multiplyScalar(moveSpeed));
    }
    
    // Fizyka skoku i grawitacji
    newPosition.y += velocityY;
    velocityY -= gravity; // Grawitacja
    
    // Sprawdź czy gracz jest na ziemi
    const groundLevel = 1.6; // Wysokość oczu gracza na ziemi
    if (newPosition.y <= groundLevel) {
        newPosition.y = groundLevel;
        velocityY = 0;
        isOnGround = true;
    } else {
        isOnGround = false;
    }
    
    // Sprawdź kolizje ze ścianami (tylko poziome)
    const playerRadius = 0.5;
    let canMoveXZ = true;
    
    for (let wall of walls) {
        const wallBox = new THREE.Box3().setFromObject(wall);
        const playerBox = new THREE.Box3(
            new THREE.Vector3(newPosition.x - playerRadius, newPosition.y - 0.5, newPosition.z - playerRadius),
            new THREE.Vector3(newPosition.x + playerRadius, newPosition.y + 2, newPosition.z + playerRadius)
        );
        
        if (wallBox.intersectsBox(playerBox)) {
            // Sprawdź czy kolizja jest tylko pozioma (nie z sufitem)
            const wallTop = wallBox.max.y;
            const playerBottom = playerBox.min.y;
            
            if (playerBottom < wallTop) {
                // Kolizja pozioma - zablokuj ruch X/Z
                canMoveXZ = false;
            }
            
            // Kolizja z sufitem
            if (newPosition.y > wallBox.max.y) {
                velocityY = 0; // Zatrzymaj skok
            }
        }
    }
    
    // Ograniczenia obszaru gry (tylko poziome)
    if (newPosition.x < -9 || newPosition.x > 9 || newPosition.z < -9 || newPosition.z > 9) {
        canMoveXZ = false;
    }
    
    // Zastosuj ruch jeśli nie ma kolizji
    if (canMoveXZ) {
        camera.position.x = newPosition.x;
        camera.position.z = newPosition.z;
    }
    
    // Zawsze aktualizuj Y (wysokość) dla skoku
    camera.position.y = newPosition.y;
}

function onMouseMove(event) {
    if (!gameState.gameRunning) return;
    if (document.pointerLockElement !== document.getElementById('gameCanvas')) return;
    
    const sensitivity = 0.002;
    
    controls.yaw -= event.movementX * sensitivity;
    controls.pitch -= event.movementY * sensitivity;
    
    // Ogranicz pitch
    controls.pitch = Math.max(-Math.PI / 2, Math.min(Math.PI / 2, controls.pitch));
}

function onMouseClick(event) {
    if (!gameState.gameRunning) return;
    
    gameState.shots++;
    
    // Animacja odrzutu broni (dla obrazka 2D)
    weaponRecoilTarget = 0.5;
    
    // Strzał z kamery
    raycaster.setFromCamera(new THREE.Vector2(0, 0), camera);
    const intersects = raycaster.intersectObjects(targets);
    
    // Dźwięk strzału (zawsze odtwarzany)
    playGunshot();
    
    if (intersects.length > 0) {
        const hitTarget = intersects[0].object;
        if (hitTarget.userData.isTarget) {
            // Trafienie!
            gameState.hits++;
            gameState.score += 100;
            gameState.targetsLeft--;
            
            // Dodatkowy dźwięk trafienia (po krótkiej przerwie)
            setTimeout(() => {
                playSound(1200, 0.08, 'sine'); // Wysoki dźwięk trafienia
            }, 50);
            
            // Usuń cel
            scene.remove(hitTarget);
            targets = targets.filter(t => t !== hitTarget);
            
            // Sprawdź czy wszystkie cele zniszczone
            if (gameState.targetsLeft === 0) {
                endGame('Wszystkie cele zniszczone!');
            }
        }
    }
    
    updateUI();
}

function endGame(message) {
    gameState.gameRunning = false;
    gameState.gameOver = true;
    
    const accuracy = gameState.shots > 0 ? ((gameState.hits / gameState.shots) * 100).toFixed(1) : 0;
    
    document.getElementById('gameOverText').textContent = message;
    document.getElementById('finalStats').innerHTML = `
        <strong>Wynik:</strong> ${gameState.score} punktów<br>
        <strong>Celność:</strong> ${accuracy}% (${gameState.hits}/${gameState.shots})
    `;
    document.getElementById('gameOver').style.display = 'block';
    
    playSound(600, 0.3, 'sine');
    
    document.exitPointerLock();
}

function updateUI() {
    document.getElementById('score').textContent = gameState.score;
    document.getElementById('hits').textContent = gameState.hits;
    document.getElementById('shots').textContent = gameState.shots;
    document.getElementById('targetsLeft').textContent = gameState.targetsLeft;
}

function animate() {
    requestAnimationFrame(animate);
    
    if (gameState.gameRunning) {
        // Aktualizuj rotację kamery
        camera.rotation.order = 'YXZ';
        camera.rotation.y = controls.yaw;
        camera.rotation.x = controls.pitch;
        
        // Poruszaj gracza
        movePlayer();
        
        // Animuj cele (obracanie)
        targets.forEach(target => {
            target.rotation.z += 0.01;
        });
    }
    
    renderer.render(scene, camera);
}
</script>

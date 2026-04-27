<?php
ini_set('session.cookie_lifetime', 60 * 60 * 24 * 30);
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login_page.php"); exit; }
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>TIMER : 엉덩이.STORE</title>
    <link rel="icon" type="image/png" href="favicon.png">    
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow-models/posenet"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@900&display=swap" rel="stylesheet">
    <style>
        :root { --neon: #00ff41; --alert: #ff003c; }
        body { background: #000; color: #fff; font-family: 'Inter', sans-serif; padding: 40px; margin: 0; overflow-x: hidden; }
        
        #timer-display { 
            font-size: 150px; font-weight: 900; letter-spacing: -10px; line-height: 0.9; margin-bottom: 20px; 
            font-variant-numeric: tabular-nums; 
        }
        .ms { color: var(--neon); font-size: 60px; margin-left: 10px; letter-spacing: -2px; }

        .content-wrapper { display: flex; gap: 20px; align-items: flex-start; width: fit-content; }

        .v-container { 
            position: relative; width: 640px; height: 480px; border: 1px solid #222; overflow: hidden; background: #050505; 
            flex-shrink: 0;
        }
        video { width: 100%; height: 100%; transform: rotateY(180deg); filter: grayscale(0.8); display: block; }
        video.blinded { filter: grayscale(0.8) blur(40px); }
        #center-status { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 60px; font-weight: 900; display: none; z-index: 5; text-shadow: 0 0 20px #000; }
        #status { position: absolute; top: 20px; right: 20px; background: #000; border: 1px solid #fff; padding: 8px 15px; font-weight: 900; font-size: 12px; z-index: 10; }

        .controls { margin-top: 30px; display: flex; gap: 10px; width: 640px; }
        button { flex: 1; padding: 25px; font-weight: 900; border: none; font-size: 18px; cursor: pointer; transition: 0.2s; }
        .p-btn { background: #fff; color: #000; }
        .s-btn { background: #222; color: #fff; }
        .b-btn { background: #444; color: #fff; }
        .b-btn.active { background: var(--neon); color: #000; }

        #todo-callout {
            width: 360px; 
            height: 480px; 
            background: #050505; border: 1px solid #222;
            padding: 0; display: flex; flex-direction: column;
            box-sizing: border-box; flex-shrink: 0;
        }
        .todo-handle { padding: 15px 20px; cursor: default; background: #0a0a0a; display: flex; justify-content: space-between; align-items: center; }
        .todo-title { font-size: 12px; font-weight: 900; color: var(--neon); letter-spacing: 2px; }
        
        .todo-content { padding: 0 20px 20px 20px; display: flex; flex-direction: column; flex: 1; overflow: hidden; }
        .todo-input-group { display: flex; border-bottom: 1px solid #333; margin-bottom: 15px; }
        .todo-input { 
            flex: 1; background: transparent; border: none; color: #fff; padding: 14px 0; 
            font-family: 'Inter'; font-size: 14px; 
        }
        .todo-input:focus { outline: none; }
        .todo-add-btn { background: transparent; border: none; color: var(--neon); cursor: pointer; font-weight: 900; font-size: 14px; }
        
        .todo-list { list-style: none; padding: 0; margin: 0; overflow-y: auto; flex: 1; }
        .todo-item { display: flex; align-items: center; padding: 14px 0; border-bottom: 1px solid #111; gap: 14px; }
        
        .todo-check {
            width: 20px; height: 20px; border: 2px solid #444; cursor: pointer;
            display: flex; align-items: center; justify-content: center; transition: 0.2s; flex-shrink: 0;
        }
        .todo-item.done .todo-check { background: var(--neon); border-color: var(--neon); }
        .todo-item.done .todo-check::after { content: '✔'; color: #000; font-size: 13px; font-weight: 900; }
        
        .todo-text { flex: 1; cursor: pointer; font-size: 14px; color: #aaa; font-weight: 600; transition: 0.2s; line-height: 1.4; }
        .todo-item.done .todo-text { text-decoration: line-through; color: #333; }
        .todo-item:hover .todo-text { color: #fff; }
        
        .todo-del { color: var(--alert); font-size: 11px; font-weight: 900; cursor: pointer; opacity: 0.2; transition: 0.2s; margin-left: auto; }
        .todo-item:hover .todo-del { opacity: 1; }

        .todo-list::-webkit-scrollbar { width: 3px; }
        .todo-list::-webkit-scrollbar-thumb { background: #222; }
    </style>
</head>
<body>

    <div id="timer-display">00:00:00<span class="ms">.00</span></div>
    
    <div class="content-wrapper">
        <div class="v-container">
            <video id="webcam" autoplay playsinline></video>
            <div id="center-status">LOST</div>
            <div id="status">INIT...</div>
        </div>

        <div id="todo-callout">
            <div class="todo-handle">
                <div class="todo-title">CURRENT_OBJECTIVES _</div>
            </div>
            <div class="todo-content">
                <div class="todo-input-group">
                    <input type="text" id="todoInput" class="todo-input" placeholder="Add mission target..." autocomplete="off">
                    <button class="todo-add-btn" onclick="addTodo()">ADD</button>
                </div>
                <ul id="todoList" class="todo-list"></ul>
            </div>
        </div>
    </div>

    <div class="controls">
        <button class="b-btn" id="blind-toggle" onclick="toggleBlind()">BLIND MODE: OFF</button>
        <button class="p-btn" onclick="togglePause()">PAUSE / RESUME</button>
        <button class="s-btn" onclick="stopStudy()">END SESSION</button>
    </div>

    <script>
        let todos = JSON.parse(localStorage.getItem('study_todos')) || [];
        function renderTodos() {
            localStorage.setItem('study_todos', JSON.stringify(todos));
            const list = document.getElementById('todoList');
            list.innerHTML = todos.map(t => `
                <li class="todo-item ${t.done ? 'done' : ''}">
                    <div class="todo-check" onclick="toggleTodo(${t.id})"></div>
                    <span class="todo-text" onclick="toggleTodo(${t.id})">${t.text}</span>
                    <span class="todo-del" onclick="deleteTodo(${t.id})">DEL</span>
                </li>
            `).join('');
        }
        function addTodo() {
            const input = document.getElementById('todoInput');
            if (!input.value.trim()) return;
            todos.push({ id: Date.now(), text: input.value.trim(), done: false });
            input.value = '';
            renderTodos();
        }
        function toggleTodo(id) {
            todos = todos.map(t => t.id === id ? { ...t, done: !t.done } : t);
            renderTodos();
        }
        function deleteTodo(id) {
            todos = todos.filter(t => t.id !== id);
            renderTodos();
        }
        document.getElementById('todoInput').addEventListener('keypress', (e) => { if (e.key === 'Enter') addTodo(); });
        renderTodos();

        let net, isPresent = false, isPaused = false, isBlind = false, elapsed = 0, sessionId = null;
        const display = document.getElementById('timer-display');
        const statusTag = document.getElementById('status');
        const video = document.getElementById('webcam');
        const centerStatus = document.getElementById('center-status');
        const blindBtn = document.getElementById('blind-toggle');

        async function init() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ video: true });
                video.srcObject = stream;
                statusTag.innerText = "LOADING AI...";
                net = await posenet.load();
                const res = await fetch('start_session.php', { method: 'POST' });
                const data = await res.json();
                if (data.session_id) {
                    sessionId = data.session_id;
                    statusTag.innerText = "READY";
                    detect();
                    setInterval(tick, 10);
                }
            } catch (e) { statusTag.innerText = "ERROR"; }
        }

        async function detect() {
            if (!isPaused && net) {
                const poses = await net.estimatePoses(video, { flipHorizontal: false });
                isPresent = (poses.length > 0 && poses[0].keypoints[0].score > 0.5);
                const txt = isPresent ? "TRACKING" : "LOST";
                const color = isPresent ? "#00ff41" : "#ff003c";
                statusTag.innerText = txt; statusTag.style.color = color;
                centerStatus.innerText = txt; centerStatus.style.color = color;
            }
            requestAnimationFrame(detect);
        }

        function tick() {
            if (isPresent && !isPaused && sessionId) {
                elapsed += 10;
                const h = Math.floor(elapsed / 3600000).toString().padStart(2, '0');
                const m = Math.floor((elapsed % 3600000) / 60000).toString().padStart(2, '0');
                const s = Math.floor((elapsed % 60000) / 1000).toString().padStart(2, '0');
                const ms = Math.floor((elapsed % 1000) / 10).toString().padStart(2, '0');
                display.innerHTML = `${h}:${m}:${s}<span class="ms">.${ms}</span>`;
                if (elapsed % 1000 === 0) {
                    fetch('heartbeat.php', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: 'session_id=' + sessionId });
                }
            }
        }

        function toggleBlind() {
            isBlind = !isBlind;
            video.classList.toggle('blinded', isBlind);
            centerStatus.style.display = isBlind ? 'block' : 'none';
            blindBtn.innerText = isBlind ? "BLIND MODE: ON" : "BLIND MODE: OFF";
            blindBtn.classList.toggle('active', isBlind);
        }
        function togglePause() { isPaused = !isPaused; statusTag.innerText = isPaused ? "PAUSED" : "RESUMING..."; }
        function stopStudy() { if (confirm("측정을 종료하시겠습니까?")) location.href = 'analysis.php'; }
        window.onload = init;
    </script>
</div>
</body>
</html>

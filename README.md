<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>スケジュール</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }
        
        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        
        button {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
        }
        
        button:hover {
            background-color: #0056b3;
        }
        
        .toggle-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .toggle-link a {
            color: #007bff;
            text-decoration: none;
        }
        
        .message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
        }
        
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .hidden {
            display: none;
        }
        
        .user-header {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .user-info {
            font-size: 16px;
        }
        
        .logout-btn {
            background-color: #dc3545;
            padding: 8px 16px;
            width: auto;
            margin: 0;
        }
        
        .logout-btn:hover {
            background-color: #c82333;
        }
        
        /* スケジュール管理のスタイル */
        .add-form {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }

        .form-row {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }

        .form-row input, .form-row button {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        .form-row input {
            flex: 1;
        }

        .form-row button {
            background: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            min-width: 80px;
            width: auto;
            margin: 0;
        }

        .form-row button:hover {
            background: #0056b3;
        }

        .schedule-list {
            list-style: none;
        }

        .schedule-item {
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .schedule-item.completed {
            background: #f0f0f0;
            text-decoration: line-through;
            opacity: 0.7;
        }

        .schedule-info {
            flex: 1;
        }

        .schedule-time {
            font-weight: bold;
            color: #007bff;
        }

        .schedule-title {
            margin-top: 5px;
            color: #333;
        }

        .schedule-actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 5px 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 12px;
        }

        .btn-complete {
            background: #28a745;
            color: white;
        }

        .btn-delete {
            background: #dc3545;
            color: white;
        }

        .empty {
            text-align: center;
            color: #999;
            padding: 40px;
            font-style: italic;
        }

        @media (max-width: 600px) {
            .form-row {
                flex-direction: column;
            }
            
            .schedule-item {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .schedule-actions {
                margin-top: 10px;
            }
            
            .user-header {
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- メッセージエリア -->
        <div id="message"></div>
        
        <!-- ログインフォーム -->
        <div id="login-form">
            <h1>スケジュール</h1>
            <form id="loginForm">
                <div class="form-group">
                    <label>メールアドレス:</label>
                    <input type="email" id="login-email" required>
                </div>
                <div class="form-group">
                    <label>パスワード:</label>
                    <input type="password" id="login-password" required>
                </div>
                <button type="submit">ログイン</button>
            </form>
            <div class="toggle-link">
                <a href="#" onclick="showRegister()">新規登録</a>
            </div>
        </div>
        
        <!-- 登録フォーム -->
        <div id="register-form" class="hidden">
            <h1>新規登録</h1>
            <form id="registerForm">
                <div class="form-group">
                    <label>ユーザー名:</label>
                    <input type="text" id="register-username" required>
                </div>
                <div class="form-group">
                    <label>メールアドレス:</label>
                    <input type="email" id="register-email" required>
                </div>
                <div class="form-group">
                    <label>パスワード:</label>
                    <input type="password" id="register-password" required>
                </div>
                <button type="submit">登録</button>
            </form>
            <div class="toggle-link">
                <a href="#" onclick="showLogin()">ログイン画面に戻る</a>
            </div>
        </div>
        
        <!-- スケジュール管理画面 -->
        <div id="schedule-app" class="hidden">
            <div class="user-header">
                <div class="user-info">
                    ようこそ、<strong id="current-username"></strong>さん
                </div>
                <button class="logout-btn" onclick="logout()">ログアウト</button>
            </div>
            
            <h1>1日のスケジュール管理</h1>
            
            <div class="add-form">
                <form id="scheduleForm">
                    <div class="form-row">
                        <input type="time" id="taskTime" required>
                        <input type="text" id="taskTitle" placeholder="予定を入力" required>
                        <button type="submit">追加</button>
                    </div>
                </form>
            </div>

            <ul class="schedule-list" id="scheduleList">
                <li class="empty">予定がありません</li>
            </ul>
        </div>
    </div>

    <script>
        // ユーザー管理
        let users = JSON.parse(localStorage.getItem('users') || '[]');
        let currentUser = null;
        
        // スケジュール管理
        let schedules = [];
        let idCounter = 1;
        
        // 画面切り替え
        function showLogin() {
            document.getElementById('login-form').classList.remove('hidden');
            document.getElementById('register-form').classList.add('hidden');
            document.getElementById('schedule-app').classList.add('hidden');
            clearMessage();
        }
        
        function showRegister() {
            document.getElementById('login-form').classList.add('hidden');
            document.getElementById('register-form').classList.remove('hidden');
            clearMessage();
        }
        
        function showScheduleApp() {
            document.getElementById('login-form').classList.add('hidden');
            document.getElementById('register-form').classList.add('hidden');
            document.getElementById('schedule-app').classList.remove('hidden');
            
            document.getElementById('current-username').textContent = currentUser.username;
            loadUserSchedules();
        }
        
        // メッセージ表示
        function showMessage(message, type) {
            document.getElementById('message').innerHTML = 
                `<div class="message ${type}">${message}</div>`;
        }
        
        function clearMessage() {
            document.getElementById('message').innerHTML = '';
        }
        
        // ログイン処理
        document.getElementById('loginForm').onsubmit = function(e) {
            e.preventDefault();
            
            const email = document.getElementById('login-email').value;
            const password = document.getElementById('login-password').value;
            
            const user = users.find(u => u.email === email && u.password === password);
            
            if (user) {
                currentUser = user;
                showMessage('ログイン成功！', 'success');
                setTimeout(() => {
                    clearMessage();
                    showScheduleApp();
                }, 1000);
            } else {
                showMessage('メールアドレスまたはパスワードが間違っています', 'error');
            }
        };
        
        // 登録処理
        document.getElementById('registerForm').onsubmit = function(e) {
            e.preventDefault();
            
            const username = document.getElementById('register-username').value;
            const email = document.getElementById('register-email').value;
            const password = document.getElementById('register-password').value;
            
            // 重複チェック
            if (users.find(u => u.email === email)) {
                showMessage('このメールアドレスは既に使用されています', 'error');
                return;
            }
            
            // ユーザー追加
            const newUser = { username, email, password };
            users.push(newUser);
            localStorage.setItem('users', JSON.stringify(users));
            
            showMessage('登録完了！ログインしてください', 'success');
            document.getElementById('registerForm').reset();
            setTimeout(showLogin, 1500);
        };
        
        // ログアウト
        function logout() {
            saveUserSchedules();
            currentUser = null;
            schedules = [];
            showMessage('ログアウトしました', 'success');
            setTimeout(showLogin, 1000);
        }
        
        // スケジュール管理機能
        function loadUserSchedules() {
            const userSchedulesKey = `schedules_${currentUser.email}`;
            schedules = JSON.parse(localStorage.getItem(userSchedulesKey) || '[]');
            if (schedules.length > 0) {
                idCounter = Math.max(...schedules.map(s => s.id)) + 1;
            }
            displaySchedules();
            setCurrentTime();
        }
        
        function saveUserSchedules() {
            if (currentUser) {
                const userSchedulesKey = `schedules_${currentUser.email}`;
                localStorage.setItem(userSchedulesKey, JSON.stringify(schedules));
            }
        }
        
        function displaySchedules() {
            const list = document.getElementById('scheduleList');
            
            if (schedules.length === 0) {
                list.innerHTML = '<li class="empty">予定がありません</li>';
                return;
            }

            // 時間順にソート
            const sortedSchedules = schedules.sort((a, b) => a.time.localeCompare(b.time));
            
            list.innerHTML = sortedSchedules.map(schedule => `
                <li class="schedule-item ${schedule.completed ? 'completed' : ''}">
                    <div class="schedule-info">
                        <div class="schedule-time">${schedule.time}</div>
                        <div class="schedule-title">${schedule.title}</div>
                    </div>
                    <div class="schedule-actions">
                        <button class="btn btn-complete" onclick="toggleComplete(${schedule.id})">
                            ${schedule.completed ? '戻す' : '完了'}
                        </button>
                        <button class="btn btn-delete" onclick="deleteSchedule(${schedule.id})">削除</button>
                    </div>
                </li>
            `).join('');
        }

        function addSchedule(event) {
            event.preventDefault();
            
            const time = document.getElementById('taskTime').value;
            const title = document.getElementById('taskTitle').value;

            schedules.push({
                id: idCounter++,
                time,
                title,
                completed: false
            });

            saveUserSchedules();
            displaySchedules();
            document.getElementById('scheduleForm').reset();
            setCurrentTime();
        }

        function toggleComplete(id) {
            const schedule = schedules.find(s => s.id === id);
            if (schedule) {
                schedule.completed = !schedule.completed;
                saveUserSchedules();
                displaySchedules();
            }
        }

        function deleteSchedule(id) {
            schedules = schedules.filter(s => s.id !== id);
            saveUserSchedules();
            displaySchedules();
        }
        
        function setCurrentTime() {
            const now = new Date();
            const currentTime = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');
            document.getElementById('taskTime').value = currentTime;
        }

        // イベントリスナー設定
        document.getElementById('scheduleForm').addEventListener('submit', addSchedule);
        
        // 初期データ（テスト用）
        if (users.length === 0) {
            users.push({ username: 'テストユーザー', email: 'test@test.com', password: '123' });
            localStorage.setItem('users', JSON.stringify(users));
        }
    </script>
</body>
</html>

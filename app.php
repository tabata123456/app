<?php
session_start();

// データベース接続設定（MAMP用）
function getDBConnection() {
    $host = 'localhost';
    $port = '8889'; // MAMPのデフォルトポート
    $dbname = 'schedule_app';
    $username = 'root'; // MAMPのデフォルトユーザー名
    $password = 'root'; // MAMPのデフォルトパスワード
    
    try {
        $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("データベース接続エラー: " . $e->getMessage());
    }
}

// メッセージ管理
$message = '';
$messageType = '';

if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $messageType = $_SESSION['message_type'];
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}

function setMessage($msg, $type) {
    $_SESSION['message'] = $msg;
    $_SESSION['message_type'] = $type;
}

// ユーザー登録処理
if (isset($_POST['action']) && $_POST['action'] == 'register') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    $pdo = getDBConnection();
    
    // メール重複チェック
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->fetchColumn() > 0) {
        setMessage('このメールアドレスは既に使用されています', 'error');
    } else {
        // ユーザー登録
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        if ($stmt->execute([$username, $email, $password])) {
            setMessage('登録完了！ログインしてください', 'success');
        } else {
            setMessage('登録に失敗しました', 'error');
        }
    }
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// ログイン処理
if (isset($_POST['action']) && $_POST['action'] == 'login') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        setMessage('ログイン成功！', 'success');
    } else {
        setMessage('メールアドレスまたはパスワードが間違っています', 'error');
    }
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// ログアウト処理
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy();
    session_start();
    setMessage('ログアウトしました', 'success');
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// スケジュール追加処理
if (isset($_POST['action']) && $_POST['action'] == 'add_schedule') {
    $time = $_POST['time'];
    $title = $_POST['title'];
    $user_id = $_SESSION['user_id'];
    
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("INSERT INTO schedules (user_id, time, title, completed) VALUES (?, ?, ?, 0)");
    if ($stmt->execute([$user_id, $time, $title])) {
        setMessage('スケジュールを追加しました', 'success');
    } else {
        setMessage('スケジュールの追加に失敗しました', 'error');
    }
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// スケジュール完了切り替え処理
if (isset($_GET['action']) && $_GET['action'] == 'toggle_complete') {
    $schedule_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];
    
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("UPDATE schedules SET completed = NOT completed WHERE id = ? AND user_id = ?");
    $stmt->execute([$schedule_id, $user_id]);
    
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// スケジュール削除処理
if (isset($_GET['action']) && $_GET['action'] == 'delete_schedule') {
    $schedule_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];
    
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("DELETE FROM schedules WHERE id = ? AND user_id = ?");
    $stmt->execute([$schedule_id, $user_id]);
    
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// スケジュール取得
$schedules = [];
if (isset($_SESSION['user_id'])) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM schedules WHERE user_id = ? ORDER BY time ASC");
    $stmt->execute([$_SESSION['user_id']]);
    $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 現在時刻を取得
$currentTime = date('H:i');
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>スケジュール管理システム</title>
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
            box-sizing: border-box;
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
            text-decoration: none;
            display: inline-block;
            text-align: center;
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
            padding: 0;
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
            text-decoration: none;
            display: inline-block;
            text-align: center;
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
        <?php if ($message): ?>
        <div class="message <?php echo $messageType; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
        <?php endif; ?>
        
        <?php if (!isset($_SESSION['user_id'])): ?>
            <?php if (!isset($_GET['register'])): ?>
            <!-- ログインフォーム -->
            <h1>スケジュール管理システム</h1>
            <form method="POST">
                <input type="hidden" name="action" value="login">
                <div class="form-group">
                    <label>メールアドレス:</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>パスワード:</label>
                    <input type="password" name="password" required>
                </div>
                <button type="submit">ログイン</button>
            </form>
            <div class="toggle-link">
                <a href="?register=1">新規登録</a>
            </div>
            <?php else: ?>
            <!-- 登録フォーム -->
            <h1>新規登録</h1>
            <form method="POST">
                <input type="hidden" name="action" value="register">
                <div class="form-group">
                    <label>ユーザー名:</label>
                    <input type="text" name="username" required>
                </div>
                <div class="form-group">
                    <label>メールアドレス:</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>パスワード:</label>
                    <input type="password" name="password" required>
                </div>
                <button type="submit">登録</button>
            </form>
            <div class="toggle-link">
                <a href="<?php echo $_SERVER['PHP_SELF']; ?>">ログイン画面に戻る</a>
            </div>
            <?php endif; ?>
        <?php else: ?>
        <!-- スケジュール管理画面 -->
        <div class="user-header">
            <div class="user-info">
                ようこそ、<strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>さん
            </div>
            <a href="?action=logout" class="logout-btn">ログアウト</a>
        </div>
        
        <h1>1日のスケジュール管理</h1>
        
        <div class="add-form">
            <form method="POST">
                <input type="hidden" name="action" value="add_schedule">
                <div class="form-row">
                    <input type="time" name="time" value="<?php echo $currentTime; ?>" required>
                    <input type="text" name="title" placeholder="予定を入力" required>
                    <button type="submit">追加</button>
                </div>
            </form>
        </div>

        <ul class="schedule-list">
            <?php if (empty($schedules)): ?>
                <li class="empty">予定がありません</li>
            <?php else: ?>
                <?php foreach ($schedules as $schedule): ?>
                <li class="schedule-item <?php echo $schedule['completed'] ? 'completed' : ''; ?>">
                    <div class="schedule-info">
                        <div class="schedule-time"><?php echo htmlspecialchars($schedule['time']); ?></div>
                        <div class="schedule-title"><?php echo htmlspecialchars($schedule['title']); ?></div>
                    </div>
                    <div class="schedule-actions">
                        <a href="?action=toggle_complete&id=<?php echo $schedule['id']; ?>" class="btn btn-complete">
                            <?php echo $schedule['completed'] ? '戻す' : '完了'; ?>
                        </a>
                        <a href="?action=delete_schedule&id=<?php echo $schedule['id']; ?>" 
                           class="btn btn-delete" 
                           onclick="return confirm('本当に削除しますか？')">削除</a>
                    </div>
                </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
        <?php endif; ?>
    </div>
</body>
</html>
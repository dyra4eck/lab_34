<?php
session_start();
include 'connect.php';
$pageTitle = 'Вход';
include 'header.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'];
    $password = $_POST['password'];
    $robotCheck = $_POST['robot_check'] ?? '';

    // Проверка робота
    if ($robotCheck !== '1') {
        $error = 'Подтвердите, что вы не робот';
    } else {
        // Остальная логика авторизации
        $sql = "SELECT client_id, login, name, password FROM клиенты WHERE login = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $login);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $client = $result->fetch_assoc();
            if (!empty($password) && password_verify($password, $client['password'])) {
                $_SESSION['user_id'] = $client['client_id'];
                $_SESSION['user_name'] = $client['name'];
                $_SESSION['user_login'] = $client['login'];
                header("Location: account.php");
                exit();
            } else {
                $error = 'Неверный пароль';
            }
        } else {
            $error = 'Пользователь не найден';
        }
    }
}
?>

<div class="main-container">
    <div class="left-column">
        <?php include 'menu.php'; ?>
    </div>
    
    <div id="content">
        <h2 class="section-title">Авторизация</h2>
        <?php if(!empty($error)): ?>
            <div class="error-message"><?= $error ?></div>
        <?php endif; ?>
        
        <form class="auth-form" method="POST">
            <input type="text" name="login" placeholder="Логин" required>
            <input type="password" name="password" placeholder="Пароль" required>
            
            <div class="robot-check">
                <input type="hidden" name="robot_check" value="0" id="robotInput">
                <button type="button" class="robot-btn" id="robotBtn">
                    <span class="robot-icon">✓</span> Я не робот
                </button>
            </div>
            
            <button type="submit" class="btn-primary">Войти</button>
        </form>
    </div>
</div>

<style>
.robot-check {
    margin: 20px 0;
}

.robot-btn {
    background: #f0f0f0;
    border: 2px solid #ccc;
    padding: 12px 25px;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    gap: 10px;
}

.robot-btn.active {
    background: #e3f2fd;
    border-color: #2196F3;
}

.robot-icon {
    font-size: 1.2em;
    opacity: 0;
    transition: opacity 0.3s;
}

.robot-btn.active .robot-icon {
    opacity: 1;
}

.error-message {
    color: #dc3545;
    margin-bottom: 15px;
    padding: 10px;
    background: #f8d7da;
    border-radius: 4px;
}
</style>

<script>
document.getElementById('robotBtn').addEventListener('click', function() {
    this.classList.toggle('active');
    document.getElementById('robotInput').value = 
        this.classList.contains('active') ? '1' : '0';
});
</script>
<?php include 'footer.php'; ?>
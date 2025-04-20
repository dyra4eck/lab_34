<?php
include 'connect.php';
$pageTitle = 'Регистрация';
include 'header.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login']);
    $password = trim($_POST['password']);
    $email = trim($_POST['email']);
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $robotCheck = trim($_POST['robot_check']);

    // Проверка робота
    if ($robotCheck !== '1') {
        $errors[] = 'Подтвердите, что вы не робот';
    } else {
        // Остальная логика регистрации
        $stmt = $conn->prepare("SELECT * FROM клиенты WHERE login = ? OR email = ?");
        $stmt->bind_param('ss', $login, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $errors[] = 'Логин или email уже заняты';
        }
    }

    if (empty($errors)) {
        $sqlMaxId = "SELECT MAX(client_id) AS max_id FROM клиенты";
        $result = $conn->query($sqlMaxId);
        $row = $result->fetch_assoc();
        $newClientId = ($row['max_id'] ?? 0) + 1;

        $stmt = $conn->prepare("INSERT INTO клиенты (client_id, login, password, email, name, phone) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('isssss', $newClientId, $login, $password, $email, $name, $phone);

        if ($stmt->execute()) {
            $_SESSION['user_id'] = $newClientId;
            $_SESSION['user_login'] = $login;
            $_SESSION['user_name'] = $name;
            header('Location: account.php');
            exit;
        } else {
            $errors[] = 'Ошибка регистрации: ' . $conn->error;
        }
    }
}
?>

<div class="main-container">
    <div class="left-column">
        <?php include 'menu.php'; ?>
    </div>
    <div id="content">
        <h2 class="section-title">Регистрация</h2>
        <?php if (!empty($errors)): ?>
            <div class="errors"><?= implode('<br>', $errors) ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="login" placeholder="Логин" required>
            <input type="password" name="password" placeholder="Пароль" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="name" placeholder="Имя" required>
            <input type="tel" name="phone" placeholder="Телефон" required>
            
            <div class="robot-check">
                <input type="hidden" name="robot_check" value="0" id="robotInput">
                <button type="button" class="robot-btn" id="robotBtn">
                    <span class="robot-icon">✓</span> Я не робот
                </button>
            </div>
            
            <button type="submit">Зарегистрироваться</button>
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

.errors {
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
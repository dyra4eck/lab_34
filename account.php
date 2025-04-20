<?php
include 'connect.php';
$pageTitle = 'Личный кабинет';
include 'header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$errors = [];
$user = [];
$stmt = $conn->prepare("SELECT * FROM клиенты WHERE client_id = ?");
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$client_id = (int)$_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login']);
    $email = trim($_POST['email']);
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $password = trim($_POST['password']);

    $hashedPassword = !empty($password) ? password_hash($password, PASSWORD_DEFAULT) : $user['password'];
    $stmt = $conn->prepare("UPDATE клиенты SET login=?, email=?, name=?, phone=?, password=? WHERE client_id=?");
    $stmt->bind_param('sssssi', $login, $email, $name, $phone, $hashedPassword, $_SESSION['user_id']);
    if ($stmt->execute()) {
        $_SESSION['user_login'] = $login;
        header('Refresh:0');
    } else {
        $errors[] = 'Ошибка обновления';
    }
}
?>

<div class="main-container">
    <div class="left-column">
        <?php include 'menu.php'; ?>
    </div>
    <div id="content">
        <h2 class="section-title">Личный кабинет</h2>
        <?php if (!empty($errors)): ?>
            <div class="errors"><?= implode('<br>', $errors) ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <input type="text" name="login" value="<?= htmlspecialchars($user['login']) ?>" required>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
            <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
            <input type="tel" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" required>
            <input type="password" name="password" placeholder="Новый пароль">
            <button type="submit">Сохранить</button>
        </form>

        <div class="order-history">
            <h3>История заказов</h3>
            <?php
            // Запрос для получения списка заказов
            $sql_orders = "SELECT 
                            o.order_id,
                            o.order_date,
                            o.status,
                            SUM(t.price * b.quantity) AS total
                          FROM заказы o
                          JOIN бронирование b ON o.order_id = b.order_id
                          JOIN билеты t ON b.ticket_id = t.ticket_id
                          WHERE o.client_id = ?
                          GROUP BY o.order_id
                          ORDER BY o.order_date DESC";

            $stmt_orders = $conn->prepare($sql_orders);
            $stmt_orders->bind_param('i', $client_id);
            $stmt_orders->execute();
            $orders = $stmt_orders->get_result();

            if ($orders->num_rows > 0) {
                while ($order = $orders->fetch_assoc()) {
                    // Запрос для деталей каждого заказа
                    $sql_details = "SELECT 
                                    t.price,
                                    t.seat,
                                    b.quantity,
                                    r.origin,
                                    r.destination,
                                    r.departure_datetime,
                                    r.arrival_datetime,
                                    a.name AS airline
                                  FROM бронирование b
                                  JOIN билеты t ON b.ticket_id = t.ticket_id
                                  JOIN полеты p ON t.flight_instance_id = p.flight_instance_id
                                  JOIN рейсы r ON p.flight_id = r.flight_id
                                  JOIN авиакомпании a ON r.airline_id = a.airline_id
                                  WHERE b.order_id = ?";

                    $stmt_details = $conn->prepare($sql_details);
                    $stmt_details->bind_param('i', $order['order_id']);
                    $stmt_details->execute();
                    $details = $stmt_details->get_result();
            ?>
                    <div class="order-item">
                        <div class="order-header">
                            <span>Заказ #<?= $order['order_id'] ?></span>
                            <span><?= date('d.m.Y H:i', strtotime($order['order_date'])) ?></span>
                            <span>Сумма: <?= number_format($order['total'], 2, ',', ' ') ?> ₽</span>
                            <button class="btn-details" onclick="toggleDetails(<?= $order['order_id'] ?>)">Подробнее ▼</button>
                        </div>
                        <div class="order-details" id="details-<?= $order['order_id'] ?>" style="display: none;">
                            <?php while ($detail = $details->fetch_assoc()): ?>
                                <div class="ticket-item">
                                    <h4><?= htmlspecialchars($detail['origin']) ?> → <?= htmlspecialchars($detail['destination']) ?></h4>
                                    <div class="ticket-info">
                                        <p>Вылет: <?= date('d.m.Y H:i', strtotime($detail['departure_datetime'])) ?></p>
                                        <p>Прилет: <?= date('d.m.Y H:i', strtotime($detail['arrival_datetime'])) ?></p>
                                        <p>Авиакомпания: <?= htmlspecialchars($detail['airline']) ?></p>
                                        <p>Место: <?= htmlspecialchars($detail['seat']) ?></p>
                                        <p>Цена: <?= number_format($detail['price'], 2, ',', ' ') ?> ₽ × <?= $detail['quantity'] ?></p>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
            <?php
                }
            } else {
                echo '<p>У вас нет заказов.</p>';
            }
            ?>
        </div>
    </div>
</div>

<style>
.order-item {
    border: 1px solid #ddd;
    margin-bottom: 15px;
    border-radius: 8px;
}

.order-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px;
    background: #f8f9fa;
    cursor: pointer;
}

.btn-details {
    background: #007bff;
    color: white;
    border: none;
    padding: 6px 12px;
    border-radius: 4px;
    cursor: pointer;
}

.order-details {
    padding: 15px;
    background: white;
    border-top: 1px solid #eee;
}

.ticket-item {
    margin-bottom: 10px;
    padding: 10px;
    border-bottom: 1px solid #eee;
}

.ticket-info {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 10px;
}
</style>

<script>
function toggleDetails(orderId) {
    const details = document.getElementById(`details-${orderId}`);
    const button = document.querySelector(`button[onclick="toggleDetails(${orderId})"]`);
    if (details.style.display === 'none') {
        details.style.display = 'block';
        button.textContent = 'Свернуть ▲';
    } else {
        details.style.display = 'none';
        button.textContent = 'Подробнее ▼';
    }
}
</script>

<?php include 'footer.php'; ?>
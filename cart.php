<?php
session_start();
include 'connect.php';
$pageTitle = 'Корзина';
include 'header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$cartItems = [];
$totalAmount = 0;

if (!empty($_SESSION['cart'])) {
    $flightInstanceIds = [];
    foreach ($_SESSION['cart'] as $item) {
        if (isset($item['flight_instance_id'])) {
            $flightInstanceIds[] = $item['flight_instance_id'];
        }
    }

    // Удаляем ранний выход, чтобы сохранить структуру страницы
    if (!empty($flightInstanceIds)) {
        $placeholders = implode(',', array_fill(0, count($flightInstanceIds), '?'));
        $sql = "SELECT 
                    p.flight_instance_id,
                    r.origin,
                    r.destination,
                    r.departure_datetime,
                    t.ticket_id,
                    t.price
                FROM рейсы r
                JOIN полеты p ON r.flight_id = p.flight_id
                JOIN билеты t ON p.flight_instance_id = t.flight_instance_id
                WHERE p.flight_instance_id IN ($placeholders)";

        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $types = str_repeat('i', count($flightInstanceIds));
            $stmt->bind_param($types, ...$flightInstanceIds);
            $stmt->execute();
            $flightsData = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

            foreach ($_SESSION['cart'] as $cartItem) {
                // Добавляем проверку наличия ключа
                if (isset($cartItem['flight_instance_id'])) {
                    foreach ($flightsData as $flight) {
                        if ($flight['flight_instance_id'] == $cartItem['flight_instance_id']) {
                            $cartItems[] = [
                                'flight_instance_id' => $flight['flight_instance_id'],
                                'ticket_id' => $flight['ticket_id'],
                                'origin' => $flight['origin'],
                                'destination' => $flight['destination'],
                                'price' => $flight['price'],
                                'quantity' => $cartItem['quantity']
                            ];
                            $totalAmount += $flight['price'] * $cartItem['quantity'];
                            break;
                        }
                    }
                }
            }
        }
    }
}
?>

<div class="main-container">
    <div class="left-column">
        <?php include 'menu.php'; ?>
    </div>

    <div id="content">
        <h2 class="section-title">Ваша корзина</h2>
        
        <?php if(!empty($cartItems)): ?>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Рейс</th>
                        <th>Маршрут</th>
                        <th>Цена</th>
                        <th>Количество</th>
                        <th>Итого</th>
                        <th>Удалить</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($cartItems as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['flight_instance_id'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($item['origin']) ?> → <?= htmlspecialchars($item['destination']) ?></td>
                        <td><?= number_format($item['price'], 0, '', ' ') ?> ₽</td>
                        <td><?= htmlspecialchars($item['quantity']) ?></td>
                        <td><?= number_format($item['price'] * $item['quantity'], 0, '', ' ') ?> ₽</td>
                        <td>
                            <a href="remove_from_cart.php?flight_instance_id=<?= $item['flight_instance_id'] ?>" class="btn-remove">✕</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="total-amount">
                Общая сумма: <?= number_format($totalAmount, 0, '', ' ') ?> ₽
            </div>
            <a href="checkout.php" class="btn-checkout">Оформить заказ</a>
        <?php else: ?>
            <div class="empty-cart">Корзина пуста</div>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>
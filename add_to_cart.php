<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$flight_instance_id = (int)$_GET['flight_instance_id'] ?? 0;
$passengers = (int)$_GET['passengers'] ?? 1;

// Получаем ticket_id из таблицы билеты
$sql_ticket = "SELECT ticket_id FROM билеты WHERE flight_instance_id = ? LIMIT 1";
$stmt_ticket = $conn->prepare($sql_ticket);
$stmt_ticket->bind_param('i', $flight_instance_id);
$stmt_ticket->execute();
$ticket_result = $stmt_ticket->get_result();

if ($ticket_result->num_rows === 0) {
    die("Билеты для этого рейса не найдены");
}

$ticket_data = $ticket_result->fetch_assoc();
$ticket_id = $ticket_data['ticket_id'];

// Проверка существования рейса
$sql_check = "SELECT flight_instance_id FROM полеты WHERE flight_instance_id = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param('i', $flight_instance_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows === 0) {
    die("Рейс не найден");
}

// Обновление корзины
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$found = false;
foreach ($_SESSION['cart'] as &$item) {
    if ($item['flight_instance_id'] === $flight_instance_id) {
        $item['quantity'] += $passengers;
        $found = true;
        break;
    }
}

if (!$found) {
    $_SESSION['cart'][] = [
        'flight_instance_id' => $flight_instance_id,
        'ticket_id' => $ticket_id,
        'quantity' => $passengers
    ];
}

header("Location: cart.php");
exit();
?>
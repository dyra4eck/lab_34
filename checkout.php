<?php
session_start();
include 'connect.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (empty($_SESSION['cart'])) {
    $_SESSION['order_error'] = 'Корзина пуста';
    header("Location: cart.php");
    exit();
}

// Получение client_id
$sql_client = "SELECT client_id FROM клиенты WHERE login = ?";
$stmt_client = $conn->prepare($sql_client);
$stmt_client->bind_param('s', $_SESSION['user_login']);
$stmt_client->execute();
$client = $stmt_client->get_result()->fetch_assoc();
$client_id = $client['client_id'] ?? null;

if (!$client_id) {
    $_SESSION['order_error'] = 'Пользователь не найден';
    header("Location: cart.php");
    exit();
}

$conn->begin_transaction();

try {
    // Получаем текущий максимальный order_id
    $sql_max_order = "SELECT MAX(order_id) as max_order FROM заказы";
    $result_max = $conn->query($sql_max_order);
    $row = $result_max->fetch_assoc();
    $order_id = $row['max_order'] ? (int)$row['max_order'] + 1 : 1;

    // Создание заказа с вычисленным order_id
    $sql_order = "INSERT INTO заказы (order_id, client_id, order_date, status) VALUES (?, ?, NOW(), 1)";
    $stmt_order = $conn->prepare($sql_order);
    $stmt_order->bind_param('ii', $order_id, $client_id);
    $stmt_order->execute();

    // Получаем максимальный booking_id
    $sql_max_booking = "SELECT MAX(booking_id) as max_booking FROM бронирование";
    $result_max_booking = $conn->query($sql_max_booking);
    $row_booking = $result_max_booking->fetch_assoc();
    $booking_id = $row_booking['max_booking'] ? (int)$row_booking['max_booking'] : 0;

    // Добавляем записи в бронирование для каждого билета в корзине
    foreach ($_SESSION['cart'] as $item) {
        $booking_id++; // Увеличиваем ID для новой записи
        $ticket_id = $item['ticket_id'];
        $quantity = $item['quantity'];

        $sql_booking = "INSERT INTO бронирование (booking_id, order_id, ticket_id, quantity) 
                        VALUES (?, ?, ?, ?)";
        $stmt_booking = $conn->prepare($sql_booking);
        $stmt_booking->bind_param('iiii', $booking_id, $order_id, $ticket_id, $quantity);
        $stmt_booking->execute();
    } 
    
    $conn->commit();
    unset($_SESSION['cart']);
    $_SESSION['order_success'] = "Заказ №$order_id успешно оформлен!";

} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['order_error'] = 'Ошибка: ' . $e->getMessage();
    header("Location: cart.php");
    exit();
}

header("Location: account.php");
exit();
?>
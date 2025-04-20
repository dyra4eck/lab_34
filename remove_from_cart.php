<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$flight_instance_id = isset($_GET['flight_instance_id']) ? (int)$_GET['flight_instance_id'] : 0;

if ($flight_instance_id > 0 && !empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $key => $item) {
        if (isset($item['flight_instance_id']) && $item['flight_instance_id'] === $flight_instance_id) {
            unset($_SESSION['cart'][$key]);
            break;
        }
    }
    $_SESSION['cart'] = array_values($_SESSION['cart']);
}

header("Location: cart.php");
exit();
?>
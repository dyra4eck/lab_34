<?php 
// Проверяем, не активна ли сессия перед запуском
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'connect.php'; 
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AeroBook - <?= $pageTitle ?? 'Бронирование авиабилетов' ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div id="header">
        <h1 class="title">
            <img src="papich.gif" alt="Логотип" class="logo">
            AeroBook
            <span class="subtitle">Система бронирования авиабилетов</span>
        </h1>
    </div>
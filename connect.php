<?php
$host = 'localhost';  // Хост БД
$user = 'root';       // Логин XAMPP по умолчанию
$pass = '';           // Пароль по умолчанию (пустая строка)
$db = 'web_web'; // Имя БД

// Создаём подключение
$conn = new mysqli($host, $user, $pass, $db);

// Проверка соединения
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

// Устанавливаем кодировку
$conn->set_charset("utf8mb4");
?>
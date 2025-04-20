<?php
session_start();
session_unset();
session_destroy();
// Удаляем cookie сессии
setcookie(session_name(), '', time() - 3600, '/');
header("Location: main_page.php");
exit();
?>
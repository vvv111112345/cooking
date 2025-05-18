<?php
require_once 'config.php';

session_destroy();
header("Location: index.php");
exit();

session_start();
$_SESSION['logout_message'] = "Вы успешно вышли";
session_destroy();
header("Location: index.php");

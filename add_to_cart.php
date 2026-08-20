<?php

session_start();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: product.php'); 
    exit;
}

$product_id = (int)$_GET['id'];

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (array_key_exists($product_id, $_SESSION['cart'])) {
    $_SESSION['cart'][$product_id] += 1;
} else {
    $_SESSION['cart'][$product_id] = 1;
}

header('Location: cart.php');
exit;
?>
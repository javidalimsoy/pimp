<?php
global $conn;
session_start();
include '../src/db.php';

if (!isset($_SESSION['session_id'])) {
    $_SESSION['session_id'] = uniqid(); // Generate unique session ID for the user
}
$session_id = $_SESSION['session_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];

    // Check if the product is already in the cart
    $stmt = $conn->prepare("SELECT * FROM cart WHERE session_id = :session_id AND product_id = :product_id");
    $stmt->execute(['session_id' => $session_id, 'product_id' => $product_id]);
    $cart_item = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cart_item) {
        // If product exists, increase quantity
        $stmt = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE id = :id");
        $stmt->execute(['id' => $cart_item['id']]);
    } else {
        // Otherwise, add new product to cart
        $stmt = $conn->prepare("INSERT INTO cart (session_id, product_id, quantity) VALUES (:session_id, :product_id, 1)");
        $stmt->execute(['session_id' => $session_id, 'product_id' => $product_id]);
    }

    header("Location: ../public/cart.php");
    exit;
}
?>
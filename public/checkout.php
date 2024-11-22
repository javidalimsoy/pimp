<?php
global $conn;
session_start();
include '../src/db.php';

if (!isset($_SESSION['session_id'])) {
    $_SESSION['session_id'] = uniqid();
}
$session_id = $_SESSION['session_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_name = $_POST['customer_name'];
    $address = $_POST['address'];

    // Calculate total
    $stmt = $conn->prepare("
        SELECT p.name, p.price, c.quantity 
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        WHERE c.session_id = :session_id
    ");
    $stmt->execute(['session_id' => $session_id]);
    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $total = 0;
    foreach ($cart_items as $item) {
        $total += $item['price'] * $item['quantity'];
    }

    // Insert into orders
    $stmt = $conn->prepare("
        INSERT INTO orders (session_id, customer_name, address, total) 
        VALUES (:session_id, :customer_name, :address, :total)
    ");
    $stmt->execute([
        'session_id' => $session_id,
        'customer_name' => $customer_name,
        'address' => $address,
        'total' => $total
    ]);
    $order_id = $conn->lastInsertId();

    // Insert into order_items
    foreach ($cart_items as $item) {
        $stmt = $conn->prepare("
            INSERT INTO order_items (order_id, product_name, quantity, price) 
            VALUES (:order_id, :product_name, :quantity, :price)
        ");
        $stmt->execute([
            'order_id' => $order_id,
            'product_name' => $item['name'],
            'quantity' => $item['quantity'],
            'price' => $item['price']
        ]);
    }

    // Clear cart
    $stmt = $conn->prepare("DELETE FROM cart WHERE session_id = :session_id");
    $stmt->execute(['session_id' => $session_id]);

    header("Location: confirmation.php?order_id=$order_id");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
    <h1>Checkout</h1>
</header>
<div class="container">
    <form action="checkout.php" method="POST">
        <label for="customer_name">Full Name:</label>
        <input type="text" name="customer_name" id="customer_name" required>

        <label for="address">Address:</label>
        <textarea name="address" id="address" rows="3" required></textarea>

        <button type="submit">Place Order</button>
    </form>
</div>
<footer>
    Rolex Shop Honeypot | All Rights Reserved
</footer>
</body>
</html>

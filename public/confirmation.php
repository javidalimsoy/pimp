<?php
session_start();
include '../src/db.php';

$order_id = $_GET['order_id'];

$stmt = $conn->prepare("SELECT * FROM orders WHERE id = :order_id");
$stmt->execute(['order_id' => $order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT * FROM order_items WHERE order_id = :order_id");
$stmt->execute(['order_id' => $order_id]);
$order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
    <h1>Order Confirmation</h1>
</header>
<div class="container">
    <h2>Thank you, <?= htmlspecialchars($order['customer_name']) ?>!</h2>
    <p>Your order has been placed successfully.</p>
    <h3>Order Details:</h3>
    <p><strong>Order ID:</strong> <?= $order['id'] ?></p>
    <p><strong>Phone:</strong> <?= htmlspecialchars($order['phone']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></p>
    <p><strong>Address:</strong> <?= htmlspecialchars($order['address']) ?></p>
    <p><strong>Delivery Notes:</strong> <?= htmlspecialchars($order['delivery_notes']) ?></p>
    <p><strong>Total:</strong> $<?= number_format($order['total'], 2) ?></p>

    <h3>Items:</h3>
    <ul>
        <?php foreach ($order_items as $item): ?>
            <li><?= $item['product_name'] ?> (x<?= $item['quantity'] ?>) - $<?= number_format($item['price'], 2) ?></li>
        <?php endforeach; ?>
    </ul>
    <a href="index.php"><button type="button">Return Home</button></a>
</div>
<footer>
    Rolex Shop Honeypot | All Rights Reserved
</footer>
</body>
</html>

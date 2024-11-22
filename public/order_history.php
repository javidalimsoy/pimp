<?php
global $conn;
session_start();
include '../src/db.php';

if (!isset($_SESSION['session_id'])) {
    $_SESSION['session_id'] = uniqid();
}
$session_id = $_SESSION['session_id'];

// Fetch orders for the current session
$stmt = $conn->prepare("
    SELECT * 
    FROM orders 
    WHERE session_id = :session_id
    ORDER BY created_at DESC
");
$stmt->execute(['session_id' => $session_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
    <h1>Your Order History</h1>
</header>
<nav>
    <a href="index.php">Home</a>
    <a href="products.php">Products</a>
    <a href="cart.php">Cart</a>
    <a href="order_history.php">Order History</a>
</nav>
<div class="container">
    <?php if ($orders): ?>
        <?php foreach ($orders as $order): ?>
            <div class="order">
                <h3>Order ID: <?= $order['id'] ?></h3>
                <p><strong>Placed on:</strong> <?= $order['created_at'] ?></p>
                <p><strong>Total:</strong> $<?= number_format($order['total'], 2) ?></p>
                <p><strong>Address:</strong> <?= htmlspecialchars($order['address']) ?></p>
                <p><strong>Delivery Notes:</strong> <?= htmlspecialchars($order['delivery_notes']) ?></p>
                <h4>Items:</h4>
                <ul>
                    <?php
                    // Fetch items for this order
                    $stmt = $conn->prepare("
                            SELECT * 
                            FROM order_items 
                            WHERE order_id = :order_id
                        ");
                    $stmt->execute(['order_id' => $order['id']]);
                    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($items as $item):
                        ?>
                        <li>
                            <?= htmlspecialchars($item['product_name']) ?>
                            (x<?= $item['quantity'] ?>) -
                            $<?= number_format($item['price'], 2) ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <hr>
        <?php endforeach; ?>
    <?php else: ?>
        <p>You have no past orders.</p>
        <a href="products.php"><button>Shop Now</button></a>
    <?php endif; ?>
</div>
<footer>
    Rolex Shop Honeypot | All Rights Reserved
</footer>
</body>
</html>

<?php
global $conn;
session_start();
include '../src/db.php';

if (!isset($_SESSION['session_id'])) {
    $_SESSION['session_id'] = uniqid();
}
$session_id = $_SESSION['session_id'];

// Fetch cart items
$stmt = $conn->prepare("
    SELECT c.id, p.name, p.price, c.quantity 
    FROM cart c 
    JOIN products p ON c.product_id = p.id 
    WHERE c.session_id = :session_id
");
$stmt->execute(['session_id' => $session_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate total
$total = 0;
foreach ($cart_items as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
    <h1>Your Cart</h1>
</header>
<nav>
    <a href="index.php">Home</a>
    <a href="products.php">Products</a>
    <a href="checkout.php">Checkout</a>
</nav>
<div class="container">
    <?php if ($cart_items): ?>
        <table>
            <thead>
            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($cart_items as $item): ?>
                <tr>
                    <td><?= $item['name'] ?></td>
                    <td>$<?= number_format($item['price'], 2) ?></td>
                    <td><?= $item['quantity'] ?></td>
                    <td>$<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <p><strong>Grand Total:</strong> $<?= number_format($total, 2) ?></p>
        <a href="checkout.php"><button>Proceed to Checkout</button></a>
    <?php else: ?>
        <p>Your cart is empty.</p>
        <a href="products.php"><button>Browse Products</button></a>
        <a href="products.php"><button type="button">Back to Products</button></a>

    <?php endif; ?>
</div>
<footer>
    Rolex Shop Honeypot | All Rights Reserved
</footer>
</body>
</html>
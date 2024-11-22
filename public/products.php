<?php global $conn;
include '../src/db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rolex Products</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
    <h1>Rolex Collection</h1>
</header>
<nav>
    <a href="index.php">Home</a>
    <a href="products.php">Products</a>
    <a href="cart.php">Cart</a>
</nav>
<div class="container">
    <div class="product-grid">
        <?php
        $stmt = $conn->query("SELECT * FROM products");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "
                <div class='product'>
                    <img src='{$row['image_url']}' alt='{$row['name']}' class='product-image'>
                    <h3>{$row['name']}</h3>
                    <p>{$row['description']}</p>
                    <p><strong>\${$row['price']}</strong></p>
                    <form action='../src/cart.php' method='POST'>
                        <input type='hidden' name='product_id' value='{$row['id']}'>
                        <button type='submit'>Add to Cart</button>
                    </form>
                </div>";
        }
        ?>
    </div>
    <a href="index.php"><button type="button">Back to Home</button></a>
</div>

<footer>
    Rolex Shop Honeypot | All Rights Reserved
</footer>
</body>
</html>
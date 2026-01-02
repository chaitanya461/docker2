
<?php
require_once 'config/database.php';
require_once 'includes/header.php';

$conn = getDatabaseConnection();

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header('Location: /login.php?redirect=cart');
    exit;
}

$user_id = $_SESSION['user_id'];

// Add to cart
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity'] ?? 1);
    
    // Check if item already in cart
    $check = $conn->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
    $check->bind_param('ii', $user_id, $product_id);
    $check->execute();
    $existing = $check->get_result();
    
    if($existing->num_rows > 0) {
        // Update quantity
        $update = $conn->prepare("UPDATE cart SET quantity = quantity + ? WHERE user_id = ? AND product_id = ?");
        $update->bind_param('iii', $quantity, $user_id, $product_id);
        $update->execute();
    } else {
        // Add new item
        $insert = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $insert->bind_param('iii', $user_id, $product_id, $quantity);
        $insert->execute();
    }
    
    header('Location: /cart.php');
    exit;
}

// Remove from cart
if(isset($_GET['remove'])) {
    $remove = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
    $remove->bind_param('ii', $_GET['remove'], $user_id);
    $remove->execute();
    header('Location: /cart.php');
    exit;
}

// Update quantity
if(isset($_POST['update_quantity'])) {
    $cart_id = intval($_POST['cart_id']);
    $quantity = intval($_POST['quantity']);
    
    if($quantity > 0) {
        $update = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
        $update->bind_param('iii', $quantity, $cart_id, $user_id);
        $update->execute();
    }
    header('Location: /cart.php');
    exit;
}

// Get cart items
$cartItems = $conn->query("
    SELECT c.*, p.name, p.price, p.discount_price, p.image_url, p.stock_quantity 
    FROM cart c 
    JOIN products p ON c.product_id = p.id 
    WHERE c.user_id = $user_id
");

$total = 0;
?>

<section class="cart-page">
    <h2>Your Shopping Cart</h2>
    
    <?php if($cartItems->num_rows > 0): ?>
    <div class="cart-container">
        <div class="cart-items">
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($item = $cartItems->fetch_assoc()): 
                        $price = $item['discount_price'] ?: $item['price'];
                        $itemTotal = $price * $item['quantity'];
                        $total += $itemTotal;
                    ?>
                    <tr>
                        <td>
                            <div class="cart-product-info">
                                <img src="<?php echo $item['image_url'] ?: '/images/default-phone.jpg'; ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                <div>
                                    <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                                    <p>Stock: <?php echo $item['stock_quantity']; ?></p>
                                </div>
                            </div>
                        </td>
                        <td>$<?php echo number_format($price, 2); ?></td>
                        <td>
                            <form method="POST" class="quantity-form">
                                <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                                <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo min($item['stock_quantity'], 10); ?>">
                                <button type="submit" name="update_quantity" class="btn btn-sm">Update</button>
                            </form>
                        </td>
                        <td>$<?php echo number_format($itemTotal, 2); ?></td>
                        <td>
                            <a href="/cart.php?remove=<?php echo $item['id']; ?>" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash"></i> Remove
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        
        <div class="cart-summary">
            <h3>Order Summary</h3>
            <div class="summary-details">
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span>$<?php echo number_format($total, 2); ?></span>
                </div>
                <div class="summary-row">
                    <span>Shipping</span>
                    <span>$10.00</span>
                </div>
                <div class="summary-row">
                    <span>Tax</span>
                    <span>$<?php echo number_format($total * 0.08, 2); ?></span>
                </div>
                <div class="summary-row total">
                    <span>Total</span>
                    <span>$<?php echo number_format($total + 10 + ($total * 0.08), 2); ?></span>
                </div>
            </div>
            
            <div class="cart-actions">
                <a href="/products.php" class="btn btn-secondary">Continue Shopping</a>
                <a href="/checkout.php" class="btn btn-primary">Proceed to Checkout</a>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="empty-cart">
        <i class="fas fa-shopping-cart fa-3x"></i>
        <h3>Your cart is empty</h3>
        <p>Add some products to your cart to see them here.</p>
        <a href="/products.php" class="btn btn-primary">Browse Products</a>
    </div>
    <?php endif; ?>
</section>

<?php
$conn->close();
require_once 'includes/footer.php';
?>

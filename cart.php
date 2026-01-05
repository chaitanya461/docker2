<?php
// cart.php - Fixed version with proper session handling

// 1. Include init.php FIRST (before any output)
require_once 'config/init.php';

// 2. Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    // Use our redirect function instead of header()
    redirect('/auth/login.php?redirect=cart');
}

$user_id = $_SESSION['user_id'];
$conn = getDatabaseConnection();

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
        $update->close();
    } else {
        // Add new item
        $insert = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $insert->bind_param('iii', $user_id, $product_id, $quantity);
        $insert->execute();
        $insert->close();
    }
    
    $check->close();
    redirect('/cart.php');
    exit;
}

// Remove from cart
if(isset($_GET['remove'])) {
    $remove_id = intval($_GET['remove']);
    $remove = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
    $remove->bind_param('ii', $remove_id, $user_id);
    $remove->execute();
    $remove->close();
    
    redirect('/cart.php');
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
        $update->close();
    }
    
    redirect('/cart.php');
    exit;
}

// Get cart items with prepared statement
$stmt = $conn->prepare("
    SELECT c.*, p.name, p.price, p.discount_price, p.image_url, p.stock_quantity 
    FROM cart c 
    JOIN products p ON c.product_id = p.id 
    WHERE c.user_id = ?
");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$cartItems = $stmt->get_result();

$total = 0;

// Now include header AFTER all PHP logic
$page_title = "Shopping Cart";
require_once 'includes/header.php';
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
                                <img src="<?php echo htmlspecialchars($item['image_url'] ?: '/images/default-phone.jpg'); ?>" 
                                     alt="<?php echo htmlspecialchars($item['name']); ?>"
                                     onerror="this.src='/images/default-phone.jpg'">
                                <div>
                                    <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                                    <p class="stock-info">
                                        <?php if($item['stock_quantity'] > 0): ?>
                                            <span class="in-stock">In Stock (<?php echo $item['stock_quantity']; ?> available)</span>
                                        <?php else: ?>
                                            <span class="out-of-stock">Out of Stock</span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td class="price-cell">
                            <?php if($item['discount_price']): ?>
                                <span class="original-price">$<?php echo number_format($item['price'], 2); ?></span>
                                <span class="current-price">$<?php echo number_format($item['discount_price'], 2); ?></span>
                            <?php else: ?>
                                <span class="current-price">$<?php echo number_format($item['price'], 2); ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form method="POST" class="quantity-form">
                                <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                                <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" 
                                       min="1" max="<?php echo min($item['stock_quantity'], 10); ?>"
                                       <?php echo $item['stock_quantity'] == 0 ? 'disabled' : ''; ?>>
                                <button type="submit" name="update_quantity" class="btn btn-sm" 
                                        <?php echo $item['stock_quantity'] == 0 ? 'disabled' : ''; ?>>
                                    Update
                                </button>
                            </form>
                        </td>
                        <td class="total-cell">$<?php echo number_format($itemTotal, 2); ?></td>
                        <td>
                            <a href="/cart.php?remove=<?php echo $item['id']; ?>" class="btn btn-danger btn-sm"
                               onclick="return confirm('Are you sure you want to remove this item?');">
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
                    <span>
                        <?php if($total >= 100): ?>
                            <span class="free-shipping">FREE</span>
                        <?php else: ?>
                            $10.00
                        <?php endif; ?>
                    </span>
                </div>
                <div class="summary-row">
                    <span>Tax (8%)</span>
                    <span>$<?php echo number_format($total * 0.08, 2); ?></span>
                </div>
                <div class="summary-row total">
                    <span>Total</span>
                    <span>
                        $<?php 
                        $shipping = ($total >= 100) ? 0 : 10;
                        $tax = $total * 0.08;
                        echo number_format($total + $shipping + $tax, 2); 
                        ?>
                    </span>
                </div>
            </div>
            
            <div class="shipping-note">
                <?php if($total >= 100): ?>
                    <p><i class="fas fa-check-circle"></i> You qualify for free shipping!</p>
                <?php else: ?>
                    <p>Add $<?php echo number_format(100 - $total, 2); ?> more to get free shipping</p>
                <?php endif; ?>
            </div>
            
            <div class="cart-actions">
                <a href="/products.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Continue Shopping
                </a>
                <?php if($cartItems->num_rows > 0): ?>
                    <a href="#" class="btn btn-primary">
                        <i class="fas fa-lock"></i> Proceed to Checkout
                    </a>
                <?php endif; ?>
            </div>
            
            <div class="payment-methods">
                <p><strong>Accepted Payment Methods:</strong></p>
                <div class="payment-icons">
                    <i class="fab fa-cc-visa"></i>
                    <i class="fab fa-cc-mastercard"></i>
                    <i class="fab fa-cc-amex"></i>
                    <i class="fab fa-cc-paypal"></i>
                    <i class="fab fa-cc-apple-pay"></i>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="empty-cart">
        <div class="empty-cart-icon">
            <i class="fas fa-shopping-cart"></i>
        </div>
        <h3>Your cart is empty</h3>
        <p>Looks like you haven't added any products to your cart yet.</p>
        <div class="empty-cart-actions">
            <a href="/products.php" class="btn btn-primary">
                <i class="fas fa-store"></i> Browse Products
            </a>
            <a href="/products.php?category=smartphones" class="btn btn-outline">
                <i class="fas fa-mobile-alt"></i> View Smartphones
            </a>
            <a href="/products.php?deal=hot" class="btn btn-outline">
                <i class="fas fa-fire"></i> Hot Deals
            </a>
        </div>
    </div>
    <?php endif; ?>
</section>

<?php
// Free result sets
if ($cartItems) {
    $cartItems->free();
}

if (isset($stmt)) {
    $stmt->close();
}

// DO NOT close the connection here - let footer.php handle it
// $conn->close();

require_once 'includes/footer.php';
?>

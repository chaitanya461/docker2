
<?php
require_once 'config/database.php';
require_once 'includes/header.php';

$conn = getDatabaseConnection();

$slug = $_GET['slug'] ?? '';

if(!$slug) {
    header('Location: /products.php');
    exit;
}

// Get product details
$stmt = $conn->prepare("
    SELECT p.*, c.name as category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    WHERE p.slug = ?
");
$stmt->bind_param('s', $slug);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if(!$product) {
    echo "<p>Product not found.</p>";
    require_once 'includes/footer.php';
    exit;
}

// Get related products
$related = $conn->query("
    SELECT * FROM products 
    WHERE category_id = {$product['category_id']} 
    AND id != {$product['id']} 
    LIMIT 4
");
?>

<section class="product-detail">
    <div class="product-images">
        <div class="main-image">
            <img src="<?php echo $product['image_url'] ?: '/images/default-phone.jpg'; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
        </div>
    </div>
    
    <div class="product-info">
        <h1><?php echo htmlspecialchars($product['name']); ?></h1>
        <p class="product-meta">
            <span>Brand: <?php echo htmlspecialchars($product['brand']); ?></span>
            <span>Model: <?php echo htmlspecialchars($product['model']); ?></span>
            <span>Category: <?php echo $product['category_name']; ?></span>
        </p>
        
        <div class="price-section">
            <?php if($product['discount_price']): ?>
                <div class="discount-price">
                    <span class="original">$<?php echo number_format($product['price'], 2); ?></span>
                    <span class="current">$<?php echo number_format($product['discount_price'], 2); ?></span>
                    <span class="save">Save <?php echo round((($product['price'] - $product['discount_price']) / $product['price']) * 100); ?>%</span>
                </div>
            <?php else: ?>
                <div class="price">$<?php echo number_format($product['price'], 2); ?></div>
            <?php endif; ?>
        </div>
        
        <div class="stock-info">
            <?php if($product['stock_quantity'] > 0): ?>
                <span class="in-stock"><i class="fas fa-check-circle"></i> In Stock</span>
                <span class="stock-quantity">(<?php echo $product['stock_quantity']; ?> available)</span>
            <?php else: ?>
                <span class="out-of-stock"><i class="fas fa-times-circle"></i> Out of Stock</span>
            <?php endif; ?>
        </div>
        
        <div class="product-description">
            <h3>Description</h3>
            <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
        </div>
        
        <?php if($product['specifications']): 
            $specs = json_decode($product['specifications'], true);
            if($specs): ?>
            <div class="specifications">
                <h3>Specifications</h3>
                <table>
                    <?php foreach($specs as $key => $value): ?>
                    <tr>
                        <th><?php echo htmlspecialchars($key); ?></th>
                        <td><?php echo htmlspecialchars($value); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
            <?php endif;
        endif; ?>
        
        <div class="action-buttons">
            <?php if($product['stock_quantity'] > 0): ?>
                <form action="/cart.php" method="POST" class="add-to-cart-form">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <div class="quantity-selector">
                        <label for="quantity">Quantity:</label>
                        <input type="number" name="quantity" value="1" min="1" max="<?php echo min($product['stock_quantity'], 10); ?>">
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-cart-plus"></i> Add to Cart
                    </button>
                </form>
            <?php else: ?>
                <button class="btn btn-secondary btn-lg" disabled>Out of Stock</button>
                <button class="btn btn-outline">Notify Me When Available</button>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Related Products -->
<section class="related-products">
    <h2>Related Products</h2>
    <div class="products-grid">
        <?php while($relatedProduct = $related->fetch_assoc()): ?>
        <div class="product-card">
            <div class="product-image">
                <img src="<?php echo $relatedProduct['image_url'] ?: '/images/default-phone.jpg'; ?>" alt="<?php echo htmlspecialchars($relatedProduct['name']); ?>">
            </div>
            <div class="product-info">
                <h3><?php echo htmlspecialchars($relatedProduct['name']); ?></h3>
                <div class="price">
                    <?php if($relatedProduct['discount_price']): ?>
                        <span class="current-price">$<?php echo number_format($relatedProduct['discount_price'], 2); ?></span>
                    <?php else: ?>
                        <span class="current-price">$<?php echo number_format($relatedProduct['price'], 2); ?></span>
                    <?php endif; ?>
                </div>
                <a href="/product-detail.php?slug=<?php echo $relatedProduct['slug']; ?>" class="btn btn-secondary">View Details</a>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</section>

<?php
$stmt->close();
$conn->close();
require_once 'includes/footer.php';
?>


<?php
require_once 'config/database.php';
require_once 'includes/header.php';

$conn = getDatabaseConnection();

// Get featured products
$featuredProducts = $conn->query("
    SELECT p.*, c.name as category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    WHERE p.stock_quantity > 0 AND p.is_featured = TRUE
    ORDER BY p.created_at DESC 
    LIMIT 6
");

// Get categories with product counts
$categories = $conn->query("
    SELECT c.*, COUNT(p.id) as product_count 
    FROM categories c 
    LEFT JOIN products p ON c.id = p.category_id 
    GROUP BY c.id 
    ORDER BY c.name
");

// Get latest products
$latestProducts = $conn->query("
    SELECT * FROM products 
    WHERE stock_quantity > 0 
    ORDER BY created_at DESC 
    LIMIT 4
");

// Get top-selling products (based on order items)
$topSelling = $conn->query("
    SELECT p.*, SUM(oi.quantity) as total_sold
    FROM products p
    LEFT JOIN order_items oi ON p.id = oi.product_id
    GROUP BY p.id
    ORDER BY total_sold DESC
    LIMIT 4
");
?>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-content">
        <h1>Discover Amazing Smartphones</h1>
        <p>Find the perfect phone with the best deals and latest technology</p>
        <div class="hero-buttons">
            <a href="/products.php" class="btn btn-primary">Shop Now</a>
            <a href="/products.php?category=smartphones" class="btn btn-outline">View Smartphones</a>
        </div>
    </div>
</section>

<!-- Featured Products -->
<section class="featured-products">
    <div class="section-header">
        <h2><i class="fas fa-star"></i> Featured Products</h2>
        <a href="/products.php?featured=true" class="view-all">View All →</a>
    </div>
    <div class="products-grid">
        <?php while($product = $featuredProducts->fetch_assoc()): ?>
        <div class="product-card">
            <div class="product-image">
                <img src="<?php echo $product['image_url'] ?: '/images/default-phone.jpg'; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                <?php if($product['discount_price']): ?>
                    <span class="discount-badge">Save <?php echo round((($product['price'] - $product['discount_price']) / $product['price']) * 100); ?>%</span>
                <?php endif; ?>
                <?php if($product['stock_quantity'] < 10 && $product['stock_quantity'] > 0): ?>
                    <span class="stock-badge">Only <?php echo $product['stock_quantity']; ?> left</span>
                <?php endif; ?>
            </div>
            <div class="product-info">
                <div class="product-header">
                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                    <p class="product-category"><?php echo $product['category_name']; ?></p>
                </div>
                <p class="product-brand"><?php echo htmlspecialchars($product['brand']); ?> - <?php echo htmlspecialchars($product['model']); ?></p>
                <div class="price">
                    <?php if($product['discount_price']): ?>
                        <span class="original-price">$<?php echo number_format($product['price'], 2); ?></span>
                        <span class="current-price">$<?php echo number_format($product['discount_price'], 2); ?></span>
                    <?php else: ?>
                        <span class="current-price">$<?php echo number_format($product['price'], 2); ?></span>
                    <?php endif; ?>
                </div>
                <div class="product-actions">
                    <a href="/product-detail.php?slug=<?php echo $product['slug']; ?>" class="btn btn-secondary">View Details</a>
                    <?php if($product['stock_quantity'] > 0): ?>
                        <form action="/cart.php" method="POST" class="add-to-cart-form">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-cart-plus"></i> Add
                            </button>
                        </form>
                    <?php else: ?>
                        <button class="btn btn-secondary" disabled>Out of Stock</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</section>

<!-- Categories -->
<section class="categories">
    <div class="section-header">
        <h2><i class="fas fa-list"></i> Browse Categories</h2>
    </div>
    <div class="categories-grid">
        <?php while($category = $categories->fetch_assoc()): ?>
        <a href="/products.php?category=<?php echo $category['slug']; ?>" class="category-card">
            <div class="category-icon">
                <i class="fas fa-mobile-alt"></i>
            </div>
            <div class="category-info">
                <h3><?php echo htmlspecialchars($category['name']); ?></h3>
                <p><?php echo $category['product_count']; ?> products</p>
            </div>
        </a>
        <?php endwhile; ?>
    </div>
</section>

<!-- Latest Products -->
<section class="latest-products">
    <div class="section-header">
        <h2><i class="fas fa-bolt"></i> New Arrivals</h2>
        <a href="/products.php?sort=newest" class="view-all">View All →</a>
    </div>
    <div class="products-grid">
        <?php while($product = $latestProducts->fetch_assoc()): ?>
        <div class="product-card">
            <div class="product-image">
                <img src="<?php echo $product['image_url'] ?: '/images/default-phone.jpg'; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                <span class="new-badge">NEW</span>
            </div>
            <div class="product-info">
                <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                <div class="price">
                    <span class="current-price">$<?php echo number_format($product['price'], 2); ?></span>
                </div>
                <a href="/product-detail.php?slug=<?php echo $product['slug']; ?>" class="btn btn-secondary">View Details</a>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</section>

<!-- Stats Section -->
<section class="stats">
    <div class="stats-container">
        <div class="stat-item">
            <i class="fas fa-box-open"></i>
            <h3>5000+</h3>
            <p>Products Available</p>
        </div>
        <div class="stat-item">
            <i class="fas fa-shipping-fast"></i>
            <h3>Free</h3>
            <p>Shipping Over $100</p>
        </div>
        <div class="stat-item">
            <i class="fas fa-shield-alt"></i>
            <h3>1 Year</h3>
            <p>Warranty on All Products</p>
        </div>
        <div class="stat-item">
            <i class="fas fa-headset"></i>
            <h3>24/7</h3>
            <p>Customer Support</p>
        </div>
    </div>
</section>

<?php
$conn->close();
require_once 'includes/footer.php';
?>

<?php
// index.php - Homepage
require_once 'config/init.php';

$page_title = "Home - Best Phone Store";
require_once 'includes/header.php';

$conn = getDatabaseConnection();

// Get featured products
$featuredProducts = $conn->query("
    SELECT p.*, c.name as category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    WHERE p.stock_quantity > 0 AND p.is_featured = TRUE
    ORDER BY p.created_at DESC 
    LIMIT 8
");

// Get categories
$categories = $conn->query("
    SELECT c.*, COUNT(p.id) as product_count 
    FROM categories c 
    LEFT JOIN products p ON c.id = p.category_id 
    GROUP BY c.id 
    ORDER BY c.name 
    LIMIT 6
");

// Get latest products
$latestProducts = $conn->query("
    SELECT * FROM products 
    WHERE stock_quantity > 0 
    ORDER BY created_at DESC 
    LIMIT 6
");

// Get discounted products
$discountedProducts = $conn->query("
    SELECT * FROM products 
    WHERE discount_price IS NOT NULL 
    AND stock_quantity > 0 
    ORDER BY (price - discount_price) DESC 
    LIMIT 4
");
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="hero-slider">
        <div class="hero-slide active">
            <div class="hero-content">
                <h1>Latest Smartphones</h1>
                <p>Discover the newest models with cutting-edge technology</p>
                <a href="/products.php?sort=newest" class="btn btn-primary btn-lg">Shop Now</a>
            </div>
            <div class="hero-image">
                <img src="https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?auto=format&fit=crop&w=800" alt="Smartphones">
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="categories-section">
    <div class="section-header">
        <h2>Browse Categories</h2>
        <a href="/products.php" class="view-all">View All</a>
    </div>
    <div class="categories-grid">
        <?php while($category = $categories->fetch_assoc()): ?>
        <a href="/products.php?category=<?php echo urlencode($category['slug']); ?>" class="category-card">
            <div class="category-icon">
                <i class="fas fa-mobile-alt"></i>
            </div>
            <h3><?php echo htmlspecialchars($category['name']); ?></h3>
            <p><?php echo $category['product_count']; ?> Products</p>
        </a>
        <?php endwhile; ?>
    </div>
</section>

<!-- Featured Products -->
<section class="featured-products">
    <div class="section-header">
        <h2>Featured Products</h2>
        <a href="/products.php?featured=true" class="view-all">View All</a>
    </div>
    <div class="products-grid">
        <?php while($product = $featuredProducts->fetch_assoc()): 
            $discount = $product['discount_price'] ? round((($product['price'] - $product['discount_price']) / $product['price']) * 100) : 0;
        ?>
        <div class="product-card">
            <div class="product-image">
                <img src="<?php echo htmlspecialchars($product['image_url'] ?: 'https://via.placeholder.com/300x300'); ?>" 
                     alt="<?php echo htmlspecialchars($product['name']); ?>">
                <?php if($discount > 0): ?>
                    <span class="badge discount">-<?php echo $discount; ?>%</span>
                <?php endif; ?>
                <?php if($product['stock_quantity'] < 5): ?>
                    <span class="badge low-stock">Low Stock</span>
                <?php endif; ?>
                <div class="product-actions">
                    <button class="action-btn wishlist" data-product-id="<?php echo $product['id']; ?>">
                        <i class="far fa-heart"></i>
                    </button>
                    <button class="action-btn compare">
                        <i class="fas fa-exchange-alt"></i>
                    </button>
                    <button class="action-btn quick-view" data-product-id="<?php echo $product['id']; ?>">
                        <i class="far fa-eye"></i>
                    </button>
                </div>
            </div>
            <div class="product-info">
                <span class="product-category"><?php echo htmlspecialchars($product['category_name']); ?></span>
                <h3><a href="/product-detail.php?slug=<?php echo urlencode($product['slug']); ?>"><?php echo htmlspecialchars($product['name']); ?></a></h3>
                <p class="product-brand"><?php echo htmlspecialchars($product['brand']); ?></p>
                <div class="product-price">
                    <?php if($product['discount_price']): ?>
                        <span class="current-price">$<?php echo formatPrice($product['discount_price']); ?></span>
                        <span class="original-price">$<?php echo formatPrice($product['price']); ?></span>
                    <?php else: ?>
                        <span class="current-price">$<?php echo formatPrice($product['price']); ?></span>
                    <?php endif; ?>
                </div>
                <div class="product-rating">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="far fa-star"></i>
                    <span>(4.0)</span>
                </div>
                <div class="product-cta">
                    <?php if($product['stock_quantity'] > 0): ?>
                        <button class="btn btn-primary add-to-cart" data-product-id="<?php echo $product['id']; ?>">
                            <i class="fas fa-cart-plus"></i> Add to Cart
                        </button>
                    <?php else: ?>
                        <button class="btn btn-outline" disabled>Out of Stock</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</section>

<!-- Latest Products -->
<section class="latest-products">
    <div class="section-header">
        <h2>New Arrivals</h2>
        <a href="/products.php?sort=newest" class="view-all">View All</a>
    </div>
    <div class="products-grid">
        <?php while($product = $latestProducts->fetch_assoc()): ?>
        <div class="product-card">
            <div class="product-image">
                <img src="<?php echo htmlspecialchars($product['image_url'] ?: 'https://via.placeholder.com/300x300'); ?>" 
                     alt="<?php echo htmlspecialchars($product['name']); ?>">
                <span class="badge new">NEW</span>
            </div>
            <div class="product-info">
                <h3><a href="/product-detail.php?slug=<?php echo urlencode($product['slug']); ?>"><?php echo htmlspecialchars($product['name']); ?></a></h3>
                <div class="product-price">
                    <span class="current-price">$<?php echo formatPrice($product['price']); ?></span>
                </div>
                <button class="btn btn-outline add-to-cart" data-product-id="<?php echo $product['id']; ?>">
                    <i class="fas fa-cart-plus"></i> Add to Cart
                </button>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</section>

<!-- Discount Banner -->
<section class="discount-banner">
    <div class="banner-content">
        <h2>Big Sale! Up to 50% Off</h2>
        <p>Limited time offer on selected smartphones</p>
        <a href="/products.php?discount=true" class="btn btn-light">Shop Sale</a>
    </div>
</section>

<!-- Brands Section -->
<section class="brands-section">
    <div class="section-header">
        <h2>Shop by Brand</h2>
    </div>
    <div class="brands-grid">
        <a href="/products.php?brand=Apple" class="brand-logo">
            <i class="fab fa-apple"></i>
            <span>Apple</span>
        </a>
        <a href="/products.php?brand=Samsung" class="brand-logo">
            <i class="fas fa-mobile-alt"></i>
            <span>Samsung</span>
        </a>
        <a href="/products.php?brand=Google" class="brand-logo">
            <i class="fab fa-google"></i>
            <span>Google</span>
        </a>
        <a href="/products.php?brand=OnePlus" class="brand-logo">
            <span>OnePlus</span>
        </a>
        <a href="/products.php?brand=Xiaomi" class="brand-logo">
            <span>Xiaomi</span>
        </a>
        <a href="/products.php?brand=Sony" class="brand-logo">
            <i class="fab fa-sony"></i>
            <span>Sony</span>
        </a>
    </div>
</section>

<?php
$conn->close();
require_once 'includes/footer.php';
?>

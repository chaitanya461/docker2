<?php
// products.php - Fixed SQL Syntax
require_once 'config/init.php';

$page_title = "Products - Phone Store";
require_once 'includes/header.php';

$conn = getDatabaseConnection();

// Get filter parameters
$category = isset($_GET['category']) ? sanitize($_GET['category']) : '';
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$minPrice = isset($_GET['min_price']) ? floatval($_GET['min_price']) : 0;
$maxPrice = isset($_GET['max_price']) ? floatval($_GET['max_price']) : 10000;
$brand = isset($_GET['brand']) ? sanitize($_GET['brand']) : '';

// Build base query
$base_query = "
    SELECT p.*, c.name as category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    WHERE p.stock_quantity > 0
";

$count_query = "
    SELECT COUNT(*) as total 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    WHERE p.stock_quantity > 0
";

// Build WHERE conditions
$where_conditions = [];
$params = [];
$types = '';

// Category filter
if ($category) {
    $where_conditions[] = "c.slug = ?";
    $params[] = $category;
    $types .= 's';
}

// Search filter
if ($search) {
    $where_conditions[] = "(p.name LIKE ? OR p.brand LIKE ? OR p.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= 'sss';
}

// Brand filter
if ($brand) {
    $where_conditions[] = "p.brand = ?";
    $params[] = $brand;
    $types .= 's';
}

// Price filter
$where_conditions[] = "p.price BETWEEN ? AND ?";
$params[] = $minPrice;
$params[] = $maxPrice;
$types .= 'dd';

// Add WHERE conditions if any
if (!empty($where_conditions)) {
    $where_clause = " AND " . implode(" AND ", $where_conditions);
    $base_query .= $where_clause;
    $count_query .= $where_clause;
}

// Pagination
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 12;
$offset = ($page - 1) * $limit;

// Get total products count
$totalProducts = 0;
try {
    $stmt = $conn->prepare($count_query);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $totalProducts = $row['total'];
    $stmt->close();
} catch (Exception $e) {
    error_log("Count query error: " . $e->getMessage());
    $totalProducts = 0;
}

$totalPages = ceil($totalProducts / $limit);

// Get products with pagination
// Create new params array for main query since we need to add limit/offset
$main_params = $params; // Copy existing params
$main_types = $types; // Copy existing types

$base_query .= " ORDER BY p.created_at DESC LIMIT ? OFFSET ?";
$main_params[] = $limit;
$main_params[] = $offset;
$main_types .= 'ii';

$products = null;
try {
    $stmt = $conn->prepare($base_query);
    if (!empty($main_params)) {
        $stmt->bind_param($main_types, ...$main_params);
    }
    $stmt->execute();
    $products = $stmt->get_result();
} catch (Exception $e) {
    error_log("Products query error: " . $e->getMessage());
    echo "<div class='error'>Error loading products. Please try again.</div>";
    $products = null;
}

// Get brands for filter
$brands = $conn->query("SELECT DISTINCT brand FROM products WHERE brand IS NOT NULL ORDER BY brand");
?>

<div class="products-page">
    <div class="sidebar">
        <h3>Filters</h3>
        <form method="GET" action="">
            <input type="hidden" name="page" value="1">
            
            <div class="filter-group">
                <h4>Price Range</h4>
                <input type="number" name="min_price" placeholder="Min $" value="<?php echo htmlspecialchars($minPrice); ?>" step="0.01">
                <input type="number" name="max_price" placeholder="Max $" value="<?php echo htmlspecialchars($maxPrice); ?>" step="0.01">
            </div>
            
            <div class="filter-group">
                <h4>Brand</h4>
                <select name="brand">
                    <option value="">All Brands</option>
                    <?php while($b = $brands->fetch_assoc()): ?>
                        <option value="<?php echo htmlspecialchars($b['brand']); ?>" 
                            <?php echo $brand == $b['brand'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($b['brand']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="filter-group">
                <h4>Search</h4>
                <input type="text" name="search" placeholder="Search products..." value="<?php echo htmlspecialchars($search); ?>">
            </div>
            
            <button type="submit" class="btn btn-primary">Apply Filters</button>
            <a href="/products.php" class="btn btn-secondary">Clear All</a>
        </form>
    </div>
    
    <div class="products-container">
        <h2>All Products <?php if($totalProducts > 0): ?>(<?php echo $totalProducts; ?> found)<?php endif; ?></h2>
        
        <?php if($products && $products->num_rows > 0): ?>
            <div class="products-grid">
                <?php while($product = $products->fetch_assoc()): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <img src="<?php echo htmlspecialchars($product['image_url'] ?: '/images/default-phone.jpg'); ?>" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>"
                                 onerror="this.src='/images/default-phone.jpg'">
                            <?php if($product['discount_price']): ?>
                                <span class="badge discount">
                                    Save <?php 
                                    $discount = round((($product['price'] - $product['discount_price']) / $product['price']) * 100);
                                    echo $discount; ?>%
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="product-info">
                            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p class="product-brand"><?php echo htmlspecialchars($product['brand']); ?></p>
                            <p class="product-category"><?php echo htmlspecialchars($product['category_name']); ?></p>
                            <div class="price">
                                <?php if($product['discount_price']): ?>
                                    <span class="original-price">$<?php echo number_format($product['price'], 2); ?></span>
                                    <span class="current-price">$<?php echo number_format($product['discount_price'], 2); ?></span>
                                <?php else: ?>
                                    <span class="current-price">$<?php echo number_format($product['price'], 2); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="stock-status">
                                <?php if($product['stock_quantity'] > 0): ?>
                                    <span class="in-stock">In Stock (<?php echo $product['stock_quantity']; ?>)</span>
                                <?php else: ?>
                                    <span class="out-of-stock">Out of Stock</span>
                                <?php endif; ?>
                            </div>
                            <a href="/product-detail.php?slug=<?php echo urlencode($product['slug']); ?>" class="btn btn-secondary">
                                View Details
                            </a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php elseif($products): ?>
            <div class="no-products">
                <i class="fas fa-search fa-3x"></i>
                <h3>No products found</h3>
                <p>Try different filters or search terms.</p>
                <a href="/products.php" class="btn btn-primary">Clear Filters</a>
            </div>
        <?php endif; ?>
        
        <!-- Pagination -->
        <?php if($totalPages > 1): ?>
        <div class="pagination">
            <?php if($page > 1): ?>
                <a href="?<?php 
                    $query = $_GET;
                    $query['page'] = $page - 1;
                    echo http_build_query($query);
                ?>">&laquo; Previous</a>
            <?php endif; ?>
            
            <?php for($i = 1; $i <= $totalPages; $i++): ?>
                <?php if($i == 1 || $i == $totalPages || ($i >= $page - 2 && $i <= $page + 2)): ?>
                    <a href="?<?php 
                        $query = $_GET;
                        $query['page'] = $i;
                        echo http_build_query($query);
                    ?>" class="<?php echo $i == $page ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php elseif($i == $page - 3 || $i == $page + 3): ?>
                    <span class="dots">...</span>
                <?php endif; ?>
            <?php endfor; ?>
            
            <?php if($page < $totalPages): ?>
                <a href="?<?php 
                    $query = $_GET;
                    $query['page'] = $page + 1;
                    echo http_build_query($query);
                ?>">Next &raquo;</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php
if ($products) {
    $products->free();
}
if (isset($stmt)) {
    $stmt->close();
}
$conn->close();
require_once 'includes/footer.php';
?>

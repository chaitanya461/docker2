<?php
require_once 'config/database.php';
require_once 'includes/header.php';

$conn = getDatabaseConnection();

// Get filter parameters
$category = isset($_GET['category']) ? $_GET['category'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$minPrice = isset($_GET['min_price']) ? floatval($_GET['min_price']) : 0;
$maxPrice = isset($_GET['max_price']) ? floatval($_GET['max_price']) : 10000;
$brand = isset($_GET['brand']) ? $_GET['brand'] : '';

// Build query
$query = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE 1=1";
$params = [];
$types = '';

if ($category) {
    $query .= " AND c.slug = ?";
    $params[] = $category;
    $types .= 's';
}

if ($search) {
    $query .= " AND (p.name LIKE ? OR p.brand LIKE ? OR p.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= 'sss';
}

if ($brand) {
    $query .= " AND p.brand = ?";
    $params[] = $brand;
    $types .= 's';
}

$query .= " AND p.price BETWEEN ? AND ? AND p.stock_quantity > 0";
$params[] = $minPrice;
$params[] = $maxPrice;
$types .= 'dd';

// Pagination
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 12;
$offset = ($page - 1) * $limit;

// Count query needs to be separate without parameters for now
$countQuery = "SELECT COUNT(*) as total FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE 1=1";

if ($category) {
    $countQuery .= " AND c.slug = '$category'";
}

if ($search) {
    $searchTerm = $conn->real_escape_string($search);
    $countQuery .= " AND (p.name LIKE '%$searchTerm%' OR p.brand LIKE '%$searchTerm%' OR p.description LIKE '%$searchTerm%')";
}

if ($brand) {
    $brandTerm = $conn->real_escape_string($brand);
    $countQuery .= " AND p.brand = '$brandTerm'";
}

$countQuery .= " AND p.price BETWEEN $minPrice AND $maxPrice AND p.stock_quantity > 0";

$totalResult = $conn->query($countQuery);
$totalRow = $totalResult->fetch_assoc();
$totalProducts = $totalRow['total'];
$totalPages = ceil($totalProducts / $limit);

$query .= " LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;
$types .= 'ii';

// Prepare and execute
$stmt = $conn->prepare($query);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$products = $stmt->get_result();

// Get brands for filter
$brands = $conn->query("SELECT DISTINCT brand FROM products WHERE brand IS NOT NULL ORDER BY brand");
?>

<div class="products-page">
    <div class="sidebar">
        <h3>Filters</h3>
        <form method="GET" action="">
            <div class="filter-group">
                <h4>Price Range</h4>
                <input type="number" name="min_price" placeholder="Min $" value="<?php echo $minPrice; ?>">
                <input type="number" name="max_price" placeholder="Max $" value="<?php echo $maxPrice; ?>">
            </div>
            
            <div class="filter-group">
                <h4>Brand</h4>
                <select name="brand">
                    <option value="">All Brands</option>
                    <?php while($b = $brands->fetch_assoc()): ?>
                        <option value="<?php echo $b['brand']; ?>" <?php echo $brand == $b['brand'] ? 'selected' : ''; ?>>
                            <?php echo $b['brand']; ?>
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
        <h2>All Products (<?php echo $totalProducts; ?> found)</h2>
        
        <div class="products-grid">
            <?php if($products->num_rows > 0): ?>
                <?php while($product = $products->fetch_assoc()): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <img src="<?php echo $product['image_url'] ?: '/images/default-phone.jpg'; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        </div>
                        <div class="product-info">
                            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p class="product-brand"><?php echo htmlspecialchars($product['brand']); ?></p>
                            <p class="product-category"><?php echo $product['category_name']; ?></p>
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
                            <a href="/product-detail.php?slug=<?php echo $product['slug']; ?>" class="btn btn-secondary">View Details</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No products found. Try different filters.</p>
            <?php endif; ?>
        </div>
        
        <!-- Pagination -->
        <?php if($totalPages > 1): ?>
        <div class="pagination">
            <?php if($page > 1): ?>
                <a href="?<?php 
                    $queryParams = $_GET;
                    $queryParams['page'] = $page - 1;
                    echo http_build_query($queryParams);
                ?>">&laquo; Previous</a>
            <?php endif; ?>
            
            <?php for($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?<?php 
                    $queryParams = $_GET;
                    $queryParams['page'] = $i;
                    echo http_build_query($queryParams);
                ?>" class="<?php echo $i == $page ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
            
            <?php if($page < $totalPages): ?>
                <a href="?<?php 
                    $queryParams = $_GET;
                    $queryParams['page'] = $page + 1;
                    echo http_build_query($queryParams);
                ?>">Next &raquo;</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php
// Free results
if ($products) {
    $products->free();
}

if (isset($stmt)) {
    $stmt->close();
}

// DO NOT close the connection here - let footer.php handle it
// $conn->close();

require_once 'includes/footer.php';
?>

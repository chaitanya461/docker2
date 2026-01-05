<?php
// includes/header.php
// This should NOT start with <?php - it's included after init.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>PhoneStore</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
</head>
<body>
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="container">
            <div class="top-bar-content">
                <div class="contact-info">
                    <span><i class="fas fa-phone"></i> +1 (555) 123-4567</span>
                    <span><i class="fas fa-envelope"></i> support@phonestore.com</span>
                </div>
                <div class="top-bar-links">
                    <?php if(isLoggedIn()): ?>
                        <span>Welcome, <?php echo htmlspecialchars($_SESSION['full_name'] ?? $_SESSION['username']); ?>!</span>
                        <?php if(isAdmin()): ?>
                            <a href="/admin/dashboard.php"><i class="fas fa-cog"></i> Admin</a>
                        <?php endif; ?>
                        <a href="/auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    <?php else: ?>
                        <a href="/auth/login.php?redirect=cart" class="btn btn-primary">Login</a>
                        <a href="/auth/register.php"><i class="btn btn-primary"></i> Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <header class="main-header">
        <div class="container">
            <div class="header-content">
                <!-- Logo -->
                <div class="logo">
                    <a href="/index.php">
                        <i class="fas fa-mobile-alt"></i>
                        <span>Phone<span class="logo-highlight">Store</span></span>
                    </a>
                </div>

                <!-- Search Bar -->
                <div class="search-container">
                    <form action="/products.php" method="GET" class="search-form">
                        <input type="text" name="search" placeholder="Search for phones, brands, accessories..." 
                               value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                        <button type="submit"><i class="fas fa-search"></i></button>
                    </form>
                </div>

                <!-- Cart & User -->
                <div class="header-actions">
                    <a href="/cart.php" class="cart-icon">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="cart-count"><?php echo getCartCount(); ?></span>
                    </a>
                    <?php if(isLoggedIn()): ?>
                        <a href="/profile.php" class="user-icon">
                            <i class="fas fa-user-circle"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <!-- Navigation -->
    <nav class="main-nav">
        <div class="container">
            <ul class="nav-menu">
                <li><a href="/index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="/products.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : ''; ?>"><i class="fas fa-mobile-alt"></i> All Products</a></li>
                <li class="nav-dropdown">
                    <a href="#"><i class="fas fa-list"></i> Categories <i class="fas fa-chevron-down"></i></a>
                    <div class="dropdown-menu">
                        <?php
                        $conn = getDatabaseConnection();
                        $categories = $conn->query("SELECT * FROM categories ORDER BY name");
                        while($cat = $categories->fetch_assoc()):
                        ?>
                            <a href="/products.php?category=<?php echo urlencode($cat['slug']); ?>">
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </a>
                        <?php endwhile; ?>
                    </div>
                </li>
                <li><a href="/products.php?deal=hot"><i class="fas fa-fire"></i> Hot Deals</a></li>
                <li><a href="#"><i class="fas fa-info-circle"></i> About</a></li>
                <li><a href="#"><i class="fas fa-phone-alt"></i> Contact</a></li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container main-content">

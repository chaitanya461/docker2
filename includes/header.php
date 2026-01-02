
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PhoneStore - Your Mobile Destination</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="container">
                <a href="/index.php" class="logo">
                    <i class="fas fa-mobile-alt"></i>
                    <span>PhoneStore</span>
                </a>
                
                <div class="nav-links">
                    <a href="/index.php"><i class="fas fa-home"></i> Home</a>
                    <a href="/products.php"><i class="fas fa-mobile"></i> Products</a>
                    <a href="/categories.php"><i class="fas fa-list"></i> Categories</a>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a href="/cart.php"><i class="fas fa-shopping-cart"></i> Cart</a>
                        <a href="/profile.php"><i class="fas fa-user"></i> Profile</a>
                        <?php if($_SESSION['role'] === 'admin'): ?>
                            <a href="/admin/dashboard.php"><i class="fas fa-cog"></i> Admin</a>
                        <?php endif; ?>
                        <a href="/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    <?php else: ?>
                        <a href="/login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
                        <a href="/register.php"><i class="fas fa-user-plus"></i> Register</a>
                    <?php endif; ?>
                </div>
                
                <div class="search-bar">
                    <form action="/products.php" method="GET">
                        <input type="text" name="search" placeholder="Search phones...">
                        <button type="submit"><i class="fas fa-search"></i></button>
                    </form>
                </div>
            </div>
        </nav>
    </header>
    
    <main class="container">

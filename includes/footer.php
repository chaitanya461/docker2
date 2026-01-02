<?php
// includes/footer.php
?>
    </main>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="container">
            <div class="footer-grid">
                <!-- Company Info -->
                <div class="footer-section">
                    <div class="logo">
                        <i class="fas fa-mobile-alt"></i>
                        <span>Phone<span class="logo-highlight">Store</span></span>
                    </div>
                    <p>Your trusted destination for the latest smartphones and accessories since 2024.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="/index.php">Home</a></li>
                        <li><a href="/products.php">Products</a></li>
                        <li><a href="/about.php">About Us</a></li>
                        <li><a href="/contact.php">Contact Us</a></li>
                        <li><a href="/faq.php">FAQ</a></li>
                    </ul>
                </div>

                <!-- Categories -->
                <div class="footer-section">
                    <h3>Categories</h3>
                    <ul>
                        <?php
                        $conn = getDatabaseConnection();
                        $categories = $conn->query("SELECT * FROM categories ORDER BY name LIMIT 5");
                        while($cat = $categories->fetch_assoc()):
                        ?>
                            <li><a href="/products.php?category=<?php echo urlencode($cat['slug']); ?>"><?php echo htmlspecialchars($cat['name']); ?></a></li>
                        <?php endwhile; ?>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div class="footer-section">
                    <h3>Contact Info</h3>
                    <ul class="contact-info">
                        <li><i class="fas fa-map-marker-alt"></i> 123 Tech Street, Silicon Valley, CA</li>
                        <li><i class="fas fa-phone"></i> +1 (555) 123-4567</li>
                        <li><i class="fas fa-envelope"></i> info@phonestore.com</li>
                        <li><i class="fas fa-clock"></i> Mon-Fri: 9AM-6PM PST</li>
                    </ul>
                </div>
            </div>

            <!-- Newsletter -->
            <div class="newsletter-section">
                <h3>Subscribe to Our Newsletter</h3>
                <p>Get the latest updates on new products and upcoming sales</p>
                <form class="newsletter-form">
                    <input type="email" placeholder="Enter your email" required>
                    <button type="submit">Subscribe</button>
                </form>
            </div>

            <!-- Copyright -->
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> PhoneStore. All rights reserved.</p>
                <div class="footer-links">
                    <a href="/privacy.php">Privacy Policy</a>
                    <a href="/terms.php">Terms of Service</a>
                    <a href="/refund.php">Refund Policy</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script>
        // Mobile menu toggle
        document.addEventListener('DOMContentLoaded', function() {
            // Dropdown menu functionality
            const dropdowns = document.querySelectorAll('.nav-dropdown');
            dropdowns.forEach(dropdown => {
                dropdown.addEventListener('mouseenter', function() {
                    this.querySelector('.dropdown-menu').style.display = 'block';
                });
                dropdown.addEventListener('mouseleave', function() {
                    this.querySelector('.dropdown-menu').style.display = 'none';
                });
            });

            // Mobile menu toggle
            const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
            const navMenu = document.querySelector('.nav-menu');
            
            if (mobileMenuBtn) {
                mobileMenuBtn.addEventListener('click', function() {
                    navMenu.classList.toggle('show');
                });
            }

            // Add to cart animation
            document.querySelectorAll('.add-to-cart').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const productId = this.dataset.productId;
                    
                    // Add animation
                    this.classList.add('adding');
                    setTimeout(() => {
                        this.classList.remove('adding');
                    }, 1000);
                    
                    // AJAX request to add to cart
                    fetch('/ajax/add-to-cart.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ product_id: productId })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update cart count
                            document.querySelector('.cart-count').textContent = data.cart_count;
                            // Show success message
                            showNotification('Product added to cart!', 'success');
                        } else {
                            showNotification(data.message || 'Failed to add to cart', 'error');
                        }
                    });
                });
            });
        });

        function showNotification(message, type = 'info') {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                <span>${message}</span>
                <button class="close-notification">&times;</button>
            `;
            
            // Add to page
            document.body.appendChild(notification);
            
            // Show notification
            setTimeout(() => {
                notification.classList.add('show');
            }, 10);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            }, 5000);
            
            // Close button
            notification.querySelector('.close-notification').addEventListener('click', function() {
                notification.classList.remove('show');
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            });
        }
    </script>
</body>
</html>
<?php
// End output buffering and flush
if (ob_get_level() > 0) {
    ob_end_flush();
}
?>

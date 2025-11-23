    <footer class="main-footer" style="padding: 2rem 0 1rem;">
        <div class="footer-container">
            <div class="footer-section">
                <h3><?php echo SITE_NAME; ?></h3>
                <p><?php echo SITE_TAGLINE; ?></p>
                <p class="footer-tagline-nepali">नेपालको बजार मूल्य सूचना</p>
            </div>
            
            <div class="footer-section">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="<?php echo SITE_URL; ?>/public/index.php">Home</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/public/products.php">Browse Products</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/public/products.php">Search Items</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/public/about.php">About Us</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h4>For Contributors</h4>
                <ul>
                    <li><a href="<?php echo SITE_URL; ?>/contributor/login.php">Login</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/public/about.php">How It Works</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/public/about.php">Join Us</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h4>Contact</h4>
                <p>Email: <?php echo SITE_EMAIL; ?></p>
                <p>Together building market transparency</p>
            </div>
        </div>
        
        <div class="footer-bottom" style="margin-top: 1rem; padding-top: 1rem;">
            <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
            <p>Built with ❤️ for Nepal</p>
        </div>
    </footer>
    
    <script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>
    <script src="<?php echo SITE_URL; ?>/assets/js/nav-modern.js"></script>
    <?php if (isset($additionalJS)): ?>
        <?php if (is_array($additionalJS)): ?>
            <?php foreach ($additionalJS as $js): ?>
                <script src="<?php echo SITE_URL . '/assets/js/' . $js; ?>"></script>
            <?php endforeach; ?>
        <?php else: ?>
            <script src="<?php echo SITE_URL . '/assets/js/' . $additionalJS; ?>"></script>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>

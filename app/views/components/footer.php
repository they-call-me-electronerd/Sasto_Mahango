    <!-- Professional Footer -->
    <footer class="professional-footer" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: #f9fafb; padding: 2rem 0 1rem; margin-top: 2rem; border-top: 3px solid #f97316; position: relative;">
        <!-- Orange accent line -->
        <div style="position: absolute; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, #f97316 0%, #ea580c 50%, #f97316 100%); box-shadow: 0 2px 8px rgba(249, 115, 22, 0.4);"></div>
        <div class="container" style="max-width: 1400px; margin: 0 auto; padding: 0 2rem;">
            <div class="row g-4">
                <!-- Brand Section -->
                <div class="col-lg-4 col-md-6">
                    <h3 style="font-size: 1.75rem; font-weight: 800; color: #f97316; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="bi bi-graph-up-arrow"></i>
                        <?php echo SITE_NAME; ?>
                    </h3>
                    <p style="color: #cbd5e1; line-height: 1.7; margin-bottom: 1rem;"><?php echo SITE_TAGLINE; ?></p>
                    <p style="color: #94a3b8; font-size: 0.875rem; font-family: 'Noto Sans Devanagari', sans-serif;">नेपालको बजार मूल्य सूचना</p>
                    <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                        <a href="#" style="width: 40px; height: 40px; background: rgba(249, 115, 22, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #f97316; text-decoration: none; transition: all 0.3s;" onmouseover="this.style.background='#f97316'; this.style.color='white';" onmouseout="this.style.background='rgba(249, 115, 22, 0.1)'; this.style.color='#f97316';"><i class="bi bi-facebook"></i></a>
                        <a href="#" style="width: 40px; height: 40px; background: rgba(249, 115, 22, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #f97316; text-decoration: none; transition: all 0.3s;" onmouseover="this.style.background='#f97316'; this.style.color='white';" onmouseout="this.style.background='rgba(249, 115, 22, 0.1)'; this.style.color='#f97316';"><i class="bi bi-twitter"></i></a>
                        <a href="#" style="width: 40px; height: 40px; background: rgba(249, 115, 22, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #f97316; text-decoration: none; transition: all 0.3s;" onmouseover="this.style.background='#f97316'; this.style.color='white';" onmouseout="this.style.background='rgba(249, 115, 22, 0.1)'; this.style.color='#f97316';"><i class="bi bi-instagram"></i></a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="col-lg-2 col-md-3 col-6">
                    <h5 style="color: white; font-weight: 700; margin-bottom: 1rem; font-size: 1.125rem;">Quick Links</h5>
                    <ul style="list-style: none; padding: 0; margin: 0;">
                        <li style="margin-bottom: 0.75rem;"><a href="<?php echo SITE_URL . '/app/views/app/views/public/index.php" style="color: #cbd5e1; text-decoration: none; display: flex; align-items: center; gap: 0.5rem; transition: color 0.3s;" onmouseover="this.style.color='#f97316';" onmouseout="this.style.color='#cbd5e1';"><i class="bi bi-chevron-right" style="font-size: 0.75rem;"></i> Home</a></li>
                        <li style="margin-bottom: 0.75rem;"><a href="<?php echo SITE_URL . '/app/views/app/views/public/products.php" style="color: #cbd5e1; text-decoration: none; display: flex; align-items: center; gap: 0.5rem; transition: color 0.3s;" onmouseover="this.style.color='#f97316';" onmouseout="this.style.color='#cbd5e1';"><i class="bi bi-chevron-right" style="font-size: 0.75rem;"></i> Browse Products</a></li>
                        <li style="margin-bottom: 0.75rem;"><a href="<?php echo SITE_URL . '/app/views/app/views/public/products.php" style="color: #cbd5e1; text-decoration: none; display: flex; align-items: center; gap: 0.5rem; transition: color 0.3s;" onmouseover="this.style.color='#f97316';" onmouseout="this.style.color='#cbd5e1';"><i class="bi bi-chevron-right" style="font-size: 0.75rem;"></i> Search Items</a></li>
                        <li style="margin-bottom: 0.75rem;"><a href="<?php echo SITE_URL . '/app/views/app/views/public/about.php" style="color: #cbd5e1; text-decoration: none; display: flex; align-items: center; gap: 0.5rem; transition: color 0.3s;" onmouseover="this.style.color='#f97316';" onmouseout="this.style.color='#cbd5e1';"><i class="bi bi-chevron-right" style="font-size: 0.75rem;"></i> About Us</a></li>
                    </ul>
                </div>

                <!-- Categories -->
                <div class="col-lg-2 col-md-3 col-6">
                    <h5 style="color: white; font-weight: 700; margin-bottom: 1rem; font-size: 1.125rem;">Categories</h5>
                    <ul style="list-style: none; padding: 0; margin: 0;">
                        <li style="margin-bottom: 0.75rem;"><a href="<?php echo SITE_URL . '/app/views/app/views/public/products.php?category=vegetables" style="color: #cbd5e1; text-decoration: none; display: flex; align-items: center; gap: 0.5rem; transition: color 0.3s;" onmouseover="this.style.color='#f97316';" onmouseout="this.style.color='#cbd5e1';"><i class="bi bi-chevron-right" style="font-size: 0.75rem;"></i> Vegetables</a></li>
                        <li style="margin-bottom: 0.75rem;"><a href="<?php echo SITE_URL . '/app/views/app/views/public/products.php?category=fruits" style="color: #cbd5e1; text-decoration: none; display: flex; align-items: center; gap: 0.5rem; transition: color 0.3s;" onmouseover="this.style.color='#f97316';" onmouseout="this.style.color='#cbd5e1';"><i class="bi bi-chevron-right" style="font-size: 0.75rem;"></i> Fruits</a></li>
                        <li style="margin-bottom: 0.75rem;"><a href="<?php echo SITE_URL . '/app/views/app/views/public/products.php?category=kitchen-appliances" style="color: #cbd5e1; text-decoration: none; display: flex; align-items: center; gap: 0.5rem; transition: color 0.3s;" onmouseover="this.style.color='#f97316';" onmouseout="this.style.color='#cbd5e1';"><i class="bi bi-chevron-right" style="font-size: 0.75rem;"></i> Kitchen</a></li>
                        <li style="margin-bottom: 0.75rem;"><a href="<?php echo SITE_URL . '/app/views/app/views/public/products.php?category=tech-gadgets" style="color: #cbd5e1; text-decoration: none; display: flex; align-items: center; gap: 0.5rem; transition: color 0.3s;" onmouseover="this.style.color='#f97316';" onmouseout="this.style.color='#cbd5e1';"><i class="bi bi-chevron-right" style="font-size: 0.75rem;"></i> Electronics</a></li>
                        <li style="margin-bottom: 0.75rem;"><a href="<?php echo SITE_URL . '/app/views/app/views/public/products.php" style="color: #cbd5e1; text-decoration: none; display: flex; align-items: center; gap: 0.5rem; transition: color 0.3s;" onmouseover="this.style.color='#f97316';" onmouseout="this.style.color='#cbd5e1';"><i class="bi bi-chevron-right" style="font-size: 0.75rem;"></i> View All</a></li>
                    </ul>
                </div>

                <!-- For Contributors -->
                <div class="col-lg-2 col-md-6 col-6">
                    <h5 style="color: white; font-weight: 700; margin-bottom: 1rem; font-size: 1.125rem;">Contributors</h5>
                    <ul style="list-style: none; padding: 0; margin: 0;">
                        <li style="margin-bottom: 0.75rem;"><a href="<?php echo SITE_URL . '/app/views/app/views/contributor/login.php" style="color: #cbd5e1; text-decoration: none; display: flex; align-items: center; gap: 0.5rem; transition: color 0.3s;" onmouseover="this.style.color='#f97316';" onmouseout="this.style.color='#cbd5e1';"><i class="bi bi-chevron-right" style="font-size: 0.75rem;"></i> Login</a></li>
                        <li style="margin-bottom: 0.75rem;"><a href="<?php echo SITE_URL . '/app/views/app/views/contributor/register.php" style="color: #cbd5e1; text-decoration: none; display: flex; align-items: center; gap: 0.5rem; transition: color 0.3s;" onmouseover="this.style.color='#f97316';" onmouseout="this.style.color='#cbd5e1';"><i class="bi bi-chevron-right" style="font-size: 0.75rem;"></i> Register</a></li>
                        <li style="margin-bottom: 0.75rem;"><a href="<?php echo SITE_URL . '/app/views/app/views/public/about.php" style="color: #cbd5e1; text-decoration: none; display: flex; align-items: center; gap: 0.5rem; transition: color 0.3s;" onmouseover="this.style.color='#f97316';" onmouseout="this.style.color='#cbd5e1';"><i class="bi bi-chevron-right" style="font-size: 0.75rem;"></i> How It Works</a></li>
                        <li style="margin-bottom: 0.75rem;"><a href="<?php echo SITE_URL . '/app/views/app/views/public/about.php" style="color: #cbd5e1; text-decoration: none; display: flex; align-items: center; gap: 0.5rem; transition: color 0.3s;" onmouseover="this.style.color='#f97316';" onmouseout="this.style.color='#cbd5e1';"><i class="bi bi-chevron-right" style="font-size: 0.75rem;"></i> Join Us</a></li>
                    </ul>
                </div>

                <!-- Contact -->
                <div class="col-lg-2 col-md-6 col-6">
                    <h5 style="color: white; font-weight: 700; margin-bottom: 1rem; font-size: 1.125rem;">Legal</h5>
                    <ul style="list-style: none; padding: 0; margin: 0;">
                        <li style="margin-bottom: 0.75rem;"><a href="<?php echo SITE_URL . '/app/views/app/views/public/privacy-policy.php" style="color: #cbd5e1; text-decoration: none; display: flex; align-items: center; gap: 0.5rem; transition: color 0.3s;" onmouseover="this.style.color='#f97316';" onmouseout="this.style.color='#cbd5e1';"><i class="bi bi-chevron-right" style="font-size: 0.75rem;"></i> Privacy Policy</a></li>
                        <li style="margin-bottom: 0.75rem;"><a href="<?php echo SITE_URL . '/app/views/app/views/public/terms-of-service.php" style="color: #cbd5e1; text-decoration: none; display: flex; align-items: center; gap: 0.5rem; transition: color 0.3s;" onmouseover="this.style.color='#f97316';" onmouseout="this.style.color='#cbd5e1';"><i class="bi bi-chevron-right" style="font-size: 0.75rem;"></i> Terms of Service</a></li>
                        <li style="margin-bottom: 0.75rem;"><a href="<?php echo SITE_URL . '/app/views/app/views/public/cookie-policy.php" style="color: #cbd5e1; text-decoration: none; display: flex; align-items: center; gap: 0.5rem; transition: color 0.3s;" onmouseover="this.style.color='#f97316';" onmouseout="this.style.color='#cbd5e1';"><i class="bi bi-chevron-right" style="font-size: 0.75rem;"></i> Cookie Policy</a></li>
                    </ul>
                </div>
            </div>
            
            <!-- Footer Bottom -->
            <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.1); text-align: center;">
                <p style="color: #94a3b8; margin: 0;">&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
                <p style="color: #64748b; margin-top: 0.5rem; font-size: 0.875rem;">Built by Team Urja on ISTN Hackathon</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Consolidated Scripts -->
    <script src="<?php echo SITE_URL; ?>/assets/js/components.js"></script>
    <script src="<?php echo SITE_URL; ?>/assets/js/animations.js"></script>
    
    <style>
        /* Responsive Navbar */
        @media (max-width: 992px) {
            .navbar-menu {
                display: none !important;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: white;
                flex-direction: column;
                padding: 1rem;
                box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                gap: 0.5rem !important;
            }
            .navbar-menu.active {
                display: flex !important;
            }
            .mobile-menu-toggle {
                display: block !important;
            }
            .professional-footer .row {
                text-align: center;
            }
            .professional-footer ul {
                display: inline-block;
                text-align: left;
            }
        }
        
        @media (max-width: 768px) {
            .navbar-container {
                padding: 0.75rem 1rem !important;
            }
            .navbar-actions {
                gap: 0.5rem !important;
            }
            .navbar-actions .btn {
                padding: 0.4rem 1rem !important;
                font-size: 0.875rem !important;
            }
        }
    </style>
    
    <script>
        // Mobile menu toggle
        document.getElementById('mobileMenuToggle')?.addEventListener('click', function() {
            const menu = document.getElementById('navbarMenu');
            menu.classList.toggle('active');
        });
    </script>
</body>
</html>

<?php
/**
 * Cookie Policy Page
 */

define('SASTOMAHANGO_APP', true);
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'Cookie Policy';
$metaDescription = 'Learn about how SastoMahango uses cookies to improve your browsing experience and provide better services.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo $metaDescription; ?>">
    <meta name="robots" content="index, follow">
    
    <title><?php echo $pageTitle . ' - ' . SITE_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-orange: #f97316;
            --primary-orange-dark: #ea580c;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-900: #111827;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            color: var(--gray-900);
            line-height: 1.7;
            background: var(--gray-50);
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Manrope', sans-serif;
            font-weight: 700;
        }
        
        .legal-hero {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: white;
            padding: 5rem 0 3rem;
            position: relative;
            overflow: hidden;
        }
        
        .legal-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"><path d="M0 0h60v60H0z" fill="none"/><path d="M30 0v60M0 30h60" stroke="rgba(255,255,255,0.1)" stroke-width="1"/></svg>');
            opacity: 0.5;
        }
        
        .legal-hero h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
            position: relative;
            z-index: 1;
        }
        
        .legal-hero .lead {
            font-size: 1.25rem;
            opacity: 0.95;
            position: relative;
            z-index: 1;
        }
        
        .legal-content {
            background: white;
            border-radius: 1.5rem;
            padding: 3rem;
            margin: -3rem auto 4rem;
            max-width: 900px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 2;
        }
        
        .legal-content h2 {
            color: #059669;
            font-size: 1.75rem;
            margin-top: 2.5rem;
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 3px solid var(--gray-100);
        }
        
        .legal-content h2:first-child {
            margin-top: 0;
        }
        
        .legal-content h3 {
            color: var(--gray-900);
            font-size: 1.25rem;
            margin-top: 1.5rem;
            margin-bottom: 0.75rem;
        }
        
        .legal-content p {
            color: var(--gray-700);
            margin-bottom: 1rem;
        }
        
        .legal-content ul, .legal-content ol {
            color: var(--gray-700);
            margin-bottom: 1.5rem;
            padding-left: 1.5rem;
        }
        
        .legal-content li {
            margin-bottom: 0.5rem;
        }
        
        .cookie-table {
            width: 100%;
            margin: 2rem 0;
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }
        
        .cookie-table thead {
            background: linear-gradient(135deg, #059669, #047857);
            color: white;
        }
        
        .cookie-table th {
            padding: 1rem;
            font-weight: 600;
            text-align: left;
        }
        
        .cookie-table td {
            padding: 1rem;
            border-bottom: 1px solid var(--gray-100);
        }
        
        .cookie-table tbody tr:last-child td {
            border-bottom: none;
        }
        
        .cookie-table tbody tr:hover {
            background: rgba(5, 150, 105, 0.05);
        }
        
        .cookie-type {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 600;
        }
        
        .cookie-type.essential {
            background: #dbeafe;
            color: #1e40af;
        }
        
        .cookie-type.analytics {
            background: #fef3c7;
            color: #92400e;
        }
        
        .cookie-type.functional {
            background: #d1fae5;
            color: #065f46;
        }
        
        .cookie-type.advertising {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .info-box {
            background: linear-gradient(135deg, rgba(5, 150, 105, 0.1), rgba(4, 120, 87, 0.05));
            border-left: 4px solid #059669;
            padding: 1.5rem;
            border-radius: 0.75rem;
            margin: 2rem 0;
        }
        
        .info-box h4 {
            color: #059669;
            font-size: 1.125rem;
            margin-bottom: 0.75rem;
        }
        
        .control-box {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(37, 99, 235, 0.05));
            border: 2px solid #3b82f6;
            padding: 1.5rem;
            border-radius: 0.75rem;
            margin: 2rem 0;
        }
        
        .control-box h4 {
            color: #3b82f6;
            font-size: 1.125rem;
            margin-bottom: 0.75rem;
        }
        
        .last-updated {
            background: var(--gray-100);
            padding: 1rem 1.5rem;
            border-radius: 0.75rem;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .last-updated i {
            color: #059669;
            font-size: 1.25rem;
        }
        
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #059669;
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 2rem;
            transition: all 0.3s;
        }
        
        .back-link:hover {
            gap: 0.75rem;
            color: #047857;
        }
        
        .navbar {
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding: 1rem 0;
        }
        
        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary-orange) !important;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        @media (max-width: 768px) {
            .legal-hero {
                padding: 3rem 0 2rem;
            }
            
            .legal-hero h1 {
                font-size: 2rem;
            }
            
            .legal-content {
                padding: 2rem 1.5rem;
                margin: -2rem 1rem 2rem;
            }
            
            .legal-content h2 {
                font-size: 1.5rem;
            }
            
            .cookie-table {
                font-size: 0.875rem;
            }
            
            .cookie-table th,
            .cookie-table td {
                padding: 0.75rem 0.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="<?php echo SITE_URL; ?>">
                <i class="bi bi-graph-up-arrow"></i>
                SastoMahango
            </a>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="legal-hero">
        <div class="container text-center">
            <h1><i class="bi bi-cookie me-3"></i>Cookie Policy</h1>
            <p class="lead">Understanding how we use cookies on SastoMahango</p>
        </div>
    </div>

    <!-- Content Section -->
    <div class="container">
        <div class="legal-content">
            <a href="<?php echo SITE_URL; ?>" class="back-link">
                <i class="bi bi-arrow-left"></i>
                Back to Home
            </a>
            
            <div class="last-updated">
                <i class="bi bi-calendar-check"></i>
                <div>
                    <strong>Last Updated:</strong> November 23, 2025
                </div>
            </div>

            <h2>1. What Are Cookies?</h2>
            <p>
                Cookies are small text files that are placed on your device (computer, smartphone, or tablet) when you visit a website. They are widely used to make websites work more efficiently and provide information to website owners.
            </p>
            <p>
                Cookies allow websites to:
            </p>
            <ul>
                <li>Remember your preferences and settings</li>
                <li>Improve your user experience</li>
                <li>Understand how you use the website</li>
                <li>Provide personalized content and features</li>
                <li>Analyze website traffic and performance</li>
            </ul>

            <h2>2. How SastoMahango Uses Cookies</h2>
            <p>
                We use cookies to enhance your experience on our platform and provide better services. Our cookies help us:
            </p>
            <ul>
                <li><strong>Remember your login status:</strong> Keep you signed in to your contributor account</li>
                <li><strong>Save your preferences:</strong> Remember your theme choice (dark/light mode), language, and display settings</li>
                <li><strong>Improve functionality:</strong> Enable features like search filters and saved searches</li>
                <li><strong>Analyze usage:</strong> Understand how visitors use our site to improve performance</li>
                <li><strong>Secure your account:</strong> Protect against unauthorized access and fraudulent activity</li>
            </ul>

            <h2>3. Types of Cookies We Use</h2>

            <h3>3.1 Essential Cookies (Required)</h3>
            <p>
                These cookies are necessary for the website to function properly. They enable core functionality such as security, authentication, and accessibility. The website cannot function properly without these cookies.
            </p>

            <h3>3.2 Functional Cookies (Optional)</h3>
            <p>
                These cookies enhance the functionality and personalization of the website. They remember your preferences and choices to provide a more personalized experience.
            </p>

            <h3>3.3 Analytics Cookies (Optional)</h3>
            <p>
                These cookies help us understand how visitors interact with our website by collecting and reporting information anonymously. This helps us improve our services.
            </p>

            <h3>3.4 Performance Cookies (Optional)</h3>
            <p>
                These cookies collect information about how visitors use our website, such as which pages are visited most often and if visitors receive error messages. This information helps us optimize website performance.
            </p>

            <h2>4. Specific Cookies We Use</h2>
            <p>Below is a detailed list of cookies used on SastoMahango:</p>

            <table class="cookie-table">
                <thead>
                    <tr>
                        <th>Cookie Name</th>
                        <th>Type</th>
                        <th>Purpose</th>
                        <th>Duration</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><code>PHPSESSID</code></td>
                        <td><span class="cookie-type essential">Essential</span></td>
                        <td>Session identifier for logged-in users</td>
                        <td>Session</td>
                    </tr>
                    <tr>
                        <td><code>user_login</code></td>
                        <td><span class="cookie-type essential">Essential</span></td>
                        <td>Maintains user authentication status</td>
                        <td>30 days</td>
                    </tr>
                    <tr>
                        <td><code>theme_preference</code></td>
                        <td><span class="cookie-type functional">Functional</span></td>
                        <td>Remembers dark/light mode preference</td>
                        <td>1 year</td>
                    </tr>
                    <tr>
                        <td><code>search_filters</code></td>
                        <td><span class="cookie-type functional">Functional</span></td>
                        <td>Saves your search and filter preferences</td>
                        <td>90 days</td>
                    </tr>
                    <tr>
                        <td><code>_ga</code></td>
                        <td><span class="cookie-type analytics">Analytics</span></td>
                        <td>Google Analytics - distinguishes users</td>
                        <td>2 years</td>
                    </tr>
                    <tr>
                        <td><code>_gid</code></td>
                        <td><span class="cookie-type analytics">Analytics</span></td>
                        <td>Google Analytics - distinguishes users</td>
                        <td>24 hours</td>
                    </tr>
                    <tr>
                        <td><code>_gat</code></td>
                        <td><span class="cookie-type analytics">Analytics</span></td>
                        <td>Google Analytics - throttles request rate</td>
                        <td>1 minute</td>
                    </tr>
                    <tr>
                        <td><code>csrf_token</code></td>
                        <td><span class="cookie-type essential">Essential</span></td>
                        <td>Security token to prevent CSRF attacks</td>
                        <td>Session</td>
                    </tr>
                </tbody>
            </table>

            <h2>5. Session vs Persistent Cookies</h2>

            <h3>5.1 Session Cookies</h3>
            <p>
                Session cookies are temporary and are deleted when you close your browser. We use them to:
            </p>
            <ul>
                <li>Maintain your login session</li>
                <li>Remember items in your comparison list during browsing</li>
                <li>Ensure security during your session</li>
            </ul>

            <h3>5.2 Persistent Cookies</h3>
            <p>
                Persistent cookies remain on your device until they expire or you delete them. We use them to:
            </p>
            <ul>
                <li>Remember your preferences between visits</li>
                <li>Keep you logged in across sessions (if you choose "Remember Me")</li>
                <li>Analyze long-term usage patterns</li>
            </ul>

            <h2>6. Third-Party Cookies</h2>
            <p>
                Some cookies are placed by third-party services that appear on our pages. We do not control these cookies. Third parties we work with include:
            </p>
            <ul>
                <li><strong>Google Analytics:</strong> Website analytics and performance tracking</li>
                <li><strong>Bootstrap CDN:</strong> CSS and JavaScript framework delivery</li>
                <li><strong>Google Fonts:</strong> Web font delivery</li>
            </ul>

            <div class="info-box">
                <h4><i class="bi bi-info-circle me-2"></i>Third-Party Privacy</h4>
                <p style="margin-bottom: 0;">
                    Third-party cookies are governed by the respective third parties' privacy policies. We recommend reviewing their policies to understand how they collect and use your data.
                </p>
            </div>

            <h2>7. Managing Your Cookie Preferences</h2>

            <div class="control-box">
                <h4><i class="bi bi-sliders me-2"></i>Your Cookie Controls</h4>
                <p>You have several options to manage cookies:</p>
            </div>

            <h3>7.1 Browser Settings</h3>
            <p>
                Most web browsers allow you to control cookies through their settings:
            </p>
            <ul>
                <li><strong>Google Chrome:</strong> Settings → Privacy and security → Cookies and other site data</li>
                <li><strong>Mozilla Firefox:</strong> Options → Privacy & Security → Cookies and Site Data</li>
                <li><strong>Safari:</strong> Preferences → Privacy → Cookies and website data</li>
                <li><strong>Microsoft Edge:</strong> Settings → Privacy, search, and services → Cookies and site permissions</li>
            </ul>

            <h3>7.2 Cookie Consent</h3>
            <p>
                When you first visit SastoMahango, we'll ask for your consent to use optional cookies. You can:
            </p>
            <ul>
                <li>Accept all cookies</li>
                <li>Reject optional cookies (essential cookies will still be used)</li>
                <li>Customize your preferences by cookie type</li>
                <li>Change your preferences at any time</li>
            </ul>

            <h3>7.3 Opt-Out of Analytics</h3>
            <p>
                You can opt-out of Google Analytics tracking by:
            </p>
            <ul>
                <li>Installing the <a href="https://tools.google.com/dlpage/gaoptout" target="_blank" rel="noopener">Google Analytics Opt-out Browser Add-on</a></li>
                <li>Disabling analytics cookies in your browser settings</li>
                <li>Using our cookie preference center to disable analytics cookies</li>
            </ul>

            <h2>8. Impact of Disabling Cookies</h2>
            <p>
                If you disable cookies, some parts of our website may not function properly:
            </p>
            <ul>
                <li><strong>Essential cookies disabled:</strong> You won't be able to log in or use contributor features</li>
                <li><strong>Functional cookies disabled:</strong> Your preferences won't be saved between visits</li>
                <li><strong>Analytics cookies disabled:</strong> We can't track how you use the site to improve it</li>
            </ul>

            <h2>9. Do Not Track Signals</h2>
            <p>
                Some browsers include a "Do Not Track" (DNT) feature that signals to websites that you don't want to be tracked. Currently, there is no industry standard for responding to DNT signals.
            </p>
            <p>
                SastoMahango respects your privacy choices. If you enable DNT in your browser:
            </p>
            <ul>
                <li>We will not set optional analytics or advertising cookies</li>
                <li>Essential and functional cookies will still be used for site functionality</li>
                <li>You can still manually control cookies through your browser settings</li>
            </ul>

            <h2>10. Cookies and Mobile Devices</h2>
            <p>
                When you access SastoMahango on mobile devices, we may use similar technologies to cookies:
            </p>
            <ul>
                <li><strong>Local Storage:</strong> Stores data in your browser for offline access</li>
                <li><strong>Session Storage:</strong> Temporary storage that's cleared when you close the browser</li>
                <li><strong>Mobile SDKs:</strong> If you use our mobile app (future feature)</li>
            </ul>
            <p>
                You can manage these through your mobile device settings.
            </p>

            <h2>11. Updates to Cookie Policy</h2>
            <p>
                We may update this Cookie Policy periodically to reflect:
            </p>
            <ul>
                <li>Changes in our cookie usage</li>
                <li>New technologies or features</li>
                <li>Legal or regulatory requirements</li>
                <li>User feedback and best practices</li>
            </ul>
            <p>
                We will notify you of significant changes by:
            </p>
            <ul>
                <li>Updating the "Last Updated" date at the top of this policy</li>
                <li>Displaying a notice on our website</li>
                <li>Sending an email notification (for registered users)</li>
            </ul>

            <h2>12. Cookie Retention Periods</h2>
            <p>
                Different cookies have different retention periods:
            </p>
            <ul>
                <li><strong>Session cookies:</strong> Deleted when you close your browser</li>
                <li><strong>Short-term cookies:</strong> 24 hours to 90 days</li>
                <li><strong>Long-term cookies:</strong> 1 year to 2 years</li>
            </ul>
            <p>
                You can delete cookies manually at any time through your browser settings.
            </p>

            <h2>13. Children's Privacy</h2>
            <p>
                SastoMahango is not intended for children under 13. We do not knowingly collect information from children. If you are a parent or guardian and believe your child has provided us with information, please contact us.
            </p>

            <h2>14. Contact Us About Cookies</h2>
            <p>
                If you have questions about our cookie policy, please contact us:
            </p>
            
            <div class="info-box">
                <h4>Contact Information</h4>
                <p style="margin-bottom: 0.5rem;"><strong>Email:</strong> privacy@sastomahango.com</p>
                <p style="margin-bottom: 0.5rem;"><strong>Support:</strong> support@sastomahango.com</p>
                <p style="margin-bottom: 0.5rem;"><strong>Phone:</strong> +977 1-XXXXXXX</p>
                <p style="margin-bottom: 0;"><strong>Address:</strong> Kathmandu, Nepal</p>
            </div>

            <h2>15. Additional Resources</h2>
            <p>
                For more information about cookies and online privacy:
            </p>
            <ul>
                <li><a href="https://www.allaboutcookies.org/" target="_blank" rel="noopener">All About Cookies</a></li>
                <li><a href="https://www.aboutcookies.org/" target="_blank" rel="noopener">About Cookies</a></li>
                <li><a href="https://tools.google.com/dlpage/gaoptout" target="_blank" rel="noopener">Google Analytics Opt-out</a></li>
            </ul>

            <div class="control-box mt-4">
                <h4><i class="bi bi-check-circle me-2"></i>Your Privacy Matters</h4>
                <p style="margin-bottom: 0;">
                    We are committed to transparency about our cookie usage. You have full control over which cookies you accept. For more details about how we handle your data, please read our <a href="privacy-policy.php">Privacy Policy</a> and <a href="terms-of-service.php">Terms of Service</a>.
                </p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer style="background: #1f2937; color: #f9fafb; padding: 2rem 0; margin-top: 4rem;">
        <div class="container text-center">
            <p style="margin: 0;">&copy; <?php echo date('Y'); ?> SastoMahango. All rights reserved.</p>
            <div style="margin-top: 1rem;">
                <a href="privacy-policy.php" style="color: #9ca3af; text-decoration: none; margin: 0 1rem;">Privacy Policy</a>
                <a href="terms-of-service.php" style="color: #9ca3af; text-decoration: none; margin: 0 1rem;">Terms of Service</a>
                <a href="cookie-policy.php" style="color: #9ca3af; text-decoration: none; margin: 0 1rem;">Cookie Policy</a>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

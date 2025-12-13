<?php
/**
 * Privacy Policy Page
 */

define('SASTOMAHANGO_APP', true);
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'Privacy Policy';
$metaDescription = 'Learn how SastoMahango collects, uses, and protects your personal information. Read our comprehensive privacy policy.';
$additionalCSS = 'pages/legal.css';

include __DIR__ . '/../includes/header_professional.php';
?>

<!-- Hero Section -->
<div class="legal-hero">
    <div class="container text-center">
        <h1><i class="bi bi-shield-lock me-3"></i>Privacy Policy</h1>
        <p class="lead">Your privacy is important to us. Learn how we protect your data.</p>
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
            <strong>Last Updated:</strong> November 23, 2025
        </div>

        <div class="legal-section">
            <h2><i class="bi bi-info-circle"></i>1. Introduction</h2>
            <p>
                Welcome to MulyaSuchi ("we," "our," or "us"). We are committed to protecting your personal information and your right to privacy. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit our website and use our services.
            </p>
            <p>
                By accessing or using MulyaSuchi, you agree to the terms of this Privacy Policy. If you do not agree with the terms of this Privacy Policy, please do not access the site.
            </p>
        </div>

        <div class="legal-section">
            <h2><i class="bi bi-database"></i>2. Information We Collect</h2>
            
            <h3>2.1 Personal Information</h3>
            <p>We may collect the following types of personal information:</p>
            <ul>
                <li><strong>Account Information:</strong> Name, email address, username, and password when you register as a contributor</li>
                <li><strong>Contact Information:</strong> Email address, phone number, and physical address if provided</li>
                <li><strong>Profile Information:</strong> Display name, profile picture, and bio (for contributors)</li>
                <li><strong>Communication Data:</strong> Messages, feedback, and correspondence with our support team</li>
            </ul>

            <h3>2.2 Automatically Collected Information</h3>
            <p>When you visit our website, we automatically collect certain information:</p>
            <ul>
                <li><strong>Device Information:</strong> IP address, browser type, operating system, and device identifiers</li>
                <li><strong>Usage Data:</strong> Pages viewed, time spent on pages, click data, and navigation patterns</li>
                <li><strong>Location Data:</strong> General geographic location based on IP address</li>
                <li><strong>Cookies and Tracking Technologies:</strong> Information collected through cookies and similar technologies</li>
            </ul>

            <h3>2.3 Information from Contributors</h3>
            <p>If you contribute price data to SastoMahango, we collect:</p>
            <ul>
                <li>Product names and descriptions</li>
                <li>Price information and market locations</li>
                <li>Photos or images of products (if uploaded)</li>
                <li>Timestamps and verification data</li>
            </ul>
        </div>

        <div class="legal-section">
            <h2><i class="bi bi-gear"></i>3. How We Use Your Information</h2>
            <p>We use the information we collect for the following purposes:</p>
            <ul>
                <li><strong>Provide Services:</strong> To operate and maintain our price tracking platform</li>
                <li><strong>User Authentication:</strong> To verify your identity and manage your account</li>
                <li><strong>Data Verification:</strong> To verify and validate price information submitted by contributors</li>
                <li><strong>Communication:</strong> To send you updates, newsletters, and important notifications</li>
                <li><strong>Improvement:</strong> To analyze usage patterns and improve our services</li>
                <li><strong>Security:</strong> To protect against fraud, unauthorized access, and other malicious activities</li>
                <li><strong>Legal Compliance:</strong> To comply with legal obligations and enforce our terms</li>
                <li><strong>Analytics:</strong> To understand user behavior and optimize the user experience</li>
            </ul>
        </div>

        <div class="legal-section">
            <h2><i class="bi bi-share"></i>4. Information Sharing and Disclosure</h2>
            
            <h3>4.1 Public Information</h3>
            <p>The following information is publicly available on SastoMahango:</p>
            <ul>
                <li>Product names, prices, and descriptions</li>
                <li>Market locations and price history</li>
                <li>Contributor usernames (not full names or email addresses)</li>
                <li>Date and time of price updates</li>
            </ul>

            <h3>4.2 Third-Party Service Providers</h3>
            <p>We may share your information with trusted third-party service providers who assist us in:</p>
            <ul>
                <li>Hosting and server management</li>
                <li>Email delivery services</li>
                <li>Analytics and performance monitoring</li>
                <li>Payment processing (if applicable)</li>
            </ul>
            <p>These providers are contractually obligated to protect your information and use it only for the purposes we specify.</p>

            <h3>4.3 Legal Requirements</h3>
            <p>We may disclose your information if required by law or in response to:</p>
            <ul>
                <li>Court orders or legal processes</li>
                <li>Government requests or investigations</li>
                <li>Protection of our rights, property, or safety</li>
                <li>Prevention of fraud or security threats</li>
            </ul>
        </div>

        <div class="legal-section">
            <h2><i class="bi bi-shield-check"></i>5. Data Security</h2>
            <p>We implement industry-standard security measures to protect your information:</p>
            <ul>
                <li><strong>Encryption:</strong> HTTPS/SSL encryption for data transmission</li>
                <li><strong>Password Security:</strong> Passwords are hashed and salted using secure algorithms</li>
                <li><strong>Access Controls:</strong> Limited access to personal data by authorized personnel only</li>
                <li><strong>Regular Monitoring:</strong> Continuous monitoring for security vulnerabilities</li>
                <li><strong>Data Backup:</strong> Regular backups to prevent data loss</li>
            </ul>
            <div class="highlight-box">
                <p>However, no method of transmission over the internet is 100% secure. We cannot guarantee absolute security of your data.</p>
            </div>
        </div>

        <div class="legal-section">
            <h2><i class="bi bi-cookie"></i>6. Cookies and Tracking Technologies</h2>
            <p>We use cookies and similar tracking technologies to enhance your experience:</p>
            <ul>
                <li><strong>Essential Cookies:</strong> Required for site functionality and security</li>
                <li><strong>Preference Cookies:</strong> Remember your settings and preferences (e.g., theme selection)</li>
                <li><strong>Analytics Cookies:</strong> Help us understand how users interact with our site</li>
                <li><strong>Session Cookies:</strong> Maintain your login session</li>
            </ul>
            <p>You can control cookies through your browser settings. Disabling cookies may affect site functionality.</p>
        </div>

        <div class="legal-section">
            <h2><i class="bi bi-person-check"></i>7. Your Privacy Rights</h2>
            <p>You have the following rights regarding your personal information:</p>
            <ul>
                <li><strong>Access:</strong> Request a copy of the personal data we hold about you</li>
                <li><strong>Correction:</strong> Request correction of inaccurate or incomplete data</li>
                <li><strong>Deletion:</strong> Request deletion of your personal data (subject to legal obligations)</li>
                <li><strong>Data Portability:</strong> Request your data in a structured, machine-readable format</li>
                <li><strong>Opt-Out:</strong> Unsubscribe from marketing communications</li>
                <li><strong>Withdraw Consent:</strong> Withdraw consent for data processing where applicable</li>
            </ul>
            <p>To exercise these rights, contact us at <strong>privacy@sastomahango.com</strong></p>
        </div>

        <div class="legal-section">
            <h2><i class="bi bi-clock-history"></i>8. Data Retention</h2>
            <p>We retain your personal information for as long as necessary to:</p>
            <ul>
                <li>Provide our services to you</li>
                <li>Comply with legal, regulatory, or contractual obligations</li>
                <li>Resolve disputes and enforce our agreements</li>
                <li>Maintain accurate price history and analytics</li>
            </ul>
            <p>When data is no longer needed, we securely delete or anonymize it.</p>
        </div>

        <div class="legal-section">
            <h2><i class="bi bi-people"></i>9. Children's Privacy</h2>
            <p>
                SastoMahango is not intended for children under the age of 13. We do not knowingly collect personal information from children. If you believe we have inadvertently collected information from a child, please contact us immediately at <strong>privacy@sastomahango.com</strong>.
            </p>
        </div>

        <div class="legal-section">
            <h2><i class="bi bi-envelope"></i>10. Contact Us</h2>
            <p>If you have questions, concerns, or requests regarding this Privacy Policy or our data practices, please contact us:</p>
            
            <div class="contact-box">
                <h3>Contact Information</h3>
                <p><strong>Email:</strong> privacy@sastomahango.com</p>
                <p><strong>Phone:</strong> +977 1-XXXXXXX</p>
                <p><strong>Address:</strong> Kathmandu, Nepal</p>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer_professional.php'; ?>

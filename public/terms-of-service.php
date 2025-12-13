<?php
/**
 * Terms of Service Page
 */

define('SASTOMAHANGO_APP', true);
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'Terms of Service';
$metaDescription = 'Read the terms and conditions for using SastoMahango. Understand your rights and responsibilities as a user.';
$additionalCSS = 'pages/legal.css';

include __DIR__ . '/../includes/header_professional.php';
?>

<!-- Hero Section -->
<div class="legal-hero">
    <div class="container text-center">
        <h1><i class="bi bi-file-text me-3"></i>Terms of Service</h1>
        <p class="lead">Understanding your rights and responsibilities on SastoMahango</p>
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
            <h2><i class="bi bi-check-circle"></i>1. Agreement to Terms</h2>
            <p>
                Welcome to SastoMahango. These Terms of Service ("Terms") govern your access to and use of our website, services, and applications (collectively, the "Service"). By accessing or using SastoMahango, you agree to be bound by these Terms.
            </p>
            <div class="highlight-box">
                <p><strong>IF YOU DO NOT AGREE TO THESE TERMS, DO NOT USE OUR SERVICE.</strong></p>
            </div>
        </div>

        <div class="legal-section">
            <h2><i class="bi bi-info-circle"></i>2. Description of Service</h2>
            <p>SastoMahango is a price tracking and market intelligence platform that provides:</p>
            <ul>
                <li>Real-time price information for various products across Nepal</li>
                <li>Historical price data and trend analysis</li>
                <li>Market insights and comparisons</li>
                <li>Community-contributed price updates</li>
                <li>Search and filtering tools for product discovery</li>
            </ul>
        </div>

        <div class="legal-section">
            <h2><i class="bi bi-person-check"></i>3. Eligibility</h2>
            <p>To use SastoMahango, you must:</p>
            <ul>
                <li>Be at least 13 years of age</li>
                <li>Have the legal capacity to enter into binding contracts</li>
                <li>Not be prohibited from using our Service under applicable laws</li>
                <li>Provide accurate and complete registration information (for contributors)</li>
            </ul>
        </div>

        <div class="legal-section">
            <h2><i class="bi bi-person-badge"></i>4. User Accounts</h2>
            
            <h3>4.1 Account Creation</h3>
            <p>To become a contributor, you must create an account by providing:</p>
            <ul>
                <li>Valid email address</li>
                <li>Username and password</li>
                <li>Full name and contact information</li>
            </ul>

            <h3>4.2 Account Security</h3>
            <p>You are responsible for:</p>
            <ul>
                <li>Maintaining the confidentiality of your account credentials</li>
                <li>All activities that occur under your account</li>
                <li>Notifying us immediately of any unauthorized access</li>
                <li>Ensuring your account information is accurate and up-to-date</li>
            </ul>
        </div>

        <div class="legal-section">
            <h2><i class="bi bi-x-circle"></i>5. User Conduct and Prohibited Activities</h2>
            
            <h3>You Agree NOT to:</h3>
            <ul>
                <li><strong>Submit False Information:</strong> Provide inaccurate, misleading, or fraudulent price data</li>
                <li><strong>Impersonate Others:</strong> Pretend to be another person or entity</li>
                <li><strong>Violate Laws:</strong> Use the Service for illegal purposes or violate any applicable laws</li>
                <li><strong>Harass Users:</strong> Engage in harassment, abuse, or threatening behavior</li>
                <li><strong>Spam:</strong> Send unsolicited messages or engage in spam activities</li>
                <li><strong>Scrape Data:</strong> Use automated tools to scrape or harvest data without permission</li>
                <li><strong>Interfere:</strong> Disrupt or interfere with the Service or servers</li>
                <li><strong>Distribute Malware:</strong> Upload viruses, malware, or harmful code</li>
            </ul>
        </div>

        <div class="legal-section">
            <h2><i class="bi bi-pencil-square"></i>6. Contributor Guidelines</h2>
            
            <h3>6.1 Price Submission</h3>
            <p>When submitting price information, you must:</p>
            <ul>
                <li>Provide accurate and current price data</li>
                <li>Verify prices from legitimate sources</li>
                <li>Include correct product names and descriptions</li>
                <li>Specify the market location accurately</li>
                <li>Update prices regularly to maintain accuracy</li>
            </ul>

            <h3>6.2 Content Ownership</h3>
            <p>
                By submitting price data and content, you grant SastoMahango a worldwide, non-exclusive, royalty-free license to use, display, reproduce, and distribute your contributions. You retain ownership of your original content.
            </p>
        </div>

        <div class="legal-section">
            <h2><i class="bi bi-shield-check"></i>7. Data Security</h2>
            <p>We implement industry-standard security measures:</p>
            <ul>
                <li><strong>Encryption:</strong> HTTPS/SSL encryption for data transmission</li>
                <li><strong>Password Security:</strong> Passwords are hashed and salted</li>
                <li><strong>Access Controls:</strong> Limited access to personal data</li>
                <li><strong>Regular Monitoring:</strong> Continuous security monitoring</li>
            </ul>
        </div>

        <div class="legal-section">
            <h2><i class="bi bi-exclamation-triangle"></i>8. Disclaimer of Warranties</h2>
            
            <div class="highlight-box">
                <p><strong>THE SERVICE IS PROVIDED "AS IS" AND "AS AVAILABLE" WITHOUT WARRANTIES OF ANY KIND.</strong></p>
            </div>

            <p>We do not warrant that:</p>
            <ul>
                <li>The Service will be uninterrupted, secure, or error-free</li>
                <li>Price information is always accurate or up-to-date</li>
                <li>Defects will be corrected</li>
                <li>The Service is free of viruses or harmful components</li>
            </ul>
        </div>

        <div class="legal-section">
            <h2><i class="bi bi-shield-x"></i>9. Limitation of Liability</h2>
            <p>TO THE MAXIMUM EXTENT PERMITTED BY LAW, SASTOMAHANGO SHALL NOT BE LIABLE FOR:</p>
            <ul>
                <li>Any indirect, incidental, special, or consequential damages</li>
                <li>Loss of profits, revenue, data, or goodwill</li>
                <li>Service interruptions or downtime</li>
                <li>Errors or inaccuracies in price information</li>
                <li>Unauthorized access to your account</li>
            </ul>
        </div>

        <div class="legal-section">
            <h2><i class="bi bi-pencil"></i>10. Changes to Terms</h2>
            <p>We may update these Terms periodically. Changes will be effective:</p>
            <ul>
                <li>Upon posting to our website (for minor changes)</li>
                <li>After notification via email (for material changes)</li>
            </ul>
            <p>Your continued use of the Service after changes constitutes acceptance of the new Terms.</p>
        </div>

        <div class="legal-section">
            <h2><i class="bi bi-envelope"></i>11. Contact Information</h2>
            <p>For questions about these Terms, please contact us:</p>
            
            <div class="contact-box">
                <h3>Contact Information</h3>
                <p><strong>Email:</strong> legal@sastomahango.com</p>
                <p><strong>Support:</strong> support@sastomahango.com</p>
                <p><strong>Phone:</strong> +977 1-XXXXXXX</p>
                <p><strong>Address:</strong> Kathmandu, Nepal</p>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer_professional.php'; ?>

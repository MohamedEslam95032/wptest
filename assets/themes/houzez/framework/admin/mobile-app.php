<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Hide admin notices on this page
remove_all_actions('admin_notices');
remove_all_actions('all_admin_notices');
?>

<style>
    /* Hide WordPress admin notices on this page */
    .notice, .update-nag, .updated, .error, .is-dismissible { display: none !important; }
    
    /* Simple Admin Page Styles */
    .houzez-mobile-app-simple {
        max-width: 900px;
        margin: 20px auto;
        text-align: center;
        padding: 20px;
    }
    
    .houzez-mobile-app-simple h1 {
        font-size: 28px;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 8px;
    }
    
    .houzez-mobile-app-simple .offer-badge {
        display: inline-block;
        background: #ef4444;
        color: #fff;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 15px;
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
    
    .houzez-mobile-app-simple h2 {
        font-size: 24px;
        font-weight: 500;
        color: #475569;
        margin-bottom: 15px;
        line-height: 1.3;
    }
    
    .houzez-mobile-app-simple .strike-price {
        text-decoration: line-through;
        color: #94a3b8;
    }
    
    .houzez-mobile-app-simple .free-text {
        color: #10b981;
        font-weight: 700;
    }
    
    .houzez-mobile-app-simple p {
        font-size: 16px;
        color: #64748b;
        margin-bottom: 20px;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
        line-height: 1.5;
    }
    
    .houzez-mobile-app-simple .cta-button {
        display: inline-block;
        background: #2563eb;
        color: #fff;
        padding: 16px 40px;
        font-size: 18px;
        font-weight: 600;
        text-decoration: none;
        border-radius: 8px;
        transition: all 0.3s ease;
        box-shadow: 0 10px 25px rgba(37, 99, 235, 0.3);
    }
    
    .houzez-mobile-app-simple .cta-button:hover {
        background: #1d4ed8;
        transform: translateY(-2px);
        box-shadow: 0 15px 35px rgba(37, 99, 235, 0.4);
    }
    
    .houzez-mobile-app-simple .cta-arrow {
        display: inline-block;
        margin-left: 10px;
        transition: transform 0.3s ease;
    }
    
    .houzez-mobile-app-simple .cta-button:hover .cta-arrow {
        transform: translateX(5px);
    }
    
    .houzez-mobile-app-simple .content-wrapper {
        display: flex;
        gap: 30px;
        align-items: flex-start;
        margin: 20px 0;
    }
    
    .houzez-mobile-app-simple .left-column {
        flex: 1;
        text-align: center;
    }
    
    .houzez-mobile-app-simple .right-column {
        flex: 1;
    }
    
    .houzez-mobile-app-simple .features-list {
        max-width: 100%;
        text-align: left;
        background: #f8fafc;
        padding: 20px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
    }
    
    .houzez-mobile-app-simple .features-list h3 {
        font-size: 16px;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 12px;
        text-align: center;
    }
    
    .houzez-mobile-app-simple .features-list ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .houzez-mobile-app-simple .features-list li {
        padding: 6px 0;
        color: #475569;
        display: flex;
        align-items: center;
        font-size: 14px;
    }
    
    .houzez-mobile-app-simple .features-list li:before {
        content: "✓";
        color: #10b981;
        font-weight: bold;
        margin-right: 10px;
        font-size: 18px;
    }
    
    .houzez-mobile-app-simple .contact-info {
        margin-top: 25px;
        font-size: 14px;
        color: #64748b;
    }
    
    .houzez-mobile-app-simple .contact-info a {
        color: #2563eb;
        text-decoration: none;
    }
    
    .houzez-mobile-app-simple .contact-info a:hover {
        text-decoration: underline;
    }
    
    .houzez-mobile-app-simple .app-preview {
        margin: 20px auto;
        max-width: 250px;
        height: 250px;
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 50px;
        color: #2563eb;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    }
    
    @media (max-width: 768px) {
        .houzez-mobile-app-simple .content-wrapper {
            flex-direction: column;
        }
        
        .houzez-mobile-app-simple .app-preview {
            margin: 20px auto;
        }
    }
</style>

<div class="wrap houzez-mobile-app-simple">
    <h1><?php esc_html_e('Houzez Flutter App Solution', 'houzez'); ?></h1>
    
    <span class="offer-badge"><?php esc_html_e('LIMITED TIME - 30 DAYS ONLY', 'houzez'); ?></span>
    
    <h2>
        <?php esc_html_e('Get Your', 'houzez'); ?> 
        <span class="strike-price">$4,999</span> 
        <?php esc_html_e('Flutter App', 'houzez'); ?> 
        <span class="free-text"><?php esc_html_e('FREE!', 'houzez'); ?></span>
    </h2>
    
    <p>
        <?php esc_html_e('Professional Flutter app for iOS and Android. Only pay $699 for setup & app store submission. This offer expires in 30 days.', 'houzez'); ?>
    </p>
    
    <div class="content-wrapper">
        <div class="left-column">
            <div class="app-preview">📱</div>
            
            <a href="<?php echo home_url('/mobile-app-offer/'); ?>" target="_blank" class="cta-button">
                <?php esc_html_e('View Limited Time Offer', 'houzez'); ?>
                <span class="cta-arrow">→</span>
            </a>
        </div>
        
        <div class="right-column">
            <div class="features-list">
                <h3><?php esc_html_e('Free Flutter App Includes:', 'houzez'); ?></h3>
                <ul>
                    <li><?php esc_html_e('Native Flutter App (iOS & Android)', 'houzez'); ?></li>
                    <li><?php esc_html_e('White-Label Branding', 'houzez'); ?></li>
                    <li><?php esc_html_e('Lifetime Updates', 'houzez'); ?></li>
                    <li><?php esc_html_e('Push Notifications', 'houzez'); ?></li>
                    <li><?php esc_html_e('Real-Time Sync with Houzez', 'houzez'); ?></li>
                    <li><?php esc_html_e('$699 Setup & App Store Submission', 'houzez'); ?></li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="contact-info">
        <?php esc_html_e('Questions? Contact our team at', 'houzez'); ?> 
        <a href="mailto:mobile@houzez.com">mobile@houzez.com</a>
    </div>
</div>
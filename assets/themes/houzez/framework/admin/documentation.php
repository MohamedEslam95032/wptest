<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_user = wp_get_current_user();


// Documentation sections data
$documentation_sections = [
    'general' => [
        'title' => __('General', 'houzez'),
        'icon' => 'dashicons-admin-page',
        'description' => __('Basic information about Houzez theme', 'houzez'),
        'articles' => [
            ['title' => __('Introduction', 'houzez'), 'url' => 'https://favethemes.zendesk.com/hc/en-us/articles/360038456511-Introduction'],
            ['title' => __('Requirements', 'houzez'), 'url' => 'https://favethemes.zendesk.com/hc/en-us/articles/360038085092-Requirements'],
            ['title' => __('What\'s Included In My Purchase?', 'houzez'), 'url' => 'https://favethemes.zendesk.com/hc/en-us/articles/360038456531-What-s-Included-In-My-Purchase-'],
            ['title' => __('Where Is My Purchase Code?', 'houzez'), 'url' => 'https://favethemes.zendesk.com/hc/en-us/articles/360038085112-Where-Is-My-Purchase-Code-'],
            ['title' => __('How To Get Support', 'houzez'), 'url' => 'https://favethemes.zendesk.com/hc/en-us/articles/360038085252-How-To-Get-Support'],
            ['title' => __('Licensing', 'houzez'), 'url' => 'https://favethemes.zendesk.com/hc/en-us/articles/360038091352-Licensing'],
            ['title' => __('Hosting', 'houzez'), 'url' => 'https://favethemes.zendesk.com/hc/en-us/articles/360038091412-Hosting'],
        ],
        'section_url' => 'https://favethemes.zendesk.com/hc/en-us/sections/360007646111-General'
    ],
    'get_started' => [
        'title' => __('Get Started', 'houzez'),
        'icon' => 'dashicons-welcome-learn-more',
        'description' => __('Installation and setup guides', 'houzez'),
        'articles' => [
            ['title' => __('Download Files', 'houzez'), 'url' => 'https://favethemes.zendesk.com/hc/en-us/articles/360038091632-Download-Files-'],
            ['title' => __('Install Theme Via WordPress Admin Panel (Recommended)', 'houzez'), 'url' => 'https://favethemes.zendesk.com/hc/en-us/articles/360038462631-Install-Theme-Via-WordPress-Admin-Panel-Recommended-'],
            ['title' => __('Install Theme Via FTP (Expert)', 'houzez'), 'url' => 'https://favethemes.zendesk.com/hc/en-us/articles/360038091712-Install-Theme-Via-FTP-Expert-'],
            ['title' => __('Theme Activation', 'houzez'), 'url' => 'https://favethemes.zendesk.com/hc/en-us/articles/360038091752-Theme-Activation'],
            ['title' => __('Import The Demo Content', 'houzez'), 'url' => 'https://favethemes.zendesk.com/hc/en-us/articles/360038462711-Import-The-Demo-Content-'],
            ['title' => __('Switch Between Different Demos', 'houzez'), 'url' => 'https://favethemes.zendesk.com/hc/en-us/articles/360038462731-Switch-Between-Different-Demos-'],
            ['title' => __('How To Update Houzez', 'houzez'), 'url' => 'https://favethemes.zendesk.com/hc/en-us/articles/360038091792-How-To-Update-Houzez'],
            ['title' => __('Child Theme Installation', 'houzez'), 'url' => 'https://favethemes.zendesk.com/hc/en-us/articles/360038091772-Child-Theme-Installation'],
            ['title' => __('Common Installation Errors', 'houzez'), 'url' => 'https://favethemes.zendesk.com/hc/en-us/articles/360038091832-Common-Installation-Errors-'],
        ],
        'section_url' => 'https://favethemes.zendesk.com/hc/en-us/sections/360007543632-Get-Started'
    ]
];

$total_articles = 0;
foreach ($documentation_sections as $section) {
    $total_articles += count($section['articles']);
}
?>

<div class="wrap houzez-template-library">
    <div class="houzez-header">
        <div class="houzez-header-content">
            <div class="houzez-logo">
                <h1><?php esc_html_e('Documentation & Help', 'houzez'); ?></h1>
            </div>
            <div class="houzez-header-actions">
                <a href="https://favethemes.zendesk.com/hc/en-us/categories/360002468932-Houzez" target="_blank" class="houzez-btn houzez-btn-primary">
                    <i class="dashicons dashicons-external"></i>
                    <?php esc_html_e('View Online Documentation', 'houzez'); ?>
                </a>
                <a href="https://favethemes.zendesk.com/hc/en-us/requests/new" target="_blank" class="houzez-btn houzez-btn-secondary">
                    <i class="dashicons dashicons-sos"></i>
                    <?php esc_html_e('Get Support', 'houzez'); ?>
                </a>
            </div>
        </div>
    </div>

    <div class="houzez-dashboard">
        <!-- Quick Stats -->
        <div class="houzez-stats-grid">
            <div class="houzez-stat-card">
                <div class="houzez-stat-icon">
                    <i class="dashicons dashicons-book"></i>
                </div>
                <div class="houzez-stat-content">
                    <h3><?php echo intval($total_articles); ?></h3>
                    <p><?php esc_html_e('Help Articles', 'houzez'); ?></p>
                </div>
            </div>

            <div class="houzez-stat-card">
                <div class="houzez-stat-icon">
                    <i class="dashicons dashicons-category"></i>
                </div>
                <div class="houzez-stat-content">
                    <h3><?php echo count($documentation_sections); ?></h3>
                    <p><?php esc_html_e('Documentation Sections', 'houzez'); ?></p>
                </div>
            </div>

            <div class="houzez-stat-card">
                <div class="houzez-stat-icon">
                    <i class="dashicons dashicons-sos"></i>
                </div>
                <div class="houzez-stat-content">
                    <h3><?php esc_html_e('Dedicated', 'houzez'); ?></h3>
                    <p><?php esc_html_e('Support Team', 'houzez'); ?></p>
                </div>
            </div>

            <div class="houzez-stat-card">
                <div class="houzez-stat-icon">
                    <i class="dashicons dashicons-update"></i>
                </div>
                <div class="houzez-stat-content">
                    <h3><?php esc_html_e('Always', 'houzez'); ?></h3>
                    <p><?php esc_html_e('Up to Date', 'houzez'); ?></p>
                </div>
            </div>
        </div>

        <!-- Quick Actions Card -->
        <div class="houzez-main-card">
            <div class="houzez-card-header">
                <h2>
                    <i class="dashicons dashicons-admin-tools"></i>
                    <?php esc_html_e('Quick Actions', 'houzez'); ?>
                </h2>
                <div class="houzez-status-badge houzez-status-success">
                    <?php esc_html_e('Available', 'houzez'); ?>
                </div>
            </div>
            <div class="houzez-card-body">
                <div class="houzez-actions houzez-actions-three-column">
                    <div class="houzez-action">
                        <div class="houzez-action-icon">
                            <i class="dashicons dashicons-book-alt"></i>
                        </div>
                        <div class="houzez-action-content">
                            <h4><?php esc_html_e('Browse Documentation', 'houzez'); ?></h4>
                            <p><?php esc_html_e('Access comprehensive guides and tutorials for all Houzez features and functionality.', 'houzez'); ?></p>
                            <a href="https://favethemes.zendesk.com/hc/en-us/categories/360002468932-Houzez" target="_blank" class="houzez-btn houzez-btn-outline">
                                <?php esc_html_e('View Documentation', 'houzez'); ?>
                            </a>
                        </div>
                    </div>

                    <div class="houzez-action">
                        <div class="houzez-action-icon">
                            <i class="dashicons dashicons-sos"></i>
                        </div>
                        <div class="houzez-action-content">
                            <h4><?php esc_html_e('Contact Support', 'houzez'); ?></h4>
                            <p><?php esc_html_e('Get help from our dedicated support team for technical issues and questions.', 'houzez'); ?></p>
                            <a href="https://favethemes.zendesk.com/hc/en-us/requests/new" target="_blank" class="houzez-btn houzez-btn-outline">
                                <?php esc_html_e('Submit Ticket', 'houzez'); ?>
                            </a>
                        </div>
                    </div>

                    <div class="houzez-action">
                        <div class="houzez-action-icon">
                            <i class="dashicons dashicons-video-alt3"></i>
                        </div>
                        <div class="houzez-action-content">
                            <h4><?php esc_html_e('Video Tutorials', 'houzez'); ?></h4>
                            <p><?php esc_html_e('Watch step-by-step video guides to learn how to use Houzez effectively.', 'houzez'); ?></p>
                            <a href="https://www.youtube.com/@favethemesmedia9833" target="_blank" class="houzez-btn houzez-btn-outline">
                                <?php esc_html_e('Watch Videos', 'houzez'); ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Documentation Sections -->
        <?php foreach ($documentation_sections as $section_key => $section): ?>
        <div class="houzez-main-card">
            <div class="houzez-card-header">
                <h2>
                    <i class="dashicons <?php echo esc_attr($section['icon']); ?>"></i>
                    <?php echo esc_html($section['title']); ?>
                </h2>
                <div class="houzez-status-badge houzez-status-success">
                    <?php echo count($section['articles']); ?> <?php esc_html_e('Articles', 'houzez'); ?>
                </div>
            </div>
            <div class="houzez-card-body">
                <p class="houzez-description">
                    <?php echo esc_html($section['description']); ?>
                </p>
                
                <div class="houzez-documentation-grid">
                    <?php foreach ($section['articles'] as $article): ?>
                    <div class="houzez-doc-item">
                        <div class="houzez-doc-icon">
                            <i class="dashicons dashicons-media-document"></i>
                        </div>
                        <div class="houzez-doc-content">
                            <h4>
                                <a href="<?php echo esc_url($article['url']); ?>" target="_blank">
                                    <?php echo esc_html($article['title']); ?>
                                </a>
                            </h4>
                            <div class="houzez-doc-meta">
                                <span class="houzez-doc-type">
                                    <i class="dashicons dashicons-book"></i>
                                    <?php esc_html_e('Guide', 'houzez'); ?>
                                </span>
                                <a href="<?php echo esc_url($article['url']); ?>" target="_blank" class="houzez-doc-link">
                                    <i class="dashicons dashicons-external"></i>
                                    <?php esc_html_e('Read More', 'houzez'); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="houzez-section-footer">
                    <a href="<?php echo esc_url($section['section_url']); ?>" target="_blank" class="houzez-btn houzez-btn-outline">
                        <i class="dashicons dashicons-external"></i>
                        <?php esc_html_e('View All Articles', 'houzez'); ?>
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

        <!-- Support Information Card -->
        <div class="houzez-main-card">
            <div class="houzez-card-header">
                <h2>
                    <i class="dashicons dashicons-info"></i>
                    <?php esc_html_e('Support Information', 'houzez'); ?>
                </h2>
            </div>
            
            <div class="houzez-card-body">
                <div class="houzez-actions">
                    <div class="houzez-action">
                        <div class="houzez-action-icon">
                            <i class="dashicons dashicons-clock"></i>
                        </div>
                        <div class="houzez-action-content">
                            <h4><?php esc_html_e('Response Time', 'houzez'); ?></h4>
                            <p><?php esc_html_e('We typically respond to support tickets within 24 hours during business days.', 'houzez'); ?></p>
                        </div>
                    </div>

                    <div class="houzez-action">
                        <div class="houzez-action-icon">
                            <i class="dashicons dashicons-admin-network"></i>
                        </div>
                        <div class="houzez-action-content">
                            <h4><?php esc_html_e('License Required', 'houzez'); ?></h4>
                            <p><?php esc_html_e('Premium support is available for verified license holders with valid purchase codes.', 'houzez'); ?></p>
                        </div>
                    </div>

                    <div class="houzez-action">
                        <div class="houzez-action-icon">
                            <i class="dashicons dashicons-format-chat"></i>
                        </div>
                        <div class="houzez-action-content">
                            <h4><?php esc_html_e('Support Channels', 'houzez'); ?></h4>
                            <p><?php esc_html_e('Get help through our support ticket system, documentation, and community forum.', 'houzez'); ?></p>
                        </div>
                    </div>

                    <div class="houzez-action">
                        <div class="houzez-action-icon">
                            <i class="dashicons dashicons-update"></i>
                        </div>
                        <div class="houzez-action-content">
                            <h4><?php esc_html_e('Regular Updates', 'houzez'); ?></h4>
                            <p><?php esc_html_e('Documentation is regularly updated with new features and improvements.', 'houzez'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
	</div>
</div>

<style>
.houzez-documentation-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
    margin-bottom: 25px;
}

.houzez-doc-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 6px;
    border: 1px solid #e9ecef;
    transition: all 0.2s ease;
}

.houzez-doc-item:hover {
    background: #e9ecef;
    border-color: #0088cc;
}

.houzez-doc-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #004274 0%, #0088cc 100%);
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 16px;
    flex-shrink: 0;
}

.houzez-doc-content {
    flex: 1;
    min-width: 0;
}

.houzez-doc-content h4 {
    margin: 0 0 8px 0;
    font-size: 14px;
    font-weight: 600;
    line-height: 1.4;
}

.houzez-doc-content h4 a {
    color: #1d2327;
    text-decoration: none;
    transition: color 0.2s ease;
}

.houzez-doc-content h4 a:hover {
    color: #0088cc;
}

.houzez-doc-meta {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    font-size: 12px;
}

.houzez-doc-type {
    display: flex;
    align-items: center;
    gap: 4px;
    color: #646970;
}

.houzez-doc-type .dashicons {
    font-size: 14px;
    width: 14px;
    height: 14px;
}

.houzez-doc-link {
    display: flex;
    align-items: center;
    gap: 4px;
    color: #0088cc;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.2s ease;
}

.houzez-doc-link:hover {
    color: #006ca8;
}

.houzez-doc-link .dashicons {
    font-size: 12px;
    width: 12px;
    height: 12px;
}

.houzez-section-footer {
    text-align: center;
    padding-top: 20px;
    border-top: 1px solid #e9ecef;
}

/* Three-column layout for Quick Actions */
.houzez-actions-three-column {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}

.houzez-actions-three-column .houzez-action {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    padding: 25px 20px;
    border: 2px solid #f8f9fa;
    border-radius: 8px;
    transition: all 0.2s ease;
    min-height: 300px;
}

.houzez-actions-three-column .houzez-action:hover {
    border-color: #0088cc;
    background: #f0f8ff;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 136, 204, 0.1);
}

.houzez-actions-three-column .houzez-action-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    background: linear-gradient(135deg, #004274 0%, #0088cc 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
    flex-shrink: 0;
    margin-bottom: 20px;
}

.houzez-actions-three-column .houzez-action-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    width: 100%;
}

.houzez-actions-three-column .houzez-action-content h4 {
    margin: 0 0 12px 0;
    font-size: 16px;
    font-weight: 600;
    color: #1d2327;
}

.houzez-actions-three-column .houzez-action-content p {
    margin: 0 0 20px 0;
    color: #646970;
    font-size: 14px;
    line-height: 1.5;
    flex: 1;
}

.houzez-actions-three-column .houzez-btn {
    margin-top: auto;
    width: 100%;
    justify-content: center;
    padding: 10px 16px;
    font-size: 13px;
}

/* Responsive breakpoints */
@media (max-width: 900px) {
    .houzez-actions-three-column {
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }
}

@media (max-width: 600px) {
    .houzez-doc-item {
        flex-direction: column;
        text-align: center;
    }
    
    .houzez-doc-meta {
        flex-direction: column;
        gap: 8px;
    }
    
    .houzez-actions-three-column {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .houzez-actions-three-column .houzez-action {
        flex-direction: row;
        text-align: left;
        min-height: auto;
        padding: 20px;
        align-items: flex-start;
    }
    
    .houzez-actions-three-column .houzez-action-icon {
        width: 50px;
        height: 50px;
        font-size: 20px;
        margin-bottom: 0;
        margin-right: 15px;
        flex-shrink: 0;
    }
    
    .houzez-actions-three-column .houzez-action-content {
        text-align: left;
    }
    
    .houzez-actions-three-column .houzez-btn {
        width: auto;
        margin-top: 15px;
        align-self: flex-start;
    }
}
</style>
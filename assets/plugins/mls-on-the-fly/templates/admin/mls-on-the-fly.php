<div class="mls-otf-main-page-container">
    <div class="mls-otf-main-page-content-wrapper">
        <div class="mls-otf-main-page-center-content">
            <!-- Page heading -->
            <h1 class="mls-otf-main-page-heading">Welcome to MLS On The Fly®</h1>

            <!-- Top section with Demo Data and Real Data -->
            <div class="mls-otf-main-page-top-section">
                <div class="mls-otf-main-page-half-section mls-otf-main-page-demo-section">
                    <h1 class="mls-otf-main-page-title">Get Demo Data</h1>
                    <p class="mls-otf-main-page-description">Access a wide range of real estate listings with our free
                        demo data.</p>
                    <button class="mls-otf-main-page-btn-primary demo-data-button">Access Demo Data</button>
                </div>
                <div class="mls-otf-main-page-half-section mls-otf-main-page-real-section">
                    <h1 class="mls-otf-main-page-title">Get Real Data</h1>
                    <p class="mls-otf-main-page-description">Sign up for access to live real estate data tailored to
                        your needs.</p>
                    <button class="mls-otf-main-page-btn-secondary real-data-button">Access Real Data</button>
                </div>
            </div>

            <!-- Section: Where we are -->
            <h2 class="mls-otf-main-page-section-title">Where we are</h2>
            <div class="mls-otf-main-page-grid-container">
                <div class="mls-otf-main-page-grid-item us-flag"></div>
                <div class="mls-otf-main-page-grid-item canada-flag"></div>
                <div class="mls-otf-main-page-grid-item mexico-flag"></div>
                <div class="mls-otf-main-page-grid-item ecuador-flag"></div>
            </div>

            <!-- Section: Documentation -->
            <h2 class="mls-otf-main-page-section-title">Documentation</h2>

            <div class="mls-otf-main-page-doc-item">
                <div class="mls-otf-main-page-doc-content">
                    <div class="mls-otf-main-page-icon"></div>
                    <div class="mls-otf-main-page-doc-text">
                        <p class="mls-otf-main-page-doc-title">What is MLS On The Fly ®?</p>
                    </div>
                </div>
                <a target="_blank" href="https://realtyna.com/mls-on-the-fly/" class="mls-otf-main-page-btn-secondary">Read</a>
            </div>

            <div class="mls-otf-main-page-doc-item">
                <div class="mls-otf-main-page-doc-content">
                    <div class="mls-otf-main-page-icon"></div>
                    <div class="mls-otf-main-page-doc-text">
                        <p class="mls-otf-main-page-doc-title">How to Sign Up On RealtyFeed Dashboard?</p>
                    </div>
                </div>
                <a target="_blank" href="https://support.realtyna.com/index.php?/Knowledgebase/Article/View/862/0/how-to-sign-up-on-realtyfeed-dashboard"
                   class="mls-otf-main-page-btn-secondary">Read</a>
            </div>

            <div class="mls-otf-main-page-doc-item">
                <div class="mls-otf-main-page-doc-content">
                    <div class="mls-otf-main-page-icon"></div>
                    <div class="mls-otf-main-page-doc-text">
                        <p class="mls-otf-main-page-doc-title">How to Setup MLS On The Fly ® on Houzez Theme?</p>
                    </div>
                </div>
                <a target="_blank" href="https://support.realtyna.com/index.php?/Knowledgebase/Article/View/869/0/how-to-setup-mls-on-the-fly-on-houzez-theme"
                   class="mls-otf-main-page-btn-secondary">Read</a>
            </div>

            <div class="mls-otf-main-page-doc-item">
                <div class="mls-otf-main-page-doc-content">
                    <div class="mls-otf-main-page-icon"></div>
                    <div class="mls-otf-main-page-doc-text">
                        <p class="mls-otf-main-page-doc-title">How to Setup MLS On The Fly™ on Your Website?</p>
                    </div>
                </div>
                <a target="_blank" href="https://support.realtyna.com/index.php?/Knowledgebase/Article/View/863/0/how-to-setup-mls-on-the-fly-on-your-website"
                   class="mls-otf-main-page-btn-secondary">Read</a>
            </div>
        </div>
    </div>

    <!-- Wizard container, initially hidden -->
    <div id="mls-otf-demo-wizard" class="mls-otf-wizard-container" style="display: none;">
        <div class="progress-bar">
            <div class="step"></div>
            <div class="step"></div>
            <div class="step"></div>
            <div class="step"></div>
            <div class="step"></div>
        </div>

        <!-- Step 1: Register - Verify - Complete Profile -->
        <div class="mls-otf-wizard-step" id="step-1">
            <div class="mls-otf-button-group">
                <button class="mls-otf-wizard-btn btn-next">Next</button>
                <button class="mls-otf-wizard-btn btn-close">Close</button>
            </div>


            <h2>Step 1: Register - Verify - Complete Profile</h2>
            <p>To get API details for Demo or real MLS data, you need to sign up on the RealtyFeed Dashboard! Follow
                these steps:</p>
            <ol>
                <li><strong>Sign Up:</strong> Go to the <a target="_blank" href="https://dashboard.realtyfeed.com">RealtyFeed
                        Dashboard</a> and register your account. If you already have a Realtyna.com account, you can use
                    the same credentials.
                </li>
                <img src="<?php
                echo REALTYNA_MLS_ON_THE_FLY_URL . 'assets/image/wizards/sign-up.jpg' ?>" alt="Sign Up Image"
                     style="max-width:100%;">
                <li><strong>Verify Your Email:</strong> You will receive a verification code in your email. Enter it and
                    proceed.
                </li>
                <li><strong>Complete Your Profile:</strong> Fill in general information, expertise, bio, and social
                    media accounts. These won’t affect your access or permissions.
                </li>
                <img src="<?php
                echo REALTYNA_MLS_ON_THE_FLY_URL . 'assets/image/wizards/verify.jpg' ?>" alt="Verify Email Image"
                     style="max-width:100%;">
            </ol>
            <div class="mls-otf-button-group">
                <button class="mls-otf-wizard-btn btn-prev">Previous</button>
                <button class="mls-otf-wizard-btn btn-next">Next</button>
                <button class="mls-otf-wizard-btn btn-close">Close</button>
            </div>

        </div>

        <!-- Step 2: Choose MLS Demo Package -->
        <div class="mls-otf-wizard-step" id="step-2" style="display: none;">
            <div class="mls-otf-button-group">
                <button class="mls-otf-wizard-btn btn-prev">Previous</button>
                <button class="mls-otf-wizard-btn btn-next">Next</button>
                <button class="mls-otf-wizard-btn btn-close">Close</button>
            </div>

            <h2>Step 2: Choose MLS Demo Package</h2>
            <p>In this step, you can choose your MLS demo package by following these steps:</p>
            <ol>
                <li><strong>Navigate to MLS Verification:</strong> Select <strong>MLS Data API &gt;&gt; MLS Router™ (MLS
                        On The Fly™)</strong> and click Next.
                </li>
                <img src="<?php
                echo REALTYNA_MLS_ON_THE_FLY_URL . 'assets/image/wizards/choose-mls-demo-package.jpg' ?>"
                     alt="Choose MLS Demo Package Image" style="max-width:100%;">
                <li><strong>Search and select your MLS provider:</strong> You can choose more than one MLS if necessary.
                </li>
                <img src="<?php
                echo REALTYNA_MLS_ON_THE_FLY_URL . 'assets/image/wizards/select-mls.jpg' ?>" alt="Select MLS"
                     style="max-width:100%;">
                <li><strong>Proceed to Checkout:</strong> After selecting the MLS demo package, proceed to checkout.
                </li>
                <img src="<?php
                echo REALTYNA_MLS_ON_THE_FLY_URL . 'assets/image/wizards/checkout.jpg' ?>" alt="Checkout Image"
                     style="max-width:100%;">
            </ol>
            <div class="mls-otf-button-group">
                <button class="mls-otf-wizard-btn btn-prev">Previous</button>
                <button class="mls-otf-wizard-btn btn-next">Next</button>
                <button class="mls-otf-wizard-btn btn-close">Close</button>
            </div>

        </div>

        <!-- Step 3: Get Your API Keys -->
        <div class="mls-otf-wizard-step" id="step-3" style="display: none;">
            <div class="mls-otf-button-group">
                <button class="mls-otf-wizard-btn btn-prev">Previous</button>
                <button class="mls-otf-wizard-btn btn-next">Next</button>
                <button class="mls-otf-wizard-btn btn-close">Close</button>
            </div>

            <h2>Step 3: Get Your API Keys</h2>
            <p>Once you've completed the checkout process, access your API keys by following these steps:</p>
            <ol>
                <li><strong>Go to API/Key Settings:</strong> In the dashboard, click on your profile image, then select
                    "API/Key Settings."
                </li>
                <img src="<?php
                echo REALTYNA_MLS_ON_THE_FLY_URL . 'assets/image/wizards/api-keys.jpg' ?>" alt="API Keys Image"
                     style="max-width:100%;">
                <li><strong>Copy Your API Details:</strong> Your API Key, Client ID, and Client Secret will be available
                    here. Copy them for later use.
                </li>
            </ol>
            <div class="mls-otf-button-group">
                <button class="mls-otf-wizard-btn btn-prev">Previous</button>
                <button class="mls-otf-wizard-btn btn-next">Next</button>
                <button class="mls-otf-wizard-btn btn-close">Close</button>
            </div>

        </div>

        <!-- Step 4: Enter Your API Keys -->
        <div class="mls-otf-wizard-step" id="step-4" style="display: none;">
            <div class="mls-otf-button-group">
                <button class="mls-otf-wizard-btn btn-prev">Previous</button>
                <button type="submit" class="mls-otf-wizard-btn submit-keys-btn">Submit</button>
                <button class="mls-otf-wizard-btn btn-close">Close</button>
            </div>

            <h2>Step 4: Enter Your API Keys</h2>
            <p>Enter the API keys you copied in the fields below:</p>

            <div id="error-message" style="display:none; color: red;"></div>

            <div id="support-guide" style="display:none; color: black;">
                <p>If you continue facing issues, you can reach out to us via:</p>
                <p>Email: <a target="_blank" href="mailto:support@realtyna.com">support@realtyna.com</a></p>
                <p>Or visit: <a target="_blank" href="https://support.realtyna.com/">https://support.realtyna.com/</a> to create a
                    support ticket.</p>
            </div>


            <form id="mls-api-key-form">
                <?php
                // Replace with your PHP code for the SettingsField inputs.
                use Realtyna\Core\Utilities\SettingsField;
                $settings = \Realtyna\MlsOnTheFly\Settings\Settings::get_settings();

                SettingsField::input(array(
                    'parent_name' => 'mls-on-the-fly-settings',
                    'id' => 'mls-on-the-fly-settings-api-key',
                    'child_name' => 'api_key',
                    'label' => __('Realty Feed API Key', 'realtyna-mls-on-the-fly'),
                    'type' => 'password',
                    'value' => $settings['api_key'] ?? '',
                ));

                SettingsField::input(array(
                    'parent_name' => 'mls-on-the-fly-settings',
                    'child_name' => 'client_id',
                    'id' => 'mls-on-the-fly-settings-client-id',
                    'label' => __('Realty Feed Client ID', 'realtyna-mls-on-the-fly'),
                    'type' => 'password',
                    'value' => $settings['client_id'] ?? '',
                ));

                SettingsField::input(array(
                    'parent_name' => 'mls-on-the-fly-settings',
                    'child_name' => 'client_secret',
                    'id' => 'mls-on-the-fly-settings-client-secret',
                    'label' => __('Realty Feed Client Secret', 'realtyna-mls-on-the-fly'),
                    'type' => 'password',
                    'value' => $settings['client_secret'] ?? '',
                ));
                ?>
            </form>


            <div class="mls-otf-button-group">
                <button class="mls-otf-wizard-btn btn-prev">Previous</button>
                <button type="submit" class="mls-otf-wizard-btn submit-keys-btn">Submit</button>
                <button class="mls-otf-wizard-btn btn-close">Close</button>
            </div>
        </div>


        <div class="mls-otf-wizard-step" id="step-5" style="display: none;">
            <div class="mls-otf-all-set-content">
                <h2>You are all set!</h2>
                <p>The demo API key you entered is working, and you can go to your properties management page (e.g.,
                    WPL, Houzez, etc.) and see the list of all demo properties.</p>
                <p>You can close this wizard now and start your MLS approval process by clicking on the <strong>Access
                        Real Data</strong> button.</p>
            </div>

            <div class="mls-otf-button-group">
                <button class="mls-otf-wizard-btn btn-close">Close</button>
            </div>
        </div>

    </div>


    <!-- Real Data Wizard Container, initially hidden -->
    <div id="mls-otf-realdata-wizard" class="mls-otf-wizard-container" style="display: none;">
        <div class="progress-bar">
            <div class="step"></div>
            <div class="step"></div>
            <div class="step"></div>
        </div>

        <!-- Step 1: Register - Verify - Complete Profile -->
        <div class="mls-otf-wizard-step" id="step-1">
            <div class="mls-otf-button-group">
                <button class="mls-otf-wizard-btn realdata-btn-next">Next</button>
                <button class="mls-otf-wizard-btn realdata-btn-close">Close</button>
            </div>


            <h2>Step 1: Register - Verify - Complete Profile</h2>
            <p>To get API details for Demo or real MLS data, you need to sign up on the RealtyFeed Dashboard! Follow
                these steps:</p>
            <ol>
                <li><strong>Sign Up:</strong> Go to the <a target="_blank" href="https://dashboard.realtyfeed.com">RealtyFeed
                        Dashboard</a> and register your account. If you already have a Realtyna.com account, you can use
                    the same credentials.
                </li>
                <img src="<?php
                echo REALTYNA_MLS_ON_THE_FLY_URL . 'assets/image/wizards/sign-up.jpg' ?>" alt="Sign Up Image"
                     style="max-width:100%;">
                <li><strong>Verify Your Email:</strong> You will receive a verification code in your email. Enter it and
                    proceed.
                </li>
                <li><strong>Complete Your Profile:</strong> Fill in general information, expertise, bio, and social
                    media accounts. These won’t affect your access or permissions.
                </li>
                <img src="<?php
                echo REALTYNA_MLS_ON_THE_FLY_URL . 'assets/image/wizards/verify.jpg' ?>" alt="Verify Email Image"
                     style="max-width:100%;">
            </ol>
            <div class="mls-otf-button-group">
                <button class="mls-otf-wizard-btn realdata-btn-next">Next</button>
                <button class="mls-otf-wizard-btn realdata-btn-close">Close</button>
            </div>

        </div>

        <!-- Step 2: MLS Verification -->
        <div class="mls-otf-wizard-step" id="step-2" style="display: none;">
            <div class="mls-otf-button-group">
                <button class="mls-otf-wizard-btn realdata-btn-prev">Previous</button>
                <button class="mls-otf-wizard-btn realdata-btn-next">Next</button>
                <button class="mls-otf-wizard-btn realdata-btn-close">Close</button>
            </div>

            <h2>Step 2: MLS Verification</h2>
            <p>To verify your MLS membership in the RealtyFeed Dashboard, follow these steps:</p>
            <ol>
                <li><strong>Sign in:</strong> Go to the <a target="_blank" href="https://dashboard.realtyfeed.com/">RealtyFeed
                        Dashboard</a> and log in.
                </li>

                <li><strong>Click on Menu &gt; MLS Verification:</strong> Access the MLS verification section from the
                    menu.
                </li>

                <img src="<?php
                echo REALTYNA_MLS_ON_THE_FLY_URL . 'assets/image/wizards/mls-verification.jpg' ?>" alt="API Keys Image"
                     style="max-width:100%;">


                <li><strong>Select your MLS and click "Verify":</strong> Choose your MLS from the list and proceed with
                    the verification.
                </li>

                <img src="<?php
                echo REALTYNA_MLS_ON_THE_FLY_URL . 'assets/image/wizards/select-real-mls.jpg' ?>" alt="API Keys Image"
                     style="max-width:100%;">

                <li>Fill out the necessary information. Ensure it matches your MLS records.</li>
            </ol>


            <div class="mls-otf-button-group">
                <button class="mls-otf-wizard-btn realdata-btn-prev">Previous</button>
                <button class="mls-otf-wizard-btn realdata-btn-next">Next</button>
                <button class="mls-otf-wizard-btn realdata-btn-close">Close</button>
            </div>
        </div>

        <!-- Step 3: Final Message -->
        <div class="mls-otf-wizard-step" id="step-3" style="display: none;">

            <h2>Step 3: Approval Time</h2>
            <p>Your MLS membership verification request has been submitted. The final approval from your MLS may take
                10-14 business days.</p>
            <p>If you have any questions, feel free to contact our support team.</p>

            <div class="mls-otf-button-group">
                <button class="mls-otf-wizard-btn realdata-btn-close">Close</button>
            </div>
        </div>
    </div>
</div>


<div class="mls-otf-data-tracking-message">
    <p>We track data usage to improve our services. By using this plugin, you agree to our data tracking policy.</p>
</div>

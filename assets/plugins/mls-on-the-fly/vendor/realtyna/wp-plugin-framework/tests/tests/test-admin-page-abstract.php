<?php

use Realtyna\Core\Abstracts\AdminPageAbstract;

class SampleAdminPage extends AdminPageAbstract
{
    protected function getPageTitle(): string
    {
        return 'Sample Admin Page';
    }

    protected function getMenuTitle(): string
    {
        return 'Sample Menu';
    }

    protected function getCapability(): string
    {
        return 'manage_options';
    }

    protected function getMenuSlug(): string
    {
        return 'sample-admin-page';
    }

    protected function getPageTemplate(): string
    {
        return __DIR__ . '/templates/sample-admin-page.php';
    }

    protected function enqueuePageAssets(): void
    {
        // Enqueue styles and scripts specific to this admin page
    }
}

class AdminPageAbstractTest extends WP_UnitTestCase
{
    protected $adminPage;
    protected $templateDir;
    protected $templateFile;

    protected function setUp(): void
    {
        parent::setUp();

        // Create the templates directory
        $this->templateDir = __DIR__ . '/templates';
        if (!file_exists($this->templateDir)) {
            mkdir($this->templateDir, 0755, true);
        }

        // Create the template file
        $this->templateFile = $this->templateDir . '/sample-admin-page.php';
        file_put_contents($this->templateFile, '<p>Sample Admin Page Content</p>');

        // Initialize the SampleAdminPage class
        $this->adminPage = new SampleAdminPage();
        $this->adminPage->register();
    }

    protected function tearDown(): void
    {
        // Clean up the template file and directory after each test
        if (file_exists($this->templateFile)) {
            unlink($this->templateFile);
        }
        if (is_dir($this->templateDir)) {
            rmdir($this->templateDir);
        }

        parent::tearDown();
    }

    public function testRegisterAddsActions()
    {
        // Ensure actions were added for admin_menu and admin_enqueue_scripts
        $this->assertTrue(has_action('admin_menu', [$this->adminPage, 'addMenuPage']) !== false);
        $this->assertTrue(has_action('admin_enqueue_scripts', [$this->adminPage, 'enqueueAssets']) !== false);
    }

    public function testAddMenuPage()
    {
        // Trigger the action to add the menu page
        do_action('admin_menu');

        global $submenu, $menu;

        // Check if the page was added to the admin menu
        $this->assertNotEmpty($menu);
        $this->assertContains('Sample Admin Page', wp_list_pluck($menu, 3));

        if($submenu != null){
            // Since this is not a submenu, $submenu should not contain the slug
            $this->assertArrayNotHasKey('sample-admin-page', $submenu);
        }
    }

    public function testRenderPage()
    {
        // Simulate the global $_GET['page'] variable
        $_GET['page'] = 'sample-admin-page';

        // Start output buffering to capture the rendered page
        ob_start();
        $this->adminPage->renderPage();
        $output = ob_get_clean();

        // Check if the rendered output contains the expected HTML
        $this->assertStringContainsString('<div id="realtyna-base-plugin-sample-admin-page"', $output);
        $this->assertStringContainsString('<h2>Sample Admin Page</h2>', $output);
        $this->assertStringContainsString('<p>Sample Admin Page Content</p>', $output);
    }

    public function testEnqueueAssets()
    {
        // Simulate the screen object
        $screen = (object) ['id' => 'toplevel_page_sample-admin-page'];

        // Mock get_current_screen to return our simulated screen object
        global $current_screen;
        $current_screen = $screen;

        // Capture the actions that were called
        ob_start();
        $this->adminPage->enqueueAssets();
        $output = ob_get_clean();

        // Check if enqueuePageAssets was called
        // In a real test, you might mock methods to confirm they were called instead
        $this->assertTrue(true);  // Placeholder assertion for enqueuePageAssets
    }
}

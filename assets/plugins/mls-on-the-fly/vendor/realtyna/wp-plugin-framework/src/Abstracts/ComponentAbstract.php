<?php
namespace Realtyna\Core\Abstracts;

/**
 * Class ComponentAbstract
 *
 * A base class for components in the Realtyna plugin.
 * This class handles the registration of subcomponents, admin pages, custom post types, AJAX handlers, shortcodes,
 * REST API endpoints, widgets, custom taxonomies, and admin notices.
 */
abstract class ComponentAbstract
{
    protected array $subComponents = [];
    protected array $adminPages = [];
    protected array $postTypes = [];
    protected array $ajaxHandlers = [];
    protected array $shortcodes = [];
    protected array $restApiEndpoints = [];
    protected array $widgets = [];
    protected array $customTaxonomies = [];
    protected array $adminNotices = [];

    public function __construct()
    {
        $this->postTypes();
        $this->subComponents();
        $this->adminPages();
        $this->ajaxHandlers();
        $this->shortcodes();
        $this->restApiEndpoints();
        $this->widgets();
        $this->customTaxonomies();
        add_action('wp_ajax_dismiss_admin_notice', [$this, 'handleDismissAdminNotice']);
        add_action('admin_notices', [$this, 'displayAdminNotices']);
    }

    /**
     * Register all services related to this component.
     *
     * @return void
     */
    public function register(): void
    {
    }

    /**
     * Register custom post types.
     *
     * @return void
     */
    public function postTypes(): void
    {
    }

    /**
     * Register subcomponents.
     *
     * @return void
     */
    public function subComponents(): void
    {
    }

    /**
     * Register admin pages.
     *
     * @return void
     */
    public function adminPages(): void
    {
    }

    /**
     * Register AJAX handlers.
     *
     * @return void
     */
    public function ajaxHandlers(): void
    {
    }

    /**
     * Register shortcodes.
     *
     * @return void
     */
    public function shortcodes(): void
    {
    }

    /**
     * Register REST API endpoints.
     *
     * @return void
     */
    public function restApiEndpoints(): void
    {
    }

    /**
     * Register widgets.
     *
     * @return void
     */
    public function widgets(): void
    {
    }

    /**
     * Register custom taxonomies.
     *
     * @return void
     */
    public function customTaxonomies(): void
    {
    }

    /**
     * Add an admin page to be registered and displayed in the WordPress admin area.
     *
     * @param string $adminPageClass The class name of the admin page.
     * @param mixed $container An optional container for dependency injection.
     * @return void
     */
    public function addAdminPage(string $adminPageClass, $container = null): void
    {
        $this->adminPages[] = $adminPageClass;
        $service = $container ? $container::get($adminPageClass) : new $adminPageClass();
        if ($service instanceof AdminPageAbstract && method_exists($service, 'register')) {
            $service->register();
        }
    }

    /**
     * Add a custom post type to be registered with WordPress.
     *
     * @param string $postTypeClass The class name of the custom post type.
     * @param mixed $container An optional container for dependency injection.
     * @return void
     */
    public function addPostType(string $postTypeClass, $container = null): void
    {
        $this->postTypes[] = $postTypeClass;
        $service = $container ? $container::get($postTypeClass) : new $postTypeClass();
        add_action('after_setup_theme', [$service, 'register']);
    }

    /**
     * Add a subcomponent to be registered and initialized.
     *
     * @param string $subComponentClass The class name of the subcomponent.
     * @param mixed $container An optional container for dependency injection.
     * @return void
     */
    public function addSubComponent(string $subComponentClass, $container = null): void
    {
        $this->subComponents[] = $subComponentClass;
        $service = $container ? $container::get($subComponentClass) : new $subComponentClass();
        if ($service instanceof ComponentAbstract && method_exists($service, 'register')) {
            $service->register();
        }
    }

    /**
     * Add an AJAX handler to be registered with WordPress.
     *
     * @param string $ajaxHandlerClass The class name of the AJAX handler.
     * @param mixed $container An optional container for dependency injection.
     * @return void
     */
    public function addAjaxHandler(string $ajaxHandlerClass, $container = null): void
    {
        $this->ajaxHandlers[] = $ajaxHandlerClass;
        $service = $container ? $container::get($ajaxHandlerClass) : new $ajaxHandlerClass();
        if ($service instanceof AjaxHandlerAbstract && method_exists($service, 'register')) {
            $service->register();
        }
    }

    /**
     * Add a shortcode to be registered with WordPress.
     *
     * @param string $shortcodeClass The class name of the shortcode.
     * @param mixed $container An optional container for dependency injection.
     * @return void
     */
    public function addShortcode(string $shortcodeClass, $container = null): void
    {
        $this->shortcodes[] = $shortcodeClass;
        $service = $container ? $container::get($shortcodeClass) : new $shortcodeClass();
        if ($service instanceof ShortcodeAbstract && method_exists($service, 'register')) {
            $service->register();
        }
    }

    /**
     * Add a REST API endpoint to be registered with WordPress.
     *
     * @param string $restApiEndpointClass The class name of the REST API endpoint.
     * @param mixed $container An optional container for dependency injection.
     * @return void
     */
    public function addRestApiEndpoint($restApiEndpointClass, $container = null): void
    {
        $this->restApiEndpoints[] = $restApiEndpointClass;
        $container ? $container::get($restApiEndpointClass) : new $restApiEndpointClass();
    }

    /**
     * Add a widget to be registered with WordPress.
     *
     * @param string $widgetClass The class name of the widget.
     * @param mixed $container An optional container for dependency injection.
     * @return void
     */
    public function addWidget(string $widgetClass, $container = null): void
    {
        $this->widgets[] = $widgetClass;
        $container ? $container::get($widgetClass) : new $widgetClass();
    }

    /**
     * Add a custom taxonomy to be registered with WordPress.
     *
     * @param string $customTaxonomyClass The class name of the custom taxonomy.
     * @param mixed $container An optional container for dependency injection.
     * @return void
     */
    public function addCustomTaxonomy(string $customTaxonomyClass, $container = null): void
    {
        $this->customTaxonomies[] = $customTaxonomyClass;
        $service = $container ? $container::get($customTaxonomyClass) : new $customTaxonomyClass();
        if ($service instanceof CustomTaxonomyAbstract) {
            add_action('init', [$service, 'registerTaxonomy']);
        }
    }

    /**
     * Adds an admin notice to be displayed in the WordPress admin area.
     *
     * @param string $message The message to be displayed in the notice.
     * @param string $type The type of notice ('success', 'warning', 'error', 'info'). Defaults to 'info'.
     * @return void
     */
    public function addAdminNotice(string $message, string $type = 'info', bool $isClosable = false, string $noticeId = ''): void
    {
        $this->adminNotices[] = [
            'message' => $message,
            'type' => $type,
            'is_closable' => $isClosable,
            'notice_id' => $noticeId
        ];
    }


    /**
     * Displays the registered admin notices in the WordPress admin area.
     *
     * @return void
     */
    public function displayAdminNotices(): void
    {
        foreach ($this->adminNotices as $notice) {
            // Skip the notice if it has been dismissed
            if ($notice['is_closable'] && $notice['notice_id'] && get_option('dismissed_notice_' . $notice['notice_id'])) {
                continue;
            }

            $class = "notice notice-{$notice['type']}";
            if ($notice['is_closable']) {
                $class .= ' is-dismissible';
            }

            echo "<div class=\"{$class}\" data-notice-id=\"{$notice['notice_id']}\"><p>{$notice['message']}</p></div>";
        }
        // Enqueue the script for handling notice dismissal
        echo $this->enqueueNoticeDismissScript();
    }
    public function enqueueNoticeDismissScript(): void
    {
        ?>
        <script type="text/javascript">
            document.addEventListener('DOMContentLoaded', function() {
                const notices = document.querySelectorAll('.notice.is-dismissible');

                notices.forEach(function(notice) {
                    const noticeId = notice.getAttribute('data-notice-id');

                    if (noticeId) {
                        notice.addEventListener('click', function(event) {
                            if (event.target.classList.contains('notice-dismiss')) {
                                // Send AJAX request to save the dismissed notice
                                fetch(ajaxurl, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/x-www-form-urlencoded'
                                    },
                                    body: 'action=dismiss_admin_notice&notice_id=' + noticeId
                                });
                            }
                        });
                    }
                });
            });
        </script>
        <?php
    }

    public function handleDismissAdminNotice(): void
    {
        if (isset($_POST['notice_id'])) {
            $noticeId = sanitize_text_field($_POST['notice_id']);
            update_option('dismissed_notice_' . $noticeId, true);
        }

        wp_die(); // Required to terminate immediately and return a proper response
    }

}

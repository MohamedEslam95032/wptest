<?php

namespace Realtyna\Core\Abstracts;

/**
 * Class AdminPageAbstract
 *
 * An abstract class that defines the structure for creating admin pages in the WordPress admin area.
 *
 * @package Realtyna\Core\Utilities
 */
abstract class AdminPageAbstract
{
    /**
     * Method to initialize the admin page.
     */
    public function register(): void
    {
        add_action('admin_menu', [$this, 'addMenuPage']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAssets']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueGlobalAssets']);
    }
    /**
     * Add the admin page or submenu to the WordPress admin menu.
     *
     * @return void
     */
    public function addMenuPage(): void
    {
        if ($this->isSubmenu()) {
            add_submenu_page(
                $this->getParentSlug(),
                $this->getPageTitle(),
                $this->getMenuTitle(),
                $this->getCapability(),
                $this->getMenuSlug(),
                [$this, 'renderPage']
            );
        } else {
            add_menu_page(
                $this->getPageTitle(),
                $this->getMenuTitle(),
                $this->getCapability(),
                $this->getMenuSlug(),
                [$this, 'renderPage'],
                $this->getIconUrl(),
                $this->getPosition()
            );
        }
    }

    /**
     * Abstract method to get the page title.
     *
     * @return string
     */
    abstract protected function getPageTitle(): string;

    /**
     * Abstract method to get the menu title.
     *
     * @return string
     */
    abstract protected function getMenuTitle(): string;

    /**
     * Abstract method to get the required capability.
     *
     * @return string
     */
    abstract protected function getCapability(): string;

    /**
     * Abstract method to get the menu slug.
     *
     * @return string
     */
    abstract protected function getMenuSlug(): string;

    /**
     * Abstract method to get the template file for the page.
     *
     * Child classes should implement this to define the template path.
     *
     * @return string
     */
    abstract protected function getPageTemplate(): string;

    /**
     * Abstract method to enqueue styles and scripts for the admin page.
     *
     * Child classes should implement this method to enqueue specific styles and scripts.
     *
     * @return void
     */
    abstract protected function enqueuePageAssets(): void;

    abstract public function enqueueGlobalAssets(): void;

    /**
     * Render the admin page content by including the template file.
     *
     * @return void
     */
    public function renderPage(): void
    {
        $page_slug = str_replace(['realtyna-realtyna-base-plugin-', 'realtyna-realtyna-base-plugin'], '', $_GET['page'] ?? '');
        $page_id = $page_slug ? $page_slug : 'main';
        echo '<div id="realtyna-base-plugin-' . esc_attr($page_id) . '" class="realtyna-base-plugin-dashboard-wrapper">';
        echo '<div class="realtyna-base-plugin-dashboard-header-wrapper">';
        echo '<h2>' . esc_html($this->getPageTitle()) . '</h2>';
        echo '<div class="realtyna-base-plugin-dashboard-header-sub-title">';
        echo '</div></div>'; // Close header wrapper

        echo '<div class="realtyna-base-plugin-dashboard-content-wrapper">';

        $template = $this->getPageTemplate();
        if (file_exists($template)) {
            include $template;
        } else {
            echo '<p>' . esc_html__('Template file not found.', 'realtyna-core') . '</p>';
        }

        echo '</div>'; // Close content wrapper
        echo '</div>'; // Close dashboard wrapper
    }

    /**
     * Enqueue styles and scripts for the admin page.
     *
     * This method checks if the current screen matches the page slug and then calls the enqueuePageAssets method.
     *
     * @return void
     */
    public function enqueueAssets(): void
    {
        $screen = get_current_screen();

        // Check if we are on the current admin page
        if ($screen && str_contains($screen->id, $this->getMenuSlug())) {
            // Remove all admin notices
            $this->removeAdminNotices();

            // Enqueue page-specific assets
            $this->enqueuePageAssets();
        }
    }

    /**
     * Remove all admin notices from the page.
     *
     * @return void
     */
    protected function removeAdminNotices(): void
    {
        remove_all_actions('admin_notices');
        remove_all_actions('all_admin_notices');
    }

    /**
     * Determine if the page is a submenu.
     *
     * @return bool
     */
    protected function isSubmenu(): bool
    {
        return false;
    }

    /**
     * Get the parent slug for the submenu.
     *
     * Child classes should override this if the page is a submenu.
     *
     * @return string
     */
    protected function getParentSlug(): string
    {
        return '';
    }

    /**
     * Get the icon URL for the menu.
     *
     * @return string
     */
    protected function getIconUrl(): string
    {
        return 'dashicons-admin-generic'; // Default icon
    }

    /**
     * Get the position of the menu item.
     *
     * @return int
     */
    protected function getPosition(): int
    {
        return 99; // Default position
    }
}

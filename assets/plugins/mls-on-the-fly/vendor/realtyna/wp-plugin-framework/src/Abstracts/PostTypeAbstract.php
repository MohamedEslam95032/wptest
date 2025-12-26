<?php

namespace Realtyna\Core\Abstracts;

/**
 * Class PostTypeAbstract
 *
 * Provides a base class for defining custom post types in WordPress.
 * This abstract class allows developers to easily create and register custom post types
 * by defining post type specific parameters and settings.
 */
abstract class PostTypeAbstract
{
    /**
     * @var string The custom post type slug.
     */
    protected string $postType;

    /**
     * @var array The arguments array used to register the custom post type.
     */
    protected array $args = [];

    /**
     * @var array The labels array used to define the labels for the custom post type.
     */
    protected array $labels = [];

    /**
     * PostTypeAbstract constructor.
     *
     * Initializes the post type by setting default labels and arguments.
     * These can be overridden in child classes.
     */
    public function __construct()
    {
        // Set default labels and arguments
        $this->setDefaultLabels();
        $this->setDefaultArgs();
    }

    /**
     * Define the custom post type slug.
     *
     * This method must be implemented by the child class to specify the post type slug.
     *
     * @return string The post type slug.
     */
    abstract protected function definePostType(): string;

    /**
     * Modify the arguments for the custom post type.
     *
     * This method must be implemented by the child class to customize the arguments
     * used to register the post type.
     *
     * @param array $args The default arguments.
     * @return array The modified arguments.
     */
    abstract protected function modifyArgs(array $args): array;

    /**
     * Register the custom post type with WordPress.
     *
     * This method registers the post type using the defined slug and modified arguments.
     *
     * @return void
     */
    public function register(): void
    {
        $this->postType = $this->definePostType();
        $this->args = $this->modifyArgs($this->args);
        register_post_type($this->postType, $this->args);
    }

    /**
     * Set the default labels for the custom post type.
     *
     * This method defines a set of default labels that can be overridden by child classes
     * or updated using the `setLabels` method.
     *
     * @return void
     */
    protected function setDefaultLabels(): void
    {
        $this->labels = [
            'name'               => __('Items', 'text-domain'),
            'singular_name'      => __('Item', 'text-domain'),
            'menu_name'          => __('Items', 'text-domain'),
            'name_admin_bar'     => __('Item', 'text-domain'),
            'add_new'            => __('Add New', 'text-domain'),
            'add_new_item'       => __('Add New Item', 'text-domain'),
            'new_item'           => __('New Item', 'text-domain'),
            'edit_item'          => __('Edit Item', 'text-domain'),
            'view_item'          => __('View Item', 'text-domain'),
            'all_items'          => __('All Items', 'text-domain'),
            'search_items'       => __('Search Items', 'text-domain'),
            'parent_item_colon'  => __('Parent Items:', 'text-domain'),
            'not_found'          => __('No items found.', 'text-domain'),
            'not_found_in_trash' => __('No items found in Trash.', 'text-domain'),
        ];
    }

    /**
     * Set the default arguments for the custom post type.
     *
     * This method defines a set of default arguments that can be overridden by child classes
     * or updated using the `setArgs` method.
     *
     * @return void
     */
    protected function setDefaultArgs(): void
    {
        $this->args = [
            'labels'             => $this->labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => ['slug' => 'item'],
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => ['title', 'editor', 'thumbnail'],
        ];
    }

    /**
     * Set or update the labels for the custom post type.
     *
     * This method allows child classes or external code to override the default labels.
     *
     * @param array $labels An associative array of labels.
     * @return void
     */
    public function setLabels(array $labels): void
    {
        $this->labels = array_merge($this->labels, $labels);
    }

    /**
     * Set or update the arguments for the custom post type.
     *
     * This method allows child classes or external code to override the default arguments.
     *
     * @param array $args An associative array of arguments.
     * @return void
     */
    public function setArgs(array $args): void
    {
        $this->args = array_merge($this->args, $args);
    }
}

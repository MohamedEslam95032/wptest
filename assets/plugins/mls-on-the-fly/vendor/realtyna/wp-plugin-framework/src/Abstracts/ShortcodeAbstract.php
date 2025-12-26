<?php

namespace Realtyna\Core\Abstracts;

/**
 * Class ShortcodeAbstract
 *
 * An abstract class that defines the structure for handling shortcodes in WordPress.
 *
 * @package Realtyna\Core\Abstracts
 */
abstract class ShortcodeAbstract
{
    /**
     * ShortcodeAbstract constructor.
     *
     * Registers the shortcode with WordPress.
     */
    public function __construct()
    {
        add_shortcode($this->getTag(), [$this, 'render']);
    }

    /**
     * Get the tag name for the shortcode.
     *
     * @return string
     */
    abstract protected function getTag(): string;

    /**
     * Handle the rendering of the shortcode.
     *
     * This method should be implemented by child classes to process the shortcode and return the output.
     *
     * @param array $atts Shortcode attributes.
     * @param string|null $content The enclosed content (if any).
     * @return string The output of the shortcode.
     */
    abstract public function render(array $atts, ?string $content = null): string;

    /**
     * Generate and return the output for the shortcode.
     *
     * This method can be used by child classes to process and return the final output of the shortcode.
     *
     * @param array $atts Processed attributes.
     * @param string|null $content The enclosed content (if any).
     * @return string The final output to be displayed.
     */
    protected function output(array $atts, ?string $content = null): string
    {
        // Example output (this should be customized in the subclass)
        return '<div>' . esc_html($content) . '</div>';
    }
}
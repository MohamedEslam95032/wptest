<?php

namespace Realtyna\Core\Abstracts;

use WP_Widget;

/**
 * Class WidgetAbstract
 *
 * An abstract class that defines the structure for creating widgets in WordPress.
 *
 * @package Realtyna\Core\Abstracts
 */
abstract class WidgetAbstract extends WP_Widget
{
    /**
     * WidgetAbstract constructor.
     *
     * Sets up the widget's name, description, and other options.
     */
    public function __construct()
    {
        $widget_options = $this->getWidgetOptions();
        parent::__construct(
            $this->getWidgetId(),
            $this->getWidgetName(),
            $widget_options
        );

        add_action('widgets_init', function () {
            register_widget(static::class);
        });
    }

    /**
     * Get the widget's ID.
     *
     * @return string
     */
    abstract protected function getWidgetId(): string;

    /**
     * Get the widget's name.
     *
     * @return string
     */
    abstract protected function getWidgetName(): string;

    /**
     * Get the widget's options, including description and any other settings.
     *
     * @return array
     */
    abstract protected function getWidgetOptions(): array;

}

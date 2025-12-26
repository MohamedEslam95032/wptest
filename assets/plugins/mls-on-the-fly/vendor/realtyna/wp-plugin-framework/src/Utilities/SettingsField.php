<?php

namespace Realtyna\Core\Utilities;

/**
 * Class SettingsField
 *
 * Handles rendering of various settings fields in the WordPress admin.
 *
 * @package Realtyna\Core\Utilities
 */
class SettingsField
{
    /**
     * Echo start wrapper
     *
     * @param string $id Unique identifier for the field wrapper.
     * @return void
     */
    public static function start_wrap(string $id = ''): void
    {
        if ($id != '') {
            echo '<div class="realtyna-core-field-wrapper ' . esc_attr($id) . '-field-wrapper">';
        } else {
            echo '<div class="realtyna-core-field-wrapper">';
        }
    }

    /**
     * Echo end wrapper
     *
     * @return void
     */
    public static function end_wrap(): void
    {
        echo '</div>';
    }

    /**
     * Echo label
     *
     * @param array $attributes {
     *     @type string $label Text for the field label.
     * }
     * @return void
     */
    public static function label(array $attributes): void
    {
        $text = $attributes['label'] ?? '';
        if (!$text) {
            return;
        }

        echo '<label class="realtyna-core-field-label">' .
            esc_html($text)
            . '</label>';
    }

    /**
     * Echo description
     *
     * @param array $attributes {
     *     @type string $description Text for the field description.
     * }
     * @return void
     */
    public static function description(array $attributes): void
    {
        $description = $attributes['description'] ?? '';
        if (!$description) {
            return;
        }
        echo '<div class="realtyna-core-field-description">' .
            esc_html($description)
            . '</div>';
    }

    /**
     * Echo input field
     *
     * @param array $attributes {
     *     @type string $parent_name Parent name for the field (e.g., option group).
     *     @type string $child_name Name of the field.
     *     @type string $id HTML id attribute.
     *     @type string $type Input type attribute (default: 'text').
     *     @type string $value Current value of the field.
     *     @type string $label Label text for the field.
     *     @type string $description Description text for the field.
     * }
     * @return void
     */
    public static function input(array $attributes): void
    {
        $parent_name = $attributes['parent_name'] ?? '';
        $child_name = $attributes['child_name'] ?? '';
        $id = $attributes['id'] ?? '';
        $name = "{$parent_name}[{$child_name}]";
        $type = $attributes['type'] ?? 'text';
        $value = $attributes['value'] ?? '';

        self::start_wrap($id);
        self::label($attributes);

        echo '<input autocomplete="off" type="' . esc_attr($type) . '" id="' . esc_attr($id) . '" name="' . esc_attr(
                $name
            ) . '" value="' . esc_attr($value) . '"/>';

        self::description($attributes);
        self::end_wrap();
    }

    /**
     * Echo checkbox
     *
     * @param array $attributes {
     *     @type string $parent_name Parent name for the field (e.g., option group).
     *     @type string $child_name Name of the field.
     *     @type string $id HTML id attribute.
     *     @type string $label Label text for the checkbox.
     *     @type string $value Value for the checkbox.
     *     @type bool   $checked Whether the checkbox is checked.
     *     @type string $description Description text for the field.
     * }
     * @return void
     */
    public static function checkbox(array $attributes): void
    {
        $parent_name = $attributes['parent_name'] ?? '';
        $child_name = $attributes['child_name'] ?? '';
        $id = $attributes['id'] ?? '';
        $name = "{$parent_name}[{$child_name}]";
        $text = $attributes['label'] ?? '';
        $value = $attributes['value'] ?? '';
        $checked = $attributes['checked'] ?? false;

        self::start_wrap($id);

        echo '<label>
                <input type="checkbox" id="' . esc_attr($id) . '" name="' . esc_attr(
                $name
            ) . '" class="" value="' . esc_attr($value) . '" ' . checked($checked, true, false) . '/>'
            . esc_html($text)
            . '</label>';

        self::description($attributes);
        self::end_wrap();
    }

    /**
     * Echo select dropdown
     *
     * @param array $attributes {
     *     @type string $parent_name Parent name for the field (e.g., option group).
     *     @type string $child_name Name of the field.
     *     @type string $id HTML id attribute.
     *     @type array  $options Array of options in the form of key-value pairs.
     *     @type string $value Currently selected value.
     *     @type string $label Label text for the field.
     *     @type string $description Description text for the field.
     * }
     * @return void
     */
    public static function select(array $attributes): void
    {
        $parent_name = $attributes['parent_name'] ?? '';
        $child_name = $attributes['child_name'] ?? '';
        $id = $attributes['id'] ?? '';
        $name = "{$parent_name}[{$child_name}]";
        $options = $attributes['options'] ?? [];
        $selected_value = $attributes['value'] ?? '';

        self::start_wrap($id);
        self::label($attributes);

        if (empty($options)) {
            echo '<p>' . esc_html__('No option.', 'realtyna-core') . '</p>';
            return;
        }

        echo '<select id="' . esc_attr($id) . '" name="' . esc_attr($name) . '">';
        foreach ($options as $o_key => $o_title) {
            $selected = $selected_value === $o_key ? true : false;
            echo '<option value="' . esc_attr($o_key) . '" ' . selected(
                    $selected,
                    true,
                    false
                ) . '>' . esc_html($o_title) . '</option>';
        }
        echo '</select>';

        self::description($attributes);
        self::end_wrap();
    }

    /**
     * Echo textarea
     *
     * @param array $attributes {
     *     @type string $parent_name Parent name for the field (e.g., option group).
     *     @type string $child_name Name of the field.
     *     @type string $id HTML id attribute.
     *     @type string $value Current value of the textarea.
     *     @type string $label Label text for the field.
     *     @type string $description Description text for the field.
     * }
     * @return void
     */
    public static function textarea(array $attributes): void
    {
        $parent_name = $attributes['parent_name'] ?? '';
        $child_name = $attributes['child_name'] ?? '';
        $id = $attributes['id'] ?? '';
        $name = "{$parent_name}[{$child_name}]";
        $value = $attributes['value'] ?? '';

        self::start_wrap($id);
        self::label($attributes);

        echo '<textarea id="' . esc_attr($id) . '" name="' . esc_attr($name) . '">'
            . esc_textarea($value) . '</textarea>';

        self::description($attributes);
        self::end_wrap();
    }

    /**
     * Echo radio buttons
     *
     * @param array $attributes {
     *     @type string $parent_name Parent name for the field (e.g., option group).
     *     @type string $child_name Name of the field.
     *     @type string $id HTML id attribute.
     *     @type array  $options Array of options in the form of key-value pairs.
     *     @type string $value Currently selected value.
     *     @type string $label Label text for the field.
     *     @type string $description Description text for the field.
     * }
     * @return void
     */
    public static function radio(array $attributes): void
    {
        $parent_name = $attributes['parent_name'] ?? '';
        $child_name = $attributes['child_name'] ?? '';
        $id = $attributes['id'] ?? '';
        $name = "{$parent_name}[{$child_name}]";
        $options = $attributes['options'] ?? [];
        $selected_value = $attributes['value'] ?? '';

        self::start_wrap($id);
        self::label($attributes);

        foreach ($options as $o_key => $o_title) {
            $checked = $selected_value === $o_key ? true : false;
            echo '<label><input type="radio" id="' . esc_attr($id . '_' . $o_key) . '" name="' . esc_attr(
                    $name
                ) . '" value="' . esc_attr($o_key) . '" ' . checked(
                    $checked,
                    true,
                    false
                ) . '/> ' . esc_html($o_title) . '</label><br>';
        }

        self::description($attributes);
        self::end_wrap();
    }

    /**
     * Echo file upload input
     *
     * @param array $attributes {
     *     @type string $parent_name Parent name for the field (e.g., option group).
     *     @type string $child_name Name of the field.
     *     @type string $id HTML id attribute.
     *     @type string $value Value of the current file.
     *     @type string $label Label text for the field.
     *     @type string $description Description text for the field.
     * }
     * @return void
     */
    public static function file_upload(array $attributes): void
    {
        $parent_name = $attributes['parent_name'] ?? '';
        $child_name = $attributes['child_name'] ?? '';
        $id = $attributes['id'] ?? '';
        $name = "{$parent_name}[{$child_name}]";
        $value = $attributes['value'] ?? '';

        self::start_wrap($id);
        self::label($attributes);

        echo '<input type="file" id="' . esc_attr($id) . '" name="' . esc_attr($name) . '" value="' . esc_attr($value) . '"/>';

        self::description($attributes);
        self::end_wrap();
    }

    /**
     * Echo color picker input
     *
     * @param array $attributes {
     *     @type string $parent_name Parent name for the field (e.g., option group).
     *     @type string $child_name Name of the field.
     *     @type string $id HTML id attribute.
     *     @type string $value Current color value (default: '#ffffff').
     *     @type string $label Label text for the field.
     *     @type string $description Description text for the field.
     * }
     * @return void
     */
    public static function color_picker(array $attributes): void
    {
        $parent_name = $attributes['parent_name'] ?? '';
        $child_name = $attributes['child_name'] ?? '';
        $id = $attributes['id'] ?? '';
        $name = "{$parent_name}[{$child_name}]";
        $value = $attributes['value'] ?? '#ffffff';

        self::start_wrap($id);
        self::label($attributes);

        echo '<input type="text" id="' . esc_attr($id) . '" name="' . esc_attr($name) . '" class="realtyna-color-picker" value="' . esc_attr($value) . '"/>';

        self::description($attributes);
        self::end_wrap();
    }

    /**
     * Validate and sanitize input data
     *
     * @param string $input The input value to be sanitized.
     * @param string $type The type of field (e.g., 'text', 'email', 'url', etc.).
     * @return string|array Sanitized value.
     */
    public static function sanitize_input($input, $type)
    {
        switch ($type) {
            case 'text':
                return sanitize_text_field($input);
            case 'email':
                return sanitize_email($input);
            case 'url':
                return esc_url_raw($input);
            case 'textarea':
                return sanitize_textarea_field($input);
            case 'checkbox':
                return (bool)$input;
            case 'number':
                return is_numeric($input) ? (int)$input : 0;
            default:
                return sanitize_text_field($input);
        }
    }

    /**
     * Register settings fields dynamically
     *
     * @param array $fields Array of field configurations.
     *                      Each field should have a 'type' key and other relevant attributes.
     * @return void
     */
    public static function register_fields(array $fields): void
    {
        foreach ($fields as $field) {
            $type = $field['type'] ?? 'text';
            $callback = [self::class, $type];
            if (is_callable($callback)) {
                call_user_func($callback, $field);
            }
        }
    }

    /**
     * Conditionally show or hide fields
     *
     * @param array $attributes {
     *     @type callable $condition A callable that returns true or false.
     *     @type string   $type Type of input field to render if the condition is met.
     *     @type array    $other Field-specific attributes (e.g., parent_name, child_name, label, etc.).
     * }
     * @return void
     */
    public static function conditional_logic(array $attributes): void
    {
        $condition = $attributes['condition'] ?? null;

        if ($condition && !$condition()) {
            return; // Do not render the field if the condition is not met
        }

        // Render the field based on its type
        $type = $attributes['type'] ?? 'input';
        $callback = [self::class, $type];
        if (is_callable($callback)) {
            call_user_func($callback, $attributes);
        }
    }

    /**
     * Echo repeater input (text field with add/remove buttons)
     *
     * @param array $attributes {
     *     @type string $parent_name Parent name for the field (e.g., option group).
     *     @type string $child_name Name of the field.
     *     @type string $id HTML id attribute.
     *     @type array  $values Array of values (if editing existing data).
     *     @type string $label Label text for the field.
     *     @type string $description Description text for the field.
     * }
     * @return void
     */
    public static function repeater_input(array $attributes): void
    {
        $parent_name = $attributes['parent_name'] ?? '';
        $child_name = $attributes['child_name'] ?? '';
        $id = $attributes['id'] ?? '';
        $name = "{$parent_name}[{$child_name}]";
        $values = $attributes['values'] ?? ['']; // Default to one empty field if no values exist.

        self::start_wrap($id);
        self::label($attributes);

        echo '<div class="realtyna-repeater-container" data-name="' . esc_attr($name) . '">';

        foreach ($values as $value) {
            echo '<div class="realtyna-repeater-item">
                <input type="text" name="' . esc_attr($name . '[]') . '" value="' . esc_attr($value) . '" />
                <button type="button" class="realtyna-repeater-remove">Delete</button>
              </div>';
        }

        echo '</div>';
        echo '<button type="button" class="realtyna-repeater-add">Add More</button>';

        self::description($attributes);
        self::end_wrap();

        // Add JavaScript to handle the dynamic addition/removal of fields
        add_action('admin_footer', function () {
            ?>
            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    document.querySelectorAll('.realtyna-repeater-add').forEach(button => {
                        button.addEventListener('click', function () {
                            let container = this.previousElementSibling;
                            let name = container.getAttribute('data-name');
                            let newItem = document.createElement('div');
                            newItem.classList.add('realtyna-repeater-item');
                            newItem.innerHTML = `
                            <input type="text" name="${name}[]" value="" />
                            <button type="button" class="realtyna-repeater-remove">Delete</button>
                        `;
                            container.appendChild(newItem);
                        });
                    });

                    document.addEventListener('click', function (event) {
                        if (event.target.classList.contains('realtyna-repeater-remove')) {
                            event.target.parentElement.remove();
                        }
                    });
                });
            </script>
            <style>
                .realtyna-repeater-item {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    margin-bottom: 5px;
                }
                .realtyna-repeater-remove {
                    background: #DD1435;
                    border-radius: 8px;
                    border: 1px solid #DD1435;
                    color: #fff;
                    fill: #fff;
                    padding: 5px 15px;
                }
                .realtyna-repeater-add {
                    background: #6302DD;
                    border-radius: 8px;
                    border: 1px solid #6302DD;
                    color: #fff;
                    fill: #fff;
                    padding: 5px 15px;
                }
            </style>
            <?php
        });
    }


}

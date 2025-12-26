# ShortcodeAbstract

The `ShortcodeAbstract` class provides a standardized structure for creating and managing shortcodes in WordPress plugins. By extending this abstract class, developers can easily define shortcodes and handle their rendering in a consistent and reusable way.

## Purpose

The primary purpose of `ShortcodeAbstract` is to:

- **Simplify Shortcode Creation**: Provide a reusable framework for defining and handling shortcodes in WordPress.
- **Encapsulate Shortcode Logic**: Allow developers to encapsulate the logic for processing and rendering shortcodes in a single, reusable class.
- **Ensure Consistent Output**: Provide methods for generating and returning the final output of the shortcode.

## Key Methods

### Abstract Methods

These methods must be implemented by any class that extends `ShortcodeAbstract`:

- **`getTag()`**: Returns the tag name of the shortcode. This tag is used as the unique identifier for the shortcode when it is added to a post or page.
- **`render(array $atts, ?string $content = null)`**: Handles the rendering of the shortcode. This method must be implemented to process the shortcode attributes and content and generate the final output.

### Concrete Methods

These methods are already implemented in the `ShortcodeAbstract` class and provide common functionality:

- **`__construct()`**: Registers the shortcode with WordPress using the tag name returned by `getTag()`.
- **`output(array $atts, ?string $content = null)`**: Generates and returns the final output for the shortcode. This method can be used by child classes to handle the actual rendering of the shortcode.

## Example Usage

Here’s an example of how you might use the `ShortcodeAbstract` class to create a `TestShortcode`:

```php
namespace Realtyna\BasePlugin\Components\Test\Shortcodes;

use Realtyna\Core\Abstracts\ShortcodeAbstract;

class TestShortcode extends ShortcodeAbstract
{
    protected function getTag(): string
    {
        return 'test_shortcode';
    }

    public function render(array $atts, ?string $content = null): string
    {
        // Process attributes with defaults
        $atts = shortcode_atts([
            'title' => 'Default Title',
        ], $atts, $this->getTag());

        // Generate the output using the helper method
        return $this->output($atts, $content);
    }

    protected function output(array $atts, ?string $content = null): string
    {
        // Example output (customized)
        $output = '<div class="test-shortcode">';
        $output .= '<h2>' . esc_html($atts['title']) . '</h2>';
        if ($content) {
            $output .= '<p>' . esc_html($content) . '</p>';
        }
        $output .= '</div>';

        return $output;
    }
}
```

### Explanation

- **`getTag()`**: Defines the shortcode tag as `test_shortcode`. This tag is used when inserting the shortcode into a post or page.
- **`render(array $atts, ?string $content = null)`**: Handles the processing of shortcode attributes and enclosed content. In this example, it uses `shortcode_atts()` to merge default attributes with those provided by the user and then generates the output.
- **`output(array $atts, ?string $content = null)`**: Generates the final output for the shortcode. In this example, it creates an HTML structure with the title and content provided by the shortcode.

### How It Works

1. **Shortcode Registration**: When the `TestShortcode` class is instantiated, the `__construct()` method registers the shortcode with WordPress using the tag `test_shortcode`.
2. **Shortcode Rendering**: When the shortcode is used in a post or page, WordPress calls the `render()` method, which processes the attributes and content and returns the final output.
3. **Output Generation**: The `output()` method is used to generate the actual HTML output for the shortcode, which is then returned to be displayed on the front end.

## Conclusion

The `ShortcodeAbstract` class simplifies the process of creating custom shortcodes in WordPress plugins by providing a consistent structure and handling much of the boilerplate code for you. By extending this class, developers can focus on the specific logic and behavior of their shortcodes while ensuring that best practices are followed.

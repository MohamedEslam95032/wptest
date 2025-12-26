### **Creating a Custom Integration for MLS On The Fly**

This guide provides a comprehensive walkthrough for developing a custom integration within the MLS On The Fly system by implementing the `Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\Interfaces\IntegrationInterface`. The following sections outline the implementation process and link to additional resources for further details.

#### **1. Implement the Integration Interface**

Your integration must implement the `IntegrationInterface` to ensure compatibility with the MLS On The Fly system. Here is a basic template for your custom integration class:

```php
namespace YourNameSpace;

use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\Interfaces\IntegrationInterface;

class CustomIntegration implements IntegrationInterface
{
    public $name = 'custom_integration';
    public array $metaWhiteList = [];
    public array $customPostTypes = [
        'custom_post_type',
    ];
    public array $customTaxonomies = [
        'custom_taxonomy_1',
        'custom_taxonomy_2',
    ];

    public function __construct()
    {
        // Initialization code, such as hooks or filters, goes here.
    }

    public function getMappingDir(): string
    {
        // Optionally return the directory where the JSON mapping files are located.
        return ''; // Returning an empty string will use the default structure.
    }

    // Additional methods as required by the IntegrationInterface.
}
```

#### **2. Create Mapping Files**

For your integration to function properly, you need to create JSON files that define how MLS data maps to your WordPress implementation. These files are:

- **Post Mapping (`custom_integration-post.json`)**: Defines mappings from MLS data to WordPress post fields.
- **Meta Mapping (`custom_integration-meta.json`)**: Maps MLS data to custom meta fields.
- **Query Mapping (`custom_integration-query.json`)**: Specifies how to query MLS data.

For detailed explanations of each mapping file, please refer to the [Mapping Documentation](mappin.md).

#### **3. Configure and Initialize Your Integration**

Set up hooks and filters within your integration class to customize its behavior. Example setup might include:

```php
public function __construct()
{
    // Hook into WordPress to add custom settings or modify behavior.
    add_action('init', function () {
        // Custom initialization logic, such as reading mapping files.
    }, 99999);

    // Add custom settings tabs
    add_filter('realtyna/mls-on-the-fly/admin/settings/tabs', [$this, 'filterSettingsTabs']);

    // Custom post-processing logic
    add_filter('realtyna_mls_on_the_fly_cloud_posts', [$this, 'modifyCloudPosts'], 10, 3);
    
    // Registering integration dynamically with a filter
    $this->integration = apply_filters('mls_on_the_fly_final_integration', $this->integration);
}

// Example of a custom filter function for adding settings tabs
public function filterSettingsTabs($tabs)
{
    $tabs['custom_integration'] = [
        'title'  => __('Custom Integration', 'realtyna-mls-on-the-fly'),
        'priority' => 50,
        'filename' => REALTYNA_MLS_ON_THE_FLY_TEMPLATES_PATH . "admin/settings-page/custom-integration-tab.php"
    ];
    return $tabs;
}

// Example of modifying posts
public function modifyCloudPosts($posts, $listings, $RFQuery)
{
    // Custom logic to modify posts before they are processed.
    return $posts;
}
```

#### **4. Register the Integration with MLS On The Fly**

Make sure your integration class is autoloaded and available within the MLS On The Fly system. The integration detection method should properly instantiate and configure your class based on the system's requirements.

#### **5. Testing Your Integration**

After setting up your integration:

1. **Activate it within WordPress**: Ensure the integration is active and correctly detected by the system.
2. **Verify mappings**: Check that MLS data is properly imported into your custom post types and meta fields.
3. **Debug as needed**: Use WordPress debugging tools and logs to address any potential issues.

### **Example: Houzez Integration**

Refer to the `HouzezIntegration` class for an in-depth example. This class includes various hooks, filters, and methods that manage data synchronization, meta fields, and custom query logic.

Following this guide will enable you to create a robust custom integration that seamlessly integrates with MLS On The Fly, ensuring efficient and accurate data handling within your WordPress site. For further details on JSON mappings, see the [Mapping Documentation](mapping.md).
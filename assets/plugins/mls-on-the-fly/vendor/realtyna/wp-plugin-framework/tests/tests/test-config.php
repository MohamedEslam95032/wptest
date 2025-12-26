<?php

use Realtyna\Core\Config;

class ConfigTest extends WP_UnitTestCase
{
    protected $config;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a temporary configuration array to simulate a configuration file
        $this->configArray = [
            'database' => [
                'host' => 'localhost',
                'username' => 'root',
                'password' => 'password',
            ],
            'app' => [
                'name' => 'MyApp',
                'version' => '1.0.0',
            ],
        ];

        // Create a temporary config file for testing
        $this->configFilePath = wp_tempnam();
        file_put_contents($this->configFilePath, '<?php return ' . var_export($this->configArray, true) . ';');

        // Initialize the Config class with the temporary config file
        $this->config = new Config($this->configFilePath);
    }

    protected function tearDown(): void
    {
        // Clean up the temporary config file after each test
        if (file_exists($this->configFilePath)) {
            unlink($this->configFilePath);
        }

        parent::tearDown();
    }

    public function testGetExistingValue()
    {
        // Test retrieving an existing value
        $this->assertEquals('localhost', $this->config->get('database.host'));
        $this->assertEquals('MyApp', $this->config->get('app.name'));
    }

    public function testGetNonExistingValueWithDefault()
    {
        // Test retrieving a non-existing value with a default fallback
        $this->assertEquals('default_value', $this->config->get('non.existing.key', 'default_value'));
    }

    public function testGetNonExistingValueWithoutDefault()
    {
        // Test retrieving a non-existing value without a default fallback (should return null)
        $this->assertNull($this->config->get('non.existing.key'));
    }

    public function testExceptionThrownForMissingConfigFile()
    {
        // Test if an exception is thrown when the config file is missing
        $this->expectException(Exception::class);
        new Config('/path/to/nonexistent/config.php');
    }
}

<?php
namespace Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\RFClient;

use Exception;
use Realtyna\Core\Abstracts\ComponentAbstract;
use Realtyna\MlsOnTheFly\Boot\App;
use Realtyna\MlsOnTheFly\Boot\Log;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\RFClient\SDK\RF\Exceptions\RequestFailedException;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\RFClient\SDK\RF\RF;
use Realtyna\MlsOnTheFly\Settings\Settings;

class RFClientComponent extends ComponentAbstract
{
    /**
     * @var RF|null The initialized RF client.
     */
    protected ?RF $RF = null;

    /**
     * Registers the component and initializes the RF client.
     * @throws Exception
     */
    public function register(): void
    {
        $this->initRFClient();
        if ($this->RF){
            App::set(RF::class,  $this->RF);
        }
    }

    /**
     * Initializes the Realty Feed (RF) client.
     *
     * This method retrieves the necessary credentials from the settings and initializes
     * the RF client. If the credentials are invalid, the RF client will not be initialized.
     *
     * @return void
     */
    protected function initRFClient(): void
    {
        // Retrieve credentials from settings
        $clientId = Settings::get_setting('client_id', false);
        $clientSecret = Settings::get_setting('client_secret', false);
        $apiKey = Settings::get_setting('api_key', false);

        // Initialize the RF client if credentials are available
        if ($clientId && $clientSecret && $apiKey) {
            try {
                $this->RF = new RF($clientId, $clientSecret, $apiKey);
                Log::info(__('RF client initialized successfully.', 'mls-on-the-fly'));
            } catch (RequestFailedException|Exception $e) {
                Log::error(__('Failed to initialize RF client: ', 'mls-on-the-fly') . $e->getMessage());
                $this->addAdminNotice(
                    __('Check your credentials. Failed to initialize RF client: ', 'mls-on-the-fly') . $e->getMessage(),
                    'error'
                );
            }
        } else {
            Log::warning(__('RF credentials are missing in settings.', 'mls-on-the-fly'));
            $this->addAdminNotice(__('<h3>MLS On The Fly ®</h3> RF credentials are missing in settings.', 'mls-on-the-fly'), 'warning');
        }
    }

    /**
     * Gets the initialized RF client instance.
     *
     * @return RF|null The initialized RF client, or null if the client is not initialized.
     */
    public function getRF(): ?RF
    {
        return $this->RF;
    }

}

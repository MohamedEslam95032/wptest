<?php
namespace Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\RFClient\SDK\RF;

use Realtyna\MlsOnTheFly\Settings\Settings;
use Realtyna\OData\Interfaces\AuthenticatorInterface;

class OAuth2Authenticator implements AuthenticatorInterface
{
    private string $clientId;
    private string $clientSecret;
    private string $apiKey;
    public string $accessToken;

    /**
     * @throws \Exception
     */
    public function __construct($clientId, $clientSecret, $apiKey)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->apiKey = $apiKey;

        // Retrieve and cache the access token upon object instantiation
        $this->accessToken = $this->retrieveAccessToken();
    }

    private function retrieveAccessToken(): string
    {
        // Check if the token is already cached
        $accessToken = get_transient('realtyna_mls_on_the_fly_rf_access_token');

        if (!$accessToken) {
            $response = wp_remote_post('https://realtyfeed-sso.auth.us-east-1.amazoncognito.com/oauth2/token', [
                'body' => [
                    'grant_type'    => 'client_credentials',
                    'client_id'     => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'scope'         => 'api/read',
                ],
            ]);
            if (is_wp_error($response)) {
                throw new \Exception('OAuth 2.0 authentication failed: ' . $response->get_error_message());
            }

            $responseJson = wp_remote_retrieve_body($response);
            $parsedResponse = json_decode($responseJson, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Error parsing JSON response: ' . json_last_error_msg());
            }
            if(isset($parsedResponse['error'])){
                // Trigger an admin notice directly
                add_action('admin_notices', function() {
                    ?>
                    <div class="notice notice-error is-dismissible">
                        <h3>MLS On The Fly ®</h3>
                        <p><?php _e('The RF credentials you entered are wrong. Please check and try again.', 'text-domain'); ?></p>
                    </div>
                    <?php
                });
                return '';
            }
            $expiresIn = $parsedResponse['expires_in'];
            $accessToken = $parsedResponse['access_token'];

            // Cache the token using WordPress transient
            set_transient('realtyna_mls_on_the_fly_rf_access_token', $accessToken, $expiresIn);
        }

        return $accessToken;
    }

    public function getHeaders(): array
    {
        // Return headers suitable for WordPress HTTP API
        return [
            'Authorization' => 'Bearer ' . $this->accessToken,
            'x-api-key'     => $this->apiKey,
            'origin'        => Settings::get_setting('rf_origin', ''),
            'referer'        => Settings::get_setting('rf_referer', '')
        ];
    }
}

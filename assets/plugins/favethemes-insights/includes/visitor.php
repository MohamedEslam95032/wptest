<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
if(!class_exists('Fave_Visitor')) {
    class Fave_Visitor {

        public $ip = null;
        public $referrer = null;
        public $language = null;
        public $unique_identifier = null;

        /**
         * Generate a unique identifier to identify unique visitors.
         *
         * @access public
         * @return md5|string
         */
        public static function unique_identifier() {
            return md5( json_encode( [
                self::get_language(),
                self::get_browser(),
                self::get_ip(),
                self::get_platform(),
                
            ] ) );
        }

        /**
         * Get user OS info based.
         *
         * @link  https://stackoverflow.com/questions/3441880/get-users-os-and-version-number/15497878#15497878
         * @access public
         * @return string|os
         */
        public static function get_platform() {
            // Get the user agent from the server or set it to an empty string
            $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';

            // Check if the user is on a mobile device or a desktop
            $device = wp_is_mobile() ? 'mobile' : 'desktop';

            // Initialize the OS platform variable
            $os_platform = false;

            // Array of OS regex patterns and their corresponding names
            $os_array = [
                '/macintosh|mac os x/i'            => 'macOS',
                '/ubuntu/i'                        => 'Ubuntu',
                '/linux/i'                         => 'Linux',
                '/android/i'                       => 'Android',
                '/iphone|ipad|ipod/i'              => 'iOS',
                '/webos/i'                         => 'webOS',
                '/windows nt 10/i'                 => 'Windows 10',
                '/windows nt 6.2|windows nt 6.3/i' => 'Windows 8',
                '/windows nt 6.1/i'                => 'Windows 7',
                '/win32/i'                         => 'Windows',
            ];

            // Iterate through the OS array and check the user agent against each regex pattern
            foreach ($os_array as $regex => $value) {
                if (preg_match($regex, $user_agent)) {
                    $os_platform = $value;
                    break; // Exit the loop once the OS platform is found
                }
            }

            // Return the platform and device information as an associative array
            return [
                'platform' => $os_platform,
                'device' => $device,
            ];
        }



        /**
         * Get user IP address if available in $_SERVER.
         *
         * @access public
         * @return false|IP Address
         */
        public static function get_ip() {
            // Array of server keys used to obtain the client's IP address
            $server_ip_keys = [
                'HTTP_CLIENT_IP',
                'HTTP_X_FORWARDED_FOR',
                'HTTP_X_FORWARDED',
                'HTTP_X_CLUSTER_CLIENT_IP',
                'HTTP_FORWARDED_FOR',
                'HTTP_FORWARDED',
                'REMOTE_ADDR',
            ];

            // Iterate through the server keys array
            foreach ($server_ip_keys as $key) {
                // Check if the server key is set and if the value is a valid IP address
                if (!isset($_SERVER[$key]) || !filter_var($_SERVER[$key], FILTER_VALIDATE_IP)) {
                    continue; // Skip the current iteration if the condition is not met
                }

                // Return the valid IP address
                return $_SERVER[$key];
            }

            // Fallback to the local IP address if none of the server keys return a valid IP
            return '127.0.0.1';
        }



        /**
         * Get visitor's browser language code
         *
         * @access public
         * @return false|language
         */
        public static function get_language($default = 'en') {
            // Check if the 'HTTP_ACCEPT_LANGUAGE' header is set
            if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
                // Split the 'HTTP_ACCEPT_LANGUAGE' header value into an array of languages
                $available_languages = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
                $languages = [];

                // Process each language entry from the header value
                foreach ($available_languages as $entry) {
                    if (preg_match("/(.*);q=([0-1]{0,1}.\d{0,4})/i", $entry, $matches)) {
                        $languages[$matches[1]] = (float)$matches[2];
                    } else {
                        $languages[$entry] = 1.0;
                    }
                }

                // Find the language with the highest quality value
                $highest_quality = 0.0;
                foreach ($languages as $language => $quality) {
                    if ($quality > $highest_quality) {
                        $highest_quality = $quality;
                        $default = $language;
                    }
                }

                return $default;
            }

            // Return false if the 'HTTP_ACCEPT_LANGUAGE' header is not set
            return false;
        }


        /**
         * Get user browser info based on $_SERVER['HTTP_USER_AGENT']
         *
         * @link  https://stackoverflow.com/questions/3441880/get-users-os-and-version-number/15497878#15497878
         * @access public
         * @return false|language
         */

        public static function get_browser() {
            // Check if the 'HTTP_USER_AGENT' header is set
            $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
            $browser = false;
            $browser_array = [
                '/msie/i' => 'Internet Explorer',
                '/firefox/i' => 'Firefox',
                '/safari/i' => 'Safari',
                '/konqueror/i' => 'Konqueror',
                '/chrome/i' => 'Chrome',
                '/edge/i' => 'Edge',
                '/opera/i' => 'Opera',
                '/netscape/i' => 'Netscape',
                '/mobile/i' => 'Handheld Browser'
            ];

            // Iterate through the array of browser regex patterns and values
            foreach ($browser_array as $regex => $value) {
                // If the regex pattern matches the user agent, set the browser value
                if (preg_match($regex, $user_agent)) {
                    $browser = $value;
                }
            }

            // Return the detected browser or false if no match was found
            return $browser;
        }


        /**
         * Get referrer URL if available
         *
         * @access public
         * @return array|url|domain
         */
        public static function get_referrer() {
            // Check if the 'HTTP_REFERER' header is set
            if (!empty($_SERVER['HTTP_REFERER'])) {

                $url = $_SERVER['HTTP_REFERER'];
                $parts = parse_url($url);

                // If parsing the URL fails or the 'host' part is empty, return false
                if ($parts === false || empty($parts['host'])) {
                    return false;
                }

                // Prepare an array containing the URL and its domain
                $referrer_info = [
                    'url' => $url,
                    'domain' => $parts['host'],
                ];

                // Return the referrer information
                return $referrer_info;
            }

            // Return false if the 'HTTP_REFERER' header is not set
            return false;
        }


        /**
         * Get user agent
         *
         * @access public
         * @return string
         */
        public static function get_user_agent() {
            return isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        }

        /**
         * Get user country and city (best-effort) via IP-based geolocation.
         * Replaces geoplugin.net (now paid) with free HTTPS providers.
         *
         * Priority:
         * 1) Cloudflare country header (if available)
         * 2) ipwho.is
         * 3) ipapi.co
         *
         * Return format: "City,Country Name,Country Code" (or best available)
         */
        public static function get_location() {
            $cookie_obj = new FTI_Cookies();
            $cookie_key = md5('fave_visitor_location');
            $cookie = $cookie_obj->get($cookie_key);
            if (!empty($cookie)) {
                return $cookie;
            }

            $ip = self::get_ip();
            $transient_key = 'fave_visitor_location_' . $ip;
            $location_data = get_transient($transient_key);
            if ($location_data !== false && !empty($location_data)) {
                $cookie_obj->set($cookie_key, $location_data, time() + DAY_IN_SECONDS);
                return $location_data;
            }

            $timeout_args = [
                'timeout'   => 4,         // be snappy
                'sslverify' => true,
                'headers'   => [
                    'Accept' => 'application/json',
                ],
            ];

            // 1) Fast path: Cloudflare country header (no city, but instant)
            // If your site is behind Cloudflare, this header is present.
            if (!empty($_SERVER['HTTP_CF_IPCOUNTRY'])) {
                $cc = sanitize_text_field($_SERVER['HTTP_CF_IPCOUNTRY']);
                // Country name not provided by CF header; keep city empty.
                $location_data = implode(',', array_filter(['', '', strtoupper($cc)]));
                set_transient($transient_key, $location_data, DAY_IN_SECONDS);
                $cookie_obj->set($cookie_key, $location_data, time() + DAY_IN_SECONDS);
                return $location_data;
            }

            // Helper to safely build the final string
            $build = function($city, $countryName, $countryCode) {
                $city        = is_string($city) ? trim($city) : '';
                $countryName = is_string($countryName) ? trim($countryName) : '';
                $countryCode = is_string($countryCode) ? strtoupper(trim($countryCode)) : '';
                // Prefer having at least a country code/name
                if ($city === '' && $countryName === '' && $countryCode === '') {
                    return '';
                }
                return "{$city},{$countryName},{$countryCode}";
            };

            // 2) ipwho.is (no key, HTTPS)
            $resp = wp_remote_get("https://ipwho.is/" . rawurlencode($ip) . "?fields=city,country,country_code,success", $timeout_args);
            if (!is_wp_error($resp) && wp_remote_retrieve_response_code($resp) === 200) {
                $json = json_decode(wp_remote_retrieve_body($resp), true);
                if (!empty($json) && (!isset($json['success']) || $json['success'] === true)) {
                    $location_data = $build(
                        $json['city'] ?? '',
                        $json['country'] ?? '',
                        $json['country_code'] ?? ''
                    );
                }
            }

            // 3) Fallback: ipapi.co (free tier, HTTPS, no key for basics)
            if (empty($location_data)) {
                $resp2 = wp_remote_get("https://ipapi.co/" . rawurlencode($ip) . "/json/", $timeout_args);
                if (!is_wp_error($resp2) && wp_remote_retrieve_response_code($resp2) === 200) {
                    $j2 = json_decode(wp_remote_retrieve_body($resp2), true);
                    if (!empty($j2) && empty($j2['error'])) {
                        $location_data = $build(
                            $j2['city'] ?? '',
                            $j2['country_name'] ?? '',
                            $j2['country_code'] ?? ''
                        );
                    }
                }
            }

            // If we still have nothing, set a minimal placeholder to avoid repeated calls
            if (empty($location_data)) {
                // Last resort: just cache an empty string briefly to prevent hammering
                $location_data = '';
                set_transient($transient_key, $location_data, HOUR_IN_SECONDS);
            } else {
                set_transient($transient_key, $location_data, DAY_IN_SECONDS);
            }

            $cookie_obj->set($cookie_key, $location_data, time() + DAY_IN_SECONDS);
            return $location_data;
        }

    }
}
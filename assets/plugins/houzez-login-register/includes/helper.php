<?php 
if (!function_exists('houzez_login_option')) {
    /**
     * Retrieve a specific option value from the 'houzez_options' settings.
     *
     * This function fetches the value of a given option from the 'houzez_options' stored in the database.
     * If the requested option does not exist, it returns the provided default value.
     *
     * @param string $opt_name The name of the option to retrieve.
     * @param mixed $default The default value to return if the option does not exist.
     * @return mixed The value of the option or the default value.
     */
    function houzez_login_option($opt_name, $default = null) {
        $houzez_options = get_option('houzez_options', []);

        return $houzez_options[$opt_name] ?? $default;
    }
}
<?php
/**
 * Security Helper Functions for Houzez Theme Functionality
 *
 * This file contains security functions to prevent Local File Inclusion (LFI) attacks
 * and other path-based vulnerabilities.
 *
 * @package Houzez Theme Functionality
 * @since 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Sanitize template slug to prevent directory traversal attacks
 *
 * @param string $slug The template slug to sanitize
 * @return string Sanitized slug safe for use in file paths
 */
if ( ! function_exists( 'houzez_sanitize_template_slug' ) ) {
    function houzez_sanitize_template_slug( $slug ) {
        if ( empty( $slug ) ) {
            return '';
        }

        // Remove any directory traversal attempts
        $slug = str_replace( array( '../', '..\\', '..', './', '.\\', '/', '\\' ), '', $slug );

        // Remove any null bytes
        $slug = str_replace( chr(0), '', $slug );

        // Allow only alphanumeric characters, dashes, and underscores
        $slug = preg_replace( '/[^a-zA-Z0-9_-]/', '', $slug );

        // Remove any double dashes or underscores
        $slug = preg_replace( '/[-_]{2,}/', '-', $slug );

        // Trim dashes and underscores from the beginning and end
        $slug = trim( $slug, '-_' );

        return $slug;
    }
}

/**
 * Validate that a template path is within the allowed plugin directory
 *
 * @param string $path The file path to validate
 * @return bool True if path is valid and safe, false otherwise
 */
if ( ! function_exists( 'houzez_validate_template_path' ) ) {
    function houzez_validate_template_path( $path ) {
        if ( empty( $path ) ) {
            return false;
        }

        // Check for null bytes first
        if ( strpos( $path, chr(0) ) !== false ) {
            return false;
        }

        // Get the plugin directory real path
        $plugin_dir = realpath( HOUZEZ_PLUGIN_DIR );
        if ( ! $plugin_dir ) {
            return false;
        }

        // Get the real path of the file's directory
        $file_dir = realpath( dirname( $path ) );

        // If realpath fails, the path doesn't exist or is invalid
        if ( ! $file_dir ) {
            // For non-existent files, check the constructed path
            $file_dir = dirname( $path );

            // Remove any traversal attempts from the constructed path
            if ( strpos( $file_dir, '..' ) !== false || strpos( $file_dir, './' ) !== false ) {
                return false;
            }

            // Ensure it starts with the plugin directory
            if ( strpos( $file_dir, HOUZEZ_PLUGIN_DIR ) !== 0 ) {
                return false;
            }
        } else {
            // For existing paths, ensure the real path is within plugin directory
            if ( strpos( $file_dir, $plugin_dir ) !== 0 ) {
                return false;
            }
        }

        // Check file extension - only allow PHP and HTML files
        $extension = strtolower( pathinfo( $path, PATHINFO_EXTENSION ) );
        $allowed_extensions = array( 'php', 'html' );

        if ( ! in_array( $extension, $allowed_extensions ) ) {
            return false;
        }

        return true;
    }
}

/**
 * Get the whitelist of allowed template field names
 *
 * @return array Array of allowed field names for template inclusion
 */
if ( ! function_exists( 'houzez_get_allowed_template_fields' ) ) {
    function houzez_get_allowed_template_fields() {
        return array(
            // Property details fields
            'property_id',
            'property_price',
            'property_size',
            'property_land',
            'property_bedrooms',
            'property_bathrooms',
            'property_rooms',
            'property_garage',
            'property_garage_size',
            'property_year',
            'property_status',
            'property_type',

            // Address fields
            'property_address',
            'property_zip',
            'property_country',
            'property_state',
            'property_city',
            'property_area',

            // Additional safe fields can be added here
        );
    }
}

/**
 * Validate if a field name is allowed for template inclusion
 *
 * @param string $field The field name to validate
 * @return bool True if field is allowed, false otherwise
 */
if ( ! function_exists( 'houzez_is_allowed_template_field' ) ) {
    function houzez_is_allowed_template_field( $field ) {
        $allowed_fields = houzez_get_allowed_template_fields();
        return in_array( $field, $allowed_fields, true );
    }
}

/**
 * Safely extract arguments array without overwriting critical variables
 *
 * @param array $args The arguments array to extract
 * @param array $protected_vars Array of variable names that should not be overwritten
 * @return array Array of extracted variables
 */
if ( ! function_exists( 'houzez_safe_extract' ) ) {
    function houzez_safe_extract( $args, $protected_vars = array() ) {
        $default_protected = array( 'template', 'slug', 'name', 'args', 'this', 'post', 'wp_query' );
        $protected_vars = array_merge( $default_protected, $protected_vars );

        $extracted = array();

        if ( is_array( $args ) && ! empty( $args ) ) {
            foreach ( $args as $key => $value ) {
                // Skip if the key is in the protected list
                if ( in_array( $key, $protected_vars, true ) ) {
                    continue;
                }

                // Validate the key name (alphanumeric and underscore only)
                if ( ! preg_match( '/^[a-zA-Z_][a-zA-Z0-9_]*$/', $key ) ) {
                    continue;
                }

                $extracted[ $key ] = $value;
            }
        }

        return $extracted;
    }
}

/**
 * Log security events for monitoring
 *
 * @param string $event_type Type of security event
 * @param string $message Event message
 * @param array $context Additional context data
 */
if ( ! function_exists( 'houzez_log_security_event' ) ) {
    function houzez_log_security_event( $event_type, $message, $context = array() ) {
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG === true ) {
            $log_entry = sprintf(
                '[HOUZEZ SECURITY] [%s] %s | Context: %s',
                $event_type,
                $message,
                json_encode( $context )
            );

            error_log( $log_entry );
        }
    }
}
<?php

namespace Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\PostInjection\SubComponents;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\Interfaces\IntegrationInterface;

class AttachmentHandler{

    public function __construct()
    {
        add_filter('get_attached_file', [$this, 'filterGetAttachedFile'], 10, 2);
        add_filter('wp_get_attachment_url', [$this, 'filterGetAttachmentUrl'], 10, 2);
        add_filter('wp_get_attachment_image_src', [$this, 'filterGetAttachmentImageSrc'], 10, 3);
    }

    public function filterGetAttachedFile($file, $attachment_id)
    {
        if ($attachment_id < 0) {
            $attachment = get_post($attachment_id);

            return $attachment->media_url ?? $file;
        }
        return $file;
    }

    public function filterGetAttachmentUrl($url, $attachment_id)
    {
        if ($attachment_id < 0) {
            $attachment = get_post($attachment_id);
            return $attachment->media_url;
        }
        return $url;
    }

    // Function to filter the image source of an attachment based on specific conditions.
    public function filterGetAttachmentImageSrc($image, $attachment_id, $size): array|bool
    {
        // Check if the attachment ID is invalid (less than 0).
        if ($attachment_id < 0) {
            // Get all registered image sizes in WordPress.
            $registeredImageSizes = wp_get_registered_image_subsizes();

            // If the $image variable is a boolean, return it as is.
            if (is_bool($image)) {
                return $image;
            }

            // Check if the requested size is 'post-thumbnail'.
            if ($size == 'post-thumbnail') {
                // Set specific dimensions for 'post-thumbnail'.
                $width = 90;
                $height = 90;
                $crop = true;
            } else {
                // If $size is an array, extract width and height.
                if (is_array($size)) {
                    $width = $size[0] ?? 300; // Default to 300 if width is not provided.
                    $height = $size[1] ?? 300; // Default to 300 if height is not provided.
                    $crop = true; // Default crop to true.
                } else {
                    // Check if the size exists in the registered image sizes.
                    if (isset($registeredImageSizes[$size])) {
                        // Get dimensions and crop from the registered sizes.
                        $width = $registeredImageSizes[$size]['width'] ?? 300;
                        $height = $registeredImageSizes[$size]['height'] ?? 300;
                        $crop = $registeredImageSizes[$size]['crop'] ?? true;
                    } else {
                        // Fallback to default dimensions if size is not registered.
                        $width = 300;
                        $height = 300;
                        $crop = true;
                    }
                }
            }

            // Update the $image array with calculated dimensions and crop information.
            $image[1] = $width;
            $image[2] = $height;
            $image[3] = $crop;
        }

        // Return the modified or original $image array.
        return $image;
    }


}
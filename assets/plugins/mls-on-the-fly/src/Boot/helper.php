<?php

if (!function_exists('d')) {
    function d()
    {
        call_user_func_array('dump', func_get_args());
    }
}
/**
 * Dump variables and die.
 */
if (!function_exists('dd')) {
    function dd()
    {
        call_user_func_array('dump', func_get_args());
        die();
    }
}
if (!function_exists('mls_on_the_fly_get_svg')) {
    function mls_on_the_fly_get_svg($name): false|string
    {
        return file_get_contents(REALTYNA_MLS_ON_THE_FLY_DIR . "assets/image/{$name}.svg");
    }
}

if (!function_exists('mls_on_the_fly_kses_svg')) {
    function mls_on_the_fly_kses_svg($svg)
    {
        return $svg; //TODO: develop
    }
}


if (!function_exists('mls_on_the_fly_delete_directory')) {
    function mls_on_the_fly_delete_directory($dir): bool
    {
        if (!file_exists($dir)) {
            return true;
        }
        if (!is_dir($dir)) {
            return unlink($dir);
        }
        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }
            if (!mls_on_the_fly_delete_directory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }
        return rmdir($dir);
    }
}

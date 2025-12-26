<?php
// Add functions to use in mapping files

function mls_on_the_fly_get_svg( $name ) {

    return file_get_contents( REALTYNA_MLS_ON_THE_FLY_BASE_PATH . "assets/image/{$name}.svg" );
}

function mls_on_the_fly_kses_svg( $svg ) {
    return $svg; //TODO: develop
}
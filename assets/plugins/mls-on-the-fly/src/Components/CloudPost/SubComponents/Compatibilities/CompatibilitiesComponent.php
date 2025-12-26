<?php

namespace Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Compatibilities;

use Realtyna\Core\Abstracts\ComponentAbstract;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Compatibilities\SitemapProviders\AIOSeo;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Compatibilities\SitemapProviders\WPSEO_Cloud_Post_Type_Sitemap_Provider;

class CompatibilitiesComponent extends ComponentAbstract
{
    public function register(): void
    {
        add_filter('wpseo_sitemaps_providers', [$this, 'yoastCompatibility'], 10, 1);
        if (class_exists('\AIOSEO\Plugin\AIOSEO')) {
            $AIOSeo = new AIOSeo();
            $AIOSeo->init();
        }
    }

    public function yoastCompatibility($external_providers): array
    {
        $external_providers[] = new WPSEO_Cloud_Post_Type_Sitemap_Provider();
        return $external_providers;
    }
}
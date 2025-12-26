<?php

namespace Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Compatibilities\SitemapProviders;


use Realtyna\MlsOnTheFly\Boot\App;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\Interfaces\IntegrationInterface;
use WP_Query;
use wpl_db;
use wpl_global;
use wpl_property;
use wpl_settings;

class AIOSeo
{

    private array $post_types = [];
    private IntegrationInterface $activeIntegration;


    public function init()
    {
        $this->activeIntegration = App::get(IntegrationInterface::class);
        $this->post_types = $this->activeIntegration->customPostTypes;
        if (!empty($this->post_types) && aioseo()->options->sitemap->general->enable) {
            add_filter('aioseo_sitemap_indexes', [$this, 'addIndex']);
            foreach ($this->post_types as $post_type) {
                aioseo()->addons->loadAddon($post_type, new wpl_addon_pro_aioseo_helper($this));
            }
        }
    }

    function addIndex($indexes)
    {
        foreach ($this->post_types as $post_type) {
            $offset = 100;
            $total = $this->get_post_type_count($post_type);
            $pages = ceil($total / $offset);
            $timestamp = time();
            $aio_sitemap_filename = aioseo()->options->sitemap->general->filename;
            $prefix = $post_type . '-';
            for ($i = 1; $i <= $pages; $i++) {
                $indexes[] = array(
                    'loc' => get_site_url() . '/' . $prefix . $i . '-' . $aio_sitemap_filename . '.xml',
                    'lastmod' => date('c', $timestamp),
                    'count' => 100,
                );
            }
        }

        return $indexes;
    }

    /**
     * Retrieves the count of posts for a given post type.
     *
     * @param string $post_type The post type.
     * @return int
     */
    protected function get_post_type_count(string $post_type): int
    {
        $args = [
            'post_type' => $post_type,
            'posts_per_page' => 1,
            'orderby' => 'date',
            'order' => 'DESC',
        ];

        $query = new WP_Query($args);

        if (count($query->posts) > 0 && isset($query->posts[0])) {
            $lastPost = $query->posts[0];
            $this->last_modification_date = $lastPost->post_date;
        }

        return $query->found_posts;
    }


    function get()
    {
        foreach ($this->post_types as $post_type) {
            $site_path = aioseo()->sitemap->indexName;
            $priority = 0.6;
            $change_frequency = 'weekly';
            $sm_object = [];
            $offset = 100;
            $prefix = $post_type . '-';
            if ($site_path == $post_type) {
                $total = $this->get_post_type_count($post_type);
                $pages = ceil($total / $offset);
                $timestamp = time();;
                $aio_sitemap_filename = aioseo()->options->sitemap->general->filename;
                for ($i = 1; $i <= $pages; $i++) {
                    $sm_object[] = array(
                        'loc' => get_site_url() . $prefix . $i . '-' . $aio_sitemap_filename . '.xml',
                        'priority' => $priority,
                        'changefreq' => $change_frequency,
                        'lastmod' => date('c', $timestamp)
                    );
                }
                return $sm_object;
            }
            if (str_contains($site_path, $prefix)) {
                $current_index = intval(str_replace($prefix, '', $site_path));
                $start = (max($current_index, 1) - 1) * $offset;

                $posts = $this->get_posts($post_type, 100, $start);
                $sm_data = [];
                foreach ($posts as $post) {
                    $sm_data[] = $this->get_url($post);
                }
//                $wpl_items = wpl_global::get_wpl_item_links([], $start, $offset);
//                $sm_data = [];
//                foreach ($wpl_items as $wpl_item) {
//                    $sm_data[] = array(
//                        'loc' => $wpl_item['link'],
//                        'priority' => $priority,
//                        'changefreq' => $change_frequency,
//                        'lastmod' => date('c', $wpl_item['time'])
//                    );
//                }
                return $sm_data;
            }
            return [];
        }
    }

    /**
     * Retrieves a set of posts based on type, count, and offset.
     *
     * @param string $type The post type.
     * @param int $count The number of posts to retrieve.
     * @param int $offset The offset to start retrieving posts.
     * @return array
     */
    protected function get_posts($type, $count, $offset)
    {
        $args = [
            'post_type' => $type,
            'posts_per_page' => $count,
            'offset' => $offset,
            'orderby' => 'modified',
            'order' => 'ASC',
        ];

        $query = new WP_Query($args);
        $posts = $query->posts;
        $post_ids = wp_list_pluck($posts, 'ID');

        update_meta_cache('post', $post_ids);

        return $posts;
    }

    protected function get_url($post)
    {
        $url = [];

        if ($this->activeIntegration->name == 'wpl') {
            if (isset($post->post_name) && $post->post_name != '') {
                // Retrieve necessary metadata for the property
                $post->meta_data['zip_id'] = $post->meta_data['wpl_zip_name'] ?? null;
                $mls_id = $post->meta_data['wpl_mls_id'];
                $ref_id = $post->meta_data['wpl_ref_id'];
                $source = $post->meta_data['wpl_source'];
                $kind = $post->meta_data['wpl_kind'];
                $rfData = (array)$post->meta_data['realty_feed_raw_data'];

                if (empty($kind)) {
                    $kind = 0;
                    $post->meta_data['kind'] = 0;
                }

                // Get property ID based on kind, MLS ID, and source
                $property_id = wpl_db::select("SELECT id FROM `#__wpl_properties` WHERE kind = '$kind' and mls_id = '$mls_id' and `source` = '$source'", 'loadResult');

                // Create new property if it doesn't exist
                if (empty($property_id)) {
                    $default_user_id = wpl_settings::get('rf_default_user') ?? 0;

                    $property_id = wpl_property::create_property_default(1, $kind);

                    $property = [
                        'kind' => $kind,
                        'mls_id' => $mls_id,
                        'user_id' => $default_user_id,
                        'ref_id' => $ref_id,
                        'source' => $source,
                        'deleted' => 0,
                        'expired' => 0,
                        'confirmed' => 1,
                        'finalized' => 1,
                    ];

                    wpl_db::update('wpl_properties', $property, 'id', $property_id);

                    // Handle multisites
                    wpl_db::update('wpl_properties', ['source_blog_id' => wpl_global::get_current_blog_id()], 'id', $property_id);
                }

                // Get the property link
                $wplUrl = wpl_property::get_property_link([], $property_id);
                $url['loc'] = $wplUrl;
            }
        } else {
            $url['loc'] = apply_filters('wpseo_xml_sitemap_post_url', get_permalink($post), $post);
        }


        // Set the modification date and other attributes
        $modified = max($post->post_modified_gmt, $post->post_date_gmt);
        if ($modified !== '0000-00-00 00:00:00') {
            $url['lastmod'] = $modified;
        }

        $url['changefreq'] = 'daily';


        $url['priority'] = 1;


        return $url;
    }

}

class wpl_addon_pro_aioseo_helper
{
    public $root;
    public $content;

    function __construct($mainClass)
    {
        $this->root = $mainClass;
        $this->content = $mainClass;
    }
}
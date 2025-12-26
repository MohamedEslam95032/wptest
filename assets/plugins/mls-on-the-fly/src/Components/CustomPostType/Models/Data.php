<?php

namespace Realtyna\MlsOnTheFly\Components\CustomPostType\Models;

use WP_Term;
use WP_Query;

/**
 * A helper class to centralize data to gain better performance on server side.
 * 
 * @author Melvin <melvin.g@realtyna.com>
 * 
 * @version 1.0.0
 */
class Data
{
    /**
     * Types Variables
     */

    /**
     * Hierarchical array of property types
     * 
     * @var array
     */
    private static $hierarchicalPropertyTypes;

    /**
     * Hierarchical array of non-empty property types
     * 
     * @var array
     */
    private static $hierarchicalNoneEmptyPropertyTypes;

    /**
     * Features Variables
     */

    /**
     * Features array containing key(slug) => value(name) pair
     * 
     * @var array
     */
    private static $featuresSlugName = array();

    /**
     * Features array containing key(term_id) => value(name) pair
     * 
     * @var array
     */
    private static $featuresIdName = array();

    /**
     * Hierarchical array of property features
     * 
     * @var array
     */
    private static $hierarchicalPropertyFeatures;

    /**
     * Statuses Variables
     */

    /**
     * Statuses array containing key(slug) => value(name) pair
     *
     * @var array
     */
    private static $statusesSlugName = array();

    /**
     * Statuses array containing key(term_id) => value(name) pair
     * 
     * @var array
     */
    private static $statusesIdName = array();

    /**
     * Hierarchical array of property statuses
     * 
     * @var array
     */
    private static $hierarchicalPropertyStatuses;

    /**
     * Others
     */

    /**
     * Array of agents with agent id as index and title as value
     *
     * @var array
     */
    private static $agentIdName;

    /**
     * Array of agencies with agency id as index and title as value
     * 
     * @var array
     */
    private static $agenciesIdName;

    /**
     * Returns hierarchical array of property types
     * 
     * @author Melvin <melvin.g@realtyna.com>
     *
     * @param bool $hideEmpty
     *
     * @return array
     */
    public static function getHierarchicalPropertyTypes(bool $hideEmpty = false): array
    {
        if (empty(self::$hierarchicalPropertyTypes) && !$hideEmpty) {
            self::$hierarchicalPropertyTypes = self::getHierarchicalTerms('property-type');
        } else if (empty(self::$hierarchicalNoneEmptyPropertyTypes) && $hideEmpty) {
            self::$hierarchicalNoneEmptyPropertyTypes = self::getHierarchicalTerms('property-type', true);
        }

        if ($hideEmpty) {
            return self::$hierarchicalNoneEmptyPropertyTypes;
        } else {
            return self::$hierarchicalPropertyTypes;
        }
    }

    /**
     * Returns a hierarchical array of terms where children terms are in children array.
     *
     * @author Melvin <melvin.g@realtyna.com>
     *
     * @param string  $taxonomyName
     * 
     * @param boolean $hideEmpty
     * 
     * @param string  $orderby - (name, slug, term_group, term_id, id, description, parent, term_order)
     * 
     * @param string  $order
     *
     * @return array
     */
    private static function getHierarchicalTerms($taxonomyName, $hideEmpty = false, $orderby = 'name', $order = 'ASC')
    {
        $hierarchicalTermsArray = array();

        $taxonomyTerms = get_terms(
            array(
                'taxonomy'         => $taxonomyName,
                'hide_empty'       => $hideEmpty,
                'suppress_filters' => false,
                'orderby'          => $orderby,
                'order'            => $order
            )
        );

        if (!empty($taxonomyTerms) && !is_wp_error($taxonomyTerms)) {
            $parentsIndex = 0;   // we have to use array index as we will be converting this array to JavaScript array for search form

            foreach ($taxonomyTerms as $index => $term) {
                if ($term->parent == 0) {
                    $hierarchicalTermsArray[$parentsIndex] = self::getTermData($term);

                    unset($taxonomyTerms[$index]);    // to optimise performance

                    self::addTermChildren(
                        $hierarchicalTermsArray[$parentsIndex], // parent term
                        $taxonomyTerms, // all terms from database
                        $hierarchicalTermsArray[$parentsIndex]['children'] // children array
                    );

                    $parentsIndex++;
                }
            }
        }

        return $hierarchicalTermsArray;
    }

    /**
     * Navigate through taxonomy terms array and organise children terms in children array.
     * 
     * @author Melvin <melvin.g@realtyna.com>
     *
     * @param array $parentTermData
     * 
     * @param array $taxonomyTerms
     * 
     * @param array $children
     * 
     */
    private static function addTermChildren(array $parentTermData, array &$taxonomyTerms, array &$children)
    {
        if (!empty($taxonomyTerms)) {
            $childrenIndex = 0;

            foreach ($taxonomyTerms as $index => $term) {
                if ($term->parent == $parentTermData['term_id']) {
                    $children[$childrenIndex] = self::getTermData($term);

                    unset($taxonomyTerms[$index]);   // to optimise performance

                    self::addTermChildren(
                        $children[$childrenIndex],
                        $taxonomyTerms,
                        $children[$childrenIndex]['children']    //children array
                    );

                    $childrenIndex++;
                }
            }
        }
    }

    /**
     * Returns an associative array containing necessary fields from WP_Term object.
     * 
     * @author Melvin <melvin.g@realtyna.com>
     *
     * @param WP_Term $term
     *
     * @return array
     */
    private static function getTermData(WP_Term $term): array
    {
        $termData             = array();
        $termData['term_id']  = $term->term_id;
        $termData['name']     = $term->name;
        $termData['slug']     = $term->slug;
        $termData['parent']   = $term->parent;
        $termData['count']    = $term->count;
        $termData['children'] = array();

        return $termData;
    }

    /**
     * Returns array of property features containing key(slug) => value(name) pair
     * 
     * @author Melvin <melvin.g@realtyna.com>
     *
     * @param bool $hideEmpty
     *
     * @return array
     */
    public static function getFeaturesSlugName(bool $hideEmpty = false): array
    {
        if (empty(self::$featuresSlugName)) {
            self::assembleSlugNameArray(self::getHierarchicalPropertyFeatures(), self::$featuresSlugName, $hideEmpty);
        }

        return self::$featuresSlugName;
    }

    /**
     * Returns array of property features containing key(term_id) => value(name) pair
     * 
     * @author Melvin <melvin.g@realtyna.com>
     *
     * @param bool $hideEmpty
     *
     * @return array
     */
    public static function getFeaturesIdName(bool $hideEmpty = false): array
    {
        if (empty(self::$featuresIdName)) {
            self::assembleIdNameArray(self::getHierarchicalPropertyFeatures(), self::$featuresIdName, $hideEmpty);
        }

        return self::$featuresIdName;
    }

    /**
     * Returns hierarchical array of property features
     * 
     * @author Melvin <melvin.g@realtyna.com>
     *
     * @return array
     */
    public static function getHierarchicalPropertyFeatures(): array
    {
        if (empty(self::$hierarchicalPropertyFeatures)) {
            self::$hierarchicalPropertyFeatures = self::getHierarchicalTerms('property-feature');
        }

        return self::$hierarchicalPropertyFeatures;
    }

    /**
     * Generate array containing key(slug) => value(name) pair from a hierarchical terms array.
     * 
     * @author Melvin <melvin.g@realtyna.com>
     *
     * @param array $hierarchicalTermsArray
     * 
     * @param array $termsArray
     * 
     * @param bool $hideEmpty
     * 
     * @param string $prefix
     * 
     * @return void
     */
    private static function assembleSlugNameArray(array $hierarchicalTermsArray, array &$termsArray, bool $hideEmpty = false, string $prefix = '')
    {
        if (!empty($hierarchicalTermsArray)) {
            foreach ($hierarchicalTermsArray as $term) {
                if ($hideEmpty && empty($term['count'])) {
                    continue;   // skip the iteration if hide empty is true and current term's count is 0
                }
                $termsArray[$term['slug']] = $prefix . $term['name'];
                if (!empty($term['children'])) {
                    self::assembleSlugNameArray($term['children'], $termsArray, $hideEmpty, '- ' . $prefix);
                }
            }
        }
    }

    /**
     * Generate array containing key(slug) => value(name) pair from a hierarchical terms array.
     * 
     * @author Melvin <melvin.g@realtyna.com>
     *
     * @param array $hierarchicalTermsArray
     * 
     * @param array $termsArray
     * 
     * @param bool $hideEmpty
     * 
     * @param string $prefix
     * 
     * @return void
     */
    private static function assembleIdNameArray(array $hierarchicalTermsArray, array &$termsArray, bool $hideEmpty = false, string $prefix = '')
    {
        if (!empty($hierarchicalTermsArray)) {
            foreach ($hierarchicalTermsArray as $term) {
                if ($hideEmpty && empty($term['count'])) {
                    continue;   // skip the iteration if hide empty is true and current term's count is 0
                }
                $termsArray[$term['term_id']] = $prefix . $term['name'];
                if (!empty($term['children'])) {
                    self::assembleIdNameArray($term['children'], $termsArray, $hideEmpty, '- ' . $prefix);
                }
            }
        }
    }

    /**
     * Returns array of property statuses containing key(slug) => value(name) pair
     * 
     * @author Melvin <melvin.g@realtyna.com>
     *
     * @param bool $hideEmpty
     *
     * @return array
     */
    public static function getStatusesSlugName(bool $hideEmpty = false): array
    {
        if (empty(self::$statusesSlugName)) {
            self::assembleSlugNameArray(self::getHierarchicalPropertyStatuses(), self::$statusesSlugName, $hideEmpty);
        }

        return self::$statusesSlugName;
    }

    /**
     * Returns array of property statuses containing key(term_id) => value(name) pair
     * 
     * @author Melvin <melvin.g@realtyna.com>
     *
     * @param bool $hideEmpty
     *
     * @return array
     */
    public static function getStatusesIdName(bool $hideEmpty = false): array
    {
        if (empty(self::$statusesIdName)) {
            self::assembleIdNameArray(self::getHierarchicalPropertyStatuses(), self::$statusesIdName, $hideEmpty);
        }

        return self::$statusesIdName;
    }

    /**
     * Returns hierarchical array of property statuses
     * 
     * @author Melvin <melvin.g@realtyna.com>
     *
     * @return array
     */
    public static function getHierarchicalPropertyStatuses(): array
    {
        if (empty(self::$hierarchicalPropertyStatuses)) {
            self::$hierarchicalPropertyStatuses = self::getHierarchicalTerms('property-status');
        }

        return self::$hierarchicalPropertyStatuses;
    }

    /**
     * Returns an array of agencies with agent id as index and title as value
     *
     * @author Melvin <melvin.g@realtyna.com>
     *
     * @return array|null
     */
    public static function getAgenciesIdName(): array
    {
        if (empty(self::$agenciesIdName)) {

            $agenciesQuery = new WP_Query(array(
                'post_type'        => 'agency',
                'posts_per_page'   => -1,
                'suppress_filters' => false, //So WPML can filter the posts according to current language
            ));

            $agenciesArray = array();

            if (!empty($agenciesQuery->posts)) {
                foreach ($agenciesQuery->posts as $singleAgency) {
                    $agenciesArray[$singleAgency->ID] = $singleAgency->post_title;
                }

                self::$agenciesIdName = $agenciesArray;
            } else {
                self::$agenciesIdName = $agenciesArray;
            }
        }

        return self::$agenciesIdName;
    }

    /**
     * Returns an array of agents with agent id as index and title as value
     * 
     * @author Melvin <melvin.g@realtyna.com>
     * 
     * @return array|null
     */
    public static function getAgentsIdName(): array
    {
        if (empty(self::$agentIdName)) {

            $agentsQuery = new WP_Query(array(
                'post_type'        => 'agent',
                'posts_per_page'   => -1,
                'suppress_filters' => false, //So WPML can filter the posts according to current language
            ));

            $agentsArray = array();

            if (!empty($agentsQuery->posts)) {
                foreach ($agentsQuery->posts as $singleAgent) {
                    $agentsArray[$singleAgent->ID] = $singleAgent->post_title;
                }
                self::$agentIdName = $agentsArray;
            } else {
                self::$agentIdName = $agentsArray;
            }
        }

        return self::$agentIdName;
    }
}

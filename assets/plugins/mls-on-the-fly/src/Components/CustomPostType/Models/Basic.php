<?php

namespace Realtyna\MlsOnTheFly\Components\CustomPostType\Models;

class Basic
{
    /**
     * Return Enenergy Performance Certificate default fields.
     * 
     * @author Melvin <melvin.g@realtyna.com>
     * 
     * @return array
     */
    public static function epcDefaultFields()
    {
        return apply_filters(
            'x_wpl_epc_default_fields',
            array(
                array(
                    'name'  => 'A+',
                    'color' => '#00845a',
                ),
                array(
                    'name'  => 'A',
                    'color' => '#18b058',
                ),
                array(
                    'name'  => 'B',
                    'color' => '#8dc643',
                ),
                array(
                    'name'  => 'C',
                    'color' => '#ffcc01',
                ),
                array(
                    'name'  => 'D',
                    'color' => '#f6ac63',
                ),
                array(
                    'name'  => 'E',
                    'color' => '#f78622',
                ),
                array(
                    'name'  => 'F',
                    'color' => '#ef1d3a',
                ),
                array(
                    'name'  => 'G',
                    'color' => '#d10400',
                ),
            )
        );
    }

    /**
     * Comma separated taxonomy terms with admin side links.
     * 
     * @param string $taxonomy
     * 
     * @param string $postType
     * 
     * @param int $postId
     * 
     * @return string|bool
     */
    public static function adminTaxonomyTerms($taxonomy, $postType, $postId)
    {
        $terms = get_the_terms($postId, $taxonomy);

        if (!empty($terms)) {
            $out = array();
            /* Loop through each term, linking to the 'edit posts' page for the specific term. */
            foreach ($terms as $term) {
                $out[] = sprintf(
                    '<a href="%s">%s</a>',
                    esc_url(
                        add_query_arg(
                            array(
                                'post_type' => $postType,
                                $taxonomy   => $term->slug,
                            ),
                            'edit.php'
                        )
                    ),
                    esc_html(sanitize_term_field('name', $term->name, $term->term_id, $taxonomy, 'display'))
                );
            }

            /* Join the terms, separating them with a comma. */
            return join(', ', $out);
        }

        return false;
    }
}

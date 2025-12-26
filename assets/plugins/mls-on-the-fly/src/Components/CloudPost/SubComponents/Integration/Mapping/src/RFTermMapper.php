<?php

namespace Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\Mapping\src;


use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\RFClient\SDK\RF\Entities\RFTermEntity;

class RFTermMapper
{

    private MappingConfig $mappingConfig;

    public function __construct(MappingConfig $mappingConfig)
    {
        $this->mappingConfig = $mappingConfig;
    }


    public function mapToWPTerm(array $terms, string $taxonomy, $parent = ''): array
    {
        $wpTerms = [];

        foreach ($terms as $term) {
            $wpTerms[] = self::mapSingleToWPTerm($term, $taxonomy, $parent);
        }

        return $wpTerms;
    }

    public function mapSingleToWPTerm(RFTermEntity $term, string $taxonomy, $parent = ''): \WP_Term
    {
        $replaces = $this->mappingConfig->getQueryTaxonomyReplaces($taxonomy);
        $termName = $term->getName();
        if ($replaces) {
            foreach ($replaces as $replace) {
                if ($term->getName() == $replace['search']) {
                    $termName = $replace['replace'];
                }
            }
        }

        if ($parent) {
            $slug = $parent->name . '-' . $termName;
        } else {
            $slug = $termName;
        }
        $slug = sanitize_title($slug);
        $currentTerms = get_terms([
            'taxonomy' => $taxonomy
        ]);
        $curentTermsIds= [];
        foreach ($currentTerms as $currentTerm){
            $curentTermsIds[$currentTerm->slug] = $currentTerm->term_id;
        }


        $wpTerm = new \stdClass();
        if(isset($curentTermsIds[$slug])){
            $wpTerm->term_id = $curentTermsIds[$slug];
        }

        $wpTerm->name = $termName;
        $wpTerm->slug = sanitize_title($slug);
        $wpTerm->term_group = 0;
        $wpTerm->taxonomy = $taxonomy;
        $wpTerm->description = '';
        $wpTerm->parent = $parent->term_id ?? 0; // Default parent ID is 0 if not provided
        $wpTerm->count = $term->getCount() ?? 1; // Default count is 1
        $wpTerm->filter = 'raw';

        return new \WP_Term($wpTerm);
    }
}

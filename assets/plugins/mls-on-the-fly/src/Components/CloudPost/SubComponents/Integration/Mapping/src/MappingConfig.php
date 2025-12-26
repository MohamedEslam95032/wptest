<?php

namespace Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\Mapping\src;

use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\Interfaces\IntegrationInterface;

class MappingConfig
{
    private static ?MappingConfig $instance = null;
    private array $postMapping = [];
    private array $metaMapping = [];
    private array $queryMapping = [];

    public function __construct(IntegrationInterface $integration)
    {
        $this->loadMapping($integration);
    }

    private function loadMapping(IntegrationInterface $integration): void
    {
        // Define the option name based on the application and type
        $optionNamePost = "realtyna_mapping_" . $integration->name . "_post";
        $optionNameMeta = "realtyna_mapping_" . $integration->name . "_meta";
        $optionNameQuery = "realtyna_mapping_" . $integration->name . "_query";

        // Attempt to retrieve the mapping data from the database
        $postMapping = get_option($optionNamePost, false);
        $metaMapping = get_option($optionNameMeta, false);
        $queryMapping = get_option($optionNameQuery, false);

        // Determine the directory where mapping files are stored.
        $mappingDir = $integration->getMappingDir();

        // If getMappingDir() returns an empty string, fall back to the default directory structure
        if (empty($mappingDir)) {
            $mappingDir = REALTYNA_MLS_ON_THE_FLY_DIR . 'src/Components/CloudPost/SubComponents/Integration/Mapping/applications/' . $integration->name . '/';
        } else {
            // If a custom directory is provided, append a trailing slash if it's missing
            $mappingDir = rtrim($mappingDir, '/') . '/';
        }

        // Load the post mapping if not retrieved from the database
        if ($postMapping === false) {
            $postMappingFile = $mappingDir . $integration->name . '-post.json';
            $postMappingContent = file_get_contents($postMappingFile);
            $postMapping = json_decode($postMappingContent, true);
        }

        // Load the meta mapping if not retrieved from the database
        if ($metaMapping === false) {
            $metaMappingFile = $mappingDir . $integration->name . '-meta.json';
            $metaMappingContent = file_get_contents($metaMappingFile);
            $metaMapping = json_decode($metaMappingContent, true);
        }

        // Load the query mapping if not retrieved from the database
        if ($queryMapping === false) {
            $queryMappingFile = $mappingDir . $integration->name . '-query.json';
            $queryMappingContent = file_get_contents($queryMappingFile);
            $queryMapping = json_decode($queryMappingContent, true);
        }

        // Apply both old and new filters to the mappings
        $this->postMapping = apply_filters("mls_on_the_fly_post_mapping_file", $postMapping);
        $this->metaMapping = apply_filters("mls_on_the_fly_meta_mapping_file", $metaMapping);
        $this->queryMapping = apply_filters("mls_on_the_fly_query_mapping_file", $queryMapping);

        // Old filters for backward compatibility
        $this->postMapping = apply_filters("rf_shell_post_mapping_file", $this->postMapping);
        $this->metaMapping = apply_filters("rf_shell_meta_mapping_file", $this->metaMapping);
        $this->queryMapping = apply_filters("rf_shell_query_mapping_file", $this->queryMapping);
    }


    public function getMappings(string $type): array
    {
        $propertyName = $type . "Mapping";
        return $this->$propertyName;
    }

    // Getter method for queryMapping that applies both old and new filters dynamically
    public function getQueryMapping(): array
    {
        $queryMapping = apply_filters("mls_on_the_fly_query_mapping_file", $this->queryMapping);
        return apply_filters("rf_shell_query_mapping_file", $queryMapping);
    }

    
    public function getPostMapping(string $key = ''): array
    {
        if ($key == '') {
            return $this->postMapping;
        }
        return $this->postMapping[$key] ?? [];
    }

    public function getMetaMapping(string $key = ''): array
    {
        if ($key == '') {
            return $this->metaMapping;
        }
        return $this->metaMapping[$key] ?? [];
    }

    // Methods to get specific parts of a mapping key for both post and meta mapping
    private function getMappingPart(string $key, array $mapping, string $part): ?string
    {
        return $mapping[$key][$part] ?? null;
    }

    public function getMappingMapping(string $key): ?string
    {
        return $this->getMappingPart($key, $this->postMapping, 'mapping') ?? $this->getMappingPart(
            $key,
            $this->metaMapping,
            'mapping'
        );
    }

    public function getDefault(string $key): ?string
    {
        return $this->getMappingPart($key, $this->postMapping, 'default') ?? $this->getMappingPart(
            $key,
            $this->metaMapping,
            'default'
        );
    }

    public function getMethod(string $key): ?string
    {
        return $this->getMappingPart($key, $this->postMapping, 'method') ?? $this->getMappingPart(
            $key,
            $this->metaMapping,
            'method'
        );
    }

    // Methods specific to query mapping
    public function getQueryPostMapping(string $key): ?string
    {
        return $this->getQueryMapping()['post_metas'][$key]['rf_field'] ?? null;
    }

    public function getQueryPostMappingMethod(string $key): ?string
    {
        return $this->getQueryMapping()['post_metas'][$key]['method'] ?? null;
    }

    public function getQueryPostMappingValue(string $key): ?bool
    {
        return $this->getQueryMapping()['post_metas'][$key]['value'] ?? null;
    }

    public function getQueryTaxonomyMapping(string $key): string|array|null
    {
        return $this->getQueryMapping()['taxonomies'][$key]['mapping'] ?? null;
    }

    public function getQueryTaxonomyChild(string $key): ?string
    {
        return $this->getQueryMapping()['taxonomies'][$key]['child'] ?? null;
    }

    public function getQueryTaxonomyMethod(string $key): ?string
    {
        return $this->getQueryMapping()['taxonomies'][$key]['method'] ?? null;
    }

    public function getQueryTaxonomyReplaces(string $key): ?array
    {
        return $this->getQueryMapping()['taxonomies'][$key]['replaces'] ?? null;
    }

    public function getQueryTaxonomySeparator(string $key): ?string
    {
        return $this->getQueryMapping()['taxonomies'][$key]['separator'] ?? null;
    }

    public function getQueryKeyMapping(string $key): ?string
    {
        return $this->getQueryMapping()['key_mappings'][$key] ?? null;
    }

    public function getQueryPostTypeMapping(string $key): ?string
    {
        return $this->getQueryMapping()['post_types'][$key]['rf_field'] ?? null;
    }

    public function getQueryPostTypeMethod(string $key): ?string
    {
        return $this->getQueryMapping()['post_types'][$key]['method'] ?? null;
    }

    public function getQueryPostTypeOperator(string $key): ?string
    {
        return $this->getQueryMapping()['post_types'][$key]['operator'] ?? null;
    }

    public function getQueryPostTypeValue(string $key): ?string
    {
        return $this->getQueryMapping()['post_types'][$key]['value'] ?? null;
    }
}

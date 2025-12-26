<?php

namespace Realtyna\Core\Abstracts;

/**
 * Class CustomTaxonomyAbstract
 *
 * An abstract class that defines the structure for creating custom taxonomies in WordPress.
 *
 * @package Realtyna\Core\Abstracts
 */
abstract class CustomTaxonomyAbstract
{

    /**
     * Register the taxonomy with WordPress.
     *
     * @return void
     */
    public function registerTaxonomy(): void
    {
        register_taxonomy(
            $this->getTaxonomySlug(),
            $this->getPostTypes(),
            $this->getTaxonomyArgs()
        );
    }

    /**
     * Get the taxonomy slug.
     *
     * This method should be implemented by child classes to return the taxonomy slug.
     *
     * @return string
     */
    abstract protected function getTaxonomySlug(): string;

    /**
     * Get the post types that the taxonomy applies to.
     *
     * This method should be implemented by child classes to return an array of post type slugs.
     *
     * @return array
     */
    abstract protected function getPostTypes(): array;

    /**
     * Get the arguments for registering the taxonomy.
     *
     * This method should be implemented by child classes to return an array of arguments for register_taxonomy.
     *
     * @return array
     */
    abstract protected function getTaxonomyArgs(): array;

    /**
     * Get the labels for the taxonomy.
     *
     * This method should be implemented by child classes to return an array of labels for the taxonomy.
     *
     * @return array
     */
    abstract protected function getTaxonomyLabels(): array;
}

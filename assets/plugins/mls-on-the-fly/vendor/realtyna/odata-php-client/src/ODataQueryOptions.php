<?php

namespace Realtyna\OData;

class ODataQueryOptions
{
    private array $selectFields = [];
    private array $expandFields = [];
    private array $orderBy = [];
    private int $top = 20;
    private int $skip = 0;
    private array $groupByFields = [];

    /**
     * Set the $select option for the query.
     *
     * @param array $fields Fields to be selected.
     *
     * @return $this
     */
    public function select(array $fields): static
    {
        $this->selectFields = $fields;
        return $this;
    }

    /**
     * Set the $expand option for the query.
     *
     * @param array $fields Fields to be expanded.
     *
     * @return $this
     */
    public function expand(array $fields): static
    {
        $this->expandFields = $fields;
        return $this;
    }

    /**
     * Set the $orderby option for the query.
     *
     * @param array $fields Fields to be ordered by and their sorting direction.
     *
     * @return $this
     */
    public function orderBy(array $fields): static
    {
        $this->orderBy = $fields;
        return $this;
    }

    /**
     * Set the $top option for the query.
     *
     * @param int $count The maximum number of records to return.
     *
     * @return $this
     */
    public function top(int $count): static
    {
        $this->top = $count;
        return $this;
    }

    /**
     * Set the $skip option for the query.
     *
     * @param int $count The number of records to skip before starting to return records.
     *
     * @return $this
     */
    public function skip(int $count): static
    {
        $this->skip = $count;
        return $this;
    }

    /**
     * Add a field to group by.
     *
     * @param string $field The field to group by.
     *
     * @return $this
     */
    public function groupBy(string $field): static
    {
        $this->groupByFields[] = "($field)";

        return $this;
    }

    /**
     * Generate the query string with the specified options.
     *
     * @return string The formatted query string.
     */
    public function buildQuery(): string
    {
        $queryParts = [];

        if (!empty($this->selectFields)) {
            $queryParts[] = '$select=' . implode(',', $this->selectFields);
        }

        if (!empty($this->expandFields)) {
            $queryParts[] = '$expand=' . implode(',', $this->expandFields);
        }

        if (!empty($this->orderBy)) {
            $queryParts[] = '$orderby=' . implode(' ', $this->orderBy);
        }

        if ($this->top !== 0) {
            $queryParts[] = '$top=' . $this->top;
        }

        if ($this->skip !== 0) {
            $queryParts[] = '$skip=' . $this->skip;
        }

        if (!empty($this->groupByFields)) {
            $queryParts[] = '$apply='. 'groupby(' . implode(', ', $this->groupByFields) . ')';
        }

        return implode('&', $queryParts);
    }
}

<?php

namespace Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\RFClient\SDK\RF;

/**
 * Class RFQuery
 * Represents a query for the RF (RealtyFeed) API.
 *
 * @package Realtyna\MlsOnTheFly\SDK\RF
 */
class RFQuery
{
    /**
     * @var string $entity The entity type for the query (e.g., "Property").
     */
    private string $entity = 'Property';

    /**
     * @var string $find The find parameter for the query.
     */
    private string $find = '';

    /**
     * @var int $top The number of records to retrieve in the query (default: 10).
     */
    private int $top = 10;

    /**
     * @var int $skip The number of records to skip in the query (default: 0).
     */
    private int $skip = 0;

    /**
     * @var bool $count Indicates whether to include count information in the query result (default: true).
     */
    private bool $count = true;

    /**
     * @var array $select An array of fields to select in the query.
     */
    private array $select = [];

    /**
     * @var array $filters An array of filter conditions for the query.
     */
    private array $filters = [];

    /**
     * @var string $search The search parameter for the query.
     */
    private string $search;

    /**
     * @var array $orders An array of sorting orders for the query.
     */
    private array $orders = [];
    private array $groups = [];

    private string $after_key = '';
    private array $custom_params = [];

    /**
     * RFQuery constructor.
     * Initializes a new RFQuery instance.
     */
    public function __construct()
    {
    }

    /**
     * Generate a cache key based on the query parameters.
     *
     * @return string
     */
    public function getCacheKey(): string
    {
        $result = $this->entity;
        $result .= isset($this->orderBy) ? ',orderBy:' . $this->orderBy : '';
        $result .= !empty($this->orders) ? ',orders:' . json_encode($this->orders) : '';
        $result .= isset($this->find) ? ',find:' . $this->find : '';
        $result .= $this->top ? ',top:' . $this->top : '';
        $result .= $this->skip ? ',skip:' . $this->skip : '';
        $result .= !empty($this->select) ? ',select:' . json_encode($this->select) : '';
        $result .= !empty($this->filters) ? ',filters:' . json_encode($this->filters) : '';
        $result .= isset($this->search) ? ',search:' . $this->search : '';
        $result .= !empty($this->groups) ? ',groups:' . json_encode($this->groups) : '';
        $result .= ',after_key:' . json_encode($this->after_key);
        $result .= ',custom_params:' . json_encode($this->custom_params);

        $result = hash('sha256', $result);
        return $result;
    }

    /**
     * Get the sorting orders for the query.
     *
     * @return array
     */
    public function get_orders(): array
    {
        return $this->orders;
    }

    /**
     * Set the sorting orders for the query.
     *
     * @param array $order An array containing sorting information (column and direction).
     */
    public function set_order(array $order): void
    {
        $column = $order['column'] ?? $order[0];
        $direction = $order['direction'] ?? ($order[1] ?? 'asc');

        $this->orders[] = [
            'column' => $column,
            'direction' => $direction
        ];
    }

    /**
     * Get the number of records to retrieve (top) in the query.
     *
     * @return int
     */
    public function get_top(): int
    {
        return $this->top;
    }

    /**
     * Set the number of records to retrieve (top) in the query.
     *
     * @param int $top The number of records to retrieve.
     */
    public function set_top(int $top): void
    {
        $this->top = $top;
    }

    /**
     * Get the number of records to skip in the query.
     *
     * @return int
     */
    public function get_skip(): int
    {
        return $this->skip;
    }

    /**
     * Set the number of records to skip in the query.
     *
     * @param int $skip The number of records to skip.
     */
    public function set_skip(int $skip): void
    {
        $this->skip = $skip;
    }

    /**
     * Check if the count information should be included in the query result.
     *
     * @return bool
     */
    public function is_count(): bool
    {
        return $this->count;
    }

    /**
     * Set whether to include count information in the query result.
     *
     * @param bool $count A boolean indicating whether to include count information.
     */
    public function set_count(bool $count): void
    {
        $this->count = $count;
    }

    /**
     * Get the fields to select in the query.
     *
     * @return array
     */
    public function get_select(): array
    {
        return $this->select;
    }

    /**
     * Set the fields to select in the query.
     *
     * @param array $select An array of fields to select.
     */
    public function set_select(array $select): void
    {
        $this->select = $select;
    }

    /**
     * Get the filters for the query as a collection.
     *
     * @return array
     */
    public function get_filters(): array
    {
        return $this->filters;
    }

    /**
     * Set the filters for the query.
     *
     * @param array $filters An array of filter conditions.
     */
    public function set_filters(array $filters): void
    {
        $this->filters = $filters;
    }

    /**
     * Add a filter condition to the query.
     *
     * @param string $method The filter method (e.g., "where", "orWhere").
     * @param string|array $key The filter key (e.g., "column_name") or an array of nested filters.
     * @param string $operator The filter operator (e.g., "=", ">", "<").
     * @param mixed $value The filter value (ignored if $key is an array).
     * @param string $boolean The boolean operator (e.g., "and", "or").
     */
    public function add_filter($method, $key, $operator = '', $value = '', $boolean = 'and'): void
    {
        $this->filters[] = [
            'method' => $method,
            'key' => $key,
            'operator' => $operator,
            'value' => $value,
            'boolean' => $boolean,
        ];
    }

    /**
     * Open a filter group with a logical relation (AND/OR).
     *
     * @param string $relation The logical relation (AND/OR).
     */
    public function open_filter(string $relation = 'AND'): void
    {
        $this->filters[] = [
            'type' => 'open',
            'relation' => strtoupper($relation),
        ];
    }

    /**
     * Close the current open filter group.
     */
    public function close_filter(): void
    {
        $this->filters[] = [
            'type' => 'close',
        ];
    }


    /**
     * Get the search parameter for the query.
     *
     * @return string
     */
    public function get_search(): string
    {
        return $this->search;
    }

    /**
     * Set the search parameter for the query.
     *
     * @param string $search The search parameter.
     */
    public function set_search(string $search): void
    {
        $this->search = $search;
    }

    /**
     * Get the entity type for the query.
     *
     * @return string
     */
    public function get_entity(): string
    {
        return $this->entity;
    }

    /**
     * Set the entity type for the query.
     *
     * @param string $entity The entity type.
     */
    public function set_entity(string $entity): void
    {
        $this->entity = $entity;
    }

    /**
     * Get the find parameter for the query.
     *
     * @return string
     */
    public function get_find(): string
    {
        return $this->find;
    }

    /**
     * Set the find parameter for the query.
     *
     * @param string $find The find parameter.
     */
    public function set_find(string $find): void
    {
        $this->find = $find;
    }

    /**
     * @return array
     */
    public function get_groups(): array
    {
        return $this->groups;
    }

    /**
     * @param array $groups
     */
    public function set_groups(array $groups): void
    {
        $this->groups = $groups;
    }

    /**
     * @return string
     */
    public function get_after_key(): string
    {
        return $this->after_key;
    }

    /**
     * @param string $after_key
     */
    public function set_after_key(string $after_key): void
    {
        $this->after_key = $after_key;
    }

    public function set_params(array $rf_params)
    {
        $this->custom_params = $rf_params;
    }

    public function get_custom_params(): array
    {
        return $this->custom_params;
    }
}

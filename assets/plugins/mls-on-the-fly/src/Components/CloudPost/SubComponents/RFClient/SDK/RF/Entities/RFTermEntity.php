<?php

namespace Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\RFClient\SDK\RF\Entities;
// This will be renamed to RFterm after we deleted RFTerm Model class
class RFTermEntity
{
    private string $name;
    private int $count;
    private string $slug;
    private string $parent;
    private string $RFField;

    public function __construct(string $name, int $count, string $RFField, string $parent = '')
    {
        $this->name = $name;
        $this->count = $count;
        $this->RFField = $RFField;
        $this->slug = $this->generateSlug($name);
        $this->parent = $parent;
    }

    private function generateSlug(string $name): string
    {
        // Convert name to lowercase and replace spaces with dashes
        $slug = strtolower(str_replace(' ', '-', $name));
        // Remove any special characters except dashes and alphanumeric characters
        $slug = preg_replace('/[^a-z0-9\-]/', '', $slug);
        return $slug;
    }

    public static function fromArray(array $data): RFTermEntity
    {
        $arrayKey = array_keys($data)[0];

        return new self(
            $data[$arrayKey] ?? '',
            $data[$arrayKey . 'Count'] ?? 0,
            $arrayKey,
            ''
        );
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getParent(): string
    {
        return $this->parent;
    }

    public function getRFField(): string
    {
        return $this->RFField;
    }

    public static function createFromArray(array $dataArray): array
    {
        $terms = [];

        foreach ($dataArray as $data) {
            $terms[] = self::fromArray($data);
        }

        return $terms;
    }
}

<?php

namespace Realtyna\OData;

use Exception;
use Realtyna\OData\Exceptions\ODataResponseException;

class ODataResponseParser
{
    /**
     * Parse the response content, which can be JSON or XML.
     *
     * @param string $response The response content (JSON or XML).
     *
     * @return array|null An array representing the parsed response data, or null on failure.
     *
     * @throws ODataResponseException If there's an error during parsing.
     */
    public function parseResponse(string $response): ?array
    {
        // Try to decode as JSON
        $jsonData = json_decode($response, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            return $jsonData;
        }


        $document = new \DOMDocument();
        $document->loadXml($response);

        $data[] = $this->domNodeToArray($document->documentElement);

        if (!empty($data)) {
            // Convert XML to an array
            $jsonXml = json_encode($data);
            $arrayData = json_decode($jsonXml, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                return $arrayData;
            }
        }

        throw new ODataResponseException('Error parsing the response content.');
    }

    /**
     * Extract and return the value of a specific property from the parsed response.
     *
     * @param array|null $parsedResponse The parsed response data.
     * @param string $propertyName The name of the property to extract.
     *
     * @return mixed|null The value of the specified property, or null if not found.
     */
    public function extractProperty(?array $parsedResponse, string $propertyName): mixed
    {
        if (is_array($parsedResponse) && array_key_exists($propertyName, $parsedResponse)) {
            return $parsedResponse[$propertyName];
        }

        return null;
    }

    /**
     * Extract and return a collection of entities from the parsed response.
     *
     * @param array|null $parsedResponse The parsed response data.
     * @param string $entityType The name of the entity type to extract.
     *
     * @return array An array of entity data, or an empty array if not found.
     */
    public function extractEntities(?array $parsedResponse, string $entityType = 'array'): array
    {
        $entities = [];
        if (is_array($parsedResponse) && array_key_exists('value', $parsedResponse)) {
            foreach ($parsedResponse['value'] as $entity) {
                if($entityType == 'array') {
                    $entities[] = $entity;
                }elseif(class_exists($entityType)){
                    $entities[] = new $entityType($entity);
                }
            }
        }

        return $entities;
    }

    function domNodeToArray($node) {
        $nodesWithNephews = [];
        if ($node->hasAttributes()) {
            foreach ($node->attributes as $attr) {
                $data['@' . $attr->name] = $attr->value;
            }
        }

        if ($node->hasChildNodes()) {
            foreach ($node->childNodes as $child) {
                if ($child->nodeType === XML_ELEMENT_NODE) {
                    if(isset($data[$child->nodeName])){
                        $tmp = $data[$child->nodeName];
                        if(!in_array($child->nodeName, $nodesWithNephews)){
                            $data[$child->nodeName] = [];
                            $data[$child->nodeName][] = $tmp;
                        }
                        $data[$child->nodeName][] = $this->domNodeToArray($child);
                        $nodesWithNephews[] = $child->nodeName;
                    }else{
                        $data[$child->nodeName] = $this->domNodeToArray($child);
                    }
                } elseif ($child->nodeType === XML_TEXT_NODE) {
                    $data = $child->nodeValue;
                }
            }
        }

        return $data;
    }
}

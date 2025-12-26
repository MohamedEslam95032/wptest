<?php

namespace Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\Mapping\src;


use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\RFClient\SDK\RF\Entities\RFProperty;
use Realtyna\MlsOnTheFly\Settings\Settings;
use stdClass;
use WP_Post;

class RFPropertyMapper
{
    private MappingConfig $mappingConfig;
    private RFProperty $RFProperty;

    public function __construct(MappingConfig $mappingConfig)
    {
        $this->mappingConfig = $mappingConfig;
    }

    public function mapRFPropertyTPWPTerm(RFProperty $RFProperty, $taxonomies, \WP_Term_Query $term_query): array
    {
        $terms = [];
        foreach ($taxonomies as $taxonomy) {
            $RFFields = $this->mappingConfig->getQueryTaxonomyMapping($taxonomy);
            $RFChildFields = $this->mappingConfig->getQueryTaxonomyChild($taxonomy);
            $replaces = $this->mappingConfig->getQueryTaxonomyReplaces($taxonomy);

            if (is_string($RFFields)) {
                $RFFields = [$RFFields];
            }

            if (is_string($RFChildFields)) {
                $RFChildFields = [$RFChildFields];
            }

            if (is_array($RFFields)) {
                foreach ($RFFields as $RFField) {
                    if (property_exists($RFProperty, $RFField)) {
                        if (is_array($RFProperty->$RFField)) {
                            foreach ($RFProperty->$RFField as $rfChildField) {
                                if ($replaces) {
                                    foreach ($replaces as $replace) {
                                        if ($rfChildField == $replace['search']) {
                                            $rfChildField = $replace['replace'];
                                        }
                                    }
                                }
                                $term = get_term_by('name', $rfChildField, $taxonomy);
                                if ($term) {
                                    $term->object_id = (int)$RFProperty->post_id;
                                    $terms[] = $term;
                                }
                            }
                        } else {
                            if ($replaces) {
                                foreach ($replaces as $replace) {
                                    if ($RFProperty->$RFField == $replace['search']) {
                                        $RFProperty->$RFField = $replace['replace'];
                                    }
                                }
                            }
                            $term = get_term_by('name', $RFProperty->$RFField, $taxonomy);
                            if ($term) {
                                $term->object_id = (int)$RFProperty->post_id;
                                $terms[] = $term;
                            }
                        }
                    }
                }
            }

            if (is_array($RFChildFields)) {
                foreach ($RFChildFields as $RFChildField) {
                    if (property_exists($RFProperty, $RFChildField)) {
                        $term = get_term_by('name', $RFProperty->$RFChildField, $taxonomy);
                        if ($term) {
                            $term->object_id = (int)$RFProperty->post_id;
                            $terms[] = $term;
                        }
                    }
                }
            }
        }
        return $this->filterTermFields($terms, $term_query->query_vars['fields']);
    }

    /**
     * Filter term fields based on specified fields parameter.
     *
     * @param array $terms Array of WP_Term objects.
     * @param mixed $fields Fields parameter to determine which fields to include.
     *
     * @return array|int|null Filtered array or count based on the specified fields.
     */
    private function filterTermFields(array $terms, string $fields): array|int|null
    {
        $_terms = array();

        // Check the requested fields and populate $_terms accordingly
        if ('id=>parent' === $fields) {
            foreach ($terms as $term) {
                $_terms[$term->term_id] = $term->parent;
            }
        } elseif ('ids' === $fields) {
            foreach ($terms as $term) {
                $_terms[] = (int)$term->term_id;
            }
        } elseif ('tt_ids' === $fields) {
            foreach ($terms as $term) {
                $_terms[] = (int)$term->term_taxonomy_id;
            }
        } elseif ('names' === $fields) {
            foreach ($terms as $term) {
                $_terms[] = $term->name;
            }
        } elseif ('slugs' === $fields) {
            foreach ($terms as $term) {
                $_terms[] = $term->slug;
            }
        } elseif ('id=>name' === $fields) {
            foreach ($terms as $term) {
                $_terms[$term->term_id] = $term->name;
            }
        } elseif ('id=>slug' === $fields) {
            foreach ($terms as $term) {
                $_terms[$term->term_id] = $term->slug;
            }
        } elseif ('all' === $fields || 'all_with_object_id' === $fields) {
            $_terms = $terms;
        } elseif ('count' === $fields) {
            $_terms = count($terms);
        }

        return $_terms;
    }

    public function mapRFPropertyToWPPost(RFProperty $RFProperty, ?\WP_Query $wp_query): WP_Post
    {
        apply_filters("realtyna-mls-on-the-fly-rf-property", $RFProperty);
        $this->RFProperty = $RFProperty;

        $wpPostObject = new stdClass();
        $postMappings = $this->mappingConfig->getPostMapping();
        // Apply post mappings to generate the WP_Post object properties.
        foreach ($postMappings as $key => $value) {
            if ($key == 'post_name') {
                $value['mapping'] = '{ListingKey}-' . Settings::get_setting('url_patterns', '');
                $renderedValue = self::renderMappingValue($value);
                $renderedValue = str_replace(' ', '-', $renderedValue); // Replace spaces with dashes
                $renderedValue = preg_replace('/-+/', '-', $renderedValue); // Remove extra dashes
            }else{
                $renderedValue = self::renderMappingValue($value);
            }

            $wpPostObject->$key = $renderedValue;
        }

        if (isset($this->RFProperty->Media)) {
            $wpPostObject->medias = $this->RFProperty->Media;
        }

        $metaMappings = $this->mappingConfig->getMetaMapping();
        // Apply meta mappings to generate metadata for the WP_Post object.
        foreach ($metaMappings as $key => $value) {
            $renderedValue = self::renderMappingValue($value);
            if ($renderedValue) {
                $wpPostObject->meta_data[$key] = $renderedValue;
            }
        }
        $wpPostObject->meta_data['realty_feed_raw_data'] = $this->RFProperty;
        return new WP_Post($wpPostObject);
    }

    protected function renderMappingValue(mixed $value): mixed
    {
        $rendered = '';

        if (is_array($value)) {
            if (!empty($value['mapping'])) {
                $mapping = $value['mapping'];
                $returnType = $value['return_type'] ?? 'string';

                $rendered = $this->compile($mapping, $returnType);
            } elseif (!empty($value['default'])) {
                $rendered = $value['default'];
            }

            if (!empty($value['replaces'])) {
                foreach ($value['replaces'] as $replaceObject) {
                    $rendered = str_replace($replaceObject['search'], $replaceObject['replace'], $rendered);
                }
            }
        } else {
            $rendered = $value;
        }

        return $rendered;
    }


    /**
     * Compiles a given value by applying various transformation rules.
     *
     * This method takes a value and applies a series of transformation rules to it based on the provided `$returnType`.
     * The supported transformation rules include parsing "contains," "inArray," "if," "sum," "min," "lower," "upper,"
     * "max," "or," and "nestedArray." These rules manipulate the value according to their specific logic.
     *
     * @param string $value The value to be compiled.
     * @param string $returnType The desired return type for the compiled value (e.g., 'string' or 'boolean').
     *
     * @return string|int|bool|float The compiled value based on the specified rules and return type.
     */
    protected function compile(string $value, string $returnType = 'string')
    {
        $value = $this->parseContains($value, $returnType);
        $value = $this->parseInArray($value, $returnType);
        $value = $this->parseIf($value, $returnType);
        $value = $this->parseMethod($value, $returnType);
        $value = $this->parseSum($value, $returnType);
        $value = $this->parseMin($value, $returnType);
        $value = $this->parseLower($value, $returnType);
        $value = $this->parseUpper($value, $returnType);
        $value = $this->parsemax($value, $returnType);
        $value = $this->parseOr($value, $returnType);
        $value = $this->parseNestedArray($value, $returnType);

        return $this->parseVariables($value, $returnType);
    }


    /**
     * Replace placeholders in a string with actual values from the RFData object.
     *
     * This method searches for placeholders enclosed in curly braces `{}` within the given string value
     * and replaces them with the corresponding values from the `$this->RFProperty` object. The replacement
     * can be done either as strings or booleans, depending on the specified return type.
     *
     * @param string $value The string containing placeholders to be replaced.
     * @param string $returnType The return type for replacements: 'string' or 'boolean'.
     *
     * @return float|bool|int|string The updated string with placeholders replaced or a boolean value
     *                         based on the specified return type.
     */
    protected function parseVariables(string $value, string $returnType = 'string')
    {
        // Use regular expression to find all placeholders within curly braces.
        preg_match_all('/\{([^}]+)}/', $value, $matches);

        // If no matches are found, return the original value.
        if (empty($matches)) {
            return $value;
        }

        foreach ($matches[1] as $match) {

            // Extract prefix and postfix if any
            preg_match('/^([^a-zA-Z0-9]*)([a-zA-Z0-9]+)([^a-zA-Z0-9]*)$/', $match, $parts);

            $prefix = $parts[1] ?? ''; // Special characters before the main value
            $cleanMatch = $parts[2] ?? $match; // The actual property name
            $postfix = $parts[3] ?? ''; // Special characters after the main value

            // Check if the property with this name exists in $this->RFProperty, is set, and not empty.
            if (property_exists($this->RFProperty, $cleanMatch) && isset($this->RFProperty->$cleanMatch) && $this->RFProperty->$cleanMatch) {
                if ($returnType == 'boolean' && is_array($this->RFProperty->$cleanMatch)) {
                    $value = str_replace('{' . $match . '}', strval(true), $value);
                } elseif ($returnType == 'array') {
                    $value = $this->RFProperty->$cleanMatch;
                } elseif ($returnType == 'stringed-array') {
                    $items = [];
                    if (is_array($this->RFProperty->$cleanMatch)) {
                        foreach ($this->RFProperty->$cleanMatch as $item) {
                            $items[] = "'$item'";
                        }
                    } else {
                        $items[] = "'" . $this->RFProperty->$cleanMatch . "'";
                    }
                    $value = str_replace('{' . $match . '}', '[' . implode(',', $items) . ']', $value);
                } elseif ($returnType != 'boolean') {
                    if (is_array($this->RFProperty->$cleanMatch)) {
                        $this->RFProperty->$cleanMatch = implode(',', $this->RFProperty->$cleanMatch);
                    }
                    // Reapply prefix and postfix around the replaced value
                    $value = str_replace('{' . $match . '}', $prefix . strval($this->RFProperty->$cleanMatch) . $postfix, $value);
                }
            } else {
                if ($returnType == 'string') {
                    $value = str_replace('{' . $match . '}', '', (string)$value);
                } elseif ($returnType == 'array') {
                    $value = [];
                } elseif ($returnType == 'stringed-array') {
                    $value = str_replace('{' . $match . '}', '[]', $value);
                } elseif ($returnType == 'boolean') {
                    $value = false;
                }
            }

            // Check if the resulting value is numeric and convert it if necessary.
            $value = is_numeric($value) && $returnType != 'string' ? $value + 0 : $value;
        }

        return $value;
    }


    /**
     * Replace placeholders in a string with actual values from the RFData object using an 'OR' logic.
     *
     * This method searches for placeholders enclosed in curly braces `{}` within the given string value.
     * If the placeholders contain multiple items separated by '|', it tries to replace them with actual values
     * from the `$this->RFProperty` object using an 'OR' logic. If any of the items has a non-empty value, the replacement
     * is performed. The replacement can be done either as strings or booleans, depending on the specified return type.
     *
     * @param string $value The string containing placeholders to be replaced using 'OR' logic.
     * @param string $returnType The return type for replacements: 'string' or 'boolean'.
     *
     * @return string|boolean   The updated string with placeholders replaced or a boolean value
     *                         based on the specified return type.
     */
    protected function parseOr(string $value, string $returnType = 'string'): bool|string
    {
        // Use regular expression to find all placeholders within curly braces.
        preg_match_all('/\{([^}]+)}/', $value, $matches);

        // If no matches are found, return the original value.
        if (empty($matches)) {
            return $value;
        }

        foreach ($matches[1] as $match) {
            // Check if the placeholder contains multiple items separated by '|'.
            if (!strpos($match, '|')) {
                continue;
            }

            $items = explode('|', $match);

            foreach ($items as $item) {
                // Replace each item within the placeholder and check if it's not empty.
                $matchValue = self::parseVariables('{' . $item . '}');
                if (!empty($matchValue)) {
                    // If any item has a non-empty value, replace the entire placeholder and return the updated value.
                    return str_replace('{' . $match . '}', $matchValue, $value);
                }
            }
        }

        return $value;
    }


    /**
     * Replace placeholders in a string with values from nested arrays in the RFData object.
     *
     * This method searches for placeholders enclosed in curly braces `{}` within the given string value.
     * If the placeholders contain dot-separated keys, it navigates the RFData object's nested arrays and
     * retrieves the corresponding values. The replacement can be done either as strings or booleans,
     * depending on the specified return type.
     *
     * @param string $value The string containing placeholders to be replaced with nested array values.
     * @param string $returnType The return type for replacements: 'string' or 'boolean'.
     *
     * @return string|boolean   The updated string with placeholders replaced or a boolean value
     *                         based on the specified return type.
     */
    protected function parseNestedArray(string $value, string $returnType = 'string'): bool|string
    {
        // Use regular expression to find all placeholders within curly braces.
        preg_match_all('/\{([^}]+)}/', $value, $matches);

        // If no matches are found, return the original value.
        if (empty($matches)) {
            return $value;
        }

        foreach ($matches[1] as $match) {
            // Check if the placeholder contains dot-separated keys.
            if (!strpos($match, '.')) {
                continue;
            }

            $result = [];
            $matchValue = $this->RFProperty;

            $arrayKeys = explode('.', $match);

            if (in_array('*', $arrayKeys)) {
                $foundAsterix = false;
                foreach ($arrayKeys as $arrayKey) {
                    if ($arrayKey == '*') {
                        $foundAsterix = true;
                        continue;
                    }

                    if ($foundAsterix) {
                        $matchValue = array_column($matchValue, $arrayKey);
                        $foundAsterix = false;
                    } else {
                        $matchValue = is_array($matchValue) ? $matchValue[$arrayKey] : $matchValue->$arrayKey;
                    }
                    $result = json_encode($matchValue);
                }
            } else {
                foreach ($arrayKeys as $arrayKey) {
                    $matchValue = is_array($matchValue) ? $matchValue[$arrayKey] : $matchValue->$arrayKey;
                    $result = $matchValue;
                }
            }

            $value = str_replace('{' . $match . '}', $result, $value);
        }

        return $value;
    }

    /**
     * Parse and evaluate an IF condition within the given string and return the result based on the specified return type.
     *
     * This method searches for an IF condition within the input string, evaluates it, and returns the result
     * based on the specified return type. The IF condition should be in the format: IF(condition, trueValue, falseValue).
     *
     * @param string $value The string containing the IF condition to be parsed and evaluated.
     * @param string $returnType The return type for the result: 'string' or 'boolean'.
     *
     * @return string|boolean   The evaluated result based on the specified return type.
     */
    protected function parseIf(string $value, string $returnType = 'string'): bool|string
    {
        // Use regular expression to match and extract the IF condition.
        preg_match(
            '/IF\(\s*((?:[^,()]|\((?:[^()]|\([^()]*\))*\))*)\s*,\s*([\'"])?([^\'"]*)\2\s*,\s*([\'"])?([^\'"]*)\4\s*\)/',
            $value,
            $matches
        );

        // If no matches are found, return the original value.
        if (empty($matches)) {
            return $value;
        }

        $condition = $matches[1];
        $trueValue = $matches[3];
        $falseValue = $matches[5];

        $returnType = 'string';
//        if (str_contains($condition, 'in_array')) {
//            $returnType = 'stringed-array';
//        }

        // Evaluate the condition using the compile method with 'boolean' return type.
        $result = self::compile($condition, $returnType);
        if (gettype($result) != "boolean") {
            eval("\$result = ($result);");
        }
        // Determine the return value based on the condition result.
        $returnValue = $result ? self::compile($trueValue) : self::compile($falseValue);

        // Replace the original IF expression with the evaluated result.
        return str_replace($matches[0], $returnValue, $value);
    }

    /**
     * Parse and evaluate a Method within the given string and return the result based on the specified return type.
     *
     * This method searches for a Method within the input string, evaluates it, and returns the result
     * based on the specified return type. The Method should be in the format: METHOD(methodName(inputParameter1, inputParameter2, ...)).
     *
     * @param string $value The string containing the METHOD to be parsed and evaluated.
     * @param string $returnType The return type for the result: 'string' or 'boolean'.
     *
     * @return string|boolean   The evaluated result based on the specified return type.
     */
    protected function parseMethod(string $value, string $returnType = 'string'): bool|string
    {
        // Use regular expression to match and extract the METHOD.
        preg_match('/METHOD\(([\w:\\\\]+)\((.*)\)\)/', $value, $matches);

        // If no matches are found, return the original value.
        if (empty($matches)) {
            return $value;
        }

        $method = $matches[1];
        $inputParameters = $matches[2];

        $inputParametersArray = explode('(,)', $inputParameters);
        $compiledInputParameters = array_map([self::class, 'compile'], $inputParametersArray);

        // run the method with input parameters
        $returnValue = $method(...$compiledInputParameters);

        // Replace the original METHOD expression with the evaluated result.
        return str_replace($matches[0], $returnValue, $value);
    }

    /**
     * Parse and calculate the sum of values within the given string and return the result as a string.
     *
     * This method searches for a SUM expression within the input string, calculates the sum of the values,
     * and returns the result as a string. The SUM expression should be in the format: SUM(value1, value2, ...).
     *
     * @param string $value The string containing the SUM expression to be parsed and calculated.
     *
     * @return string       The calculated sum as a string.
     */
    protected function parseSum(string $value): string
    {
        // Use regular expression to match and extract the values within the SUM expression.
        preg_match('/SUM\(([^)]+)\)/', $value, $matches);

        // If no matches are found, return the original value.
        if (empty($matches)) {
            return $value;
        }

        $parameters = explode(', ', $matches[1]);

        // Parse variables within the parameters and calculate the sum.
        $parameters = array_map(self::parseVariables(...), $parameters);
        $sum = array_sum($parameters);

        // Replace the original SUM expression with the calculated sum.
        return str_replace($matches[0], $sum, $value);
    }

    /**
     * Parse and find the minimum value among the values within the given string and return the result as a string.
     *
     * This method searches for a MIN expression within the input string, finds the minimum value among the values,
     * and returns the result as a string. The MIN expression should be in the format: MIN(value1, value2, ...).
     *
     * @param string $value The string containing the MIN expression to be parsed and evaluated.
     *
     * @return string       The minimum value as a string.
     */
    protected function parseMin(string $value): string
    {
        // Use regular expression to match and extract the values within the MIN expression.
        preg_match('/MIN\(([^)]+)\)/', $value, $matches);

        // If no matches are found, return the original value.
        if (empty($matches)) {
            return $value;
        }

        $parameters = explode(', ', $matches[1]);

        // Parse variables within the parameters and find the minimum value.
        $parameters = array_map(self::parseVariables(...), $parameters);
        $min = min($parameters);

        // Replace the original MIN expression with the minimum value.
        return str_replace($matches[0], $min, $value);
    }

    /**
     * Parse and find the maximum value among the values within the given string and return the result as a string.
     *
     * This method searches for a MAX expression within the input string, finds the maximum value among the values,
     * and returns the result as a string. The MAX expression should be in the format: MAX(value1, value2, ...).
     *
     * @param string $value The string containing the MAX expression to be parsed and evaluated.
     *
     * @return string       The maximum value as a string.
     */
    protected function parseMax(string $value): string
    {
        // Use regular expression to match and extract the values within the MAX expression.
        preg_match('/MAX\(([^)]+)\)/', $value, $matches);

        // If no matches are found, return the original value.
        if (empty($matches)) {
            return $value;
        }

        $parameters = explode(', ', $matches[1]);

        // Parse variables within the parameters and find the maximum value.
        $parameters = array_map(self::parseVariables(...), $parameters);
        $max = max($parameters);

        // Replace the original MAX expression with the maximum value.
        return str_replace($matches[0], $max, $value);
    }

    /**
     * Parse and convert the given string to lowercase and return the result.
     *
     * This method searches for a LOWER expression within the input string, converts the enclosed string to lowercase,
     * and returns the result. The LOWER expression should be in the format: LOWER(value).
     *
     * @param string $value The string containing the LOWER expression to be parsed and evaluated.
     *
     * @return string       The lowercase string.
     */
    protected function parseLower(string $value): string
    {
        // Use regular expression to match and extract the value within the LOWER expression.
        preg_match('/LOWER\(([^)]+)\)/', $value, $matches);

        // If no matches are found, return the original value.
        if (empty($matches)) {
            return $value;
        }

        // Convert the enclosed string to lowercase.
        $lower = strtolower(self::parseVariables($matches[1]));

        // Replace the original LOWER expression with the lowercase result.
        return str_replace($matches[0], $lower, $value);
    }

    /**
     * Parse and convert the given string to uppercase and return the result.
     *
     * This method searches for an UPPER expression within the input string, converts the enclosed string to uppercase,
     * and returns the result. The UPPER expression should be in the format: UPPER(value).
     *
     * @param string $value The string containing the UPPER expression to be parsed and evaluated.
     *
     * @return string       The uppercase string.
     */
    protected function parseUpper(string $value): string
    {
        // Use regular expression to match and extract the value within the UPPER expression.
        preg_match('/UPPER\(([^)]+)\)/', $value, $matches);

        // If no matches are found, return the original value.
        if (empty($matches)) {
            return $value;
        }

        // Convert the enclosed string to uppercase.
        $upper = strtoupper(self::parseVariables($matches[1]));

        // Replace the original UPPER expression with the uppercase result.
        return str_replace($matches[0], $upper, $value);
    }

    /**
     * Parse and check if a value exists within an array within the given string and return the result as a string.
     *
     * This method searches for an IN_ARRAY expression within the input string, checks if the specified value exists
     * within the corresponding array in the data object, and returns the result as a string ('true' or 'false').
     * The IN_ARRAY expression should be in the format: IN_ARRAY({arrayKey}, searchValue).
     *
     * @param string $value The string containing the IN_ARRAY expression to be parsed and evaluated.
     *
     * @return string       The result as 'true' if the value exists in the array, or 'false' if not.
     */
    protected function parseInArray(string $value): string
    {
        // Use regular expression to match and extract the array key and search value within the IN_ARRAY expression.
        preg_match('/IN_ARRAY\(\{([^)]+)}, ([^)]+)\)/', $value, $matches);

        // If no matches are found, return the original value.
        if (empty($matches)) {
            return $value;
        }

        $key = $matches[1];

        // Check if the specified key exists and is an array in the data object.
        if (isset($this->RFProperty->$key) && is_array($this->RFProperty->$key)) {
            $result = in_array($matches[2], $this->RFProperty->$key);
            $result = $result ? 'true' : 'false';
        } else {
            $result = 'false';
        }

        // Replace the original IN_ARRAY expression with the result ('true' or 'false').
        return str_replace($matches[0], $result, $value);
    }


    /**
     * Parse and check if a string contains a specific substring and return the result as a string.
     *
     * This method searches for a CONTAINS expression within the input string, checks if the specified substring exists
     * within the corresponding string in the data object, and returns the result as a string ('true' or 'false').
     * The CONTAINS expression should be in the format: CONTAINS({stringKey}, searchSubstring).
     *
     * @param string $value The string containing the CONTAINS expression to be parsed and evaluated.
     * @param string $returnType
     *
     * @return string       The result as 'true' if the substring is found, or 'false' if not.
     */
    protected function parseContains(string $value, string $returnType): string
    {
        // Use regular expression to match and extract the string key and search substring within the CONTAINS expression.
        preg_match('/CONTAINS\(\{([^)]+)}, ([^)]+)\)/', $value, $matches);

        // If no matches are found, return the original value.
        if (empty($matches)) {
            return $value;
        }

        $key = $matches[1];
        $key = str_replace('{', '', $key);
        $key = str_replace('}', '', $key);

        // Check if the specified key exists and is a string in the data object.
        $result = str_contains($this->RFProperty->$key, $matches[2]);
        $result = $result ? 'true' : 'false';

        // Replace the original CONTAINS expression with the result ('true' or 'false').
        return str_replace($matches[0], $result, $value);
    }
}

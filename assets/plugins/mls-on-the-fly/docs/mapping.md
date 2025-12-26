## Detailed Explanation of Parsing Methods with Examples

### `compile` Method

This method orchestrates the parsing of different expressions and conditions within your mapping values, applying various parsing functions to interpret and transform the data as specified.

### `parseVariables` Method

Replaces placeholders within the string with actual data values. This method is essential for direct data replacement and can also be used to concatenate multiple fields into a single string.

**Example of Concatenation:**
```json
{
    "full_address": {
        "mapping": "{City}, {State}, {Country}"
    }
}
```
This combines the city, state, and country fields into a single string for the `full_address`.

### `parseNestedArray` Method

Extracts values from nested arrays or objects using a path notation, which is particularly useful for accessing data within complex structured data.

**Example:**
```json
{
    "custom_field": {
        "mapping": "{Address.City}"
    }
}
```
Fetches the `City` value from a nested `Address` object.

### `parseIf` Method

Executes conditional logic directly within the mapping to dynamically determine values based on other data fields.

**Example:**
```json
{
    "post_status": {
        "mapping": "IF('{Availability}' == 'Available', 'publish', 'pending')"
    }
}
```
Sets the `post_status` based on the `Availability` field.

### `parseMethod` Method

Allows for the invocation of specific methods, potentially with arguments, to perform more complex or reusable logic.

**Example:**
```json
{
    "complex_calculation_field": {
        "mapping": "METHOD(CustomClass::calculateValue({BaseValue}, 10))"
    }
}
```
Calls a static method to perform a calculation based on the `BaseValue`.

### `parseSum`, `parseMin`, `parseMax`, `parseLower`, `parseUpper`

These functions handle arithmetic operations and string transformations directly within the mappings.

**Example of `parseSum`:**
```json
{
    "total_cost": {
        "mapping": "SUM({Item1}, {Item2}, {Item3})"
    }
}
```
Calculates the sum of three items and maps it to `total_cost`.

**Example of `parseMin` and `parseMax`:**
```json
{
    "temperature_range": {
        "mapping": "MIN({TempJan}, {TempFeb}, {TempMar}), MAX({TempJan}, {TempFeb}, {TempMar})"
    }
}
```
Finds the minimum and maximum temperatures.

**Example of `parseLower` and `parseUpper`:**
```json
{
    "normalized_city": {
        "mapping": "UPPER({City})"
    },
    "search_key": {
        "mapping": "LOWER({Keyword})"
    }
}
```
Converts city names to uppercase for normalization and keywords to lowercase for search functionalities.

### `parseInArray` Method

Verifies if a specified value exists within an array.

**Example:**
```json
{
    "feature_available": {
        "mapping": "IN_ARRAY({Features}, 'Pool')"
    }
}
```
Checks if 'Pool' is listed in the `Features` array.

### `parseContains` Method

Determines if a string contains a specified substring, useful for keyword checks within text fields.

**Example:**
```json
{
    "has_keyword": {
        "mapping": "CONTAINS({Description}, 'luxury')"
    }
}
```
Verifies if the description contains the word 'luxury'.

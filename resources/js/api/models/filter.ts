/**
 * Generated by orval v7.6.0 🍺
 * Do not edit manually.
 * logitry
 * OpenAPI spec version: 0.1
 */

/**
 * A comma-separated list of values used for filtering records.
            <br><br>Exact Match:<br>
            - By default, values are compared exactly.
            <br>Example:<br> `filter[column]=1` searches for records where `column` equals exactly to `1`.
            <br>Example:<br> `filter[column]=1,2` searches for records where `column` equals exactly to `1` or `2`.
            <br><br>Partial Match:<br>
            - Prefix values with `~` to compare them partially.
            <br>Example:<br> `filter[column]=~1` searches for records where `column` contains `1`.
            <br>Example:<br> `filter[column]=~1,2` searches for records where `column` contains `1` or `2`.
            <br><br>Exclusion:<br>
            - Prefix values with `!` to filter them out.
            <br>Example:<br> `filter[column]=!1` filters out records where `column` equals exactly to `1`.
            <br>Example:<br> `filter[column]=!1,2` filters out records where `column` equals exactly to `1` or `2`.
            <br><br>Partial Exclusion:<br>
            - Combine `!` and `~` to exclude partial matches.
            <br>Example:<br> `filter[column]=!~1` filters out records where `column` contains `1`.
            <br>Example:<br> `filter[column]=!~1,2` filters out records where `column` contains `1` or `2`.
            <br><br>Note: `!` must be used before any other flags (`~`).
 */
export type Filter = string;

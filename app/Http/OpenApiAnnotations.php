<?php

namespace App\Http;

/**
 * Class OpenApiAnnotations.
 *
 * This class is solely as a place for common
 * OpenApi annotations.
 */
abstract class OpenApiAnnotations
{
    /**
     * @OA\Info(title="logitry", version="0.1"),
     *   @OA\Server(
     *     url="http://localhost:8000",
     *     description="Local server"
     *   )
     * )
     *
     * @OA\SecurityScheme(
     *   securityScheme="bearerAuth",
     *   type="http",
     *   scheme="bearer"
     * )
     */

    /**
     * @OA\Schema(
     *   schema="PageSize",
     *   description="Max number of results on page.",
     *   type="integer", example=25
     * )
     * @OA\Schema(
     *   schema="PageNumber",
     *   description="Page number to be returned.",
     *   type="integer", example=1
     * )
     * @OA\Schema(
     *   schema="PageOmit",
     *   description="Determines if pagination should be omitted.
            The size parameter would be set to 1 million.",
     *   type="integer"
     * )
     * @OA\Schema(
     *   schema="PaginationLinks",
     *   description="Pagination meta links object.",
     *   required = {"first", "prev", "self", "next", "last"},
     *   @OA\Property(property="first", type="string",
     *     example="http://localhost:8000/api/resource?page[size]=10&page[number]=1"),
     *   @OA\Property(property="prev", type="string", nullable=true, example=null),
     *   @OA\Property(property="self", type="string",
     *     example="http://localhost:8000/api/resource?page[size]=10&page[number]=1"),
     *   @OA\Property(property="next", type="string", nullable=true,
     *     example="http://localhost:8000/api/resource?page[size]=10&page[number]=2"),
     *   @OA\Property(property="last", type="string", nullable=true,
     *     example="http://localhost:8000/api/resource?page[size]=10&page[number]=5"),
     * )
     * @OA\Schema(
     *   schema="PaginationMeta",
     *   description="Pagination meta object.",
     *   required = {"from", "to", "total", "path", "per_page", "current_page", "last_page", "links"},
     *   @OA\Property(property="from", type="integer", nullable=true, example=1),
     *   @OA\Property(property="to", type="integer", nullable=true, example=10),
     *   @OA\Property(property="total", type="integer", example=50),
     *   @OA\Property(property="path", type="integer", nullable=true,
     *     example="http://localhost:8000/api/resource"),
     *   @OA\Property(property="per_page", type="integer", example=10),
     *   @OA\Property(property="current_page", type="integer", example=1),
     *   @OA\Property(property="last_page", type="integer", example=5),
     *   @OA\Property(property="links", ref ="#/components/schemas/PaginationLinks"),
     * )
     */

     /**
     * @OA\Schema(
     *   schema="Filter",
     *   description="A comma-separated list of values used for filtering records.
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
            <br><br>Note: `!` must be used before any other flags (`~`).",
     *   type="string", example=""
     * )
     * @OA\Schema(
     *   schema="Sort",
     *   description="A comma-separated list of column names used for sorting results.
            <br><br>Ascending Order:<br>
            - Specify the column name directly (e.g., `sort=column`).
            <br>Example:<br> `sort=column` sorts by `column` in ascending order.
            <br><br>Multiple Columns:<br>
            - Specify multiple column names, separated by commas.
            <br>Example: `sort=column_one,column_two` sorts by `column_one` in ascending order,
            and then by `column_two` for records with identical `column_one` values.
            <br><br>Descending Order:<br>
            - Prefix the column name with a hyphen (`-`).
            <br>Example: `sort=-column` sorts by `column` in descending order.
            <br><br>Mixed Order:<br>
            - Use a combination of ascending and descending orders.
            <br>Example: `sort=column_one,-column_two` sorts by `column_one` in ascending order
            and then by `column_two` in descending order for records with identical `column_one` values.",
     *   type="string", example=""
     * )
     */
}

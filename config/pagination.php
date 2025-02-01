<?php

return [
    /*
     * The maximum number of results that will be returned
     * when using the paginator.
     */
    'max_results' => 1000,

    /*
     * The default number of results that will be returned
     * when using the paginator.
     */
    'default_size' => 100,

    /*
     * The key of the page[x] query string parameter for page number.
     */
    'number_parameter' => 'number',

    /*
     * The key of the page[x] query string parameter for page size.
     */
    'size_parameter' => 'size',

    /*
     * The key of the page[x] query string parameter, which determines
     * if the pagination should be omitted.
     */
    'omit_parameter' => 'omit',

    /*
     * Determines if logic for omitting pagination should be ignored.
     * If true, then the omit parameter will be ignored.
     */
    'ignore_omit_pagination' => false,

    /*
     * Simple pagination is used to perform a more efficient query.
     */
    'use_simple_pagination' => false,

    /*
     * Here you can override the base url to be used in the link items.
     */
    'base_url' => null,

    /*
     * The name of the query parameter used for pagination
     */
    'pagination_parameter' => 'page',
];

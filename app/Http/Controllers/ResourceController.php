<?php

namespace App\Http\Controllers;

use App\Http\Resources\BaseCollection;
use App\Http\Resources\BaseResource;
use App\Http\Resources\ResourcePaginator;
use App\Http\Responses\ApiResponse;
use App\Models\BaseModel;
use Exception;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;

/**
 * Class ResourceController.
 */
abstract class ResourceController extends BaseController
{
    /**
     * Controller's resource class.
     *
     * @var BaseResource|string
     */
    protected BaseResource|string $resourceClass;

    /**
     * Controller's resource collection class.
     *
     * @var BaseCollection|string
     */
    protected BaseCollection|string $collectionClass;

    /**
     * Get controller's resource class.
     *
     * @return BaseResource|string
     */
    public function getResourceClass(): BaseResource|string
    {
        return $this->resourceClass;
    }

    /**
     * Get controller's resource collection class.
     *
     * @return BaseCollection|string
     */
    public function getCollectionClass(): BaseCollection|string
    {
        return $this->collectionClass;
    }

    /**
     * Return given model wrapped in controller's resource.
     *
     * @param BaseModel|array $model
     * @param array|null $includes
     * @param array|null $appends
     *
     * @return BaseResource
     */
    public function asResource(
        BaseModel|array $model,
        ?array $includes = null,
        ?array $appends = null
    ): BaseResource {
        $class = $this->getResourceClass();
        $resource = new $class($model);

        if (isset($includes)) {
            $resource->setPassedIncludes($includes);
        }
        if (isset($appends)) {
            $resource->setPassedAppends($appends);
        }

        return $resource;
    }

    /**
     * Return given collection wrapped in controller's
     * resource collection.
     *
     * @param Collection $models
     * @param array|null $includes
     * @param array|null $appends
     *
     * @return BaseCollection
     */
    public function asCollection(
        Collection $models,
        ?array $includes = null,
        ?array $appends = null
    ): BaseCollection {
        $class = $this->getCollectionClass();
        $collection = new $class($models);

        if (isset($includes)) {
            $collection->setPassedIncludes($includes);
        }
        if (isset($appends)) {
            $collection->setPassedAppends($appends);
        }

        return $collection;
    }

    /**
     * Return given model wrapped in controller's resource api response.
     *
     * @param BaseModel|array $model
     * @param array|null $includes
     * @param array|null $appends
     *
     * @return ApiResponse
     */
    public function asResourceResponse(
        BaseModel|array $model,
        ?array $includes = null,
        ?array $appends = null
    ): ApiResponse {
        $resource = $this->asResource($model, $includes, $appends);
        $schema = $resource->getSimpleSchema();

        return ApiResponse::ok(
            [
                'schema' => $schema?->toArray(),
                'data' => $resource,
            ]
        );
    }

    /**
     * Return given collection wrapped in controller's
     * resource collection api response.
     *
     * @param Collection $models
     * @param array|null $includes
     * @param array|null $appends
     *
     * @return ApiResponse
     */
    public function asCollectionResponse(
        Collection $models,
        ?array $includes = null,
        ?array $appends = null
    ): ApiResponse {
        return ApiResponse::ok(
            [
                'data' => $this->asCollection($models, $includes, $appends),
            ]
        );
    }

    /**
     * Paginate query builder with a resource paginator and wrap
     * it into controller's resource collection api response.
     *
     * @param Builder|EloquentBuilder $builder
     *
     * @return ApiResponse
     * @throws Exception
     */
    public function asPaginatedResponse(Builder|EloquentBuilder $builder): ApiResponse
    {
        $paginator = new ResourcePaginator(
            $this->paginate($builder),
            $this->collectionClass
        );

        return ApiResponse::ok($paginator->toArray());
    }
}

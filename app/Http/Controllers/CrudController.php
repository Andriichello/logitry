<?php

namespace App\Http\Controllers;

use App\Http\Requests\Crud\DestroyRequest;
use App\Http\Requests\Crud\IndexRequest;
use App\Http\Requests\Crud\ShowRequest;
use App\Http\Requests\Crud\StoreRequest;
use App\Http\Requests\Crud\UpdateRequest;
use App\Http\Requests\CrudRequest;
use App\Http\Responses\ApiResponse;
use App\Models\BaseModel;
use App\Policies\CrudPolicy;
use App\Repositories\CrudRepository;
use BadMethodCallException;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Throwable;

/**
 * Class CrudController.
 */
abstract class CrudController extends ResourceController
{
    /**
     * Available operations with corresponding request classes.
     *
     * @var array<string, string|CrudRequest>
     */
    protected array $operations = [
        'index' => IndexRequest::class,
        'show' => ShowRequest::class,
        'store' => StoreRequest::class,
        'update' => UpdateRequest::class,
        'destroy' => DestroyRequest::class,
    ];

    /**
     * Controller model's crud repository.
     *
     * @var CrudPolicy
     */
    protected CrudPolicy $policy;

    /**
     * Controller model's crud repository.
     *
     * @var CrudRepository
     */
    protected CrudRepository $repository;

    /**
     * CrudController constructor.
     *
     * @param CrudPolicy $policy
     * @param CrudRepository $repository
     */
    public function __construct(CrudPolicy $policy, CrudRepository $repository)
    {
        $this->policy = $policy;
        $this->repository = $repository;
    }

    /**
     * Handle calls to missing methods on the controller.
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     *
     * @throws BadMethodCallException|ValidationException|AuthorizationException
     */
    public function __call($method, $parameters): mixed
    {
        if (in_array($method, $this->getOperations())) {
            $request = Arr::first(
                Arr::wrap($parameters),
                fn($param) => $param instanceof Request,
            );

            return $this->handle($method, $request ?? request());
        }

        return parent::__call($method, $parameters);
    }

    /**
     * Handle controller operation.
     *
     * @param string $operation
     * @param Request $request
     *
     * @return ApiResponse
     * @throws ValidationException
     * @throws AuthorizationException
     * @SuppressWarnings(PHPMD)
     */
    private function handle(string $operation, Request $request): ApiResponse
    {
        /** @var CrudRequest $class */
        $class = $this->getOperationRequest($operation);
        $crudRequest = $class::createFrom($request);

        $this->checkPolicy($crudRequest);
        $crudRequest->validateResolved();

        $method = 'handle' . ucfirst($operation);
        return $this->$method($crudRequest);
    }

    /**
     * Handle `index` operation.
     *
     * @param IndexRequest $request
     *
     * @return ApiResponse
     * @throws Exception
     */
    protected function handleIndex(IndexRequest $request): ApiResponse
    {
        $builder = $this->builder($request);

        return $this->asPaginatedResponse($builder)
            ->setMessage('OK')
            ->setStatus(200);
    }

    /**
     * Handle `show` operation.
     *
     * @param ShowRequest $request
     *
     * @return ApiResponse
     */
    protected function handleShow(ShowRequest $request): ApiResponse
    {
        $isSoftDeletable = $this->repository->isSoftDeletable();

        /** @var BaseModel $model */
        $model = $this->builder($request)
            // @phpstan-ignore-next-line
            ->when($isSoftDeletable, fn ($builder) => $builder->withTrashed())
            ->findOrFail($request->getId());

        return $this->asResourceResponse($model)
            ->setMessage('OK')
            ->setStatus(200);
    }

    /**
     * Handle `store` operation.
     *
     * @param StoreRequest $request
     *
     * @return ApiResponse
     * @throws ValidationException
     */
    protected function handleStore(StoreRequest $request): ApiResponse
    {
        $model = $this->repository->create($request->validated());

        return $this->asResourceResponse($model)
            ->setMessage('Created')
            ->setStatus(201);
    }

    /**
     * Handle `update` operation.
     *
     * @param UpdateRequest $request
     *
     * @return ApiResponse
     * @throws Throwable
     */
    protected function handleUpdate(UpdateRequest $request): ApiResponse
    {
        $isSoftDeletable = $this->repository->isSoftDeletable();

        /** @var BaseModel $model */
        $model = $this->builder($request)
            // @phpstan-ignore-next-line
            ->when($isSoftDeletable, fn ($builder) => $builder->withTrashed())
            ->findOrFail($request->getId());

        $updated = $this->repository->update($model, $request->validated());

        if (!$updated) {
            throw new Exception('Failed to update ' . $model::class, 500);
        }

        return $this->asResourceResponse($model->fresh())
            ->setMessage('Updated')
            ->setStatus(200);
    }

    /**
     * Handle `destroy` operation.
     *
     * @param DestroyRequest $request
     *
     * @return ApiResponse
     * @throws Throwable
     */
    protected function handleDestroy(DestroyRequest $request): ApiResponse
    {
        $isSoftDeletable = $this->repository->isSoftDeletable();

        /** @var BaseModel $model */
        $model = $this->builder($request)
            // @phpstan-ignore-next-line
            ->when($isSoftDeletable, fn ($builder) => $builder->withTrashed())
            ->findOrFail($request->getId());

        $deleted = $request->force()
            ? $this->repository->forceDelete($model)
            : $this->repository->delete($model);

        if (!$deleted) {
            throw new Exception('Failed to delete ' . $model::class, 500);
        }

        return ApiResponse::ok()
            ->setMessage('Deleted');
    }

    /**
     * Check if user is authorized to perform request.
     *
     * @param CrudRequest $request
     *
     * @return void
     * @throws AuthorizationException
     */
    protected function checkPolicy(CrudRequest $request): void
    {
        if (empty($this->policy)) {
            return;
        }

        $result = $this->policy->determine($request);
        if ($result === false) {
            throw new AuthorizationException();
        }
    }

    /**
     * Get available operations.
     *
     * @return string[]
     */
    public function getOperations(): array
    {
        return array_keys($this->operations);
    }

    /**
     * Get request class for the given operation.
     *
     * @param string $operation
     *
     * @return class-string<CrudRequest>|null
     */
    public function getOperationRequest(string $operation): ?string
    {
        return data_get($this->operations, $operation);
    }

    /**
     * Get eloquent query builder instance.
     *
     * @param CrudRequest $request
     *
     * @return EloquentBuilder
     * @SuppressWarnings(PHPMD)
     */
    protected function builder(CrudRequest $request): EloquentBuilder
    {
        return $this->repository->builder($request);
    }
}

<?php

namespace KhantNyar\ApiUtils;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use KhantNyar\ApiUtils\Http\Responses\ApiResponse;

class ApiUtils
{
    protected Builder $query;
    protected array $columns = ['*'];
    protected int $limit = 1000;
    protected int $perPage = 30;


    public function __construct(Builder $query)
    {
        $this->query = $query;
    }

    public static function make(Builder $query): self
    {
        return new self($query);
    }

    public function paginate(int $perPage)
    {
        $this->perPage = $perPage;
        $this->query->paginate($this->perPage);
        return $this;
    }

    public function limit(int $limit)
    {
        $this->limit = $limit;
        $this->query->limit($this->limit);
        return $this;
    }

    public function searchable(array $columns = ['*'],   bool $searchable = true,): self
    {
        if ($searchable && request()->has('search')) {
            $searchTerm = request()->input('search');
            $this->query->where(function ($q) use ($columns, $searchTerm) {
                foreach ($columns as $column) {
                    $q->orWhere($column, 'LIKE', '%' . $searchTerm . '%');
                }
            });
        }
        return $this;
    }

    public function sortable(string $column = 'id', string $direction = 'desc'): self
    {
        $this->query->orderBy($column, $direction);
        return $this;
    }


    public function get(...$columns)
    {
        if (!empty($columns)) {
            $this->columns = $columns;
        }
        return $this->query->get($this->columns);
    }

    public function collection(string $resourceClass): JsonResponse
    {
        $results = $this->query->paginate($this->perPage, $this->columns);
        return $resourceClass::collection($results)->response();
    }

    public function response(...$columns): ApiResponse
    {
        $results = $this->query->paginate($this->perPage, $this->columns);

        return new ApiResponse(200, [
            'data' => $results->items(),
            'meta' => [
                'limit' => $this->limit,
                'per_page' => $this->perPage,
                'total' => $results->total(),
                'current_page' => $results->currentPage(),
                'last_page' => $results->lastPage(),
            ]
        ]);
    }
}

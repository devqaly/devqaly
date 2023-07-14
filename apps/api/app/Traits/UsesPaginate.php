<?php

namespace App\Traits;

trait UsesPaginate
{
    public function perPageQueryName(): string
    {
        return 'perPage';
    }

    public function paginationValidation(int $maxPerPage = 50): array
    {
        return [
            $this->perPageQueryName() => 'integer|max:' . $maxPerPage
        ];
    }

    public function getPerPage(int $default = 50): int
    {
        return (int) request()->get($this->perPageQueryName(), $default);
    }
}

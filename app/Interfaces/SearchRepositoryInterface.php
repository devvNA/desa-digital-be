<?php

namespace App\Interfaces;

interface SearchRepositoryInterface
{
    public function search(string $query): array;
}

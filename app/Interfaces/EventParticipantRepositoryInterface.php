<?php

namespace App\Interfaces;

interface EventParticipantRepositoryInterface
{
    public function getAll(?string $search, ?int $limit, bool $execute);

    public function getAllPaginated(?string $search, ?int $rowPerPage);

    public function create(array $data): array;

    public function confirmPayment(string $orderId, array $orderMeta): \App\Models\EventParticipant;

    public function update(string $id, array $data);

    public function getById(string $id);

    public function delete(string $id);
}

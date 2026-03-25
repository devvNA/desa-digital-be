<?php

namespace App\Repositories;

use App\Interfaces\DevelopmentApplicantRepositoryInterface;
use App\Models\DevelopmentApplicant;
use Illuminate\Support\Facades\DB;

class DevelopmentApplicantRepository implements DevelopmentApplicantRepositoryInterface
{

    public function getAll(?string $search, ?int $limit, bool $execute)
    {
        $query = DevelopmentApplicant::where(function ($query) use ($search) {
            if ($search) {
                $query->search($search);
            }
        });

        $query->orderBy('created_at', 'desc');

        if ($limit) {
            $query->take($limit);
        }

        if ($execute) {
            return $query->get();
        }

        return $query;
    }

    public function getAllPaginated(?string $search, ?int $rowPerPage)
    {
        try {
            $query = $this->getAll($search, $rowPerPage, false);

            return $query->paginate($rowPerPage);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function create(array $data)
    {
        DB::beginTransaction();
        try {
            $development = new DevelopmentApplicant();
            $development->development_id = $data['development_id'];
            $development->user_id = $data['user_id'];
            if (isset($data['status'])) {
                $development->status = $data['status'];
            }

            $development->save();

            DB::commit();

            return $development;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    public function update(string $id, array $data)
    {
        DB::beginTransaction();
        try {
            $development = DevelopmentApplicant::find($id);
            $development->development_id = $data['development_id'];
            $development->user_id = $data['user_id'];
            if (isset($data['status'])) {
                $development->status = $data['status'];
            }

            $development->save();

            DB::commit();

            return $development;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    public function getById(string $id)
    {
        $query = DevelopmentApplicant::where('id', $id)->first();
        return $query;
    }

    public function delete(string $id)
    {
        DB::beginTransaction();
        try {
            $development = DevelopmentApplicant::find($id);
            $development->delete();
            DB::commit();
            return $development;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }
}

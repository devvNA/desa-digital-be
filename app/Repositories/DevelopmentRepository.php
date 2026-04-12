<?php

namespace App\Repositories;

use App\Interfaces\DevelopmentRepositoryInterface;
use App\Models\Development;
use App\Models\FamilyMember;
use Illuminate\Support\Facades\DB;

class DevelopmentRepository implements DevelopmentRepositoryInterface
{
    public function getAll(?string $search,  ?string $status, ?int $limit, bool $execute,)
    {
        $query = Development::withCount('developmentApplicants')
            ->where(function ($query) use ($search) {
                if ($search) {
                    $query->search($search);
                }
            });

        if ($status === 'my-applications') {
            $query->whereHas('developmentApplicants', function ($query) {
                $members = [];
                $headOfFamily = auth()->user()->headOfFamily;
                if ($headOfFamily) {
                    $members = FamilyMember::where('head_of_family_id', $headOfFamily->id)
                        ->pluck('user_id')
                        ->toArray();
                }
                $members[] = auth()->user()->id;

                $query->whereIn('user_id', $members);
            });
        }

        $query->orderBy('created_at', 'desc');

        if ($limit) {
            $query->limit($limit);
        }

        if ($execute) {
            return $query->get();
        }

        return $query;
    }

    public function getAllPaginated(?string $search, ?string $status, ?int $rowPerPage,)
    {
        try {
            $query = $this->getAll($search, $status, null, false);

            return $query->paginate($rowPerPage);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function create(array $data)
    {
        DB::beginTransaction();
        try {
            $development = new Development;
            $development->thumbnail = $data['thumbnail']->store('assets/developments', 'public');
            $development->name = $data['name'];
            $development->description = $data['description'];
            $development->person_in_charge = $data['person_in_charge'];
            $development->start_date = $data['start_date'];
            $development->end_date = $data['end_date'];
            $development->amount = $data['amount'];
            $development->status = $data['status'];

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
            $development = Development::find($id);
            if (isset($data['thumbnail'])) {
                $development->thumbnail = $data['thumbnail']->store('assets/developments', 'public');
            }
            $development->name = $data['name'];
            $development->description = $data['description'];
            $development->person_in_charge = $data['person_in_charge'];
            $development->start_date = $data['start_date'];
            $development->end_date = $data['end_date'];
            $development->amount = $data['amount'];
            $development->status = $data['status'];

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
        return Development::where('id', $id)
            ->withCount('developmentApplicants')
            ->with('developmentApplicants.user')
            ->first();
    }

    public function delete(string $id)
    {
        DB::beginTransaction();
        try {
            $development = Development::find($id);
            $development->delete();
            DB::commit();

            return $development;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }
}

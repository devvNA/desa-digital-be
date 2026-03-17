<?php

namespace App\Repositories;

use App\Interfaces\FamilyMemberRepositoryInterface;
use App\Models\FamilyMember;
use Illuminate\Support\Facades\DB;

class FamilyMemberRepository implements FamilyMemberRepositoryInterface
{
    public function getAll(?string $search, ?int $limit, bool $execute)
    {
        $query = FamilyMember::where(function ($query) use ($search) {

            if ($search) {
                $query->search($search);
            }
        });

        $query->orderBy('created_at', 'desc');

        if ($limit) {
            $query->limit($limit);
        }

        if ($execute) {
            return $query->get();
        }

        return $query;
    }

    public function getAllPaginated(?string $search, ?int $rowPerPage)
    {
        $query = $this->getAll($search, $rowPerPage, false);

        return $query->paginate($rowPerPage);
    }

    public function getById(string $id)
    {
        $query = FamilyMember::where('id', $id)->first();
        return $query;
    }


    // public function create(array $data)
    // {
    //     DB::beginTransaction();
    //     try {
    //         $familyMember = new FamilyMember;
    //         $familyMember->head_of_family_id = $data['head_of_family_id'];
    //         $familyMember->name = $data['name'];
    //         $familyMember->identity_number = $data['identity_number'];
    //         $familyMember->gender = $data['gender'];
    //         $familyMember->date_of_birth = $data['date_of_birth'];
    //         $familyMember->phone_number = $data['phone_number'];
    //         $familyMember->occupation = $data['occupation'];
    //         $familyMember->marital_status = $data['marital_status'];
    //         $familyMember->save();

    //         DB::commit();
    //         return $familyMember;
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         throw new \Exception($e->getMessage());
    //     }
    // }

    // public function update(string $id, array $data)
    // {
    //     DB::beginTransaction();
    //     try {
    //         $familyMember = FamilyMember::find($id);
    //         $familyMember->head_of_family_id = $data['head_of_family_id'];
    //         $familyMember->name = $data['name'];
    //         $familyMember->identity_number = $data['identity_number'];
    //         $familyMember->gender = $data['gender'];
    //         $familyMember->date_of_birth = $data['date_of_birth'];
    //         $familyMember->phone_number = $data['phone_number'];
    //         $familyMember->occupation = $data['occupation'];
    //         $familyMember->marital_status = $data['marital_status'];
    //         $familyMember->save();

    //         DB::commit();
    //         return $familyMember;
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         throw new \Exception($e->getMessage());
    //     }
    // }

    // public function delete(string $id)
    // {
    //     DB::beginTransaction();
    //     try {
    //         $familyMember = FamilyMember::find($id);
    //         $familyMember->delete();

    //         DB::commit();

    //         return $familyMember;
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         throw new \Exception($e->getMessage());
    //     }
    // }
}

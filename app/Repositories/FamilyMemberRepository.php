<?php

namespace App\Repositories;

use App\Interfaces\FamilyMemberRepositoryInterface;
use App\Models\FamilyMember;
use Exception;
use Illuminate\Support\Facades\DB;

class FamilyMemberRepository implements FamilyMemberRepositoryInterface
{
    public function getAll(?string $search, ?int $limit, bool $execute)
    {
        $query = FamilyMember::with(['headOfFamily.user', 'headOfFamily.familyMembers.user', 'user'])->where(function ($query) use ($search) {

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
        $query = FamilyMember::with(['headOfFamily.user', 'headOfFamily.familyMembers.user', 'user'])->where('id', $id);
        return $query->first();
    }


    public function create(array $data)
    {
        DB::beginTransaction();
        try {
            $userRepository = new UserRepository;

            $user = $userRepository->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
            ]);

            $familyMember = new FamilyMember;
            $familyMember->user_id = $user->id;
            $familyMember->head_of_family_id = $data['head_of_family_id'];
            $familyMember->profile_picture = $data['profile_picture']->store('assets/family-members', 'public');
            $familyMember->identity_number = $data['identity_number'];
            $familyMember->gender = $data['gender'];
            $familyMember->date_of_birth = $data['date_of_birth'];
            $familyMember->phone_number = $data['phone_number'];
            $familyMember->occupation = $data['occupation'];
            $familyMember->marital_status = $data['marital_status'];
            $familyMember->relation = $data['relation'];
            $familyMember->save();

            DB::commit();
            return FamilyMember::with(['headOfFamily.user', 'headOfFamily.familyMembers.user', 'user'])->findOrFail($familyMember->id);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public function update(string $id, array $data)
    {
        DB::beginTransaction();
        try {
            $familyMember = FamilyMember::find($id);

            if (isset($data['profile_picture'])) {
                $familyMember->profile_picture = $data['profile_picture']->store('assets/family-members', 'public');
            }
            $familyMember->identity_number = $data['identity_number'];
            $familyMember->gender = $data['gender'];
            $familyMember->date_of_birth = $data['date_of_birth'];
            $familyMember->phone_number = $data['phone_number'];
            $familyMember->occupation = $data['occupation'];
            $familyMember->marital_status = $data['marital_status'];
            $familyMember->relation = $data['relation'];
            $familyMember->save();

            $userRepository = new UserRepository();

            $userRepository->update($familyMember->user_id, [
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => isset($data['password']) ? bcrypt($data['password']) : $familyMember->user->password,
            ]);

            DB::commit();
            return $familyMember;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    public function delete(string $id)
    {
        DB::beginTransaction();
        try {
            $familyMember = FamilyMember::find($id);
            $familyMember->delete();

            DB::commit();

            return $familyMember;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }
}

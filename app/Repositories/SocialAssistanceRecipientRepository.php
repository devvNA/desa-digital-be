<?php

namespace App\Repositories;

use App\Interfaces\SocialAssistanceRecipientRepositoryInterface;
use App\Models\SocialAssistanceRecipient;
use Illuminate\Support\Facades\DB;

class SocialAssistanceRecipientRepository implements SocialAssistanceRecipientRepositoryInterface
{
    /**
     * Relations required by SocialAssistanceRecipientResource → SocialAssistanceResource / HeadofFamilyResource.
     */
    private function resourceRelations(): array
    {
        return [
            'socialAssistance' => fn($q) => $q->withCount('socialAssistanceRecipient'),
            'headOfFamily.user',
        ];
    }

    public function getAll(?string $search, ?int $limit, bool $execute)
    {
        $query = SocialAssistanceRecipient::with($this->resourceRelations())
            ->when($search, fn($q, $s) => $q->search($s));

        $query->orderBy('created_at', 'desc');

        if (auth()->user()->hasRole('head-of-family')) {
            $headOfFamilyId = auth()->user()?->headOfFamily?->id;

            if (! $headOfFamilyId) {
                return $execute ? collect() : $query->whereRaw('1 = 0');
            }

            $query->where('head_of_family_id', $headOfFamilyId);
        }

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
        try {
            $query = $this->getAll($search, null, false);

            return $query->paginate($rowPerPage);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function getById(string $id)
    {
        return SocialAssistanceRecipient::with($this->resourceRelations())
            ->where('id', $id)
            ->first();
    }

    public function create(array $data)
    {
        DB::beginTransaction();
        try {
            $socialAssistanceRecipient = new SocialAssistanceRecipient;
            $socialAssistanceRecipient->social_assistance_id = $data['social_assistance_id'];
            $socialAssistanceRecipient->head_of_family_id = $data['head_of_family_id'];
            $socialAssistanceRecipient->bank = $data['bank'];
            $socialAssistanceRecipient->amount = $data['amount'];
            $socialAssistanceRecipient->reason = $data['reason'];
            $socialAssistanceRecipient->account_number = $data['account_number'];

            if (isset($data['proof'])) {
                $socialAssistanceRecipient->proof = $data['proof']->store('assets/social-assistance-recipients', 'public');
            }

            $socialAssistanceRecipient->status = $data['status'] ?? 'pending';

            $socialAssistanceRecipient->save();

            DB::commit();

            return $socialAssistanceRecipient;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    public function update(string $id, array $data)
    {
        DB::beginTransaction();
        try {
            $socialAssistanceRecipient = SocialAssistanceRecipient::find($id);
            $socialAssistanceRecipient->social_assistance_id = $data['social_assistance_id'];
            $socialAssistanceRecipient->head_of_family_id = $data['head_of_family_id'];
            $socialAssistanceRecipient->bank = $data['bank'];
            $socialAssistanceRecipient->amount = $data['amount'];
            $socialAssistanceRecipient->reason = $data['reason'];
            $socialAssistanceRecipient->account_number = $data['account_number'];

            if (isset($data['proof'])) {
                $socialAssistanceRecipient->proof = $data['proof']->store('assets/social-assistance-recipients', 'public');
            }

            if (isset($data['status'])) {
                $socialAssistanceRecipient->status = $data['status'];
            }

            $socialAssistanceRecipient->save();

            DB::commit();

            return $socialAssistanceRecipient;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    public function delete(string $id)
    {
        DB::beginTransaction();
        try {
            $socialAssistanceRecipient = SocialAssistanceRecipient::find($id);
            $socialAssistanceRecipient->delete();

            DB::commit();

            return $socialAssistanceRecipient;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }
}

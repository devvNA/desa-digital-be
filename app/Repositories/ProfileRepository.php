<?php

namespace App\Repositories;

use App\Interfaces\ProfileRepositoryInterface;
use App\Models\Profile;
use Illuminate\Support\Facades\DB;

class ProfileRepository implements ProfileRepositoryInterface
{
    public function get()
    {
        return Profile::with('profileImages')->first();
    }

    public function getById(string $id)
    {
        return Profile::with('profileImages')->find($id);
    }

    public function create(array $data)
    {
        DB::beginTransaction();
        try {
            $profile = new Profile;
            $profile->thumbnail = $data['thumbnail']->store('assets/profile', 'public');
            $profile->name = $data['name'];
            $profile->about = $data['about'];
            $profile->headman = $data['headman'];
            $profile->people = $data['people'];
            $profile->agricultural_area = $data['agricultural_area'];
            $profile->total_area = $data['total_area'];
            $profile->save();

            if (array_key_exists('images', $data)) {
                foreach ($data['images'] as $image) {
                    $profile->profileImages()->create([
                        'image' => $image->store('assets/profile', 'public'),
                    ]);
                }
            }

            DB::commit();

            return $profile;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    public function update(array $data)
    {
        DB::beginTransaction();
        try {
            $profile = Profile::with('profileImages')->first();

            if (! $profile) {
                throw new \Exception('Profile Tidak Ditemukan');
            }

            if (isset($data['thumbnail'])) {
                $profile->thumbnail = $data['thumbnail']->store('assets/profile', 'public');
            }

            $profile->name = $data['name'];
            $profile->about = $data['about'];
            $profile->headman = $data['headman'];
            $profile->people = $data['people'];
            $profile->agricultural_area = $data['agricultural_area'];
            $profile->total_area = $data['total_area'];
            $profile->save();

            if (array_key_exists('images', $data)) {
                foreach ($data['images'] as $image) {
                    $profile->profileImages()->create([
                        'image' => $image->store('assets/profile', 'public'),
                    ]);
                }
            }

            DB::commit();

            return $profile->load('profileImages');
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }
}

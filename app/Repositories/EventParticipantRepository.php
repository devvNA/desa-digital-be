<?php

namespace App\Repositories;

use App\Interfaces\EventParticipantRepositoryInterface;
use App\Models\Event;
use App\Models\EventParticipant;
use Illuminate\Support\Facades\DB;
use Midtrans\Config;
use Midtrans\Snap;

class EventParticipantRepository implements EventParticipantRepositoryInterface
{
    public function getAll(?string $search, ?int $limit, bool $execute)
    {
        $query = EventParticipant::where(function ($query) use ($search) {
            if ($search) {
                $query->search($search);
            }
        });

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
            $event = Event::where('id', $data['event_id'])->first();

            $eventParticipant = new EventParticipant;
            $eventParticipant->event_id = $data['event_id'];
            $eventParticipant->head_of_family_id = $data['head_of_family_id'];
            $eventParticipant->quantity = $data['quantity'];
            $eventParticipant->total_price = $event->price * $data['quantity'];
            $eventParticipant->payment_status = $eventParticipant->payment_status ?? 'pending';
            $eventParticipant->save();

            DB::commit();

            Config::$serverKey = config('midtrans.serverKey');
            Config::$isProduction = config('midtrans.isProduction');
            Config::$isSanitized = config('midtrans.isSanitized');
            Config::$is3ds = config('midtrans.is3ds');

            $params = [
                'transaction_details' => [
                    'order_id' => $eventParticipant->id,
                    'gross_amount' => (int) $eventParticipant->total_price,
                ],
                'item_details' => [
                    [
                        'id' => $event->id,
                        'price' => (int) $event->price,
                        'quantity' => (int) $eventParticipant->quantity,
                        'name' => $event->name,
                        'brand' => 'Desa Digital',
                        'category' => 'Event Ticket',
                        'merchant_name' => 'Desa Digital'
                    ],
                ],
                'customer_details' => [
                    'first_name' => auth()->user()->name,
                    'last_name' => 'XXX',
                    'email' => auth()->user()->email,
                    'phone' => '082142185804',
                    'billing_address' => [
                        'first_name' => 'Budi',
                        'last_name' => 'Susanto',
                        'email' => 'budi@example.com',
                        'phone' => '08123456789',
                        'address' => 'Sudirman No.12',
                        'city' => 'Jakarta',
                        'postal_code' => '12190',
                        'country_code' => 'IDN',
                    ],
                    'shipping_address' => [
                        'first_name' => 'Budi',
                        'last_name' => 'Susanto',
                        'email' => 'budi@example.com',
                        'phone' => '0812345678910',
                        'address' => 'Sudirman',
                        'city' => 'Jakarta',
                        'postal_code' => '12190',
                        'country_code' => 'IDN',
                    ],
                ],
                // 'customer_details' => [
                //     'first_name' => auth()->user()->name,
                //     'last_name' => 'XXX',
                //     'email' => 'devit@app.com',
                //     'phone' => '082142185804',
                //     'billing_address' => 'Jl. Raya No. 123',
                //     'shipping_address' => 'Jl. Raya No. 123',
                // ],
                "custom_expiry" => [
                    "expiry_duration" => 1,
                    "unit" => "day"
                ],

            ];

            $snapToken = Snap::getSnapToken($params);
            $eventParticipant->snap_token = $snapToken;

            return $eventParticipant;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    public function update(string $id, array $data)
    {
        DB::beginTransaction();
        try {
            $event = Event::where('id', $data['event_id'])->first();

            $eventParticipant = EventParticipant::find($id);
            $eventParticipant->event_id = $data['event_id'];
            $eventParticipant->head_of_family_id = $data['head_of_family_id'];

            if (isset($data['quantity'])) {
                $eventParticipant->quantity = $data['quantity'];
            }

            if (isset($data['payment_status'])) {
                $eventParticipant->payment_status = $data['payment_status'];
            }

            $eventParticipant->total_price = $event->price * $eventParticipant->quantity;
            $eventParticipant->save();

            DB::commit();

            return $eventParticipant;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    public function getById(string $id)
    {
        $query = EventParticipant::where('id', $id)->first();

        return $query;
    }

    public function delete(string $id)
    {
        DB::beginTransaction();
        try {
            $eventParticipant = EventParticipant::find($id);
            $eventParticipant->delete();

            DB::commit();

            return $eventParticipant;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }
}

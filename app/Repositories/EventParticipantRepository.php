<?php

namespace App\Repositories;

use App\Interfaces\EventParticipantRepositoryInterface;
use App\Models\Event;
use App\Models\EventParticipant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Midtrans\Config;
use Midtrans\Snap;

class EventParticipantRepository implements EventParticipantRepositoryInterface
{
    /**
     * Relations needed by EventParticipantResource.
     */
    private function resourceRelations(): array
    {
        return ['event', 'headOfFamily.user'];
    }

    public function getAll(?string $search, ?int $limit, bool $execute)
    {
        $query = EventParticipant::with($this->resourceRelations())
            ->when($search, fn ($q, $s) => $q->search($s))
            ->orderBy('created_at', 'desc');

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

        return $execute ? $query->get() : $query;
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
        return EventParticipant::with($this->resourceRelations())
            ->where('id', $id)
            ->first();
    }

    /**
     * Generate a Midtrans snap token WITHOUT persisting to DB.
     * The actual row is created only when Midtrans confirms payment via callback.
     *
     * Returns an associative array with the order metadata + snap_token
     * so the frontend can proceed to the payment page.
     */
    public function create(array $data): array
    {
        $event = Event::findOrFail($data['event_id']);

        $orderId = (string) Str::uuid();
        $quantity = (int) $data['quantity'];
        $totalPrice = (int) ($event->price * $quantity);

        // --- Midtrans snap token ---
        Config::$serverKey = config('midtrans.serverKey');
        Config::$isProduction = config('midtrans.isProduction');
        Config::$isSanitized = config('midtrans.isSanitized');
        Config::$is3ds = config('midtrans.is3ds');

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $totalPrice,
            ],
            'item_details' => [
                [
                    'id' => $event->id,
                    'price' => (int) $event->price,
                    'quantity' => $quantity,
                    'name' => $event->name,
                    'brand' => 'Desa Digital',
                    'category' => 'Event Ticket',
                    'merchant_name' => 'Desa Digital',
                ],
            ],
            'customer_details' => [
                'first_name' => auth()->user()->name,
                'email' => auth()->user()->email,
            ],
            'custom_expiry' => [
                'expiry_duration' => 1,
                'unit' => 'day',
            ],
            // custom_field values are echoed back in Midtrans webhook notifications.
            'custom_field1' => $data['head_of_family_id'],
            'custom_field2' => $event->id,
            'custom_field3' => (string) $quantity,
        ];

        $snapToken = Snap::getSnapToken($params);

        return [
            'order_id' => $orderId,
            'event_id' => $event->id,
            'head_of_family_id' => $data['head_of_family_id'],
            'quantity' => $quantity,
            'total_price' => $totalPrice,
            'payment_status' => 'pending',
            'snap_token' => $snapToken,
        ];
    }

    /**
     * Called by the Midtrans callback when payment is confirmed (settlement/capture).
     * Only at this point do we persist the event participant row.
     */
    public function confirmPayment(string $orderId, array $orderMeta): EventParticipant
    {
        return DB::transaction(function () use ($orderId, $orderMeta) {
            $participant = new EventParticipant;
            $participant->id = $orderId;
            $participant->event_id = $orderMeta['event_id'];
            $participant->head_of_family_id = $orderMeta['head_of_family_id'];
            $participant->quantity = $orderMeta['quantity'];
            $participant->total_price = $orderMeta['total_price'];
            $participant->payment_status = 'paid';
            $participant->save();

            return $participant;
        });
    }

    public function update(string $id, array $data)
    {
        DB::beginTransaction();
        try {
            $eventParticipant = EventParticipant::findOrFail($id);

            if (isset($data['event_id'])) {
                $event = Event::findOrFail($data['event_id']);
                $eventParticipant->event_id = $data['event_id'];
            } else {
                $event = $eventParticipant->event;
            }

            if (isset($data['head_of_family_id'])) {
                $eventParticipant->head_of_family_id = $data['head_of_family_id'];
            }

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

    public function delete(string $id)
    {
        DB::beginTransaction();
        try {
            $eventParticipant = EventParticipant::findOrFail($id);
            $eventParticipant->delete();

            DB::commit();

            return $eventParticipant;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }
}

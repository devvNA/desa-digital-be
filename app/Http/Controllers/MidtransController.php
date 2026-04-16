<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventParticipant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MidtransController extends Controller
{
    public function callback(Request $request)
    {
        Log::info('Midtrans Webhook:', $request->all());

        $serverKey = config('midtrans.serverKey');
        $hashedKey = hash('sha512', $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        if ($hashedKey !== $request->signature_key) {
            return response()->json(['message' => 'Invalid signature key'], 403);
        }

        $transactionStatus = $request->transaction_status;
        $orderId = $request->order_id;

        // Idempotency: check if the participant row already exists.
        $existing = EventParticipant::where('id', $orderId)->first();

        $isPaid = in_array($transactionStatus, ['settlement', 'capture'])
            && ($transactionStatus !== 'capture' || $request->fraud_status !== 'challenge');

        // --- Payment confirmed → insert row if it doesn't exist yet ---
        if ($isPaid) {
            if (! $existing) {
                $this->persistParticipant($orderId, $request);
            } else {
                $existing->update(['payment_status' => 'paid']);
            }

            return response()->json(['message' => 'Payment confirmed, participant registered.'], 200);
        }

        // --- Not paid: update existing row if present ---
        if ($existing) {
            $status = $transactionStatus === 'pending' ? 'pending' : 'canceled';
            $existing->update(['payment_status' => $status]);
        }

        return response()->json(['message' => 'Payment status processed.'], 200);
    }

    /**
     * Insert the event_participants row using custom_field data from the Midtrans webhook.
     *
     * custom_field1 = head_of_family_id
     * custom_field2 = event_id
     * custom_field3 = quantity
     */
    private function persistParticipant(string $orderId, Request $request): void
    {
        $headOfFamilyId = $request->input('custom_field1');
        $eventId = $request->input('custom_field2');
        $quantity = (int) ($request->input('custom_field3', 1));

        if (! $headOfFamilyId || ! $eventId) {
            Log::warning('Midtrans callback missing custom_field data, skipping participant creation.', [
                'order_id' => $orderId,
                'custom_field1' => $headOfFamilyId,
                'custom_field2' => $eventId,
            ]);

            return;
        }

        DB::transaction(function () use ($orderId, $eventId, $headOfFamilyId, $quantity, $request) {
            $event = Event::find($eventId);
            $totalPrice = $event
                ? (int) ($event->price * $quantity)
                : (int) $request->gross_amount;

            $participant = new EventParticipant;
            $participant->id = $orderId;
            $participant->event_id = $eventId;
            $participant->head_of_family_id = $headOfFamilyId;
            $participant->quantity = $quantity;
            $participant->total_price = $totalPrice;
            $participant->payment_status = 'paid';
            $participant->save();
        });
    }
}

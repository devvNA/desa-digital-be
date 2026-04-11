<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\EventParticipantStoreRequest;
use App\Http\Requests\EventParticipantUpdateRequest;
use App\Http\Resources\EventParticipantResource;
use App\Http\Resources\PaginateResourse;
use App\Interfaces\EventParticipantRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Symfony\Component\HttpFoundation\JsonResponse;

class EventParticipantController extends Controller implements HasMiddleware
{
    protected EventParticipantRepositoryInterface $eventParticipantRepository;

    public function __construct(EventParticipantRepositoryInterface $eventParticipantRepository)
    {
        $this->eventParticipantRepository = $eventParticipantRepository;
    }

    public static function middleware()
    {
        return [
            new Middleware(PermissionMiddleware::using('event-participant-list|event-participant-create|event-participant-edit|event-participant-delete'), ['index', 'getAllPaginated', 'show']),
            new Middleware(PermissionMiddleware::using('event-participant-create'), ['store']),
            new Middleware(PermissionMiddleware::using('event-participant-edit'), ['update']),
            new Middleware(PermissionMiddleware::using('event-participant-delete'), ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $event = $this->eventParticipantRepository->getAll($request->search, $request->limit, true);

            return ResponseHelper::jsonResponse(true, 'Data event berhasil diambil', EventParticipantResource::collection($event), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    public function getAllPaginated(Request $request)
    {
        $request = $request->validate([
            'search' => 'nullable|string',
            'row_per_page' => 'required|integer',
        ]);

        try {
            $users = $this->eventParticipantRepository->getAllPaginated(
                $request['search'] ?? null,
                $request['row_per_page']
            );

            return ResponseHelper::jsonResponse(true, 'Data event berhasil diambil', PaginateResourse::make($users, EventParticipantResource::class), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EventParticipantStoreRequest $request)
    {
        $request = $request->validated();

        try {
            $eventParticipant = $this->eventParticipantRepository->create($request);

            return ResponseHelper::jsonResponse(true, 'Data Peserta Event Berhasil Ditambahkan', EventParticipantResource::make($eventParticipant), 201);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $eventParticipant = $this->eventParticipantRepository->getById($id);
            if (! $eventParticipant) {
                return ResponseHelper::jsonResponse(false, 'Data Peserta Event Tidak Ditemukan', null, 404);
            }

            return ResponseHelper::jsonResponse(true, 'Data Peserta Event Berhasil Diambil', EventParticipantResource::make($eventParticipant), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EventParticipantUpdateRequest $request, string $id)
    {
        $request = $request->validated();

        try {
            $eventParticipant = $this->eventParticipantRepository->getById($id);
            if (! $eventParticipant) {
                return ResponseHelper::jsonResponse(false, 'Data Peserta Event Tidak Ditemukan', null, 404);
            }

            $eventParticipant = $this->eventParticipantRepository->update($id, $request);

            return ResponseHelper::jsonResponse(true, 'Data Peserta Event Berhasil Diperbarui', EventParticipantResource::make($eventParticipant), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $eventParticipant = $this->eventParticipantRepository->getById($id);

            if (! $eventParticipant) {
                return ResponseHelper::jsonResponse(false, 'Data Peserta Event Tidak Ditemukan', null, 404);
            }

            $eventParticipant = $this->eventParticipantRepository->delete($id);

            return new JsonResponse([
                'success' => true,
                'message' => 'Data Peserta Event Berhasil Dihapus',
                'data' => [
                    'id' => $id,
                ],
            ], 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }
}

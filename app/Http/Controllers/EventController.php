<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\EventStoreRequest;
use App\Http\Requests\EventUpdateRequest;
use App\Http\Resources\EventResource;
use App\Http\Resources\PaginateResourse;
use App\Interfaces\EventRepositoryInterface;
use Illuminate\Http\Request;

class EventController extends Controller
{
    protected EventRepositoryInterface $eventRepository;

    public function __construct(EventRepositoryInterface $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $event = $this->eventRepository->getAll($request->search, $request->limit, true);
            return ResponseHelper::jsonResponse(true, 'Data event berhasil diambil', EventResource::collection($event), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    public function getAllPaginated(Request $request)
    {
        $request = $request->validate([
            'search' => 'nullable|string',
            'row_per_page' => 'required|integer'
        ]);

        try {
            $users = $this->eventRepository->getAllPaginated(
                $request['search'] ?? null,
                $request['row_per_page']
            );
            return ResponseHelper::jsonResponse(true, 'Data event berhasil diambil', PaginateResourse::make($users, EventResource::class), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(EventStoreRequest $request)
    {
        $request = $request->validated();

        try {
            $event = $this->eventRepository->create($request);
            return ResponseHelper::jsonResponse(true, 'Data event berhasil ditambahkan', EventResource::make($event), 201);
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
            $event = $this->eventRepository->getById($id);

            if (!$event) {
                return ResponseHelper::jsonResponse(false, 'Data event tidak ditemukan', null, 404);
            }

            return ResponseHelper::jsonResponse(true, 'Data event berhasil diambil', EventResource::make($event), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EventUpdateRequest $request, string $id)
    {
        $request = $request->validated();

        try {
            $event = $this->eventRepository->getById($id);

            if (!$event) {
                return ResponseHelper::jsonResponse(false, 'Data event tidak ditemukan', null, 404);
            }

            $event = $this->eventRepository->update($id, $request);

            return ResponseHelper::jsonResponse(true, 'Data event berhasil diupdate', new EventResource($event), 200);
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
            $event = $this->eventRepository->getById($id);
            if (!$event) {
                return ResponseHelper::jsonResponse(false, 'Data event tidak ditemukan', null, 404);
            }
            $event = $this->eventRepository->delete($id);
            return ResponseHelper::jsonResponse(true, 'Data event berhasil dihapus', new EventResource($event), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }
}

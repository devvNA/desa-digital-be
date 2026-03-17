<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\HeadofFamilyStoreRequest;
use App\Http\Requests\HeadofFamilyUpdateRequest;
use App\Http\Resources\HeadofFamilyResource;
use App\Http\Resources\PaginateResourse;
use App\Interfaces\HeadOfFamilyRepositoryInterface;
use Illuminate\Http\JsonResponse as HttpJsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class HeadofFamilyController extends Controller
{
    private HeadOfFamilyRepositoryInterface $headOfFamilyRepository;

    public function __construct(HeadOfFamilyRepositoryInterface $headOfFamilyRepository)
    {
        $this->headOfFamilyRepository = $headOfFamilyRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $headOfFamilies = $this->headOfFamilyRepository->getAll($request->search, $request->limit, true);

            return ResponseHelper::jsonResponse(true, 'Data kepala keluarga berhasil diambil', HeadofFamilyResource::collection($headOfFamilies), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false,  $e->getMessage(), null, 500);
        }
    }

    public function getAllPaginated(Request $request)
    {
        $request = $request->validate([
            'search' => 'nullable|string',
            'row_per_page' => 'nullable|integer',
        ]);

        try {
            $headOfFamilies = $this->headOfFamilyRepository->getAllPaginated($request['search'] ?? null, $request['row_per_page']);

            return ResponseHelper::jsonResponse(true, 'Data berhasil diambil', PaginateResourse::make($headOfFamilies, HeadofFamilyResource::class), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false,  $e->getMessage(), null, 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(HeadofFamilyStoreRequest $request)
    {
        $request = $request->validated();

        try {
            $headOfFamily = $this->headOfFamilyRepository->create($request);

            return ResponseHelper::jsonResponse(true, 'Kepala keluarga berhasil ditambahkan', new HeadofFamilyResource($headOfFamily), 201);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false,  $e->getMessage(), null, 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $headOfFamily = $this->headOfFamilyRepository->getById($id);
            if (!$headOfFamily) {
                return ResponseHelper::jsonResponse(false, 'Kepala keluarga tidak ditemukan', null, 404);
            }
            return ResponseHelper::jsonResponse(true, 'Data kepala keluarga berhasil diambil', new HeadofFamilyResource($headOfFamily), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(HeadofFamilyUpdateRequest $request, string $id)
    {
        $request = $request->validated();

        try {
            $headOfFamily = $this->headOfFamilyRepository->getById($id);

            if (!$headOfFamily) {
                return ResponseHelper::jsonResponse(false, 'Kepala keluarga tidak ditemukan', null, 404);
            }

            $headOfFamily = $this->headOfFamilyRepository->update($id, $request);

            return ResponseHelper::jsonResponse(true, 'Kepala keluarga berhasil diupdate', new HeadofFamilyResource($headOfFamily), 200);
            // return new JsonResponse([
            //     'success' => true,
            //     'message' => 'Kepala keluarga berhasil diupdate',
            // ], 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false,  $e->getMessage(), null, 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $headOfFamily = $this->headOfFamilyRepository->getById($id);
            if (!$headOfFamily) {
                return ResponseHelper::jsonResponse(false, 'Kepala keluarga tidak ditemukan', null, 404);
            }
            $headOfFamily = $this->headOfFamilyRepository->delete($id);
            return ResponseHelper::jsonResponse(true, 'Kepala keluarga berhasil dihapus', null, 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }
}

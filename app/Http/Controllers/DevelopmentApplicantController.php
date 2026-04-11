<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\DevelopmentApplicantStoreRequest;
use App\Http\Requests\DevelopmentApplicantUpdateRequest;
use App\Http\Resources\DevelopmentApplicantResource;
use App\Http\Resources\PaginateResourse;
use App\Interfaces\DevelopmentApplicantRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Symfony\Component\HttpFoundation\JsonResponse;

class DevelopmentApplicantController extends Controller implements HasMiddleware
{
    protected DevelopmentApplicantRepositoryInterface $developmentApplicantRepository;

    public function __construct(DevelopmentApplicantRepositoryInterface $developmentApplicantRepository)
    {
        $this->developmentApplicantRepository = $developmentApplicantRepository;
    }

    public static function middleware()
    {
        return [
            new Middleware(PermissionMiddleware::using('development-applicant-list|development-applicant-create|development-applicant-edit|development-applicant-delete'), ['index', 'getAllPaginated', 'show']),
            new Middleware(PermissionMiddleware::using('development-applicant-create'), ['store']),
            new Middleware(PermissionMiddleware::using('development-applicant-edit'), ['update']),
            new Middleware(PermissionMiddleware::using('development-applicant-delete'), ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        try {
            $development = $this->developmentApplicantRepository->getAll($request->search, $request->limit, true);

            return ResponseHelper::jsonResponse(true, 'Data Pendaftar Pembangunan berhasil diambil', DevelopmentApplicantResource::collection($development), 200);
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
            $users = $this->developmentApplicantRepository->getAllPaginated(
                $request['search'] ?? null,
                $request['row_per_page']
            );

            return ResponseHelper::jsonResponse(true, 'Data Pendaftar Pembangunan berhasil diambil', PaginateResourse::make($users, DevelopmentApplicantResource::class), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DevelopmentApplicantStoreRequest $request)
    {
        $request = $request->validated();

        try {
            $development = $this->developmentApplicantRepository->create($request);

            return ResponseHelper::jsonResponse(true, 'Data Pendaftar Pembangunan berhasil ditambahkan', DevelopmentApplicantResource::make($development), 201);
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
            $development = $this->developmentApplicantRepository->getById($id);

            if (! $development) {
                return ResponseHelper::jsonResponse(false, 'Data Pendaftar Pembangunan tidak ditemukan', null, 404);
            }

            return ResponseHelper::jsonResponse(true, 'Data Pendaftar Pembangunan berhasil diambil', DevelopmentApplicantResource::make($development), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DevelopmentApplicantUpdateRequest $request, string $id)
    {
        $request = $request->validated();

        try {
            $development = $this->developmentApplicantRepository->update($id, $request);

            return ResponseHelper::jsonResponse(true, 'Data Pendaftar Pembangunan berhasil diupdate', DevelopmentApplicantResource::make($development), 200);
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
            $development = $this->developmentApplicantRepository->getById($id);
            if (! $development) {
                return ResponseHelper::jsonResponse(false, 'Data Pendaftar Pembangunan tidak ditemukan', null, 404);
            }
            $development = $this->developmentApplicantRepository->delete($id);

            return new JsonResponse([
                'success' => true,
                'message' => 'Data Pendaftar Pembangunan Berhasil Dihapus',
                'data' => [
                    'id' => $id,
                ],
            ], 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }
}

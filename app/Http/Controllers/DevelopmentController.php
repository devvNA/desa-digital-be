<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\DevelopmentStoreRequest;
use App\Http\Requests\DevelopmentUpdateRequest;
use App\Http\Resources\DevelopmentResource;
use App\Http\Resources\PaginateResourse;
use App\Interfaces\DevelopmentRepositoryInterface;
use GuzzleHttp\Middleware;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Symfony\Component\HttpFoundation\JsonResponse;

class DevelopmentController extends Controller implements HasMiddleware
{
    protected DevelopmentRepositoryInterface $developmentRepository;

    public function __construct(DevelopmentRepositoryInterface $developmentRepository)
    {
        $this->developmentRepository = $developmentRepository;
    }

    public static function middleware()
    {
        return [
            new Middleware(PermissionMiddleware::using('development-list|development-create|development-edit|development-delete'), ['index', 'getAllPaginated', 'show']),
            new Middleware(PermissionMiddleware::using('development-create'), ['store']),
            new Middleware(PermissionMiddleware::using('development-edit'), ['update']),
            new Middleware(PermissionMiddleware::using('development-delete'), ['destroy']),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $development = $this->developmentRepository->getAll($request->search, $request->limit, true);
            return ResponseHelper::jsonResponse(true, 'Data Pembangunan berhasil diambil', DevelopmentResource::collection($development), 200);
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
            $users = $this->developmentRepository->getAllPaginated(
                $request['search'] ?? null,
                $request['row_per_page']
            );
            return ResponseHelper::jsonResponse(true, 'Data Pembangunan berhasil diambil', new PaginateResourse($users, DevelopmentResource::class), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DevelopmentStoreRequest $request)
    {
        $request = $request->validated();

        try {
            $development = $this->developmentRepository->create($request);
            return ResponseHelper::jsonResponse(true, 'Data Pembangunan berhasil ditambahkan', new DevelopmentResource($development), 201);
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
            $development = $this->developmentRepository->getById($id);

            if (!$development) {
                return ResponseHelper::jsonResponse(false, 'Data Pembangunan tidak ditemukan', null, 404);
            }

            return ResponseHelper::jsonResponse(true, 'Data Pembangunan berhasil diambil', DevelopmentResource::make($development), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DevelopmentUpdateRequest $request, string $id)
    {
        $request = $request->validated();

        try {
            $development = $this->developmentRepository->update($id, $request);
            return ResponseHelper::jsonResponse(true, 'Data Pembangunan berhasil diupdate', DevelopmentResource::make($development), 200);
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
            $development = $this->developmentRepository->getById($id);
            if (!$development) {
                return ResponseHelper::jsonResponse(false, 'Data Pembangunan tidak ditemukan', null, 404);
            }
            $development = $this->developmentRepository->delete($id);
            return new JsonResponse([
                'success' => true,
                'message' => 'Data Pembangunan Berhasil Dihapus',
                'data' => [
                    'id' => $id,
                ]
            ], 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }
}

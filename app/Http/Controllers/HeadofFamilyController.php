<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\HeadofFamilyStoreRequest;
use App\Http\Requests\HeadofFamilyUpdateRequest;
use App\Http\Resources\HeadofFamilyResource;
use App\Http\Resources\PaginateResourse;
use App\Interfaces\HeadOfFamilyRepositoryInterface;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Spatie\Permission\Middleware\PermissionMiddleware;

class HeadofFamilyController extends Controller implements HasMiddleware
{
    private HeadOfFamilyRepositoryInterface $headOfFamilyRepository;

    public function __construct(HeadOfFamilyRepositoryInterface $headOfFamilyRepository)
    {
        $this->headOfFamilyRepository = $headOfFamilyRepository;
    }

    public static function middleware()
    {
        return [
            new Middleware(PermissionMiddleware::using('head-of-family-list|head-of-family-create|head-of-family-edit|head-of-family-delete'), ['index', 'getAllPaginated']),
            new Middleware(PermissionMiddleware::using('head-of-family-create'), ['store']),
            new Middleware(PermissionMiddleware::using('head-of-family-delete'), ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $headOfFamilies = $this->headOfFamilyRepository->getAll($request->search, $request->limit, true);

            return ResponseHelper::jsonResponse(true, 'Data kepala keluarga berhasil diambil', HeadofFamilyResource::collection($headOfFamilies), 200);
        } catch (QueryException $e) {
            return self::handleQueryException($e);
        } catch (\Exception $e) {
            return ResponseHelper::serverErrorResponse('Gagal mengambil data kepala keluarga.');
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
        } catch (QueryException $e) {
            return self::handleQueryException($e);
        } catch (\Exception $e) {
            return ResponseHelper::serverErrorResponse('Gagal mengambil data kepala keluarga.');
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
        } catch (QueryException $e) {
            return self::handleQueryException($e);
        } catch (\Exception $e) {
            return ResponseHelper::serverErrorResponse('Gagal menambahkan kepala keluarga.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $headOfFamily = $this->headOfFamilyRepository->getById($id);
            if (! $headOfFamily) {
                return ResponseHelper::jsonResponse(false, 'Kepala keluarga tidak ditemukan', null, 404);
            }

            return ResponseHelper::jsonResponse(true, 'Data kepala keluarga berhasil diambil', new HeadofFamilyResource($headOfFamily), 200);
        } catch (\Exception $e) {
            return ResponseHelper::serverErrorResponse('Gagal mengambil data kepala keluarga.');
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

            if (! $headOfFamily) {
                return ResponseHelper::jsonResponse(false, 'Kepala keluarga tidak ditemukan', null, 404);
            }

            $headOfFamily = $this->headOfFamilyRepository->update($id, $request);

            return ResponseHelper::jsonResponse(true, 'Kepala keluarga berhasil diupdate', new HeadofFamilyResource($headOfFamily), 200);
        } catch (QueryException $e) {
            return self::handleQueryException($e);
        } catch (\Exception $e) {
            return ResponseHelper::serverErrorResponse('Gagal mengupdate kepala keluarga.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $headOfFamily = $this->headOfFamilyRepository->getById($id);
            if (! $headOfFamily) {
                return ResponseHelper::jsonResponse(false, 'Kepala keluarga tidak ditemukan', null, 404);
            }
            $headOfFamily = $this->headOfFamilyRepository->delete($id);

            return ResponseHelper::jsonResponse(true, 'Kepala keluarga berhasil dihapus', null, 200);
        } catch (\Exception $e) {
            return ResponseHelper::serverErrorResponse('Gagal menghapus kepala keluarga.');
        }
    }

    /**
     * Map duplicate entry column names to user-friendly field labels.
     */
    private static function handleQueryException(QueryException $e)
    {
        if ($e->errorInfo[1] === 1062) {
            $message = $e->getMessage();
            $field = 'Data';

            if (str_contains($message, 'identity_number')) {
                $field = 'Nomor Identitas';
            } elseif (str_contains($message, 'phone_number')) {
                $field = 'Nomor Telepon';
            } elseif (str_contains($message, 'email')) {
                $field = 'Email';
            }

            return ResponseHelper::jsonResponse(false, "{$field} sudah terdaftar.", null, 409);
        }

        return ResponseHelper::serverErrorResponse('Terjadi kesalahan pada database.');
    }
}

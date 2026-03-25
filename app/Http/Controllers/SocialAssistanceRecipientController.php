<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\SocialAssistanceRecipientStoreRequest;
use App\Http\Requests\SocialAssistanceRecipientUpdateRequest;
use App\Http\Resources\PaginateResourse;
use App\Http\Resources\SocialAssistanceRecipientResource;
use App\Http\Resources\SocialAssistanceResource;
use App\Interfaces\SocialAssistanceRecipientRepositoryInterface;
use GuzzleHttp\Middleware;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Spatie\Permission\Middleware\PermissionMiddleware;

class SocialAssistanceRecipientController extends Controller implements HasMiddleware
{
    private SocialAssistanceRecipientRepositoryInterface $socialAssistanceRecipientRepository;

    public function __construct(SocialAssistanceRecipientRepositoryInterface $socialAssistanceRecipientRepository)
    {
        $this->socialAssistanceRecipientRepository = $socialAssistanceRecipientRepository;
    }

    public static function middleware()
    {
        return [
            new Middleware(PermissionMiddleware::using('social-assistance-recipient-list|social-assistance-recipient-create|social-assistance-recipient-edit|social-assistance-recipient-delete'), ['index', 'getAllPaginated', 'show']),
            new Middleware(PermissionMiddleware::using('social-assistance-recipient-create'), ['store']),
            new Middleware(PermissionMiddleware::using('social-assistance-recipient-edit'), ['update']),
            new Middleware(PermissionMiddleware::using('social-assistance-recipient-delete'), ['destroy']),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $socialAssistances = $this->socialAssistanceRecipientRepository->getAll($request->search, $request->limit, true);
            return ResponseHelper::jsonResponse(true, 'Data Penerima Bantuan Sosial Berhasil Diambil', SocialAssistanceRecipientResource::collection($socialAssistances), 200);
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
            $socialAssistances = $this->socialAssistanceRecipientRepository->getAllPaginated(
                $request['search'] ?? null,
                $request['row_per_page']
            );
            return ResponseHelper::jsonResponse(true, 'Data Penerima Bantuan Sosial Berhasil Diambil', PaginateResourse::make($socialAssistances, SocialAssistanceRecipientResource::class), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(SocialAssistanceRecipientStoreRequest $request)
    {
        $request = $request->validated();

        try {
            $socialAssistance = $this->socialAssistanceRecipientRepository->create($request);
            return ResponseHelper::jsonResponse(true, 'Data Penerima Bantuan Sosial Berhasil Ditambahkan', SocialAssistanceRecipientResource::make($socialAssistance), 201);
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
            $socialAssistance = $this->socialAssistanceRecipientRepository->getById($id);
            if (!$socialAssistance) {
                return ResponseHelper::jsonResponse(false, 'Data Penerima Bantuan Sosial Tidak Ditemukan', null, 404);
            }
            return ResponseHelper::jsonResponse(true, 'Data Penerima Bantuan Sosial Berhasil Diambil', SocialAssistanceRecipientResource::make($socialAssistance), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SocialAssistanceRecipientUpdateRequest $request, string $id)
    {
        $request = $request->validated();

        try {
            $socialAssistance = $this->socialAssistanceRecipientRepository->getById($id);

            if (!$socialAssistance) {
                return ResponseHelper::jsonResponse(false, 'Data Penerima Bantuan Sosial Tidak Ditemukan', null, 404);
            }

            $socialAssistance = $this->socialAssistanceRecipientRepository->update($id, $request);

            return ResponseHelper::jsonResponse(true, 'Data Penerima Bantuan Sosial Berhasil Diupdate', SocialAssistanceRecipientResource::make($socialAssistance), 200);
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
            $socialAssistance = $this->socialAssistanceRecipientRepository->getById($id);

            if (!$socialAssistance) {
                return ResponseHelper::jsonResponse(false, 'Data Penerima Bantuan Sosial Tidak Ditemukan', null, 404);
            }

            $socialAssistance = $this->socialAssistanceRecipientRepository->delete($id);

            return ResponseHelper::jsonResponse(true, 'Data Penerima Bantuan Sosial Berhasil Dihapus', SocialAssistanceRecipientResource::make($socialAssistance), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }
}

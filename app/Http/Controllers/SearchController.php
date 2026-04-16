<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Interfaces\SearchRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    private SearchRepositoryInterface $searchRepository;

    public function __construct(SearchRepositoryInterface $searchRepository)
    {
        $this->searchRepository = $searchRepository;
    }

    public function __invoke(Request $request): JsonResponse
    {
        $query = $request->query('q', '');

        if (strlen(trim($query)) < 1) {
            return ResponseHelper::jsonResponse(false, 'Parameter pencarian (q) wajib diisi.', null, 422);
        }

        try {
            $data = $this->searchRepository->search(trim($query));

            return ResponseHelper::jsonResponse(true, 'Success', $data, 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }
}

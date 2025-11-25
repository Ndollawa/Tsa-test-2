<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\TopDistributorService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TopDistributorController extends Controller
{
    protected TopDistributorService $service;

    public function __construct(TopDistributorService $service)
    {
        $this->service = $service;
    }

    /**
     * GET /top-distributors?limit=200
     */
    public function index(Request $request)
{
    // Filters (if needed later)
    $filters = $request->only(['search']);

    // Pagination size
    $perPage = (int) $request->get('per_page', 10);

    // Limit for "top N distributors"
    $limit = (int) $request->get('limit', 200);

    // Fetch paginated results from service
    $results = $this->service
        ->queryTop($limit)              // returns query()
        ->paginate($perPage)                   // apply pagination here
        ->appends($request->query());          // keep filters in URL


    // Apply ranking AFTER pagination
    $paginator = $this->service->applyRanks($results);
    return Inertia::render('reports/TopDistributors', [
        'filters' => $filters,
        'results' => $paginator,
        'per_page' => $perPage,
        'limit'    => $limit,
    ]);
}

}

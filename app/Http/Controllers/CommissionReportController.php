<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\CommissionService;
use Inertia\Inertia;
use Illuminate\Http\Request;

class CommissionReportController extends Controller
{
    protected CommissionService $service;

    public function __construct(CommissionService $service)
    {
        $this->service = $service;
    }

    /**
     * GET /commission-report
     * Query params: invoice, distributor, date_from, date_to, per_page
     */
    public function index(Request $request)
    {
        $filters = $request->only(['invoice', 'distributor', 'date_from', 'date_to']);
        $perPage = (int) $request->get('per_page', 10);
        $page = (int) $request->get('page', 1);

        $results = $this->service->getReport($filters, $perPage)
            ->appends($request->query()); // Important for pagination URLs


        return Inertia::render('reports/CommissionReport', [
            'filters' => $filters,
            'results' => $results,
            'per_page' => $perPage,
        ]);
    }

    /**
     * GET /commission-report/{orderId}
     */
 public function show(Request $request)
{

    $orderId = $request->input('orderId');
    $detail = $this->service->getOrderDetails($orderId);

    if (!$detail) {
        return response()->json(['message' => 'Not found'], 404);
    }

        return response()->json(['detail' => $detail]);


    // return Inertia::render('reports/CommissionReport', [
    //     'detail' => $detail,
    // ]);
}

}

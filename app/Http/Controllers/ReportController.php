<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateReportJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class ReportController extends Controller
{
    public function create(): JsonResponse
    {
        $reportId = Str::uuid()->toString();

        GenerateReportJob::dispatch($reportId);

        return response()->json([
            'status' => 'queued',
            'reportId' => $reportId
        ]);
    }
}

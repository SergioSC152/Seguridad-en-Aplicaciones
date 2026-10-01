<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private readonly DashboardAnalyticsService $analytics) {}

    public function __invoke(Request $request): View
    {
        return view('dashboard', $this->analytics->forUser($request->user()));
    }
}

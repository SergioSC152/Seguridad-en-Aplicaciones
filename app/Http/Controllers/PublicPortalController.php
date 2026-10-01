<?php

namespace App\Http\Controllers;

use App\Services\PublicPortalService;
use Illuminate\View\View;

class PublicPortalController extends Controller
{
    public function __construct(private readonly PublicPortalService $portal) {}

    public function __invoke(): View
    {
        return view('welcome', [
            'publishedNews' => $this->portal->latestPublications(),
        ]);
    }
}

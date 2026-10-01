<?php

namespace App\Services;

use App\Models\News;
use Illuminate\Database\Eloquent\Collection;

class PublicPortalService
{
    /** @return Collection<int, News> */
    public function latestPublications(): Collection
    {
        return News::query()
            ->with('media')
            ->where('published', true)
            ->latest()
            ->limit(6)
            ->get();
    }
}

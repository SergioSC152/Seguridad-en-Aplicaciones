<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateMailSettingsRequest;
use App\Models\MailSetting;
use App\Services\MailSettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class MailSettingsController extends Controller
{
    public function __construct(private readonly MailSettingsService $settings) {}

    public function edit(): View
    {
        Gate::authorize('viewAny', MailSetting::class);

        $mailSettings = $this->settings->current();

        return view('admin.settings.mail', [
            'mailSettings' => $mailSettings,
            'passwordConfigured' => filled($mailSettings?->password),
        ]);
    }

    public function update(UpdateMailSettingsRequest $request): RedirectResponse
    {
        $mailSettings = $this->settings->current();
        if ($mailSettings) {
            Gate::authorize('update', $mailSettings);
        } else {
            Gate::authorize('viewAny', MailSetting::class);
        }

        $this->settings->save($request->validated());

        return to_route('admin.settings.mail.edit')
            ->with('success', 'La configuración de correo se guardó y ya está activa.');
    }
}

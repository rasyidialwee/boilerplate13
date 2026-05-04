<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Settings\SystemSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SystemSettingsController extends Controller
{
    /**
     * Display the system settings page.
     */
    public function index(Request $request): Response
    {
        $this->authorize('manage_system_settings');

        $settings = app(SystemSettings::class);

        return Inertia::render('settings/system', [
            'settings' => [
                'registration_enabled' => $settings->registration_enabled,
            ],
        ]);
    }

    /**
     * Update the system settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $this->authorize('manage_system_settings');

        $validated = $request->validate([
            'registration_enabled' => ['required', 'boolean'],
        ]);

        $settings = app(SystemSettings::class);
        $settings->registration_enabled = $validated['registration_enabled'];
        $settings->save();

        return redirect()->route('settings.system.index')
            ->with('success', 'System settings updated successfully.');
    }
}

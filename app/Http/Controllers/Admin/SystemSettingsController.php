<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSystemSettingsRequest;
use App\Services\SettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SystemSettingsController extends Controller
{
    public function edit(SettingsService $settings): View
    {
        $values = $settings->forForm();

        return view('admin.settings.edit', compact('values'));
    }

    public function update(UpdateSystemSettingsRequest $request, SettingsService $settings): RedirectResponse
    {
        $settings->setMany([
            'system_name' => $request->string('system_name')->toString(),
            'default_currency' => $request->string('default_currency')->toString(),
            'default_timezone' => $request->string('default_timezone')->toString(),
            'invoice_number_prefix' => strtoupper($request->string('invoice_number_prefix')->toString()),
        ]);

        activity()
            ->causedBy($request->user())
            ->event('settings.updated')
            ->withProperties($request->only([
                'system_name',
                'default_currency',
                'default_timezone',
                'invoice_number_prefix',
            ]))
            ->log('System settings updated');

        return redirect()->route('admin.settings.edit')->with('status', __('Settings saved.'));
    }
}

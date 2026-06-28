<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PixoraConfigController extends Controller
{
    private $supaUrl = 'https://movecexnjyeaipkklijv.supabase.co/rest/v1/pixora_config';
    private $supaKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Im1vdmVjZXhuanllYWlwa2tsaWp2Iiwicm9sZSI6ImFub24iLCJpYXQiOjE3NzY4OTYzMDUsImV4cCI6MjA5MjQ3MjMwNX0.LdytPpEvTDapFfh_OxxOwSf3i8af4XVSe9mdy3QDkhE';

    /**
     * Default config rows to auto-seed if missing from Supabase.
     * This ensures config never disappears — rows are re-created on demand.
     */
    private array $defaultConfigs = [
        ['key' => 'feature_boost',    'value' => null, 'enabled' => true],
        ['key' => 'feature_prepare',  'value' => null, 'enabled' => true],
        ['key' => 'feature_download', 'value' => null, 'enabled' => true],
        ['key' => 'min_version',      'value' => '2.0.0', 'enabled' => true],
        ['key' => 'latest_version',   'value' => '2.4.1', 'enabled' => true],
        ['key' => 'update_message',   'value' => '', 'enabled' => true],
        ['key' => 'announcement',     'value' => '', 'enabled' => true],
        ['key' => 'download_url',     'value' => 'https://github.com/EmFaisal-code/Pixora/releases/latest', 'enabled' => true],
    ];

    private function headers(array $extra = []): array {
        return array_merge([
            'apikey'        => $this->supaKey,
            'Authorization' => 'Bearer ' . $this->supaKey,
            'Content-Type'  => 'application/json',
        ], $extra);
    }

    /**
     * Fetch all config rows. If table is empty (rows missing after a DROP/recreate),
     * auto-seed default rows via upsert so the dashboard always shows data.
     */
    public function index() {
        $res     = Http::withHeaders($this->headers())->get($this->supaUrl . '?select=*&order=key.asc');
        $configs = $res->successful() ? $res->json() : [];

        // Auto-seed: if fewer rows than expected, upsert all defaults (safe — uses ON CONFLICT DO NOTHING logic)
        if (count($configs) < count($this->defaultConfigs)) {
            $this->seedDefaults();
            // Re-fetch after seeding
            $res     = Http::withHeaders($this->headers())->get($this->supaUrl . '?select=*&order=key.asc');
            $configs = $res->successful() ? $res->json() : [];
        }

        return view('admin-panel.pixora-config.index', compact('configs'));
    }

    /**
     * Upsert all default config rows that are missing.
     * Uses Supabase upsert (POST with Prefer: resolution=ignore-duplicates).
     */
    private function seedDefaults(): void {
        Http::withHeaders($this->headers([
            'Prefer' => 'resolution=ignore-duplicates,return=minimal',
        ]))->post($this->supaUrl, $this->defaultConfigs);
    }

    /**
     * Toggle the `enabled` flag for a feature key.
     * Uses UPSERT so toggling never fails even if the row is missing.
     */
    public function toggle($key) {
        // GET current value
        $res     = Http::withHeaders($this->headers())->get($this->supaUrl . '?key=eq.' . $key . '&select=enabled');
        $current = $res->json()[0]['enabled'] ?? true;

        // UPSERT with flipped enabled value
        Http::withHeaders($this->headers(['Prefer' => 'resolution=merge-duplicates,return=minimal']))
            ->post($this->supaUrl, [
                'key'        => $key,
                'enabled'    => !$current,
                'updated_at' => now()->toISOString(),
            ]);

        return back()->with('success', "Fitur {$key} berhasil " . (!$current ? 'diaktifkan' : 'dinonaktifkan') . ".");
    }

    /**
     * Save any text/version value for a config key.
     * Uses UPSERT — row is created if it does not exist yet.
     */
    public function setValue(Request $request, $key) {
        $value = trim($request->input('value', ''));

        Http::withHeaders($this->headers(['Prefer' => 'resolution=merge-duplicates,return=minimal']))
            ->post($this->supaUrl, [
                'key'        => $key,
                'value'      => $value,
                'updated_at' => now()->toISOString(),
            ]);

        return back()->with('success', "Konfigurasi '{$key}' berhasil disimpan.");
    }

    /**
     * Legacy: kept for backward-compat with old route.
     * Delegates to setValue for 'min_version'.
     */
    public function setVersion(Request $request) {
        $version = trim($request->input('version', '1.0.0'));
        return $this->setValue(new Request(['value' => $version]), 'min_version');
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PixoraVersionController extends Controller
{
    private $supaVersions = 'https://movecexnjyeaipkklijv.supabase.co/rest/v1/pixora_versions';
    private $supaConfig   = 'https://movecexnjyeaipkklijv.supabase.co/rest/v1/pixora_config';
    private $supaKey      = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Im1vdmVjZXhuanllYWlwa2tsaWp2Iiwicm9sZSI6ImFub24iLCJpYXQiOjE3NzY4OTYzMDUsImV4cCI6MjA5MjQ3MjMwNX0.LdytPpEvTDapFfh_OxxOwSf3i8af4XVSe9mdy3QDkhE';

    private function headers(array $extra = []): array {
        return array_merge([
            'apikey'        => $this->supaKey,
            'Authorization' => 'Bearer ' . $this->supaKey,
            'Content-Type'  => 'application/json',
        ], $extra);
    }

    public function index() {
        $res      = Http::withHeaders($this->headers())->get($this->supaVersions . '?select=*&order=created_at.desc');
        $versions = $res->successful() ? $res->json() : [];

        // Also fetch current config values to show in the Version Manager form
        $cfgRes = Http::withHeaders($this->headers())->get($this->supaConfig . '?select=key,value&key=in.(latest_version,update_message)');
        $cfgRows = $cfgRes->successful() ? $cfgRes->json() : [];
        $currentConfig = [];
        foreach ($cfgRows as $row) {
            $currentConfig[$row['key']] = $row['value'] ?? '';
        }

        return view('admin-panel.pixora-versions.index', compact('versions', 'currentConfig'));
    }

    /**
     * Add a new version AND update pixora_config (latest_version + update_message).
     * This is the key fix: version creation is now atomic with config update.
     */
    public function store(Request $request) {
        $request->validate([
            'version'        => 'required|regex:/^\d+\.\d+\.\d+$/',
            'update_message' => 'nullable|string|max:2000',
        ]);

        $version       = $request->version;
        $allowed       = $request->has('allowed');
        $updateMessage = trim($request->input('update_message', ''));
        $setAsLatest   = $request->has('set_as_latest');

        // 1. Add to pixora_versions
        Http::withHeaders($this->headers(['Prefer' => 'return=minimal']))
            ->post($this->supaVersions, ['version' => $version, 'allowed' => $allowed]);

        // 2. If "set as latest", update pixora_config.latest_version
        if ($setAsLatest) {
            Http::withHeaders($this->headers(['Prefer' => 'resolution=merge-duplicates,return=minimal']))
                ->post($this->supaConfig, [
                    'key'        => 'latest_version',
                    'value'      => $version,
                    'updated_at' => now()->toISOString(),
                ]);
        }

        // 3. If update message is provided, save to pixora_config.update_message
        if ($updateMessage !== '') {
            Http::withHeaders($this->headers(['Prefer' => 'resolution=merge-duplicates,return=minimal']))
                ->post($this->supaConfig, [
                    'key'        => 'update_message',
                    'value'      => $updateMessage,
                    'updated_at' => now()->toISOString(),
                ]);
        }

        return back()->with('success', "Versi v{$version} berhasil ditambahkan." . ($setAsLatest ? " Latest version diperbarui." : ""));
    }

    public function toggle($version) {
        $res     = Http::withHeaders($this->headers())->get($this->supaVersions . '?version=eq.' . $version . '&select=allowed');
        $current = $res->json()[0]['allowed'] ?? true;

        Http::withHeaders($this->headers())->patch($this->supaVersions . '?version=eq.' . $version, [
            'allowed' => !$current,
        ]);

        $status = !$current ? 'diizinkan' : 'diblokir';
        return back()->with('success', "Versi v{$version} berhasil {$status}.");
    }

    public function destroy($version) {
        Http::withHeaders($this->headers())->delete($this->supaVersions . '?version=eq.' . $version);
        return back()->with('success', "Versi v{$version} dihapus.");
    }
}

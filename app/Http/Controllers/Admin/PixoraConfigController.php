<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PixoraConfigController extends Controller
{
    private $supaUrl = 'https://movecexnjyeaipkklijv.supabase.co/rest/v1/pixora_config';
    private $supaKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Im1vdmVjZXhuanllYWlwa2tsaWp2Iiwicm9sZSI6ImFub24iLCJpYXQiOjE3NzY4OTYzMDUsImV4cCI6MjA5MjQ3MjMwNX0.LdytPpEvTDapFfh_OxxOwSf3i8af4XVSe9mdy3QDkhE';

    private function headers() {
        return ['apikey' => $this->supaKey, 'Authorization' => 'Bearer '.$this->supaKey, 'Content-Type' => 'application/json'];
    }

    public function index() {
        $res = Http::withHeaders($this->headers())->get($this->supaUrl . '?select=*&order=key.asc');
        $configs = $res->successful() ? $res->json() : [];
        return view('admin-panel.pixora-config.index', compact('configs'));
    }

    public function toggle($key) {
        $res = Http::withHeaders($this->headers())->get($this->supaUrl . '?key=eq.' . $key . '&select=enabled');
        $current = $res->json()[0]['enabled'] ?? true;
        Http::withHeaders($this->headers())->patch($this->supaUrl . '?key=eq.' . $key, [
            'enabled' => !$current,
            'updated_at' => now()->toISOString(),
        ]);
        return back()->with('success', "Fitur {$key} berhasil diubah.");
    }

    public function setVersion(Request $request) {
        $version = trim($request->input('version', '1.0.0'));
        Http::withHeaders($this->headers())->patch($this->supaUrl . '?key=eq.min_version', [
            'value' => $version,
            'updated_at' => now()->toISOString(),
        ]);
        return back()->with('success', "Minimum version diset ke v{$version}.");
    }
}
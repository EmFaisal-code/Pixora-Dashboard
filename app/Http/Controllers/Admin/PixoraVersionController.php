<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PixoraVersionController extends Controller
{
    private $supaUrl = 'https://movecexnjyeaipkklijv.supabase.co/rest/v1/pixora_versions';
    private $supaKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Im1vdmVjZXhuanllYWlwa2tsaWp2Iiwicm9sZSI6ImFub24iLCJpYXQiOjE3NzY4OTYzMDUsImV4cCI6MjA5MjQ3MjMwNX0.LdytPpEvTDapFfh_OxxOwSf3i8af4XVSe9mdy3QDkhE';

    private function headers($extra = []) {
        return array_merge([
            'apikey'        => $this->supaKey,
            'Authorization' => 'Bearer ' . $this->supaKey,
            'Content-Type'  => 'application/json',
        ], $extra);
    }

    public function index() {
        $res = Http::withHeaders($this->headers())->get($this->supaUrl . '?select=*&order=created_at.desc');
        $versions = $res->successful() ? $res->json() : [];
        return view('admin-panel.pixora-versions.index', compact('versions'));
    }

    public function store(Request $request) {
        $request->validate(['version' => 'required|regex:/^\d+\.\d+\.\d+$/']);
        $version = $request->version;
        $allowed = $request->has('allowed');

        Http::withHeaders($this->headers(['Prefer' => 'return=minimal']))
            ->post($this->supaUrl, ['version' => $version, 'allowed' => $allowed]);

        return back()->with('success', "Versi v{$version} berhasil ditambahkan.");
    }

    public function toggle($version) {
        $res = Http::withHeaders($this->headers())->get($this->supaUrl . '?version=eq.' . $version . '&select=allowed');
        $current = $res->json()[0]['allowed'] ?? true;

        Http::withHeaders($this->headers())->patch($this->supaUrl . '?version=eq.' . $version, [
            'allowed' => !$current,
        ]);

        $status = !$current ? 'diizinkan' : 'diblokir';
        return back()->with('success', "Versi v{$version} berhasil {$status}.");
    }

    public function destroy($version) {
        Http::withHeaders($this->headers())->delete($this->supaUrl . '?version=eq.' . $version);
        return back()->with('success', "Versi v{$version} dihapus.");
    }
}

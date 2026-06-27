<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PixoraUserController extends Controller
{
    private $supaUrl = 'https://movecexnjyeaipkklijv.supabase.co/rest/v1/pixora_users';
    private $supaKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Im1vdmVjZXhuanllYWlwa2tsaWp2Iiwicm9sZSI6ImFub24iLCJpYXQiOjE3NzY4OTYzMDUsImV4cCI6MjA5MjQ3MjMwNX0.LdytPpEvTDapFfh_OxxOwSf3i8af4XVSe9mdy3QDkhE';

    private function headers()
    {
        return [
            'apikey'        => $this->supaKey,
            'Authorization' => 'Bearer ' . $this->supaKey,
            'Content-Type'  => 'application/json',
        ];
    }

    public function index()
    {
        $response = Http::withHeaders($this->headers())
            ->get($this->supaUrl . '?select=*&order=created_at.desc');
        $users = $response->successful() ? $response->json() : [];

        // Ambil min_version dari pixora_config
        $configRes = Http::withHeaders($this->headers())
            ->get('https://movecexnjyeaipkklijv.supabase.co/rest/v1/pixora_config?key=eq.min_version&select=value');
        $minVersion = $configRes->successful() ? ($configRes->json()[0]['value'] ?? '1.0.0') : '1.0.0';

        return view('admin-panel.pixora-users.index', compact('users', 'minVersion'));
    }

    public function toggleBan($username)
    {
        // Ambil status ban saat ini
        $res = Http::withHeaders($this->headers())
            ->get($this->supaUrl . '?username=eq.' . urlencode($username) . '&select=is_banned');

        $user = $res->json()[0] ?? null;
        if (!$user) return back()->with('error', 'User tidak ditemukan.');

        $newStatus = !($user['is_banned'] ?? false);

        Http::withHeaders($this->headers())
            ->patch($this->supaUrl . '?username=eq.' . urlencode($username), [
                'is_banned'  => $newStatus,
                'updated_at' => now()->toISOString(),
            ]);

        $msg = $newStatus ? "{$username} telah di-banned." : "{$username} telah di-unban.";
        return back()->with('success', $msg);
    }

    public function destroy($username)
    {
        $response = Http::withHeaders(array_merge($this->headers(), [
            'Prefer' => 'return=minimal',
        ]))->delete($this->supaUrl . '?username=eq.' . urlencode($username));

        if ($response->successful() || $response->status() === 204) {
            return back()->with('success', "User {$username} berhasil dihapus.");
        }

        return back()->with('error', "Gagal menghapus user. Error: " . $response->status() . ' - ' . $response->body());
    }
}

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

    public function index(Request $request)
    {
        if ($request->has('sort')) {
            $sort = $request->query('sort');
            $dir = $request->query('dir', 'desc');
            $request->session()->put('pixora_users_sort', $sort);
            $request->session()->put('pixora_users_dir', $dir);
        } else {
            $sort = $request->session()->get('pixora_users_sort', 'date');
            $dir = $request->session()->get('pixora_users_dir', 'desc');
        }

        // Map order parameter for Supabase
        if ($sort === 'username') {
            $orderParam = "username.{$dir}.nullslast";
        } elseif ($sort === 'version') {
            $orderParam = "version_used.{$dir}.nullslast";
        } elseif ($sort === 'activity') {
            $orderParam = "last_seen.{$dir}.nullslast";
        } elseif ($sort === 'status') {
            $orderParam = "is_banned.{$dir}.nullslast,last_seen.{$dir}.nullslast";
        } else {
            $orderParam = "created_at.{$dir}.nullslast";
        }

        $response = Http::withHeaders($this->headers())
            ->get($this->supaUrl . '?select=*&order=' . $orderParam);
        $users = $response->successful() ? $response->json() : [];

        // Ambil min_version dari pixora_config
        $configRes = Http::withHeaders($this->headers())
            ->get('https://movecexnjyeaipkklijv.supabase.co/rest/v1/pixora_config?key=eq.min_version&select=value');
        $minVersion = $configRes->successful() ? ($configRes->json()[0]['value'] ?? '1.0.0') : '1.0.0';

        return view('admin-panel.pixora-users.index', compact('users', 'minVersion', 'sort', 'dir'));
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

    public function bulkBan(Request $request)
    {
        $request->validate([
            'usernames' => 'required|array',
            'usernames.*' => 'string',
            'reason' => 'nullable|string'
        ]);

        $usernames = $request->usernames;
        // PostgREST in.() operator with safely quoted strings
        $quotedUsernames = array_map(function($u) {
            return '"' . str_replace('"', '""', $u) . '"';
        }, $usernames);
        $inQuery = 'in.(' . implode(',', $quotedUsernames) . ')';
        $queryString = http_build_query(['username' => $inQuery]);

        $response = Http::withHeaders($this->headers())
            ->patch($this->supaUrl . '?' . $queryString, [
                'is_banned'  => true,
                'updated_at' => now()->toISOString(),
            ]);

        if ($response->successful()) {
            $adminEmail = auth()->user()->email ?? 'Admin';
            $reason = $request->reason ?: 'No reason provided';
            \Log::info("Bulk Ban by {$adminEmail} on users: " . implode(', ', $usernames) . " | Reason: {$reason}");
            return response()->json(['success' => true, 'message' => count($usernames) . ' users have been banned.']);
        }

        return response()->json(['success' => false, 'error' => 'Gagal memproses Ban: ' . $response->body()], 500);
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'usernames' => 'required|array',
            'usernames.*' => 'string',
        ]);

        $usernames = $request->usernames;
        // PostgREST in.() operator with safely quoted strings
        $quotedUsernames = array_map(function($u) {
            return '"' . str_replace('"', '""', $u) . '"';
        }, $usernames);
        $inQuery = 'in.(' . implode(',', $quotedUsernames) . ')';
        $queryString = http_build_query(['username' => $inQuery]);

        $response = Http::withHeaders(array_merge($this->headers(), [
            'Prefer' => 'return=minimal',
        ]))->delete($this->supaUrl . '?' . $queryString);

        if ($response->successful() || $response->status() === 204) {
            $adminEmail = auth()->user()->email ?? 'Admin';
            \Log::info("Bulk Delete by {$adminEmail} on users: " . implode(', ', $usernames));
            return response()->json(['success' => true, 'message' => count($usernames) . ' users have been deleted.']);
        }

        return response()->json(['success' => false, 'error' => 'Gagal menghapus pengguna: ' . $response->body()], 500);
    }
}

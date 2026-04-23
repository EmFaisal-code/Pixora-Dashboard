<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class AdminController extends Controller
{
    private $supaUrl = 'https://movecexnjyeaipkklijv.supabase.co/rest/v1/pixora_users';
    private $supaKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Im1vdmVjZXhuanllYWlwa2tsaWp2Iiwicm9sZSI6ImFub24iLCJpYXQiOjE3NzY4OTYzMDUsImV4cCI6MjA5MjQ3MjMwNX0.LdytPpEvTDapFfh_OxxOwSf3i8af4XVSe9mdy3QDkhE';

    private function headers()
    {
        return [
            'apikey'        => $this->supaKey,
            'Authorization' => 'Bearer ' . $this->supaKey,
        ];
    }

    public function dashboard()
    {
        $pixoraStats  = ['total' => 0, 'active' => 0, 'banned' => 0];
        $recentUsers  = [];

        try {
            $users = Http::withHeaders($this->headers())
                ->get($this->supaUrl . '?select=username,is_banned,created_at&order=created_at.desc')
                ->json();

            if (is_array($users)) {
                $pixoraStats['total']  = count($users);
                $pixoraStats['banned'] = count(array_filter($users, fn($u) => $u['is_banned'] ?? false));
                $pixoraStats['active'] = $pixoraStats['total'] - $pixoraStats['banned'];
                $recentUsers           = array_slice($users, 0, 5);
            }
        } catch (\Exception $e) {}

        return view('admin-panel.home', compact('pixoraStats', 'recentUsers'));
    }
}

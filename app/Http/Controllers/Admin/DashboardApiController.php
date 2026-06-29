<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class DashboardApiController extends Controller
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

    public function stats(Request $request)
    {
        $days = (int) $request->query('days', 30);
        $refresh = $request->query('refresh', false) === 'true';

        $cacheKey = "dashboard_stats_{$days}";
        if ($refresh) {
            Cache::forget($cacheKey);
        }

        return Cache::remember($cacheKey, 86400, function () use ($days) {
            return $this->generateStats($days);
        });
    }

    private function generateStats($days)
    {
        $users = Http::withHeaders($this->headers())
            ->get($this->supaUrl . '?select=created_at,version_used,last_seen')
            ->json();

        if (!is_array($users)) {
            return response()->json(['error' => 'Failed to fetch data'], 500);
        }

        $now = Carbon::now();
        $startDate = $now->copy()->subDays($days - 1)->startOfDay();

        // 1. User Growth (Daily Signups)
        $growthData = [];
        for ($i = 0; $i < $days; $i++) {
            $dateStr = $startDate->copy()->addDays($i)->format('Y-m-d');
            $growthData[$dateStr] = 0;
        }

        // 2. Version Distribution
        $versionCounts = [];

        // 3. DAU (Daily Active Users)
        $dauData = [];
        for ($i = 0; $i < $days; $i++) {
            $dateStr = $startDate->copy()->addDays($i)->format('Y-m-d');
            $dauData[$dateStr] = 0;
        }

        $currentWeekDau = 0;
        $previousWeekDau = 0;
        $currentWeekStart = $now->copy()->subDays(6)->startOfDay();
        $previousWeekStart = $now->copy()->subDays(13)->startOfDay();
        $previousWeekEnd = $currentWeekStart->copy()->subSecond();

        foreach ($users as $user) {
            // User Growth
            if (!empty($user['created_at'])) {
                $createdAt = Carbon::parse($user['created_at']);
                if ($createdAt->gte($startDate)) {
                    $dateStr = $createdAt->format('Y-m-d');
                    if (isset($growthData[$dateStr])) {
                        $growthData[$dateStr]++;
                    }
                }
            }

            // Version Distribution
            if (!empty($user['version_used'])) {
                $v = $user['version_used'];
                if (!isset($versionCounts[$v])) {
                    $versionCounts[$v] = 0;
                }
                $versionCounts[$v]++;
            } else {
                $v = 'Unknown';
                if (!isset($versionCounts[$v])) {
                    $versionCounts[$v] = 0;
                }
                $versionCounts[$v]++;
            }

            // DAU
            if (!empty($user['last_seen'])) {
                $lastSeen = Carbon::parse($user['last_seen']);
                if ($lastSeen->gte($startDate)) {
                    $dateStr = $lastSeen->format('Y-m-d');
                    if (isset($dauData[$dateStr])) {
                        $dauData[$dateStr]++;
                    }
                }
                
                // Compare weekly DAU
                if ($lastSeen->gte($currentWeekStart)) {
                    $currentWeekDau++;
                } elseif ($lastSeen->between($previousWeekStart, $previousWeekEnd)) {
                    $previousWeekDau++;
                }
            }
        }

        // Format User Growth for charts
        $growthLabels = [];
        $growthValues = [];
        foreach ($growthData as $date => $count) {
            $growthLabels[] = Carbon::parse($date)->format('d M');
            $growthValues[] = $count;
        }

        // Format Version Distribution
        $versionLabels = array_keys($versionCounts);
        $versionValues = array_values($versionCounts);

        // Format DAU
        $dauLabels = [];
        $dauValues = [];
        foreach ($dauData as $date => $count) {
            $dauLabels[] = Carbon::parse($date)->format('d M');
            $dauValues[] = $count;
        }
        
        $dauTrend = 0;
        if ($previousWeekDau > 0) {
            $dauTrend = round((($currentWeekDau - $previousWeekDau) / $previousWeekDau) * 100);
        } elseif ($currentWeekDau > 0) {
            $dauTrend = 100; // 100% increase if previous was 0 and now is > 0
        }

        return [
            'growth' => [
                'labels' => $growthLabels,
                'data' => $growthValues,
            ],
            'version' => [
                'labels' => $versionLabels,
                'data' => $versionValues,
            ],
            'dau' => [
                'labels' => $dauLabels,
                'data' => $dauValues,
                'total_current_week' => $currentWeekDau,
                'trend_percentage' => $dauTrend
            ]
        ];
    }
}

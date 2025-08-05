<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AdApiController extends Controller
{
    /**
     * Get active ads for a specific placement
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAdsByPlacement(Request $request)
    {
        try {
            $placement = $request->route('placement');

            // Validate placement
            $validPlacements = array_keys(Ad::getPlacements());
            if (!in_array($placement, $validPlacements)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid placement',
                    'valid_placements' => $validPlacements
                ], 400);
            }

            // Get active ads for this placement
            $ads = Ad::active()
                ->scheduled()
                ->forPlacement($placement)
                ->orderBy('display_order', 'asc')
                ->orderBy('created_at', 'desc')
                ->get();

            // Format ads for mobile app
            $formattedAds = $ads->map(function ($ad) {
                return [
                    'id' => $ad->id,
                    'title' => $ad->title,
                    'description' => $ad->description,
                    'image_url' => $ad->image_url,
                    'ad_type' => $ad->ad_type,
                    'placement' => $ad->placement,
                    'target_url' => $ad->target_url,
                    'display_order' => $ad->display_order,
                    'created_at' => $ad->created_at->toDateTimeString()
                ];
            });

            // Increment view count for all returned ads
            if ($formattedAds->count() > 0) {
                Ad::whereIn('id', $ads->pluck('id'))
                    ->increment('view_count');
            }

            return response()->json([
                'success' => true,
                'placement' => $placement,
                'ads' => $formattedAds,
                'count' => $formattedAds->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching ads by placement', [
                'placement' => $placement ?? 'unknown',
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to fetch ads',
                'ads' => [],
                'count' => 0
            ], 500);
        }
    }

    /**
     * Get all active ads (for general use)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllActiveAds(Request $request)
    {
        try {
            // Get query parameters
            $adType = $request->query('ad_type'); // 'internal' or 'external'
            $limit = (int) $request->query('limit', 10); // Default 10 ads
            $limit = min($limit, 50); // Max 50 ads per request

            // Build query
            $query = Ad::active()->scheduled();

            // Filter by ad type if specified
            if ($adType && in_array($adType, array_keys(Ad::getAdTypes()))) {
                $query->ofType($adType);
            }

            // Get ads
            $ads = $query->orderBy('display_order', 'asc')
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();

            // Group ads by placement
            $adsByPlacement = $ads->groupBy('placement')->map(function ($placementAds) {
                return $placementAds->map(function ($ad) {
                    return [
                        'id' => $ad->id,
                        'title' => $ad->title,
                        'description' => $ad->description,
                        'image_url' => $ad->image_url,
                        'ad_type' => $ad->ad_type,
                        'placement' => $ad->placement,
                        'target_url' => $ad->target_url,
                        'display_order' => $ad->display_order,
                        'created_at' => $ad->created_at->toDateTimeString()
                    ];
                });
            });

            // Increment view count for all returned ads
            if ($ads->count() > 0) {
                Ad::whereIn('id', $ads->pluck('id'))
                    ->increment('view_count');
            }

            return response()->json([
                'success' => true,
                'ads_by_placement' => $adsByPlacement,
                'total_count' => $ads->count(),
                'applied_filters' => [
                    'ad_type' => $adType,
                    'limit' => $limit
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching all active ads', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to fetch ads',
                'ads_by_placement' => [],
                'total_count' => 0
            ], 500);
        }
    }

    /**
     * Get ad details by ID
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAdDetails(Request $request, $id)
    {
        try {
            $ad = Ad::active()->scheduled()->find($id);

            if (!$ad) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ad not found or not active'
                ], 404);
            }

            // Increment view count
            $ad->incrementViews();

            return response()->json([
                'success' => true,
                'ad' => [
                    'id' => $ad->id,
                    'title' => $ad->title,
                    'description' => $ad->description,
                    'image_url' => $ad->image_url,
                    'ad_type' => $ad->ad_type,
                    'placement' => $ad->placement,
                    'target_url' => $ad->target_url,
                    'display_order' => $ad->display_order,
                    'view_count' => $ad->view_count,
                    'click_count' => $ad->click_count,
                    'created_at' => $ad->created_at->toDateTimeString()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching ad details', [
                'ad_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to fetch ad details'
            ], 500);
        }
    }

    /**
     * Record ad click (for analytics)
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function recordAdClick(Request $request, $id)
    {
        try {
            $ad = Ad::active()->scheduled()->find($id);

            if (!$ad) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ad not found or not active'
                ], 404);
            }

            // Increment click count
            $ad->incrementClicks();

            Log::info('Ad click recorded', [
                'ad_id' => $id,
                'ad_title' => $ad->title,
                'user_ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Click recorded',
                'ad' => [
                    'id' => $ad->id,
                    'title' => $ad->title,
                    'target_url' => $ad->target_url,
                    'click_count' => $ad->click_count
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error recording ad click', [
                'ad_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to record click'
            ], 500);
        }
    }

    /**
     * Get available placements
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAvailablePlacements()
    {
        try {
            $placements = Ad::getPlacements();

            // Add count of active ads for each placement
            $placementsWithCounts = [];
            foreach ($placements as $key => $name) {
                $count = Ad::active()->scheduled()->forPlacement($key)->count();
                $placementsWithCounts[] = [
                    'placement' => $key,
                    'name' => $name,
                    'active_ads_count' => $count
                ];
            }

            return response()->json([
                'success' => true,
                'placements' => $placementsWithCounts
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching available placements', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to fetch placements'
            ], 500);
        }
    }


    /**
     * Enhanced ad serving with rotation and frequency control
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSmartAds(Request $request)
    {
        try {
            $validated = $request->validate([
                'placement' => 'required|string|in:' . implode(',', array_keys(Ad::getPlacements())),
                'user_id' => 'nullable|integer', // For personalized ad serving
                'device_type' => 'nullable|string|in:mobile,tablet,web',
                'limit' => 'nullable|integer|min:1|max:10',
                'exclude_ids' => 'nullable|array', // Exclude recently shown ads
                'exclude_ids.*' => 'integer'
            ]);

            $placement = $validated['placement'];
            $userId = $validated['user_id'] ?? null;
            $deviceType = $validated['device_type'] ?? 'mobile';
            $limit = $validated['limit'] ?? 3;
            $excludeIds = $validated['exclude_ids'] ?? [];

            // Build smart query
            $query = Ad::active()
                ->scheduled()
                ->forPlacement($placement);

            // Exclude recently shown ads to prevent repetition
            if (!empty($excludeIds)) {
                $query->whereNotIn('id', $excludeIds);
            }

            // Get ads with weighted rotation (higher display_order = higher priority)
            $ads = $query->orderByRaw('display_order ASC, RAND()')
                ->limit($limit)
                ->get();

            // If not enough ads and we had exclusions, get more without exclusions
            if ($ads->count() < $limit && !empty($excludeIds)) {
                $additionalCount = $limit - $ads->count();
                $additionalAds = Ad::active()
                    ->scheduled()
                    ->forPlacement($placement)
                    ->whereNotIn('id', $ads->pluck('id'))
                    ->orderByRaw('display_order ASC, RAND()')
                    ->limit($additionalCount)
                    ->get();

                $ads = $ads->merge($additionalAds);
            }

            // Format ads with enhanced data
            $formattedAds = $ads->map(function ($ad) use ($userId, $deviceType) {
                return [
                    'id' => $ad->id,
                    'title' => $ad->title,
                    'description' => $ad->description,
                    'image_url' => $ad->image_url,
                    'ad_type' => $ad->ad_type,
                    'placement' => $ad->placement,
                    'target_url' => $ad->target_url,
                    'display_order' => $ad->display_order,
                    'created_at' => $ad->created_at->toDateTimeString(),
                    'performance_score' => $this->calculatePerformanceScore($ad),
                    'serving_metadata' => [
                        'served_at' => now()->toDateTimeString(),
                        'device_type' => $deviceType,
                        'placement' => $ad->placement
                    ]
                ];
            });

            // Increment view count for all served ads
            if ($formattedAds->count() > 0) {
                Ad::whereIn('id', $ads->pluck('id'))
                    ->increment('view_count');
            }

            // Log ad serving for analytics
            Log::info('Smart ads served', [
                'placement' => $placement,
                'ads_served' => $ads->pluck('id')->toArray(),
                'user_id' => $userId,
                'device_type' => $deviceType,
                'excluded_ids' => $excludeIds
            ]);

            return response()->json([
                'success' => true,
                'placement' => $placement,
                'ads' => $formattedAds,
                'count' => $formattedAds->count(),
                'serving_info' => [
                    'total_available' => Ad::active()->scheduled()->forPlacement($placement)->count(),
                    'rotation_applied' => true,
                    'exclusions_applied' => !empty($excludeIds),
                    'device_optimized' => true
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request parameters',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error in smart ad serving', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to serve ads',
                'ads' => [],
                'count' => 0
            ], 500);
        }
    }

    /**
     * Get ads with advanced filtering and targeting
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTargetedAds(Request $request)
    {
        try {
            $validated = $request->validate([
                'placements' => 'required|array|min:1', // Multiple placements
                'placements.*' => 'string|in:' . implode(',', array_keys(Ad::getPlacements())),
                'ad_types' => 'nullable|array',
                'ad_types.*' => 'string|in:' . implode(',', array_keys(Ad::getAdTypes())),
                'max_per_placement' => 'nullable|integer|min:1|max:5',
                'prioritize_new' => 'nullable|boolean', // Show newer ads first
                'include_performance' => 'nullable|boolean' // Include performance metrics
            ]);

            $placements = $validated['placements'];
            $adTypes = $validated['ad_types'] ?? null;
            $maxPerPlacement = $validated['max_per_placement'] ?? 2;
            $prioritizeNew = $validated['prioritize_new'] ?? false;
            $includePerformance = $validated['include_performance'] ?? false;

            $allAds = [];

            foreach ($placements as $placement) {
                // Build query for this placement
                $query = Ad::active()
                    ->scheduled()
                    ->forPlacement($placement);

                // Filter by ad types if specified
                if ($adTypes) {
                    $query->whereIn('ad_type', $adTypes);
                }

                // Apply sorting
                if ($prioritizeNew) {
                    $query->orderBy('created_at', 'desc')
                        ->orderBy('display_order', 'asc');
                } else {
                    $query->orderBy('display_order', 'asc')
                        ->orderBy('created_at', 'desc');
                }

                // Get ads for this placement
                $placementAds = $query->limit($maxPerPlacement)->get();

                // Format ads
                $formattedAds = $placementAds->map(function ($ad) use ($includePerformance) {
                    $adData = [
                        'id' => $ad->id,
                        'title' => $ad->title,
                        'description' => $ad->description,
                        'image_url' => $ad->image_url,
                        'ad_type' => $ad->ad_type,
                        'placement' => $ad->placement,
                        'target_url' => $ad->target_url,
                        'display_order' => $ad->display_order,
                        'created_at' => $ad->created_at->toDateTimeString()
                    ];

                    // Add performance data if requested
                    if ($includePerformance) {
                        $adData['performance'] = [
                            'view_count' => $ad->view_count,
                            'click_count' => $ad->click_count,
                            'ctr' => $ad->view_count > 0 ? round(($ad->click_count / $ad->view_count) * 100, 2) : 0,
                            'performance_score' => $this->calculatePerformanceScore($ad),
                            'days_active' => $ad->created_at->diffInDays(now())
                        ];
                    }

                    return $adData;
                });

                $allAds[$placement] = $formattedAds;

                // Increment view count for served ads
                if ($placementAds->count() > 0) {
                    Ad::whereIn('id', $placementAds->pluck('id'))
                        ->increment('view_count');
                }
            }

            return response()->json([
                'success' => true,
                'ads_by_placement' => $allAds,
                'targeting_info' => [
                    'placements_requested' => $placements,
                    'ad_types_filter' => $adTypes,
                    'max_per_placement' => $maxPerPlacement,
                    'prioritize_new' => $prioritizeNew,
                    'performance_included' => $includePerformance,
                    'total_ads_served' => collect($allAds)->flatten(1)->count()
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request parameters',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error in targeted ad serving', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to serve targeted ads'
            ], 500);
        }
    }

    /**
     * Batch record multiple ad interactions
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function batchRecordInteractions(Request $request)
    {
        try {
            $validated = $request->validate([
                'interactions' => 'required|array|min:1|max:20',
                'interactions.*.ad_id' => 'required|integer|exists:ads,id',
                'interactions.*.type' => 'required|string|in:view,click,impression',
                'interactions.*.timestamp' => 'nullable|date',
                'interactions.*.metadata' => 'nullable|array'
            ]);

            $interactions = $validated['interactions'];
            $results = [];

            foreach ($interactions as $interaction) {
                $ad = Ad::find($interaction['ad_id']);

                if (!$ad || !$ad->isCurrentlyActive()) {
                    $results[] = [
                        'ad_id' => $interaction['ad_id'],
                        'type' => $interaction['type'],
                        'success' => false,
                        'message' => 'Ad not found or not active'
                    ];
                    continue;
                }

                // Record the interaction
                switch ($interaction['type']) {
                    case 'view':
                    case 'impression':
                        $ad->incrementViews();
                        break;
                    case 'click':
                        $ad->incrementClicks();
                        break;
                }

                $results[] = [
                    'ad_id' => $interaction['ad_id'],
                    'type' => $interaction['type'],
                    'success' => true,
                    'message' => 'Interaction recorded'
                ];

                // Log for analytics
                Log::info('Ad interaction recorded', [
                    'ad_id' => $interaction['ad_id'],
                    'type' => $interaction['type'],
                    'timestamp' => $interaction['timestamp'] ?? now(),
                    'metadata' => $interaction['metadata'] ?? []
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Batch interactions processed',
                'results' => $results,
                'total_processed' => count($results),
                'successful' => collect($results)->where('success', true)->count()
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request parameters',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error in batch interaction recording', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to process interactions'
            ], 500);
        }
    }

    /**
     * Get ad analytics summary
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAdAnalytics(Request $request)
    {
        try {
            $validated = $request->validate([
                'period' => 'nullable|string|in:today,week,month,all',
                'placement' => 'nullable|string|in:' . implode(',', array_keys(Ad::getPlacements())),
                'ad_type' => 'nullable|string|in:' . implode(',', array_keys(Ad::getAdTypes()))
            ]);

            $period = $validated['period'] ?? 'all';
            $placement = $validated['placement'] ?? null;
            $adType = $validated['ad_type'] ?? null;

            // Build query
            $query = Ad::query();

            // Apply filters
            if ($placement) {
                $query->forPlacement($placement);
            }
            if ($adType) {
                $query->ofType($adType);
            }

            // Apply date filter
            switch ($period) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'week':
                    $query->where('created_at', '>=', now()->subWeek());
                    break;
                case 'month':
                    $query->where('created_at', '>=', now()->subMonth());
                    break;
                    // 'all' requires no date filter
            }

            $ads = $query->get();

            // Calculate analytics
            $totalViews = $ads->sum('view_count');
            $totalClicks = $ads->sum('click_count');
            $avgCTR = $totalViews > 0 ? round(($totalClicks / $totalViews) * 100, 2) : 0;

            // Group by placement
            $byPlacement = $ads->groupBy('placement')->map(function ($placementAds, $placement) {
                $views = $placementAds->sum('view_count');
                $clicks = $placementAds->sum('click_count');
                return [
                    'placement' => $placement,
                    'placement_name' => Ad::getPlacements()[$placement] ?? $placement,
                    'ads_count' => $placementAds->count(),
                    'total_views' => $views,
                    'total_clicks' => $clicks,
                    'ctr' => $views > 0 ? round(($clicks / $views) * 100, 2) : 0
                ];
            })->values();

            // Group by ad type
            $byAdType = $ads->groupBy('ad_type')->map(function ($typeAds, $type) {
                $views = $typeAds->sum('view_count');
                $clicks = $typeAds->sum('click_count');
                return [
                    'ad_type' => $type,
                    'ad_type_name' => Ad::getAdTypes()[$type] ?? $type,
                    'ads_count' => $typeAds->count(),
                    'total_views' => $views,
                    'total_clicks' => $clicks,
                    'ctr' => $views > 0 ? round(($clicks / $views) * 100, 2) : 0
                ];
            })->values();

            // Top performing ads
            $topAds = $ads->sortByDesc(function ($ad) {
                return $this->calculatePerformanceScore($ad);
            })->take(5)->map(function ($ad) {
                return [
                    'id' => $ad->id,
                    'title' => $ad->title,
                    'placement' => $ad->placement,
                    'ad_type' => $ad->ad_type,
                    'view_count' => $ad->view_count,
                    'click_count' => $ad->click_count,
                    'ctr' => $ad->view_count > 0 ? round(($ad->click_count / $ad->view_count) * 100, 2) : 0,
                    'performance_score' => $this->calculatePerformanceScore($ad)
                ];
            })->values();

            return response()->json([
                'success' => true,
                'analytics' => [
                    'summary' => [
                        'total_ads' => $ads->count(),
                        'active_ads' => $ads->where('is_active', true)->count(),
                        'total_views' => $totalViews,
                        'total_clicks' => $totalClicks,
                        'average_ctr' => $avgCTR
                    ],
                    'by_placement' => $byPlacement,
                    'by_ad_type' => $byAdType,
                    'top_performing_ads' => $topAds
                ],
                'filters_applied' => [
                    'period' => $period,
                    'placement' => $placement,
                    'ad_type' => $adType
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching ad analytics', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to fetch analytics'
            ], 500);
        }
    }

    /**
     * Calculate performance score for an ad
     * 
     * @param Ad $ad
     * @return float
     */
    private function calculatePerformanceScore(Ad $ad): float
    {
        // Basic performance score based on CTR, views, and age
        $ctr = $ad->view_count > 0 ? ($ad->click_count / $ad->view_count) * 100 : 0;
        $viewScore = min($ad->view_count / 100, 10); // Max 10 points for views
        $ageScore = max(0, 10 - $ad->created_at->diffInDays(now()) / 30); // Newer ads get higher score

        return round(($ctr * 2) + $viewScore + $ageScore, 2);
    }
}

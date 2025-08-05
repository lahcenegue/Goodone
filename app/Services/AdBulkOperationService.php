<?php

namespace App\Services;

use App\Models\Ad;
use App\Models\AdUserInteraction;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AdBulkOperationService
{
    /**
     * Perform bulk status change
     */
    public function bulkStatusChange(array $adIds, bool $isActive, ?int $adminId = null): array
    {
        try {
            DB::beginTransaction();

            $affected = Ad::whereIn('id', $adIds)->update([
                'is_active' => $isActive,
                'last_modified_by_admin' => $adminId,
                'updated_at' => now()
            ]);

            DB::commit();

            $action = $isActive ? 'activated' : 'deactivated';

            Log::info("Bulk ad status change", [
                'admin_id' => $adminId,
                'ad_ids' => $adIds,
                'action' => $action,
                'affected_count' => $affected
            ]);

            return [
                'success' => true,
                'affected_count' => $affected,
                'message' => "Successfully {$action} {$affected} advertisements."
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk status change failed', [
                'error' => $e->getMessage(),
                'ad_ids' => $adIds
            ]);

            return [
                'success' => false,
                'error' => 'Failed to update ad statuses: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Bulk priority update
     */
    public function bulkPriorityUpdate(array $adIds, int $priority, ?int $adminId = null): array
    {
        try {
            DB::beginTransaction();

            $affected = Ad::whereIn('id', $adIds)->update([
                'admin_priority' => $priority,
                'last_modified_by_admin' => $adminId,
                'updated_at' => now()
            ]);

            DB::commit();

            $priorityName = Ad::getPriorityLevels()[$priority] ?? 'Unknown';

            Log::info("Bulk ad priority update", [
                'admin_id' => $adminId,
                'ad_ids' => $adIds,
                'new_priority' => $priority,
                'affected_count' => $affected
            ]);

            return [
                'success' => true,
                'affected_count' => $affected,
                'message' => "Successfully updated {$affected} ads to {$priorityName}."
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk priority update failed', [
                'error' => $e->getMessage(),
                'ad_ids' => $adIds
            ]);

            return [
                'success' => false,
                'error' => 'Failed to update priorities: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Bulk approval status change
     */
    public function bulkApprovalChange(array $adIds, string $status, ?int $adminId = null): array
    {
        try {
            DB::beginTransaction();

            $validStatuses = array_keys(Ad::getApprovalStatuses());
            if (!in_array($status, $validStatuses)) {
                return [
                    'success' => false,
                    'error' => 'Invalid approval status provided.'
                ];
            }

            $affected = Ad::whereIn('id', $adIds)->update([
                'approval_status' => $status,
                'last_modified_by_admin' => $adminId,
                'updated_at' => now()
            ]);

            DB::commit();

            $statusName = Ad::getApprovalStatuses()[$status];

            Log::info("Bulk ad approval change", [
                'admin_id' => $adminId,
                'ad_ids' => $adIds,
                'new_status' => $status,
                'affected_count' => $affected
            ]);

            return [
                'success' => true,
                'affected_count' => $affected,
                'message' => "Successfully changed {$affected} ads to {$statusName}."
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk approval change failed', [
                'error' => $e->getMessage(),
                'ad_ids' => $adIds
            ]);

            return [
                'success' => false,
                'error' => 'Failed to update approval status: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Bulk delete ads
     */
    public function bulkDelete(array $adIds, ?int $adminId = null): array
    {
        try {
            DB::beginTransaction();

            // Get ads to delete (for image cleanup)
            $ads = Ad::whereIn('id', $adIds)->get();

            // Delete associated images
            foreach ($ads as $ad) {
                if ($ad->image && file_exists(storage_path('app/public/ads/' . $ad->image))) {
                    unlink(storage_path('app/public/ads/' . $ad->image));
                }
            }

            // Delete ads (interactions will be cascaded)
            $affected = Ad::whereIn('id', $adIds)->delete();

            DB::commit();

            Log::info("Bulk ad deletion", [
                'admin_id' => $adminId,
                'ad_ids' => $adIds,
                'affected_count' => $affected
            ]);

            return [
                'success' => true,
                'affected_count' => $affected,
                'message' => "Successfully deleted {$affected} advertisements."
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk delete failed', [
                'error' => $e->getMessage(),
                'ad_ids' => $adIds
            ]);

            return [
                'success' => false,
                'error' => 'Failed to delete ads: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Bulk targeting update
     */
    public function bulkTargetingUpdate(array $adIds, array $targetingData, ?int $adminId = null): array
    {
        try {
            DB::beginTransaction();

            $updateData = array_merge($targetingData, [
                'last_modified_by_admin' => $adminId,
                'updated_at' => now()
            ]);

            $affected = Ad::whereIn('id', $adIds)->update($updateData);

            DB::commit();

            Log::info("Bulk ad targeting update", [
                'admin_id' => $adminId,
                'ad_ids' => $adIds,
                'targeting_data' => $targetingData,
                'affected_count' => $affected
            ]);

            return [
                'success' => true,
                'affected_count' => $affected,
                'message' => "Successfully updated targeting for {$affected} advertisements."
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk targeting update failed', [
                'error' => $e->getMessage(),
                'ad_ids' => $adIds
            ]);

            return [
                'success' => false,
                'error' => 'Failed to update targeting: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Auto-pause poor performing ads
     */
    public function autoPausePoorPerformers(?int $adminId = null): array
    {
        try {
            $adsToPause = Ad::active()
                ->approved()
                ->where('auto_pause_enabled', true)
                ->whereNotNull('min_ctr_threshold')
                ->where('view_count', '>=', 100)
                ->whereRaw('(click_count / view_count) * 100 < min_ctr_threshold')
                ->get();

            $pausedCount = 0;
            $pausedIds = [];

            foreach ($adsToPause as $ad) {
                $ad->update([
                    'is_active' => false,
                    'admin_notes' => ($ad->admin_notes ?? '') . "\n[AUTO-PAUSED] Poor performance (CTR below threshold) - " . now()->format('Y-m-d H:i:s'),
                    'last_modified_by_admin' => $adminId
                ]);

                $pausedCount++;
                $pausedIds[] = $ad->id;
            }

            if ($pausedCount > 0) {
                Log::info("Auto-pause poor performers", [
                    'admin_id' => $adminId,
                    'paused_ads' => $pausedIds,
                    'paused_count' => $pausedCount
                ]);
            }

            return [
                'success' => true,
                'paused_count' => $pausedCount,
                'paused_ads' => $pausedIds,
                'message' => $pausedCount > 0
                    ? "Auto-paused {$pausedCount} poor performing ads."
                    : "No ads required auto-pausing."
            ];
        } catch (\Exception $e) {
            Log::error('Auto-pause failed', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'Auto-pause operation failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Reset daily spend for all ads (run daily)
     */
    public function resetDailySpend(): array
    {
        try {
            $affected = Ad::where('daily_spend', '>', 0)->update([
                'daily_spend' => 0,
                'updated_at' => now()
            ]);

            Log::info("Daily spend reset", [
                'affected_count' => $affected,
                'reset_date' => now()->toDateString()
            ]);

            return [
                'success' => true,
                'affected_count' => $affected,
                'message' => "Reset daily spend for {$affected} advertisements."
            ];
        } catch (\Exception $e) {
            Log::error('Daily spend reset failed', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'Failed to reset daily spend: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Generate bulk operation report
     */
    public function generateBulkReport(array $adIds): array
    {
        $ads = Ad::whereIn('id', $adIds)->get();

        return [
            'total_ads' => $ads->count(),
            'active_ads' => $ads->where('is_active', true)->count(),
            'inactive_ads' => $ads->where('is_active', false)->count(),
            'pending_approval' => $ads->where('approval_status', 'pending')->count(),
            'approved_ads' => $ads->where('approval_status', 'approved')->count(),
            'rejected_ads' => $ads->where('approval_status', 'rejected')->count(),
            'total_views' => $ads->sum('view_count'),
            'total_clicks' => $ads->sum('click_count'),
            'total_spend' => $ads->sum('daily_spend'),
            'avg_performance_score' => $ads->avg('performance_score'),
            'priority_distribution' => $ads->countBy('admin_priority'),
            'placement_distribution' => $ads->countBy('placement'),
            'ad_type_distribution' => $ads->countBy('ad_type')
        ];
    }
}

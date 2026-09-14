<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Utils\StudyObfuscator;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $notifications = DB::table('notifications')
            ->where('type', 'App\\Notifications\\CollaboratorAddedNotification')
            ->get();

        foreach ($notifications as $notification) {
            $data = is_array($notification->data) ? $notification->data : json_decode($notification->data, true);
            if (!$data) {
                continue;
            }

            $modified = false;
            if (!empty($data['action_url']) && preg_match('#^/study/(\d+)$#', $data['action_url'], $matches)) {
                $studyId = (int) $matches[1];
                $encodedId = StudyObfuscator::encode($studyId);
                $data['action_url'] = "/study/{$encodedId}";
                if (isset($data['study_id']) && is_numeric($data['study_id'])) {
                    $data['study_id'] = $encodedId;
                }
                $modified = true;
            }

            if ($modified) {
                DB::table('notifications')
                    ->where('id', $notification->id)
                    ->update(['data' => json_encode($data)]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reversal needed for obfuscating notification URLs
    }
};

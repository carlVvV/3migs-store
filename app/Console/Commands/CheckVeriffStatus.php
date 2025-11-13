<?php

namespace App\Console\Commands;

use App\Models\IdDocument;
use Illuminate\Console\Command;

class CheckVeriffStatus extends Command
{
    protected $signature = 'veriff:check {session_id} {--update-status=}';
    protected $description = 'Check and optionally update Veriff document status';

    public function handle()
    {
        $sessionId = $this->argument('session_id');
        $updateStatus = $this->option('update-status');

        $document = IdDocument::where('veriff_session_id', $sessionId)->first();

        if (!$document) {
            $this->error("Document not found with session_id: {$sessionId}");
            return 1;
        }

        $this->info("Found document:");
        $this->table(
            ['ID', 'User ID', 'Session ID', 'Status', 'Created At', 'Updated At'],
            [[
                $document->id,
                $document->user_id,
                $document->veriff_session_id,
                $document->status,
                $document->created_at,
                $document->updated_at,
            ]]
        );

        if ($updateStatus) {
            $oldStatus = $document->status;
            $document->status = $updateStatus;
            $document->save();
            $this->info("Status updated from '{$oldStatus}' to '{$updateStatus}'");
        }

        return 0;
    }
}


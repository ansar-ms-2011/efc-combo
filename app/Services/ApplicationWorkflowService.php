<?php

namespace App\Services;

class ApplicationWorkflowService
{
    private array $map = [
        'pending' => 'submitted',
        'submitted' => 'verified',
        'verified' => 'approved',
        'approved' => 'ready_for_delivery',
        'ready_for_delivery' => 'delivered',
    ];

    public function next($app, $action)
    {
        if ($action === 'objected') return 'objected';
        if ($action === 'rollback') return 'pending';

        return $this->map[$app->current_status] ?? 'pending';
    }
}

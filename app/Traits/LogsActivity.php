<?php

namespace App\Traits;

use App\Models\Log;

trait LogsActivity
{
    /**
     * Log an activity
     */
    protected function logActivity(
        string $transactionType,
        string $transactionId,
        string $action,
        ?string $transactionName = null,
        ?array $changes = null
    ): void {
        $user = auth()->user();
        
        if (!$user) {
            return;
        }

        Log::create([
            'transaction_type' => $transactionType,
            'transaction_id' => $transactionId,
            'transaction_name' => $transactionName,
            'action' => $action,
            'edited_by' => $user->name,
            'user_id' => $user->id,
            'changes' => $changes,
        ]);
    }

    /**
     * Log creation of a transaction
     */
    protected function logCreated(
        string $transactionType,
        string $transactionId,
        ?string $transactionName = null,
        ?array $data = null
    ): void {
        $this->logActivity(
            $transactionType,
            $transactionId,
            Log::ACTION_CREATED,
            $transactionName,
            $data ? ['created' => $data] : null
        );
    }

    /**
     * Log update of a transaction
     */
    protected function logUpdated(
        string $transactionType,
        string $transactionId,
        array $oldData,
        array $newData,
        ?string $transactionName = null
    ): void {
        $changes = $this->getChanges($oldData, $newData);
        
        if (!empty($changes)) {
            $this->logActivity(
                $transactionType,
                $transactionId,
                Log::ACTION_UPDATED,
                $transactionName,
                $changes
            );
        }
    }

    /**
     * Log deletion of a transaction
     */
    protected function logDeleted(
        string $transactionType,
        string $transactionId,
        ?string $transactionName = null,
        ?array $data = null
    ): void {
        $this->logActivity(
            $transactionType,
            $transactionId,
            Log::ACTION_DELETED,
            $transactionName,
            $data ? ['deleted' => $data] : null
        );
    }

    /**
     * Log status change of a transaction
     */
    protected function logStatusChanged(
        string $transactionType,
        string $transactionId,
        string $oldStatus,
        string $newStatus,
        ?string $transactionName = null
    ): void {
        $this->logActivity(
            $transactionType,
            $transactionId,
            Log::ACTION_STATUS_CHANGED,
            $transactionName,
            [
                'status' => [
                    'old' => $oldStatus,
                    'new' => $newStatus
                ]
            ]
        );
    }

    /**
     * Get changes between old and new data
     */
    protected function getChanges(array $oldData, array $newData): array
    {
        $changes = [];
        
        foreach ($newData as $key => $newValue) {
            $oldValue = $oldData[$key] ?? null;
            
            if ($oldValue !== $newValue) {
                $changes[$key] = [
                    'old' => $oldValue,
                    'new' => $newValue
                ];
            }
        }
        
        return $changes;
    }

    /**
     * Generate transaction name based on type and ID
     */
    protected function generateTransactionName(string $transactionType, string $transactionId): string
    {
        switch ($transactionType) {
            case Log::TYPE_QUOTATION:
                return 'Quotation #' . $transactionId;
            case Log::TYPE_ORDER:
                return 'Job Order #' . $transactionId;
            case Log::TYPE_PAYMENT:
                return 'Payment #' . $transactionId;
            case Log::TYPE_DELIVERY:
                return 'Delivery #' . $transactionId;
            default:
                return 'Transaction #' . $transactionId;
        }
    }
}

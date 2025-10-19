<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait TracksHistory
{
    protected static function bootTracksHistory()
    {
        static::created(function ($model) {
            $model->trackHistory('created', null, $model->getAttributes());
        });

        static::updated(function ($model) {
            $model->trackHistory('updated', $model->getOriginal(), $model->getChanges());
        });
    }

    public function trackHistory($action, $oldValues = null, $newValues = null)
    {
        $historyModel = $this->getHistoryModel();
        
        $historyModel::create([
            $this->getHistoryForeignKey() => $this->getKey(),
            'action' => $action,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'description' => $this->getHistoryDescription($action, $oldValues, $newValues),
            'updated_by' => Auth::id(),
        ]);
    }

    protected function getHistoryModel()
    {
        $modelName = class_basename($this);
        return "App\\Models\\{$modelName}History";
    }

    protected function getHistoryForeignKey()
    {
        return strtolower(class_basename($this)) . '_id';
    }

    protected function getHistoryDescription($action, $oldValues, $newValues)
    {
        $modelName = class_basename($this);
        
        switch ($action) {
            case 'created':
                return "{$modelName} was created";
            case 'updated':
                $changes = [];
                foreach ($newValues as $field => $newValue) {
                    $oldValue = $oldValues[$field] ?? null;
                    if ($oldValue !== $newValue) {
                        $changes[] = "{$field} changed from '{$oldValue}' to '{$newValue}'";
                    }
                }
                return "{$modelName} was updated: " . implode(', ', $changes);
            default:
                return "{$modelName} was {$action}";
        }
    }
}

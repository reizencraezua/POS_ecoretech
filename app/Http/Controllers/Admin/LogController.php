<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Log;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LogController extends Controller
{
    /**
     * Display a listing of the logs.
     */
    public function index(Request $request)
    {
        $query = Log::with('user')->orderBy('created_at', 'desc');

        // Filter by transaction type
        if ($request->filled('transaction_type')) {
            $query->where('transaction_type', $request->transaction_type);
        }

        // Filter by date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->whereBetween('created_at', [$startDate, $endDate]);
        } elseif ($request->filled('start_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $query->where('created_at', '>=', $startDate);
        } elseif ($request->filled('end_date')) {
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->where('created_at', '<=', $endDate);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Search by transaction name or ID
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('transaction_name', 'like', "%{$searchTerm}%")
                  ->orWhere('transaction_id', 'like', "%{$searchTerm}%")
                  ->orWhere('edited_by', 'like', "%{$searchTerm}%");
            });
        }

        $logs = $query->paginate(20)->withQueryString();

        // Get filter options
        $transactionTypes = Log::select('transaction_type')
            ->distinct()
            ->pluck('transaction_type')
            ->mapWithKeys(function ($type) {
                return [$type => $this->getTransactionTypeLabel($type)];
            });

        $actions = Log::select('action')
            ->distinct()
            ->pluck('action')
            ->mapWithKeys(function ($action) {
                return [$action => $this->getActionLabel($action)];
            });

        $users = Log::with('user')
            ->select('user_id', 'edited_by')
            ->distinct()
            ->get()
            ->mapWithKeys(function ($log) {
                return [$log->user_id => $log->edited_by];
            });

        return view('admin.logs.index', compact('logs', 'transactionTypes', 'actions', 'users'));
    }

    /**
     * Show the specified log.
     */
    public function show(Log $log)
    {
        $log->load('user');
        return view('admin.logs.show', compact('log'));
    }

    /**
     * Get transaction type label
     */
    private function getTransactionTypeLabel($type)
    {
        switch ($type) {
            case 'quotation':
                return 'Quotation';
            case 'order':
                return 'Job Order';
            case 'payment':
                return 'Payment';
            case 'delivery':
                return 'Delivery';
            default:
                return ucfirst($type);
        }
    }

    /**
     * Get action label
     */
    private function getActionLabel($action)
    {
        switch ($action) {
            case 'created':
                return 'Created';
            case 'updated':
                return 'Updated';
            case 'deleted':
                return 'Deleted';
            case 'status_changed':
                return 'Status Changed';
            default:
                return ucfirst($action);
        }
    }
}

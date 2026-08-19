<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with ticket statistics
     */
    public function index()
    {
        $user = auth()->user();

        // Build query based on user role
        $query = Ticket::query();

        // If staff, only show their assigned tickets
        if ($user->hasPermissionTo('view assigned tickets') && !$user->hasPermissionTo('view all tickets')) {
            $query->where('assigned_to', $user->id);
        }

        // Get total tickets count
        $totalTickets = $query->count();

        // Get status counts
        $openTickets = (clone $query)->where('status', 'open')->count();
        $inProgressTickets = (clone $query)->where('status', 'in_progress')->count();
        $resolvedTickets = (clone $query)->where('status', 'resolved')->count();
        $closedTickets = (clone $query)->where('status', 'closed')->count();

        // Get high/urgent priority tickets count
        $highUrgentTickets = (clone $query)->whereIn('priority', ['high', 'urgent'])->count();

        // Get recent tickets for display
        $recentTickets = (clone $query)->with(['creator', 'assignee'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard.index', compact(
            'totalTickets',
            'openTickets',
            'inProgressTickets',
            'resolvedTickets',
            'closedTickets',
            'highUrgentTickets',
            'recentTickets'
        ));
    }
}

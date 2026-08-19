<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Start building the query with eager loading to avoid N+1 queries
        $query = Ticket::with(['creator', 'assignee'])
            ->withCount('comments'); // Load comment count efficiently

        // Apply role-based filtering
        if ($user->hasPermissionTo('view all tickets')) {
            // Admin can view all tickets - no additional filter
        } elseif ($user->hasPermissionTo('view assigned tickets')) {
            // Staff can only view assigned tickets
            $query->where('assigned_to', $user->id);
        } else {
            abort(403, 'Unauthorized');
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Priority filter
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Assigned staff filter
        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        // Validate sort fields to prevent SQL injection
        $allowedSortFields = ['ticket_number', 'title', 'priority', 'status', 'created_at', 'updated_at'];
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Paginate results
        $tickets = $query->paginate(10)->withQueryString();

        // Get all staff for filter dropdown
        $staffMembers = \App\Models\User::select('id', 'name')->get();

        return view('admin.tickets.index', compact('tickets', 'staffMembers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = \App\Models\User::where('role', 'staff')->get();
        return view('admin.tickets.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!auth()->user()->hasPermissionTo('create tickets')) {
            abort(403, 'Unauthorized to create tickets');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'status' => 'nullable|in:open,in_progress,resolved,closed',
            'assigned_to' => 'nullable|exists:users,id', // Business Rule: Ticket should not be assigned to invalid user
            'initial_comment' => 'nullable|string', // Optional initial comment
        ]);

        // Business Rule: Do not allow OPEN ticket to move directly to CLOSED
        if (isset($validated['status']) && $validated['status'] === 'closed') {
            return redirect()->back()->withInput()->withErrors(['status' => 'New tickets cannot be created with CLOSED status. Recommended flow: OPEN → IN_PROGRESS → RESOLVED → CLOSED']);
        }

        // Use database transaction to ensure ticket and initial comment are created together
        try {
            \DB::beginTransaction();

            // Generate unique ticket number
            $validated['ticket_number'] = 'TKT-' . strtoupper(Str::random(8));
            $validated['created_by'] = auth()->id();
            $validated['status'] = $validated['status'] ?? 'open';

            // Create ticket
            $ticket = Ticket::create([
                'ticket_number' => $validated['ticket_number'],
                'title' => $validated['title'],
                'description' => $validated['description'],
                'priority' => $validated['priority'],
                'status' => $validated['status'],
                'created_by' => $validated['created_by'],
                'assigned_to' => $validated['assigned_to'] ?? null,
            ]);

            // Create initial comment if provided
            if (!empty($validated['initial_comment'])) {
                $ticket->comments()->create([
                    'user_id' => auth()->id(),
                    'comment' => $validated['initial_comment'],
                ]);
            }

            \DB::commit();

            return redirect()->route('tickets.index')->with('success', 'Ticket created successfully');

        } catch (\Exception $e) {
            \DB::rollBack();
            
            return redirect()->back()->withInput()->withErrors(['error' => 'Failed to create ticket: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket)
    {
        $user = auth()->user();

        // Admin can view all tickets
        if ($user->hasPermissionTo('view all tickets')) {
            $ticket->load(['creator', 'assignee', 'comments.user']);
            return view('admin.tickets.show', compact('ticket'));
        }

        // Staff can only view tickets assigned to them
        if ($user->hasPermissionTo('view assigned tickets') && $ticket->assigned_to === $user->id) {
            $ticket->load(['creator', 'assignee', 'comments.user']);
            return view('admin.tickets.show', compact('ticket'));
        }

        abort(403, 'Unauthorized to view this ticket');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ticket $ticket)
    {
        $users = \App\Models\User::where('role', 'staff')->get();
        return view('admin.tickets.edit', compact('ticket', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ticket $ticket)
    {
        $user = auth()->user();

        // Business Rule: Staff cannot modify tickets assigned to another staff member
        if ($user->hasPermissionTo('update assigned tickets') && !$user->hasPermissionTo('update tickets')) {
            if ($ticket->assigned_to !== $user->id) {
                return redirect()->back()->withErrors(['error' => 'You can only update tickets assigned to you']);
            }
        }

        // Admin can update all tickets
        if ($user->hasPermissionTo('update tickets')) {
            // Admin can update everything
        } 
        // Staff can update tickets assigned to them (including all fields except assignment)
        elseif ($user->hasPermissionTo('update assigned tickets') && $ticket->assigned_to === $user->id) {
            // Staff can update title, description, priority, and status
            // But cannot change assignment
        } 
        else {
            abort(403, 'Unauthorized to update this ticket');
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'priority' => 'sometimes|required|in:low,medium,high,urgent',
            'status' => 'sometimes|required|in:open,in_progress,resolved,closed',
            'assigned_to' => 'nullable|exists:users,id', // Business Rule: Ticket should not be assigned to invalid user
        ]);

        // Business Rule: Do not allow OPEN ticket to move directly to CLOSED
        if (isset($validated['status'])) {
            if ($ticket->status === 'open' && $validated['status'] === 'closed') {
                return redirect()->back()->withErrors(['error' => 'Cannot move from OPEN directly to CLOSED. Recommended flow: OPEN → IN_PROGRESS → RESOLVED → CLOSED']);
            }
        }

        // Staff cannot change assignment - remove it from validated data if staff is updating
        if ($user->hasPermissionTo('update assigned tickets') && !$user->hasPermissionTo('update tickets')) {
            unset($validated['assigned_to']);
        }

        $ticket->update($validated);

        return redirect()->route('tickets.index')->with('success', 'Ticket updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket)
    {
        // Business Rule: Staff users must not be able to delete tickets
        // Only admins with 'delete tickets' permission can delete
        if (!auth()->user()->hasPermissionTo('delete tickets')) {
            return redirect()->back()->withErrors(['error' => 'Unauthorized to delete tickets. Only admins can delete tickets.']);
        }

        $ticket->delete();

        return redirect()->route('tickets.index')->with('success', 'Ticket deleted successfully');
    }

    /**
     * Update ticket status via AJAX
     */
    public function updateStatus(Request $request, Ticket $ticket)
    {
        if (!auth()->user()->hasPermissionTo('change ticket status')) {
            return response()->json(['message' => 'Unauthorized to change status'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
        ]);

        $newStatus = $validated['status'];
        $currentStatus = $ticket->status;

        // Business Rule: Do not allow OPEN ticket to move directly to CLOSED
        if ($currentStatus === 'open' && $newStatus === 'closed') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot move from OPEN directly to CLOSED. Recommended flow: OPEN → IN_PROGRESS → RESOLVED → CLOSED'
            ], 422);
        }

        $ticket->update(['status' => $newStatus]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully',
            'status' => $newStatus,
            'status_label' => ucfirst(str_replace('_', ' ', $newStatus)),
        ]);
    }
}

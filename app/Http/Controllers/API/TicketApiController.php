<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class TicketApiController extends Controller
{
    /**
     * Display a listing of the resource.
     * GET /api/tickets
     */
    public function index(Request $request)
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated'
                ], 401);
            }

            // Start building the query with eager loading to avoid N+1 queries
            $query = Ticket::with(['creator', 'assignee'])
                ->withCount('comments'); // Load comment count efficiently

            // Apply role-based filtering
            if ($user->hasPermissionTo('view all tickets')) {
                // Admin can view all tickets
            } elseif ($user->hasPermissionTo('view assigned tickets')) {
                // Staff can only view assigned tickets
                $query->where('assigned_to', $user->id);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            // Filters
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('priority')) {
                $query->where('priority', $request->priority);
            }

            if ($request->filled('assigned_to')) {
                $query->where('assigned_to', $request->assigned_to);
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            
            $allowedSortFields = ['ticket_number', 'title', 'priority', 'status', 'created_at', 'updated_at'];
            if (in_array($sortBy, $allowedSortFields)) {
                $query->orderBy($sortBy, $sortOrder);
            }

            // Pagination
            $perPage = $request->get('per_page', 10);
            $tickets = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Tickets retrieved successfully',
                'data' => $tickets
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving tickets',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     * POST /api/tickets
     */
    public function store(Request $request)
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated'
                ], 401);
            }

            if (!$user->hasPermissionTo('create tickets')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to create tickets'
                ], 403);
            }

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'priority' => 'required|in:low,medium,high,urgent',
                'status' => 'nullable|in:open,in_progress,resolved,closed',
                'assigned_to' => 'nullable|exists:users,id',
                'initial_comment' => 'nullable|string', // Optional initial comment
            ]);

            // Business Rule: Do not allow creating ticket with CLOSED status
            if (isset($validated['status']) && $validated['status'] === 'closed') {
                return response()->json([
                    'success' => false,
                    'message' => 'New tickets cannot be created with CLOSED status'
                ], 422);
            }

            // Use database transaction to ensure ticket and initial comment are created together
            \DB::beginTransaction();

            try {
                // Generate unique ticket number
                $validated['ticket_number'] = 'TKT-' . strtoupper(Str::random(8));
                $validated['created_by'] = $user->id;
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
                        'user_id' => $user->id,
                        'comment' => $validated['initial_comment'],
                    ]);
                }

                \DB::commit();

                $ticket->load(['creator', 'assignee']);
                $ticket->loadCount('comments');

                return response()->json([
                    'success' => true,
                    'message' => 'Ticket created successfully',
                    'data' => $ticket
                ], 201);

            } catch (\Exception $e) {
                \DB::rollBack();
                throw $e;
            }

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating ticket',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     * GET /api/tickets/{id}
     */
    public function show(string $id)
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated'
                ], 401);
            }

            $ticket = Ticket::with(['creator', 'assignee', 'comments.user'])->find($id);

            if (!$ticket) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ticket not found'
                ], 404);
            }

            // Check authorization
            if ($user->hasPermissionTo('view all tickets')) {
                // Admin can view all tickets
            } elseif ($user->hasPermissionTo('view assigned tickets') && $ticket->assigned_to === $user->id) {
                // Staff can view tickets assigned to them
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to view this ticket'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'message' => 'Ticket retrieved successfully',
                'data' => $ticket
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving ticket',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     * PUT /api/tickets/{id}
     */
    public function update(Request $request, string $id)
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated'
                ], 401);
            }

            $ticket = Ticket::find($id);

            if (!$ticket) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ticket not found'
                ], 404);
            }

            // Business Rule: Staff cannot modify tickets assigned to another staff member
            if ($user->hasPermissionTo('update assigned tickets') && !$user->hasPermissionTo('update tickets')) {
                if ($ticket->assigned_to !== $user->id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You can only update tickets assigned to you'
                    ], 403);
                }
                
                // Staff cannot change assignment or status
                if ($request->has('assigned_to') || $request->has('status')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized to change assignment or status'
                    ], 403);
                }
            } elseif (!$user->hasPermissionTo('update tickets')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to update tickets'
                ], 403);
            }

            $validated = $request->validate([
                'title' => 'sometimes|required|string|max:255',
                'description' => 'sometimes|required|string',
                'priority' => 'sometimes|required|in:low,medium,high,urgent',
                'status' => 'sometimes|required|in:open,in_progress,resolved,closed',
                'assigned_to' => 'nullable|exists:users,id',
            ]);

            // Business Rule: Do not allow OPEN ticket to move directly to CLOSED
            if (isset($validated['status'])) {
                if ($ticket->status === 'open' && $validated['status'] === 'closed') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot move from OPEN directly to CLOSED. Recommended flow: OPEN → IN_PROGRESS → RESOLVED → CLOSED'
                    ], 422);
                }
            }

            $ticket->update($validated);
            $ticket->load(['creator', 'assignee']);

            return response()->json([
                'success' => true,
                'message' => 'Ticket updated successfully',
                'data' => $ticket
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating ticket',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     * DELETE /api/tickets/{id}
     */
    public function destroy(string $id)
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated'
                ], 401);
            }

            if (!$user->hasPermissionTo('delete tickets')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to delete tickets. Only admins can delete tickets.'
                ], 403);
            }

            $ticket = Ticket::find($id);

            if (!$ticket) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ticket not found'
                ], 404);
            }

            $ticket->delete();

            return response()->json([
                'success' => true,
                'message' => 'Ticket deleted successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting ticket',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

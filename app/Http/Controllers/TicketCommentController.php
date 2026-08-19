<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketComment;
use Illuminate\Http\Request;

class TicketCommentController extends Controller
{
    /**
     * Display all comments from all tickets
     */
    public function allComments(Request $request)
    {
        $user = auth()->user();
        
        $query = TicketComment::with(['ticket', 'user'])
            ->orderBy('created_at', 'desc');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('comment', 'like', "%{$search}%");
        }

        // Filter by ticket
        if ($request->filled('ticket_id')) {
            $query->where('ticket_id', $request->ticket_id);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $comments = $query->paginate(10)->withQueryString();

        // Get tickets based on user permissions
        if ($user->hasPermissionTo('view all tickets')) {
            // Admin can see all tickets
            $tickets = Ticket::select('id', 'ticket_number', 'title')->get();
        } else {
            // Staff can only see tickets assigned to them
            $tickets = Ticket::select('id', 'ticket_number', 'title')
                ->where('assigned_to', $user->id)
                ->get();
        }
        
        $users = \App\Models\User::select('id', 'name')->get();

        return view('admin.ticketcomments.index', compact('comments', 'tickets', 'users'));
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Ticket $ticket)
    {
        $comments = $ticket->comments()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($comments);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Return form view if needed
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Ticket $ticket)
    {
        if (!auth()->user()->hasPermissionTo('add comments')) {
            if ($request->ajax()) {
                return response()->json(['message' => 'Unauthorized to add comments'], 403);
            }
            abort(403, 'Unauthorized to add comments');
        }

        // Check if user can access this ticket
        $user = auth()->user();
        if (!$user->hasPermissionTo('view all tickets') && $ticket->assigned_to !== $user->id) {
            if ($request->ajax()) {
                return response()->json(['message' => 'Unauthorized to comment on this ticket'], 403);
            }
            abort(403, 'Unauthorized to comment on this ticket');
        }

        $validated = $request->validate([
            'comment' => 'required|string',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['ticket_id'] = $ticket->id;

        $comment = TicketComment::create($validated);

        // Return JSON for AJAX requests
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Comment added successfully',
                'comment' => [
                    'id' => $comment->id,
                    'comment' => $comment->comment,
                    'user' => [
                        'id' => $comment->user->id,
                        'name' => $comment->user->name,
                    ],
                    'created_at' => $comment->created_at->format('M d, Y h:i A'),
                    'created_at_human' => $comment->created_at->diffForHumans(),
                ]
            ], 201);
        }

        return redirect()->back()->with('success', 'Comment added successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket, TicketComment $comment)
    {
        // Ensure comment belongs to the ticket
        if ($comment->ticket_id !== $ticket->id) {
            return response()->json([
                'message' => 'Comment not found for this ticket'
            ], 404);
        }

        $comment->load('user');

        return response()->json($comment);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Return form view if needed
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ticket $ticket, TicketComment $comment)
    {
        // Ensure comment belongs to the ticket
        if ($comment->ticket_id !== $ticket->id) {
            return response()->json([
                'message' => 'Comment not found for this ticket'
            ], 404);
        }

        // Check permission and ownership
        if (!auth()->user()->hasPermissionTo('update own comments') || $comment->user_id !== auth()->id()) {
            return response()->json([
                'message' => 'Unauthorized to update this comment'
            ], 403);
        }

        $validated = $request->validate([
            'comment' => 'required|string',
        ]);

        $comment->update($validated);

        return response()->json([
            'message' => 'Comment updated successfully',
            'comment' => $comment->load('user')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket, TicketComment $comment)
    {
        // Ensure comment belongs to the ticket
        if ($comment->ticket_id !== $ticket->id) {
            return redirect()->back()->withErrors(['error' => 'Comment not found for this ticket']);
        }

        // Check permission and ownership
        if (!auth()->user()->hasPermissionTo('delete own comments') || $comment->user_id !== auth()->id()) {
            return redirect()->back()->withErrors(['error' => 'Unauthorized to delete this comment']);
        }

        $comment->delete();

        return redirect()->route('tickets.show', $ticket)->with('success', 'Comment deleted successfully');
    }
}

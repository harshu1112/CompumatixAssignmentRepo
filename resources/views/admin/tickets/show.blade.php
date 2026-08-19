@extends('admin.layouts.partials.webmainlayout')

@section('title', 'View Ticket - Ticket Management System')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-ticket-alt me-2"></i>Ticket #{{ $ticket->ticket_number }}</h4>
                    <div>
                        @can('update tickets')
                            <a href="{{ route('tickets.edit', $ticket) }}" class="btn btn-sm btn-light">
                                <i class="fas fa-edit me-1"></i>Edit
                            </a>
                        @elsecan('update assigned tickets')
                            @if($ticket->assigned_to === auth()->id())
                                <a href="{{ route('tickets.edit', $ticket) }}" class="btn btn-sm btn-light">
                                    <i class="fas fa-edit me-1"></i>Edit
                                </a>
                            @endif
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <h5>{{ $ticket->title }}</h5>
                    <p class="text-muted">{{ $ticket->description }}</p>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Created By:</strong> {{ $ticket->creator->name }}</p>
                            <p><strong>Assigned To:</strong> {{ $ticket->assignee ? $ticket->assignee->name : 'Unassigned' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Created At:</strong> {{ $ticket->created_at->format('M d, Y h:i A') }}</p>
                            <p><strong>Updated At:</strong> {{ $ticket->updated_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Comments Section -->
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-comments me-2"></i>Comments ({{ $ticket->comments->count() }})</h5>
                </div>
                <div class="card-body">
                    @can('add comments')
                        <form id="commentForm" data-action="{{ route('tickets.comments.store', $ticket) }}" class="mb-4">
                            @csrf
                            <div class="mb-3">
                                <label for="comment" class="form-label">Add Comment</label>
                                <textarea class="form-control" 
                                          id="comment" name="comment" rows="3" required></textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>Post Comment
                            </button>
                        </form>
                        <hr>
                    @endcan

                    <div id="commentsContainer">
                        @forelse($ticket->comments as $comment)
                            <div class="card mb-3 comment-item">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">{{ $comment->user->name }}</h6>
                                            <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                                        </div>
                                        @if($comment->user_id === auth()->id())
                                            <div class="btn-group">
                                                @can('delete own comments')
                                                    <form action="{{ route('tickets.comments.destroy', [$ticket, $comment]) }}" 
                                                          method="POST" 
                                                          onsubmit="return confirm('Are you sure you want to delete this comment?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                            </div>
                                        @endif
                                    </div>
                                    <p class="mt-2 mb-0">{{ $comment->comment }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-4" id="noCommentsMessage">
                                <i class="fas fa-comment-slash fa-3x mb-3"></i>
                                <p>No comments yet. Be the first to comment!</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Ticket Details</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small">Priority</label>
                        <div>
                            @if($ticket->priority === 'urgent')
                                <span class="badge bg-danger">Urgent</span>
                            @elseif($ticket->priority === 'high')
                                <span class="badge bg-warning text-dark">High</span>
                            @elseif($ticket->priority === 'medium')
                                <span class="badge bg-info text-dark">Medium</span>
                            @else
                                <span class="badge bg-secondary">Low</span>
                            @endif
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small">Status</label>
                        <div>
                            @can('change ticket status')
                                <select id="statusSelect" class="form-select form-select-sm" data-ticket-id="{{ $ticket->id }}">
                                    <option value="open" {{ $ticket->status == 'open' ? 'selected' : '' }}>Open</option>
                                    <option value="in_progress" {{ $ticket->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="resolved" {{ $ticket->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                    <option value="closed" {{ $ticket->status == 'closed' ? 'selected' : '' }}>Closed</option>
                                </select>
                            @else
                                @if($ticket->status === 'open')
                                    <span class="badge bg-primary">Open</span>
                                @elseif($ticket->status === 'in_progress')
                                    <span class="badge bg-warning text-dark">In Progress</span>
                                @elseif($ticket->status === 'resolved')
                                    <span class="badge bg-success">Resolved</span>
                                @else
                                    <span class="badge bg-dark">Closed</span>
                                @endif
                            @endcan
                        </div>
                    </div>

                    <hr>

                    <a href="{{ route('tickets.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-arrow-left me-2"></i>Back to Tickets
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

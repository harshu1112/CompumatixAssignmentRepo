@extends('admin.layouts.partials.webmainlayout')

@section('title', 'All Ticket Comments - Ticket Management System')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="mb-0"><i class="fas fa-comments me-2"></i>All Ticket Comments</h2>
        </div>
        <div class="col-md-6 text-end">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCommentModal">
                <i class="fas fa-plus me-2"></i>Add Comment
            </button>
        </div>
    </div>

    <!-- Add Comment Modal -->
    <div class="modal fade" id="addCommentModal" tabindex="-1" aria-labelledby="addCommentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addCommentModalLabel">
                        <i class="fas fa-plus-circle me-2"></i>Add New Comment
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('tickets.comments.store', '__TICKET_ID__') }}" 
                      method="POST" 
                      id="commentForm" 
                      data-base-url="{{ route('tickets.comments.store', '__TICKET_ID__') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="ticket_id" class="form-label">Select Ticket <span class="text-danger">*</span></label>
                            <select class="form-select @error('ticket_id') is-invalid @enderror" 
                                    id="ticket_id" name="ticket_id" required>
                                <option value="">Choose a ticket...</option>
                                @foreach($tickets as $ticket)
                                    <option value="{{ $ticket->id }}">
                                        {{ $ticket->ticket_number }} - {{ $ticket->title }}
                                    </option>
                                @endforeach
                            </select>
                            @error('ticket_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="comment" class="form-label">Comment <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('comment') is-invalid @enderror" 
                                      id="comment" name="comment" rows="4" required 
                                      placeholder="Write your comment here...">{{ old('comment') }}</textarea>
                            @error('comment')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i>Post Comment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('comments.all') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Search Comment</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="{{ request('search') }}" placeholder="Search in comments...">
                </div>

                <div class="col-md-4">
                    <label for="ticket_id" class="form-label">Filter by Ticket</label>
                    <select class="form-select" id="ticket_id" name="ticket_id">
                        <option value="">All Tickets</option>
                        @foreach($tickets as $ticket)
                            <option value="{{ $ticket->id }}" {{ request('ticket_id') == $ticket->id ? 'selected' : '' }}>
                                {{ $ticket->ticket_number }} - {{ Str::limit($ticket->title, 30) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                @can('view all tickets')
                    <div class="col-md-4">
                        <label for="user_id" class="form-label">Filter by User</label>
                        <select class="form-select" id="user_id" name="user_id">
                            <option value="">All Users</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter me-2"></i>Apply Filters
                        </button>
                        <a href="{{ route('comments.all') }}" class="btn btn-secondary">
                            <i class="fas fa-redo me-2"></i>Reset
                        </a>
                    </div>
                @else
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-2"></i>Apply Filters
                            </button>
                            <a href="{{ route('comments.all') }}" class="btn btn-secondary">
                                <i class="fas fa-redo me-2"></i>Reset
                            </a>
                        </div>
                    </div>
                @endcan
            </form>
        </div>
    </div>

    <!-- Comments List -->
    <div class="card shadow-sm">
        <div class="card-body">
            @if($comments->count() > 0)
                <div class="row">
                    @foreach($comments as $comment)
                        <div class="col-md-12 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h6 class="mb-1">
                                                <i class="fas fa-user-circle me-1"></i>{{ $comment->user->name }}
                                            </h6>
                                            <small class="text-muted">
                                                <i class="far fa-clock me-1"></i>{{ $comment->created_at->format('M d, Y h:i A') }}
                                                <span class="mx-2">•</span>
                                                {{ $comment->created_at->diffForHumans() }}
                                            </small>
                                        </div>
                                        <div>
                                            <a href="{{ route('tickets.show', $comment->ticket) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-ticket-alt me-1"></i>{{ $comment->ticket->ticket_number }}
                                            </a>
                                        </div>
                                    </div>

                                    <div class="mb-2">
                                        <strong class="text-muted small">Ticket:</strong>
                                        <a href="{{ route('tickets.show', $comment->ticket) }}" class="text-decoration-none">
                                            {{ $comment->ticket->title }}
                                        </a>
                                    </div>

                                    <div class="border-start border-3 border-primary ps-3 py-2 bg-light">
                                        <p class="mb-0">{{ $comment->comment }}</p>
                                    </div>

                                    <div class="mt-2 d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-tag me-1"></i>{{ ucfirst($comment->ticket->priority) }}
                                            </span>
                                            <span class="badge bg-info">
                                                <i class="fas fa-flag me-1"></i>{{ ucfirst(str_replace('_', ' ', $comment->ticket->status)) }}
                                            </span>
                                        </div>
                                        <div>
                                            @if($comment->user_id === auth()->id())
                                                @can('delete own comments')
                                                    <form action="{{ route('tickets.comments.destroy', [$comment->ticket, $comment]) }}" 
                                                          method="POST" 
                                                          class="d-inline"
                                                          onsubmit="return confirm('Are you sure you want to delete this comment?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                            <i class="fas fa-trash me-1"></i>Delete
                                                        </button>
                                                    </form>
                                                @endcan
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $comments->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-comment-slash fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">No comments found</h5>
                    <p class="text-muted">Try adjusting your filters or search criteria</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

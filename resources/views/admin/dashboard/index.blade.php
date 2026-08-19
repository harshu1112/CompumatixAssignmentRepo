@extends('admin.layouts.partials.webmainlayout')

@section('title', 'Dashboard - Ticket Management System')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="mb-0"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</h2>
            <p class="text-muted">Welcome back, {{ auth()->user()->name }}!</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <!-- Total Tickets -->
        <div class="col-md-4 col-lg-2 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="fas fa-ticket-alt fa-3x text-primary mb-3"></i>
                    <h3 class="mb-1">{{ $totalTickets }}</h3>
                    <p class="text-muted mb-0">Total Tickets</p>
                </div>
            </div>
        </div>

        <!-- Open Tickets -->
        <div class="col-md-4 col-lg-2 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="fas fa-folder-open fa-3x text-info mb-3"></i>
                    <h3 class="mb-1">{{ $openTickets }}</h3>
                    <p class="text-muted mb-0">Open</p>
                </div>
            </div>
        </div>

        <!-- In Progress -->
        <div class="col-md-4 col-lg-2 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="fas fa-spinner fa-3x text-warning mb-3"></i>
                    <h3 class="mb-1">{{ $inProgressTickets }}</h3>
                    <p class="text-muted mb-0">In Progress</p>
                </div>
            </div>
        </div>

        <!-- Resolved -->
        <div class="col-md-4 col-lg-2 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                    <h3 class="mb-1">{{ $resolvedTickets }}</h3>
                    <p class="text-muted mb-0">Resolved</p>
                </div>
            </div>
        </div>

        <!-- Closed -->
        <div class="col-md-4 col-lg-2 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="fas fa-times-circle fa-3x text-secondary mb-3"></i>
                    <h3 class="mb-1">{{ $closedTickets }}</h3>
                    <p class="text-muted mb-0">Closed</p>
                </div>
            </div>
        </div>

        <!-- High/Urgent Priority -->
        <div class="col-md-4 col-lg-2 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                    <h3 class="mb-1">{{ $highUrgentTickets }}</h3>
                    <p class="text-muted mb-0">High/Urgent</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Tickets -->
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Recent Tickets</h5>
                    <a href="{{ route('tickets.index') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-eye me-1"></i>View All
                    </a>
                </div>
                <div class="card-body">
                    @if($recentTickets->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Ticket Number</th>
                                        <th>Title</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th>Assigned To</th>
                                        <th>Created</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentTickets as $ticket)
                                        <tr>
                                            <td>
                                                <span class="badge bg-secondary">{{ $ticket->ticket_number }}</span>
                                            </td>
                                            <td>{{ Str::limit($ticket->title, 40) }}</td>
                                            <td>
                                                @if($ticket->priority === 'urgent')
                                                    <span class="badge bg-danger">Urgent</span>
                                                @elseif($ticket->priority === 'high')
                                                    <span class="badge bg-warning text-dark">High</span>
                                                @elseif($ticket->priority === 'medium')
                                                    <span class="badge bg-info text-dark">Medium</span>
                                                @else
                                                    <span class="badge bg-secondary">Low</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($ticket->status === 'open')
                                                    <span class="badge bg-primary">Open</span>
                                                @elseif($ticket->status === 'in_progress')
                                                    <span class="badge bg-warning text-dark">In Progress</span>
                                                @elseif($ticket->status === 'resolved')
                                                    <span class="badge bg-success">Resolved</span>
                                                @else
                                                    <span class="badge bg-dark">Closed</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($ticket->assignee)
                                                    {{ $ticket->assignee->name }}
                                                @else
                                                    <span class="text-muted">Unassigned</span>
                                                @endif
                                            </td>
                                            <td>{{ $ticket->created_at->diffForHumans() }}</td>
                                            <td>
                                                <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">No tickets yet</h5>
                            @can('create tickets')
                                <a href="{{ route('tickets.create') }}" class="btn btn-primary mt-3">
                                    <i class="fas fa-plus me-2"></i>Create Your First Ticket
                                </a>
                            @endcan
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

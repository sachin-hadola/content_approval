<x-app-layout>
    <x-slot name="header">
        <h2 class="fs-4 fw-semibold text-dark mb-0">
            {{ __('Post Details') }}
        </h2>
    </x-slot>

    <div class="container py-4">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4 rounded-3" role="alert">
                <strong>Success!</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-8 mb-4">
                <div class="card shadow-sm border-0 rounded-4 h-100">
                    <div class="card-body p-4 p-md-5">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <h1 class="fs-2 fw-bold mb-0 text-dark">{{ $post->title }}</h1>
                            <span class="badge rounded-pill fs-6 
                                @if($post->status === 'pending') bg-warning text-dark 
                                @elseif($post->status === 'approved') bg-success 
                                @else bg-danger @endif">
                                {{ ucfirst($post->status) }}
                            </span>
                        </div>

                        <div class="text-secondary small mb-5 pb-4 border-bottom">
                            <ul class="list-unstyled mb-0 w-100 row gy-2">
                                <li class="col-sm-6"><i class="bi bi-person me-2"></i><strong>Author:</strong> {{ $post->user->name }}</li>
                                <li class="col-sm-6"><i class="bi bi-calendar3 me-2"></i><strong>Created:</strong> {{ $post->created_at->format('M d, Y h:i A') }}</li>
                                @if($post->status !== 'pending' && $post->approvedBy)
                                    <li class="col-sm-6"><i class="bi bi-person-check me-2"></i><strong>{{ ucfirst($post->status) }} by:</strong> {{ $post->approvedBy->name }}</li>
                                @endif
                            </ul>
                            
                            @if($post->status === 'rejected' && $post->rejected_reason)
                                <div class="mt-3 p-3 bg-danger-subtle text-danger rounded-3 border-0 shadow-sm">
                                    <strong><i class="bi bi-exclamation-circle-fill me-2"></i>Rejection Reason:</strong> {{ $post->rejected_reason }}
                                </div>
                            @endif
                        </div>

                        <div class="fs-5 text-dark lh-lg mb-5">
                            {!! nl2br(e($post->body)) !!}
                        </div>

                        <!-- Manager/Admin Actions -->
                        @if (in_array($user->role, ['manager', 'admin']) && $post->status === 'pending')
                            <div class="mt-5 p-4" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px;">
                                <h5 class="fw-bold mb-4 d-flex align-items-center" style="color: #0f172a; font-size: 1.15rem;">
                                    <i class="bi bi-shield-check me-2" style="color: #3b82f6; font-size: 1.3rem;"></i>Manager Actions
                                </h5>
                                <div class="d-flex flex-column flex-md-row gap-2 align-items-stretch">
                                    <!-- Approve -->
                                    <div style="flex: 0 0 32%;">
                                        <form action="{{ route('posts.approve', $post->id) }}" method="POST" class="h-100">
                                            @csrf
                                            <button type="submit" class="btn w-100 fw-bold d-flex align-items-center justify-content-center shadow-none" style="background-color: #16a34a; color: white; border-radius: 6px; font-size: 1rem; border: none;">
                                                <i class="bi bi-check2 me-2 mt-1 fs-5"></i> Approve Post
                                            </button>
                                        </form>
                                    </div>
                                    <!-- Reject -->
                                    <div style="flex: 1;">
                                        <form action="{{ route('posts.reject', $post->id) }}" method="POST" class="d-flex flex-column h-100">
                                            @csrf
                                            <textarea name="rejected_reason" placeholder="Reason for rejection (required)" class="form-control flex-grow-1 mb-2 shadow-none" style="border: 1px solid #cbd5e1; border-radius: 6px; resize: vertical; min-height: 60px; font-size: 0.95rem; color: #475569;" required></textarea>
                                            <button type="submit" class="btn fw-bold shadow-none d-flex justify-content-center align-items-center" style="background-color: #dc2626; color: white; border-radius: 6px; padding-top: 0.5rem; padding-bottom: 0.5rem; font-size: 1rem; border: none;width: 150px;">
                                                <i class="bi bi-x-lg me-2" style="font-size: 0.85rem;"></i> Reject Post
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="mt-5 pt-3">
                            <a href="{{ route('dashboard') }}" class="btn btn-link text-decoration-none p-0 text-secondary hover-primary">
                                <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activity Logs Sidebar -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-2 px-4">
                        <h3 class="fs-5 fw-bold text-dark mb-0"><i class="bi bi-clock-history me-2 text-primary"></i>Activity Logs</h3>
                    </div>
                    <div class="card-body px-4 pb-4">
                        @if($post->logs->count() > 0)
                            <div class="position-relative ms-3 border-start border-2 border-primary-subtle border-opacity-50 pb-2">
                                @foreach($post->logs as $log)
                                    <div class="position-relative mb-4 ps-4">
                                        <span class="position-absolute top-0 start-0 translate-middle p-1 bg-white border border-2 rounded-circle
                                            @if($log->action === 'created') border-primary
                                            @elseif($log->action === 'approved') border-success
                                            @elseif($log->action === 'rejected') border-danger
                                            @elseif($log->action === 'updated') border-warning
                                            @else border-secondary @endif" style="width: 14px; height: 14px; margin-left: -1px; margin-top: 5px;">
                                        </span>
                                        <div class="text-secondary small fw-bold mb-1">{{ $log->created_at->format('M d, Y h:i A') }}</div>
                                        <div class="text-dark small">
                                            <strong>{{ $log->user ? $log->user->name : 'System' }}</strong> 
                                            <span class="fw-bold
                                                @if($log->action === 'created') text-primary
                                                @elseif($log->action === 'approved') text-success
                                                @elseif($log->action === 'rejected') text-danger
                                                @elseif($log->action === 'updated') text-warning
                                                @else text-secondary @endif
                                            ">{{ $log->action }}</span> this post.
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-journal-x fs-1 opacity-50 mb-2 d-block"></i>
                                <p>No activity logged.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <h2 class="fs-4 fw-semibold text-dark mb-0">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="container py-4">
        <!-- Dashboard Header Actions -->
        <div class="card shadow-sm border-0 mb-4 bg-white rounded-4">
            <div class="card-body p-4 d-flex justify-content-between align-items-center">
                <div class="text-secondary fs-5">
                    {{ __("You're logged in as a: ") }} 
                    <span class="badge bg-primary rounded-pill px-3 py-2 ms-2 fs-6">
                        {{ ucfirst($user->role) }}
                    </span>
                </div>
                @if ($user->role === 'author')
                    <a href="{{ route('posts.create') }}" class="btn btn-primary d-inline-flex align-items-center px-4 py-2 shadow-sm rounded-3 fw-bold">
                        <i class="bi bi-plus-lg me-2"></i> Create New Post
                    </a>
                @endif
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4 rounded-3" role="alert">
                <strong>Success!</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow-sm border-0 bg-white rounded-4">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-2 px-4">
                <h3 class="fs-5 fw-bold text-dark mb-0">
                    @if ($user->role === 'author')
                        <i class="bi bi-journal-text me-2 text-primary"></i> Your Posts
                    @else
                        <i class="bi bi-inbox me-2 text-primary"></i> All Posts Awaiting Approval
                    @endif
                </h3>
            </div>
            
            <div class="card-body px-4 pb-4">
                @if($posts->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="text-secondary text-uppercase small fw-bold tracking-wider py-3 rounded-start">Title</th>
                                    @if ($user->role !== 'author')
                                        <th scope="col" class="text-secondary text-uppercase small fw-bold tracking-wider py-3">Author</th>
                                    @endif
                                    <th scope="col" class="text-secondary text-uppercase small fw-bold tracking-wider py-3">Status</th>
                                    <th scope="col" class="text-secondary text-uppercase small fw-bold tracking-wider py-3">Date</th>
                                    <th scope="col" class="text-secondary text-uppercase small fw-bold tracking-wider text-end py-3 rounded-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="border-top-0">
                                @foreach ($posts as $post)
                                    <tr>
                                        <td class="py-3">
                                            <div class="fw-semibold text-dark">{{ Str::limit($post->title, 40) }}</div>
                                        </td>
                                        @if ($user->role !== 'author')
                                            <td class="py-3">
                                                <div class="text-muted">{{ $post->user->name }}</div>
                                            </td>
                                        @endif
                                        <td class="py-3">
                                            <span class="badge rounded-pill
                                                @if($post->status === 'pending') bg-warning text-dark
                                                @elseif($post->status === 'approved') bg-success 
                                                @else bg-danger @endif">
                                                {{ ucfirst($post->status) }}
                                            </span>
                                        </td>
                                        <td class="py-3 text-muted">
                                            {{ $post->created_at->format('M d, Y') }}
                                        </td>
                                        <td class="py-3 text-end">
                                            <a href="{{ route('posts.show', $post->id) }}" class="btn btn-sm btn-outline-primary me-2">View</a>
                                            
                                            @if ($user->role === 'author')
                                                <a href="{{ route('posts.edit', $post->id) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                            @endif
                                            
                                            @if ($user->role === 'admin')
                                                <form action="{{ route('posts.destroy', $post->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this post?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger ms-2">Delete</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 d-flex justify-content-end">
                        {{ $posts->links('pagination::bootstrap-5') }}
                    </div>
                @else
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-folder2-open fs-1 text-secondary opacity-50 mb-3 d-block"></i>
                        <p class="fs-5">No posts found.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <h2 class="fs-4 fw-semibold text-dark mb-0">
            {{ __('Edit Post') }}
        </h2>
    </x-slot>

    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-body p-4 p-md-5">
                        
                        @if($post->status === 'rejected')
                            <div class="alert alert-danger shadow-sm border-0 mb-4 rounded-3 text-danger bg-danger-subtle">
                                <h5 class="alert-heading text-danger fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i> This post was rejected</h5>
                                <p class="mb-0"><strong>Reason:</strong> {{ $post->rejected_reason }}</p>
                                <hr class="border-danger opacity-25">
                                <p class="mb-0 small">Updating this post will automatically resubmit it for approval.</p>
                            </div>
                        @endif

                        <form action="{{ route('posts.update', $post->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="mb-4">
                                <label for="title" class="form-label fw-bold text-secondary">Title</label>
                                <input type="text" name="title" id="title" class="form-control form-control-lg @error('title') is-invalid @enderror" required value="{{ old('title', $post->title) }}">
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="body" class="form-label fw-bold text-secondary">Content</label>
                                <textarea name="body" id="body" rows="8" class="form-control @error('body') is-invalid @enderror" required>{{ old('body', $post->body) }}</textarea>
                                @error('body')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-end align-items-center mt-5 pt-3 border-top">
                                <a href="{{ route('dashboard') }}" class="btn btn-light me-3 px-4">Cancel</a>
                                <button type="submit" class="btn btn-primary px-4 fw-semibold shadow-sm">
                                    <i class="bi bi-arrow-repeat me-2"></i>Update & Resubmit
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

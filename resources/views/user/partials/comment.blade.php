<div class="comment mt-3 border p-2 rounded">
    <strong>{{ $comment->user->name }}</strong> 
    <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
    <p>{{ $comment->body }}</p>

    <div>
        <a href="#" class="reply-toggle btn btn-sm btn-link">Reply</a>

        @if (auth()->check() && auth()->id() === $post->user_id)
            <form action="{{ route('comments.destroy', $comment->id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this comment?')">Delete</button>
            </form>
        @endif
    </div>

    <!-- Reply form (initially hidden, toggled via JS) -->
    <div class="reply-form mt-2 d-none">
        <form action="{{ route('comments.store', $post->id) }}" method="POST">
            @csrf
            <div class="form-group">
                <textarea name="body" class="form-control" rows="2" placeholder="Write a reply..."></textarea>
                <input type="hidden" name="parent_id" value="{{ $comment->id }}">
            </div>
            <button type="submit" class="btn btn-sm btn-primary">Post Reply</button>
        </form>
    </div>

    <!-- Recursive display of child comments -->
    @if ($comment->replies)
        <div class="nested-comment mt-3 ml-4">
            @foreach ($comment->replies as $reply)
                @include('user.partials.comment', ['comment' => $reply, 'post' => $post])
            @endforeach
        </div>
    @endif
</div>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>All Posts</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .post-tile {
            background-color: #f3f4f6;
            border: 1px solid #e5e7eb;
            border-radius: 5px;
            margin-bottom: 20px;
            padding: 20px;
            display: flex;
            flex-direction: column;
        }
        .post-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .post-content {
            font-size: 16px;
            flex-grow: 1;
        }
        .nested-comment {
            margin-left: 40px;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#"><b>Blog Site</b></a>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item"><a class="nav-link active" href="dashboard">My Dashboard</a></li>
                <li class="nav-item"><a class="nav-link active" href="dashall">All Posts</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('user.bookmarks') }}">My Bookmarks</a></li>
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown">
                        @if ($LoggedUser && $LoggedUser['img'])
                            <img src="{{ asset('/' . $LoggedUser['img']) }}" width="30" height="30" class="rounded-circle mr-2" alt="Profile Image">
                        @else
                            <img src="{{ asset('/uploads/default.jpg') }}" width="30" height="30" class="rounded-circle mr-2" alt="Default Profile">
                        @endif
                        @if($LoggedUser)
                            <span class="text-dark">{{ $LoggedUser['name'] }}</span>
                        @endif
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <form id="logout-form" action="{{ route('user.logout') }}" method="post">
                            @csrf
                            <button type="submit" class="dropdown-item ai-icon" style="border: none; background: none;">
                                <svg xmlns="http://www.w3.org/2000/svg" class="text-danger" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                    <polyline points="16 17 21 12 16 7"></polyline>
                                    <line x1="21" y1="12" x2="9" y2="12"></line>
                                </svg>
                                <span class="ml-2">Logout</span>
                            </button>
                        </form>
                    </div>
                </li>
            </ul>
        </div>
    </nav>

    <main role="main" class="container">
        <div class="my-3 bg-white rounded box-shadow">
            <h1 class="text-center">Our Blog Site</h1>
            <h6 class="border-bottom border-gray pb-2 mb-4">Recent updates</h6>

            <form method="GET" action="{{ route('user.dashall') }}" class="mb-4">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search posts..." value="{{ request('search') }}">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="submit">Search</button>
                    </div>
                </div>
            </form>

            <div class="row">
                @foreach ($posts as $post)
                    @php $user = $post->user; @endphp
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            @if($post->img)
                                <img src="{{ asset('/' . $post->img) }}" alt="Post Image" class="img-fluid mb-2" style="max-height: 300px;">
                            @endif
                            <div class="card-body">
                                <h5 class="card-title">Created By: {{ $user->name ?? 'Unknown User' }}</h5>
                                <h6 class="card-subtitle mb-2 text-muted">Title: {{ $post->title }}</h6>
                                <p class="card-text">Content: {{ $post->content }}</p>
                                <p class="card-text">Created At: {{ $post->created_at }}</p>
                                @if($post->tags->count())
                                    <p>
                                        Tags:
                                        @foreach ($post->tags as $tag)
                                            <span class="badge badge-info">{{ $tag->name }}</span>
                                        @endforeach
                                    </p>
                                @endif

                                @php
                                    $liked = $post->likes->where('user_id', $LoggedUser->id)->count() > 0;
                                @endphp
                                <form class="like-form" data-post-id="{{ $post->id }}">
                                    @csrf
                                    <button type="submit" class="btn btn-sm {{ $liked ? 'btn-danger' : 'btn-outline-primary' }}">
                                        {{ $liked ? 'Unlike' : 'Like' }}
                                    </button>
                                    <span class="like-count">{{ $post->likes->count() }}</span> Likes
                                </form>

                                <form action="{{ route('bookmark.toggle', $post->id) }}" method="POST">
                                    @csrf
                                    @php
                                        $isBookmarked = $post->bookmarks->contains('user_id', $LoggedUser->id);
                                    @endphp
                                    <button type="submit" class="btn btn-sm {{ $isBookmarked ? 'btn-danger' : 'btn-outline-primary' }}">
                                        {{ $isBookmarked ? 'Unbookmark' : 'Bookmark' }}
                                    </button>
                                </form>

                                <div class="mt-3">
                                    <form action="{{ route('comments.store', $post->id) }}" method="POST">
                                        @csrf
                                        <textarea name="body" class="form-control mb-2" placeholder="Add a comment..."></textarea>
                                        <input type="hidden" name="parent_id" value="">
                                        <button type="submit" class="btn btn-sm btn-primary">Post Comment</button>
                                    </form>

                                    <div class="mt-3">
                                        @foreach ($post->comments as $comment)
                                            @include('user.partials.comment', ['comment' => $comment, 'post' => $post])
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            {{ $posts->links() }}
        </div>
    </main>

    <!-- jQuery and Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Like AJAX -->
    <script>
        $(document).ready(function () {
            $('.like-form').on('submit', function (e) {
                e.preventDefault();
                var form = $(this);
                var postId = form.data('post-id');
                var token = form.find('input[name="_token"]').val();

                $.ajax({
                    url: "{{ route('post.like.toggle') }}",
                    type: 'POST',
                    data: {
                        _token: token,
                        post_id: postId
                    },
                    success: function (response) {
                        if (response.liked) {
                            form.find('button').removeClass('btn-outline-primary').addClass('btn-danger').text('Unlike');
                        } else {
                            form.find('button').removeClass('btn-danger').addClass('btn-outline-primary').text('Like');
                        }
                        form.find('.like-count').text(response.likeCount);
                    }
                });
            });

            // Toggle reply form
            $(document).on('click', '.reply-toggle', function (e) {
                e.preventDefault();
                $(this).closest('.comment').find('.reply-form').first().slideToggle();
            });
        });
    </script>
</body>
</html>

<?php
namespace App\Http\Controllers;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller{
    public function store(Request $request, Post $post){
        $request->validate(['body' => 'required']);

        Comment::create([
            'body' => $request->body,
            'user_id' => session('LoggedUser'),
            'post_id' => $post->id,
            'parent_id' => $request->parent_id // can be null for top-level comments
        ]);
        return back();
    }

    public function destroy(Comment $comment){
        $userId = session('LoggedUser');
        if ($comment->post->user_id === $userId) {
            $comment->delete();
        }
        return back();
    }
}

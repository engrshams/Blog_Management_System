<?php
namespace App\Http\Controllers;
use App\Models\Post;
use App\Models\User;
use App\Models\Bookmark;
use Illuminate\Http\Request;

class BookmarkController extends Controller{
    public function toggle(Request $request, Post $post){
        $user = User::find(session('LoggedUser'));
        $bookmark = Bookmark::where('user_id', $user->id)->where('post_id', $post->id)->first();
        if ($bookmark) {
            $bookmark->delete();
            return back()->with('success', 'Bookmark removed');
        } else {
            Bookmark::create([
                'user_id' => $user->id,
                'post_id' => $post->id,
            ]);
            return back()->with('success', 'Post bookmarked');
        }
    }

    public function bookmarkedPosts(){
        $user = User::find(session('LoggedUser'));
        $bookmarkedPosts = $user->bookmarks()->with('post.user')->latest()->simplePaginate(3);

        return view('user.bookmarks', [
            'LoggedUser' => $user,
            'bookmarkedPosts' => $bookmarkedPosts
        ]);
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Comment;

class CommentController extends Controller
{
    // Get all posts' comments
    public function index($id)
    {
        $post = Post::find($id);

        if(!$post)
        {
            return response([
                'message' => 'Oops! Couldn’t find post.'
            ], 403);
        }

        return response([
            'comments' => $post->comments()->with('user:id,name,image')->get()
        ], 200);
    }

    // Create a new comment
    public function store(Request $request, $id)
    {
        $post = Post::find($id);

        if(!$post)
        {
            return response([
                'message' => 'Oops! Couldn’t find post.'
            ], 403);
        }

        // Field validation
        $attrs = $request->validate([
            'comment' => 'required|string'
        ]);

        Comment::create([
            'comment' => $attrs['comment'],
            'post_id' => $id,
            'enjoyer_id' => auth()->user()->id
        ]);

        return response([
            'message' => 'Your comment has been added.',
        ], 200);
    }

    // Edit comment
    public function update(Request $request, $id)
    {
        $comment = Comment::find($id);

        if(!$comment)
        {
            return response([
                'message' => 'Oops! Couldn’t find comment.'
            ], 403);
        }

        if($comment->enjoyer_id != auth()->user()->id)
        {
            return response([
                'message' => 'Oops! It seems you don’t have the right permissions for this action.'
            ], 403);
        }

        // Field validation
        $attrs = $request->validate([
            'comment' => 'required|string'
        ]);

        $comment->update([
            'comment' => $attrs['comment']
        ]);

        return response([
            'message' => 'Comment was updated.'
        ], 200);
    }

    // Delete comment
    public function destroy($id)
    {
        $comment = Comment::find($id);

        if(!$comment)
        {
            return response([
                'message' => 'Oops! Couldn’t find comment.'
            ], 403);
        }

        if($comment->enjoyer_id != auth()->user()->id)
        {
            return response([
                'message' => 'Oops! It seems you don’t have the right permissions for this action.'
            ], 403);
        }

        $comment->delete();

        return response([
            'message' => 'Comment has been deleted.'
        ], 200);
    }
}

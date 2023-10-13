<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Post;

class PostController extends Controller
{
    // Get all posts
    public function index()
    {
        return response([
            'posts' => Post::orderBy('created_at', 'desc')->with('user:id,name,image')->withCount('comments', 'likes')
            ->with('likes', function($like){
                return $like->where('enjoyer_id', auth()->user()->id)
                    ->select('id', 'enjoyer_id', 'post_id')->get();
            })
            ->get()
        ], 200);
    }

    // Get single post
    public function show($id)
    {
        return response([
            'post' => Post::where('id', $id)->withCount('comments', 'likes')->get()
        ], 200);
    }

    // Create a new post
    public function store(Request $request)
    {
        // Field validation
        $attrs = $request->validate([
            'body' => 'required|string'
        ]);

        $image = $this->saveImage($request->$image, 'posts');

        $post = Post::create([
            'body' => $attrs['body'],
            'enjoyer_id' => auth()->user()->id,
            'image' => $image
        ]);

        return response([
            'message' => 'Congrats! Your post has been published.',
            'post' => $post
        ], 200);
    }

    // Edit post
    public function update(Request $request, $id)
    {
        $post = Post::find($id);

        if(!$post)
        {
            return response([
                'message' => 'Oops! Couldn’t find post.'
            ], 403);
        }

        if($post->enjoyer_id != auth()->user()->id)
        {
            return response([
                'message' => 'Oops! It seems you don’t have the right permissions for this action.'
            ], 403);
        }

        // Field validation
        $attrs = $request->validate([
            'body' => 'required|string'
        ]);

        $post->update([
            'body' => $attrs['body']
        ]);

        

        return response([
            'message' => 'Post was updated.',
            'post' => $post
        ], 200);
    }

    // Delete post
    public function destroy($id)
    {
        $post = Post::find($id);

        if(!$post)
        {
            return response([
                'message' => 'Oops! Couldn’t find post.'
            ], 403);
        }

        if($post->enjoyer_id != auth()->user()->id)
        {
            return response([
                'message' => 'Oops! It seems you don’t have the right permissions for this action.'
            ], 403);
        }

        $post->comments()->delete();
        $post->likes()->delete();
        $post->delete();

        return response([
            'message' => 'Post has been deleted.',
        ], 200);
    }
}
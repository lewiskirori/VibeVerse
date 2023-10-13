<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Like;

class LikeController extends Controller
{
    // Like or dislike
    public function likeOrDislike($id)
    {
        $post = Post::find($id);

        if(!$post)
        {
            return response([
                'message' => 'Oops! Couldn’t find post.'
            ], 403);
        }

        $like = $post->likes()->where('enjoyer_id', auth()->user()->id)->first();

        // Like react
        if(!$like)
        {
            Like::create([
                'post_id' => $id,
                'enjoyer_id' => auth()->user()->id
            ]);

            return response([
                'message' => 'I like this'
            ], 200);
        }

        // Dislike
        $like->delete();

        return response([
            'message' => 'I dislike this'
        ], 200);
    }
}

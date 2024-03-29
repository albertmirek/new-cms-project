<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class PostController extends Controller
{



    public function show(Post $post){
        return view('blog-post', ['post'=>$post]);
    }


    public function create(Post $post){
        return view('admin.posts.create');
    }

    public function store(){

        $inputs =  \request()->validate([

            'title'=>'required|min:8|max:255',
            'post_image'=>'file',
            'body' => 'required'
        ]);

        if(\request('post_image')){
            $inputs['post_image'] = \request('post_image')->store('images');
        }

        \auth()->user()->posts()->create($inputs);
        session()->flash('post-created-message','Post with title "'. $inputs['title'] .'" was created');

        return redirect()->route('post.index');

    }

    public function index(){
        $posts = \auth()->user()->posts;


        return view('admin.posts.index',['posts' => $posts]);

    }

    public function destroy(Post $post, Request $request){

        $post->delete();

        $request->session()->flash('message','Post was deleted');
        //Session::flash('message','Post was deleted');

        return back();
    }

    public function edit(Post $post){

        $this->authorize('view', $post);

        return view('admin.posts.edit',['post' => $post]);

    }

    public function update(Post $post){

        $inputs =  \request()->validate([
            'title'=>'required|min:8|max:255',
            'post_image'=>'file',
            'body' => 'required'
        ]);

        if(\request('post_image')){
            $inputs['post_image'] = \request('post_image')->store('images');
            $post->post_image = $inputs['post_image'];
        }

        $post->title = $inputs['title'];
        $post->body = $inputs['body'];

        //\auth()->user()->posts()->save($post);

        $this->authorize('update', $post);

        $post->save();

        session()->flash('post-updated-message','Post with title "'. $inputs['title'] .'" was updated');
        return redirect()->route('post.index');

    }

}

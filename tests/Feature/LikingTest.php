<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\User;
use App\Post;
use App\Comment;

class LikingTest extends TestCase
{
    use RefreshDatabase;

    public function testAUserCanLikeAPost()
    {
        $this->actingAs(factory(User::class)->create());

        $post = factory(Post::class)->create();

        $post->like();

        $this->assertCount(1, $post->likes);
        $this->assertTrue($post->likes->contains('id', auth()->id()));
    }

    public function testAUserCanLikeAComment()
    {
        $this->actingAs(factory(User::class)->create());

        $comment = factory(Comment::class)->create();

        $comment->like();

        $this->assertCount(1, $comment->likes);
        $this->assertTrue($comment->likes->contains('id', auth()->id()));
    }
}

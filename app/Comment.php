<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    /**
     * Like the current comment.
     *
     * @return void
     */
    public function like($user = null)
    {
        $user = $user ?: auth()->user();

        $this->likes()->attach($user);
    }

    /**
     * Define the relationship between the given "likable" (comment)
     * and the "likes" associated with it.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function likes()
    {
        return $this->morphToMany(User::class, 'likable')->withTimestamps();
    }
}

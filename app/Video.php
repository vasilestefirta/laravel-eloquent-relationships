<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    /**
     * Define the relationship between the given video
     * and the "watchable" is associated with.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function watchable()
    {
        return $this->morphTo();
    }
}

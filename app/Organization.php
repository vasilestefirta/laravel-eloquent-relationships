<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    /**
     * Define the relationship between the given organization and
     * all the users associated with it.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class)
            ->using(OrganizationUser::class)
            ->as('relationship')
            ->withPivot([
                'id',
                'role',
            ])
            ->withTimestamps();
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class OrganizationUser extends Pivot
{
    /**
     * Indicates if the IDs are auto-incrementing.
     * @see https://laravel.com/docs/5.8/eloquent-relationships#defining-custom-intermediate-table-models
     * @var bool
     */
    public $incrementing = true;

    /**
     * Define the relationship between the given organization user
     * and the content he/she has access to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function content()
    {
        return $this->hasMany(Content::class, 'organization_user_id');
    }
}

<?php

use App\Organization;
use App\OrganizationUser;
use App\User;
use Faker\Generator as Faker;

$factory->define(OrganizationUser::class, function (Faker $faker) {
    return [
        'organization_id' => function () {
            return factory(Organization::class)->create()->id;
        },
        'user_id' => function () {
            return factory(User::class)->create()->id;
        },
        'role' => 'consumer',
    ];
});

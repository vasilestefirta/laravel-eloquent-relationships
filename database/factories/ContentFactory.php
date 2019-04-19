<?php

use App\Content;
use App\OrganizationUser;
use Faker\Generator as Faker;

$factory->define(Content::class, function (Faker $faker) {
    return [
        'organization_user_id' => function () {
            return factory(OrganizationUser::class)->create()->id;
        },
        'content' => $faker->sentence,
    ];
});

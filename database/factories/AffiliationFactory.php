<?php

use Faker\Generator as Faker;

$factory->define(App\Affiliation::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->word,
    ];
});

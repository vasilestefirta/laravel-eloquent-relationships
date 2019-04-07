<?php

use Faker\Generator as Faker;

$factory->define(App\Series::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->sentence(2),
    ];
});

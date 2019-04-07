<?php

use Faker\Generator as Faker;

$factory->define(App\Video::class, function (Faker $faker) {
    $watchableTypes = [
        App\Collection::class,
        App\Series::class,
    ];

    $watchableType = $faker->randomElement($watchableTypes);
    $watchableId = factory($watchableType)->create()->id;

    return [
        'title' => $faker->sentence,
        'description' => $faker->sentence,
        'watchable_type' => function () use ($watchableType) {
            return $watchableType;
        },
        'watchable_id' => function () use ($watchableId) {
            return $watchableId;
        },
    ];
});

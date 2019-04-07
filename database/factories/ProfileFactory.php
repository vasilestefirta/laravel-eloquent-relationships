<?php

use Faker\Generator as Faker;

$factory->define(App\Profile::class, function (Faker $faker) {
    $name = $faker->word;

    return [
        'user_id' => function () {
            return factory(App\User::class)->create()->id;
        },
        'website_url' => $faker->url,
        'github_url' => 'https://github.com/' . $name,
        'twitter_url' => 'https://twitter.com/' . $name,
    ];
});

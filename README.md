# Eloquent Relationships

## One to One

We'll use a relationship between a `User` and a `Profile` model as an example:

### 1. Table schemas

`users` table:

```php
Schema::create('users', function (Blueprint $table) {
    $table->bigIncrements('id');
    $table->string('name');
    $table->string('email')->unique();
    $table->timestamp('email_verified_at')->nullable();
    $table->string('password');
    $table->rememberToken();
    $table->timestamps();
});
```

`profiles` table:

```php
Schema::create('profiles', function (Blueprint $table) {
    $table->bigIncrements('id');
    $table->unsignedInteger('user_id');
    $table->string('website_url');
    $table->string('github_url');
    $table->string('twitter_url');
    $table->timestamps();
});
```

### 2. Models:

`User.php` model:

```php
namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * Define the relationship between the given user and its profile.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }
}
```

`Profile.php` model:

```php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    /**
     * Define the relationship between the given profile
     * and the user it belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

### 3. Usage

Query the `profile` associated with a given `user` (output from `php artisan tinker`):

```php
>>> DB::enableQueryLog();
=> null
>>> $user = App\User::first();
=> App\User {#2964
     id: "1",
     name: "Brayan Dare Jr.",
     email: "madonna.blick@example.org",
     email_verified_at: "2019-04-06 23:35:28",
     created_at: "2019-04-06 23:35:28",
     updated_at: "2019-04-06 23:35:28",
   }
>>> $user->profile;
=> App\Profile {#2956
     id: "1",
     user_id: "1",
     website_url: "https://example.com",
     github_url: "https:/github.com/example",
     twitter_url: "https://twitter.com/example",
     created_at: "2019-04-06 23:35:28",
     updated_at: "2019-04-06 23:35:28",
   }
>>> DB::getQueryLog();
=> [
     [
       "query" => "select * from `users` limit 1",
       "bindings" => [],
       "time" => 0.32,
     ],
     [
       "query" => "select * from `profiles` where `profiles`.`user_id` = ? and `profiles`.`user_id` is not null limit 1",
       "bindings" => [
         1,
       ],
       "time" => 0.17,
     ],
   ]
>>>
```

SQL queries beautified:

```sql
-- Fetch user:
SELECT
	*
FROM
	`users`
LIMIT 1;

-- Fetch profile:
SELECT
	*
FROM
	`profiles`
WHERE
	`profiles`.`user_id` = 1
	AND `profiles`.`user_id` IS NOT NULL
LIMIT 1;
```

Query the `user` associated with a given `profile` (output from `php artisan tinker`):

```php
>>> DB::enableQueryLog();
=> null
>>> $profile = App\Profile::first();
=> App\Profile {#2940
     id: "1",
     user_id: "1",
     website_url: "https://example.com",
     github_url: "https:/github.com/example",
     twitter_url: "https://twitter.com/example",
     created_at: "2019-04-06 23:35:28",
     updated_at: "2019-04-06 23:35:28",
   }
>>> $profile->user;
=> App\User {#2941
     id: "1",
     name: "Brayan Dare Jr.",
     email: "madonna.blick@example.org",
     email_verified_at: "2019-04-06 23:35:28",
     created_at: "2019-04-06 23:35:28",
     updated_at: "2019-04-06 23:35:28",
   }
>>> DB::getQueryLog();
=> [
     [
       "query" => "select * from `profiles` limit 1",
       "bindings" => [],
       "time" => 0.58,
     ],
     [
       "query" => "select * from `users` where `users`.`id` = ? limit 1",
       "bindings" => [
         "1",
       ],
       "time" => 0.2,
     ],
   ]
>>>
```

SQL queries beautified:

```sql
-- Fetch profile:
SELECT
	*
FROM
	`profiles`
LIMIT 1;

-- Fetch user:
SELECT
	*
FROM
	`users`
WHERE
	`users`.`id` = 1
LIMIT 1
```

## One to Many

We'll use a relationship between a `User` and a `Post` model as an example:

### 1. Table schemas

`users` table:

```php
Schema::create('users', function (Blueprint $table) {
    $table->bigIncrements('id');
    $table->string('name');
    $table->string('email')->unique();
    $table->timestamp('email_verified_at')->nullable();
    $table->string('password');
    $table->rememberToken();
    $table->timestamps();
});
```

`posts` table:

```php
Schema::create('posts', function (Blueprint $table) {
    $table->bigIncrements('id');
    $table->unsignedInteger('user_id');
    $table->string('title');
    $table->string('body');
    $table->timestamps();
});
```

### 2. Models:

`User.php` model:

```php
namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * Define the relationship between the given user and the posts he/she created.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
```

`Post.php` model:

```php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    /**
     * Define the relationship between the given post
     * and the user it was created by.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

### 3. Usage

Query a list of `posts` created by the given `user` (output from `php artisan tinker`):

```php
>>> DB::enableQueryLog();
=> null
>>> $user = App\User::first();
=> App\User {#2963
     id: "1",
     name: "Brayan Dare Jr.",
     email: "madonna.blick@example.org",
     email_verified_at: "2019-04-06 23:35:28",
     created_at: "2019-04-06 23:35:28",
     updated_at: "2019-04-06 23:35:28",
   }
>>> $user->posts;
=> Illuminate\Database\Eloquent\Collection {#2956
     all: [
       App\Post {#2965
         id: "1",
         user_id: "1",
         title: "Quia rerum voluptas quis dolores corrupti dolores autem.",
         body: "Deleniti commodi et et ut autem magnam fugit.",
         created_at: "2019-04-07 00:27:43",
         updated_at: "2019-04-07 00:27:43",
       },
       App\Post {#2966
         id: "2",
         user_id: "1",
         title: "Commodi dicta ut aperiam et est.",
         body: "Excepturi iusto labore quasi cumque fugiat.",
         created_at: "2019-04-07 00:27:43",
         updated_at: "2019-04-07 00:27:43",
       },
     ],
   }
>>> DB::getQueryLog();
=> [
     [
       "query" => "select * from `users` limit 1",
       "bindings" => [],
       "time" => 0.25,
     ],
     [
       "query" => "select * from `posts` where `posts`.`user_id` = ? and `posts`.`user_id` is not null",
       "bindings" => [
         1,
       ],
       "time" => 0.17,
     ],
   ]
>>>
```

SQL queries beautified:

```sql
-- Fetch user:
SELECT
	*
FROM
	`users`
LIMIT 1;

-- Fetch posts:
SELECT
	*
FROM
	`posts`
WHERE
	`posts`.`user_id` = 1
	AND `posts`.`user_id` IS NOT NULL;
```

Query the `user` who created a given `post` (output from `php artisan tinker`):

```php
>>> DB::enableQueryLog();
=> null
>>> $post = App\Post::first();
=> App\Post {#2964
     id: "1",
     user_id: "1",
     title: "Quia rerum voluptas quis dolores corrupti dolores autem.",
     body: "Deleniti commodi et et ut autem magnam fugit.",
     created_at: "2019-04-07 00:27:43",
     updated_at: "2019-04-07 00:27:43",
   }
>>> $post->user;
=> App\User {#2965
     id: "1",
     name: "Brayan Dare Jr.",
     email: "madonna.blick@example.org",
     email_verified_at: "2019-04-06 23:35:28",
     created_at: "2019-04-06 23:35:28",
     updated_at: "2019-04-06 23:35:28",
   }
>>> DB::getQueryLog();
=> [
     [
       "query" => "select * from `posts` limit 1",
       "bindings" => [],
       "time" => 0.39,
     ],
     [
       "query" => "select * from `users` where `users`.`id` = ? limit 1",
       "bindings" => [
         "1",
       ],
       "time" => 0.15,
     ],
   ]
>>>
```

SQL queries beautified:

```sql
-- Fetch post:
SELECT
	*
FROM
	`posts`
LIMIT 1;

-- Fetch user:
SELECT
	*
FROM
	`users`
WHERE
	`users`.`id` = 1
LIMIT 1;
```

## Many to Many

We'll use a relationship between a `Post` and a `Tag` model as an example:

### 1. Table schemas

`posts` table:

```php
Schema::create('posts', function (Blueprint $table) {
    $table->bigIncrements('id');
    $table->unsignedInteger('user_id');
    $table->string('title');
    $table->string('body');
    $table->timestamps();
});
```

`tags` table:

```php
Schema::create('tags', function (Blueprint $table) {
    $table->bigIncrements('id');
    $table->string('name');
    $table->timestamps();
});
```

`post_tag` pivot table:

```php
Schema::create('post_tag', function (Blueprint $table) {
    $table->primary(['post_id', 'tag_id']);
    $table->unsignedInteger('post_id');
    $table->unsignedInteger('tag_id');
    $table->timestamps();

    $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
    $table->foreign('tag_id')->references('id')->on('tags')->onDelete('cascade');
});
```

### 2. Models:

`Post.php` model:

```php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    /**
     * Define the relationship between the given post and
     * all the tags associated with it.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class)->withTimestamps();
    }
}
```

`Tag.php` model:

```php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    /**
     * Define the relationship between the given tag and
     * all the posts it was associated to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function posts()
    {
        return $this->belongsToMany(Post::class)->withTimestamps();
    }
}
```

### 3. Usage

Query a list of `tags` associated with the given `post` (output from `php artisan tinker`):

```php
>>> DB::enableQueryLog();
=> null
>>> $post = App\Post::first();
=> App\Post {#2937
     id: "1",
     user_id: "1",
     title: "Quia rerum voluptas quis dolores corrupti dolores autem.",
     body: "Deleniti commodi et et ut autem magnam fugit.",
     created_at: "2019-04-07 00:27:43",
     updated_at: "2019-04-07 00:27:43",
   }
>>> $post->tags;
=> Illuminate\Database\Eloquent\Collection {#2940
     all: [
       App\Tag {#2938
         id: "1",
         name: "family",
         created_at: "2019-04-07 00:37:17",
         updated_at: "2019-04-07 00:37:17",
         pivot: Illuminate\Database\Eloquent\Relations\Pivot {#2928
           post_id: "1",
           tag_id: "1",
         },
       },
       App\Tag {#2944
         id: "2",
         name: "personal",
         created_at: "2019-04-07 00:37:17",
         updated_at: "2019-04-07 00:37:17",
         pivot: Illuminate\Database\Eloquent\Relations\Pivot {#2930
           post_id: "1",
           tag_id: "2",
         },
       },
     ],
   }
>>> DB::getQueryLog();
=> [
     [
       "query" => "select * from `posts` limit 1",
       "bindings" => [],
       "time" => 0.7,
     ],
     [
       "query" => "select `tags`.*, `post_tag`.`post_id` as `pivot_post_id`, `post_tag`.`tag_id` as `pivot_tag_id` from `tags` inner join `post_tag` on `tags`.`id` = `post_tag`.`tag_id` where `post_tag`.`post_id` = ?",
       "bindings" => [
         1,
       ],
       "time" => 0.18,
     ],
   ]
>>>
```

SQL queries beautified:

```sql
-- Fetch post:
SELECT
	*
FROM
	`posts`
LIMIT 1;

-- Fetch tags:
SELECT
	`tags`.*,
	`post_tag`.`post_id` AS `pivot_post_id`,
	`post_tag`.`tag_id` AS `pivot_tag_id`
FROM
	`tags`
	INNER JOIN `post_tag` ON `tags`.`id` = `post_tag`.`tag_id`
	WHERE
		`post_tag`.`post_id` = 1;
```

Query a list of `posts` associated with the given `tag` (output from `php artisan tinker`):

```php
>>> DB::enableQueryLog();
=> null
>>> $tag = App\Tag::find(2);
=> App\Tag {#2922
     id: "2",
     name: "personal",
     created_at: "2019-04-07 00:37:17",
     updated_at: "2019-04-07 00:37:17",
   }
>>> $tag->posts;
=> Illuminate\Database\Eloquent\Collection {#2940
     all: [
       App\Post {#2939
         id: "1",
         user_id: "1",
         title: "Quia rerum voluptas quis dolores corrupti dolores autem.",
         body: "Deleniti commodi et et ut autem magnam fugit.",
         created_at: "2019-04-07 00:27:43",
         updated_at: "2019-04-07 00:27:43",
         pivot: Illuminate\Database\Eloquent\Relations\Pivot {#2933
           tag_id: "2",
           post_id: "1",
         },
       },
     ],
   }
>>> DB::getQueryLog();
=> [
     [
       "query" => "select * from `tags` where `tags`.`id` = ? limit 1",
       "bindings" => [
         2,
       ],
       "time" => 0.63,
     ],
     [
       "query" => "select `posts`.*, `post_tag`.`tag_id` as `pivot_tag_id`, `post_tag`.`post_id` as `pivot_post_id` from `posts` inner join `post_tag` on `posts`.`id` = `post_tag`.`post_id` where `post_tag`.`tag_id` = ?",
       "bindings" => [
         2,
       ],
       "time" => 0.18,
     ],
   ]
>>>
```

SQL queries beautified:

```sql
-- Fetch tag:
SELECT
	*
FROM
	`tags`
WHERE
	`tags`.`id` = 2
LIMIT 1;

-- Fetch posts:
SELECT
	`posts`.*,
	`post_tag`.`tag_id` AS `pivot_tag_id`,
	`post_tag`.`post_id` AS `pivot_post_id`
FROM
	`posts`
	INNER JOIN `post_tag` ON `posts`.`id` = `post_tag`.`post_id`
	WHERE
		`post_tag`.`tag_id` = 2;
```

Attach `tag` to a given `post` (output from `php artisan tinker`):

```php
>>> DB::enableQueryLog();
=> null
>>> $post = App\Post::find(1);
=> App\Post {#2922
     id: "1",
     user_id: "1",
     title: "Quia rerum voluptas quis dolores corrupti dolores autem.",
     body: "Deleniti commodi et et ut autem magnam fugit.",
     created_at: "2019-04-07 00:27:43",
     updated_at: "2019-04-07 00:27:43",
   }
>>> $post->tags()->attach(1);
=> null
>>> DB::getQueryLog();
=> [
     [
       "query" => "select * from `posts` where `posts`.`id` = ? limit 1",
       "bindings" => [
         1,
       ],
       "time" => 0.67,
     ],
     [
       "query" => "insert into `post_tag` (`created_at`, `post_id`, `tag_id`, `updated_at`) values (?, ?, ?, ?)",
       "bindings" => [
         Illuminate\Support\Carbon @1554601014 {#2928
           date: 2019-04-07 01:36:54.250962 UTC (+00:00),
         },
         1,
         1,
         Illuminate\Support\Carbon @1554601014 {#2928},
       ],
       "time" => 1.23,
     ],
   ]
>>>
```

SQL queries beautified:

```sql
-- Fetch post:
SELECT
	*
FROM
	`posts`
WHERE
	`posts`.`id` = ?
LIMIT 1;

-- Attach tag:
INSERT INTO `post_tag` (`created_at`, `post_id`, `tag_id`, `updated_at`)
	VALUES ('2019-04-07 01:36:54', 1, 1, '2019-04-07 01:36:54');
```

Remove `tag` from a given `post` (output from `php artisan tinker`):

```php
>>> DB::enableQueryLog();
=> null
>>> $post = App\Post::find(1);
=> App\Post {#2922
     id: "1",
     user_id: "1",
     title: "Quia rerum voluptas quis dolores corrupti dolores autem.",
     body: "Deleniti commodi et et ut autem magnam fugit.",
     created_at: "2019-04-07 00:27:43",
     updated_at: "2019-04-07 00:27:43",
   }
>>> $post->tags()->detach(1);
=> 1
>>> DB::getQueryLog();
=> [
     [
       "query" => "select * from `posts` where `posts`.`id` = ? limit 1",
       "bindings" => [
         1,
       ],
       "time" => 0.65,
     ],
     [
       "query" => "delete from `post_tag` where `post_id` = ? and `tag_id` in (?)",
       "bindings" => [
         1,
         1,
       ],
       "time" => 1.72,
     ],
   ]
>>>
```

SQL queries beautified:

```sql
-- Fetch post:
SELECT
	*
FROM
	`posts`
WHERE
	`posts`.`id` = ?
LIMIT 1;

-- Remove tag:
DELETE FROM `post_tag`
WHERE `post_id` = 1
	AND `tag_id` IN (1);
```

## Has Many Through

We'll use a relationship between an `Affiliation` and a `Post` model through a `User` model as an example:

### 1. Table schemas

`affiliations` table:

```php
Schema::create('affiliations', function (Blueprint $table) {
    $table->bigIncrements('id');
    $table->string('name')->unique();
    $table->timestamps();
});
```

`users` table:

```php
Schema::create('users', function (Blueprint $table) {
    $table->bigIncrements('id');
    $table->unsignedInteger('affiliation_id');
    $table->string('name');
    $table->string('email')->unique();
    $table->timestamp('email_verified_at')->nullable();
    $table->string('password');
    $table->rememberToken();
    $table->timestamps();
});
```

`posts` table:

```php
Schema::create('posts', function (Blueprint $table) {
    $table->bigIncrements('id');
    $table->unsignedInteger('user_id');
    $table->string('title');
    $table->string('body');
    $table->timestamps();
});
```

### 2. Models:

`Affiliation.php` model:

```php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Affiliation extends Model
{
    /**
     * Define the relationship between the given affiliation
     * and all posts created by users associated with this affiliation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function posts()
    {
        return $this->hasManyThrough(Post::class, User::class);
    }
}
```

`User.php` model:

```php
namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * Define the relationship between the given user and the posts he/she created.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
```

`Post.php` model:

```php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    /**
     * Define the relationship between the given post
     * and the user it was created by.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

### 3. Usage

Query a list of `posts` created by `users` associated with the given `affiliation` (output from `php artisan tinker`):

```php
>>> DB::enableQueryLog();
=> null
>>> $affiliation = App\Affiliation::first();
=> App\Affiliation {#2937
     id: "1",
     name: "enim",
     created_at: "2019-04-07 02:56:25",
     updated_at: "2019-04-07 02:56:25",
   }
>>> $affiliation->posts;
=> Illuminate\Database\Eloquent\Collection {#2939
     all: [
       App\Post {#2941
         id: "1",
         user_id: "1",
         title: "Dignissimos in consequatur neque dolore et amet.",
         body: "Provident quo qui neque ipsam et voluptatem sit.",
         created_at: "2019-04-07 02:58:13",
         updated_at: "2019-04-07 02:58:13",
         laravel_through_key: "1",
       },
       App\Post {#2945
         id: "2",
         user_id: "2",
         title: "Dolorum eos enim non.",
         body: "Officia nulla minima aut animi labore.",
         created_at: "2019-04-07 02:58:16",
         updated_at: "2019-04-07 02:58:16",
         laravel_through_key: "1",
       },
     ],
   }
>>> DB::getQueryLog();
=> [
     [
       "query" => "select * from `affiliations` limit 1",
       "bindings" => [],
       "time" => 0.66,
     ],
     [
       "query" => "select `posts`.*, `users`.`affiliation_id` as `laravel_through_key` from `posts` inner join `users` on `users`.`id` = `posts`.`user_id` where `users`.`affiliation_id` = ?",
       "bindings" => [
         1,
       ],
       "time" => 0.19,
     ],
   ]
>>>
```

SQL queries beautified:

```sql
-- Fetch affiliation:
SELECT
	*
FROM
	`affiliations`
LIMIT 1;

-- Fetch posts:
SELECT
	`posts`.*,
	`users`.`affiliation_id` AS `laravel_through_key`
FROM
	`posts`
	INNER JOIN `users` ON `users`.`id` = `posts`.`user_id`
	WHERE
		`users`.`affiliation_id` = 1;
```

## Polymorphic Relations

We'll use a relationship between a `Video` and a `Series` or `Collection` models as an example:

### 1. Table schemas

`series` table:

```php
Schema::create('series', function (Blueprint $table) {
    $table->bigIncrements('id');
    $table->string('name');
    $table->timestamps();
});
```

`collections` table:

```php
Schema::create('collections', function (Blueprint $table) {
    $table->bigIncrements('id');
    $table->string('name');
    $table->timestamps();
});
```

`videos` table:

```php
Schema::create('videos', function (Blueprint $table) {
    $table->bigIncrements('id');
    $table->morphs('watchable'); // generates "watchable_type" and "watchable_id" columns
    $table->string('title');
    $table->string('description');
    $table->timestamps();
});
```

### 2. Models:

`Series.php` model:

```php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Series extends Model
{
    /**
     * Define the relationship between the given series
     * and all videos associated with it.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function videos()
    {
        return $this->morphMany(Video::class, 'watchable');
    }
}
```

`Collection.php` model:

```php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    /**
     * Define the relationship between the given collection
     * and all videos associated with it.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function videos()
    {
        return $this->morphMany(Video::class, 'watchable');
    }
}
```

`Video.php` model:

```php
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
```

### 3. Usage

Query a list of `videos` associated with a given `series` (output from `php artisan tinker`):

```php
>>> DB::enableQueryLog();
=> null
>>> $series = App\Series::find(2);
=> App\Series {#2922
     id: "2",
     name: "Itaque quo et.",
     created_at: "2019-04-07 03:59:48",
     updated_at: "2019-04-07 03:59:48",
   }
>>> $series->videos;
=> Illuminate\Database\Eloquent\Collection {#2940
     all: [
       App\Video {#2941
         id: "2",
         watchable_type: "App\Series",
         watchable_id: "2",
         title: "Quasi ut debitis incidunt perspiciatis.",
         description: "Optio commodi unde ut.",
         created_at: "2019-04-07 03:59:48",
         updated_at: "2019-04-07 03:59:48",
       },
     ],
   }
>>> DB::getQueryLog();
=> [
     [
       "query" => "select * from `series` where `series`.`id` = ? limit 1",
       "bindings" => [
         2,
       ],
       "time" => 0.63,
     ],
     [
       "query" => "select * from `videos` where `videos`.`watchable_id` = ? and `videos`.`watchable_id` is not null and `videos`.`watchable_type` = ?",
       "bindings" => [
         2,
         "App\Series",
       ],
       "time" => 0.15,
     ],
   ]
>>>
```

SQL queries beautified:

```sql
-- Fetch series:
SELECT
	*
FROM
	`series`
WHERE
	`series`.`id` = 2
LIMIT 1;

-- Fetch videos:
SELECT
	*
FROM
	`videos`
WHERE
	`videos`.`watchable_id` = 2
	AND `videos`.`watchable_id` IS NOT NULL
	AND `videos`.`watchable_type` = 'App\Series';
```

Find out if the given `video` is part of a `series` or `collection` (output from `php artisan tinker`):

```php
>>> DB::enableQueryLog();
=> null
>>> $video = App\Video::find(3);
=> App\Video {#2922
     id: "3",
     watchable_type: "App\Collection",
     watchable_id: "1",
     title: "Consequatur repellendus eius repellendus.",
     description: "Commodi consequatur voluptatem aut incidunt molestiae et autem.",
     created_at: "2019-04-07 03:59:48",
     updated_at: "2019-04-07 03:59:48",
   }
>>> $video->watchable;
=> App\Collection {#2936
     id: "1",
     name: "Dolorem provident.",
     created_at: "2019-04-07 03:59:48",
     updated_at: "2019-04-07 03:59:48",
   }
>>> DB::getQueryLog();
=> [
     [
       "query" => "select * from "videos" where "videos"."id" = ? limit 1",
       "bindings" => [
         3,
       ],
       "time" => 0.61,
     ],
     [
       "query" => "select * from "collections" where "collections"."id" = ? limit 1",
       "bindings" => [
         "1",
       ],
       "time" => 0.13,
     ],
   ]
>>>
```

SQL queries beautified:

```sql
-- Fetch video:
SELECT
	*
FROM
	`videos`
WHERE
	`videos`.`id` = 3
LIMIT 1;

-- Fetch `watchable`:
SELECT
	*
FROM
	`collections`
WHERE
	`collections`.`id` = 1
LIMIT 1;
```

## Many to Many Polymorphic Relations

We'll use a relationship between a `Post` or `Comment` model and the `User` model who can like posts and comments:

### 1. Table schemas

`posts` table:

```php
Schema::create('posts', function (Blueprint $table) {
    $table->bigIncrements('id');
    $table->unsignedInteger('user_id');
    $table->string('title');
    $table->string('body');
    $table->timestamps();
});
```

`comments` table:

```php
Schema::create('comments', function (Blueprint $table) {
    $table->bigIncrements('id');
    $table->unsignedInteger('post_id');
    $table->string('body');
    $table->timestamps();
});
```

`likables` table:

```php
Schema::create('likables', function (Blueprint $table) {
    $table->primary(['user_id', 'likable_id', 'likable_type']);
    $table->unsignedInteger('user_id');
    $table->morphs('likable');
    $table->timestamps();
});
```

### 2. Models:

`Posts.php` model:

```php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    /**
     * Like the current post.
     *
     * @return void
     */
    public function like($user = null)
    {
        $user = $user ?: auth()->user();
        $this->likes()->attach($user);
    }

    /**
     * Define the relationship between the given "likable" (post)
     * and the "likes" associated with it.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function likes()
    {
        return $this->morphToMany(User::class, 'likable')->withTimestamps();
    }
}
```

`Comments.php` model:

```php
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
```

`User.php` model:

```php
namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
}
```

### 3. Usage

Find `users` who liked the given `comment` (output from `php artisan tinker`):

```php
>>> DB::enableQueryLog();
=> null
>>> $comment = App\Comment::first();
=> App\Comment {#2937
     id: "1",
     post_id: "2",
     body: "Enim quisquam doloremque atque doloribus aut similique accusamus. Omnis iste odit qui sit culpa est. Nesciunt aut hic
 reprehenderit dignissimos. In repellendus enim delectus recusandae ut vel necessitatibus.",
     created_at: "2019-04-07 05:31:23",
     updated_at: "2019-04-07 05:31:23",
   }
>>> $comment->likes;
=> Illuminate\Database\Eloquent\Collection {#2928
     all: [
       App\User {#2940
         id: "3",
         affiliation_id: "3",
         name: "Shaniya Gutkowski",
         email: "yconn@example.com",
         email_verified_at: "2019-04-07 05:31:23",
         created_at: "2019-04-07 05:31:23",
         updated_at: "2019-04-07 05:31:23",
         pivot: Illuminate\Database\Eloquent\Relations\MorphPivot {#2930
           likable_id: "1",
           user_id: "3",
           likable_type: "App\Comment",
           created_at: "2019-04-07 05:31:23",
           updated_at: "2019-04-07 05:31:23",
         },
       },
     ],
   }
>>> DB::getQueryLog();
=> [
     [
       "query" => "select * from "comments" limit 1",
       "bindings" => [],
       "time" => 1.15,
     ],
     [
       "query" => "select "users".*, "likables"."likable_id" as "pivot_likable_id", "likables"."user_id" as "pivot_user_id", "likables"."likable_type" as "pivot_likable_type", "likables"."created_at" as "pivot_created_at", "likables"."updated_at" as "pivot_updated_at" from "users" inner join "likables" on "users"."id" = "likables"."user_id" where "likables"."likable_id" = ? and "likables"."likable_type" = ?",
       "bindings" => [
         1,
         "App\Comment",
       ],
       "time" => 0.21,
     ],
   ]
>>>
```

SQL queries beautified:

```sql
-- Fetch comment:
SELECT
	*
FROM
	`comments`
LIMIT 1;

-- Fetch users:
SELECT
	`users`.*,
	`likables`.`likable_id` AS `pivot_likable_id`,
	`likables`.`user_id` AS `pivot_user_id`,
	`likables`.`likable_type` AS `pivot_likable_type`,
	`likables`.`created_at` AS `pivot_created_at`,
	`likables`.`updated_at` AS `pivot_updated_at`
FROM
	`users`
	INNER JOIN `likables` ON `users`.`id` = `likables`.`user_id`
	WHERE
		`likables`.`likable_id` = 1
		AND `likables`.`likable_type` = 'App\Comment';
```

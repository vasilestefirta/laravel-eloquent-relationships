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

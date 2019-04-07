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

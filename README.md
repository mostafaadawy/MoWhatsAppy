<h1 style="color:#1919">MoWhatsAppy</h1>

This Application in **_whatsapp clone_** based `laravel backend` and
`vue as frontend`. Other similiar applications use google clouth OAyth services
for authenticating users and firebase for messages and notifications. as known
there are two methods to handel the application related to the frontend the
first method is Server Side Rendering `SSR` which is good for `SEO` browser
search, and the second method is Single Page Application `SPA`.

Although we will use `breeze` to `scafold` our project and set users
`authentication` with `vue` as `SSR`, but this will be used for `admin backend`
while we created two seperate folders one for the laravel backend with admin
settings with vue and the other forlder for the frontend using vue also.

`laravel` will be used **_instead of firebase_** and **_google cloud Auth_**.
while frondend vue will contain **_pinia_** that is used such as **_redux_** in
general or **useState and useReducer in react** to save the users data and
update it in user side while updating the server by APIs to make it more
effecient where If SSR used in this case it will require page reload evrey
message update which is not logic so we used single page application.

<p style="color:#614141">Note: Laravel breeze used for scafolding and adding required vue component and
controllers for authentication where it installs tailwind for css also vite as
Hot Module Replacement HMR frontend server</p>

<p style="color:#614141"> Note: in this application there are three servers the first for the php and the
second for vite and the third for vite with vue as frontend server</p>

Next sections will will explain step by step the installation, configuration and
implementation and editing in bothe Fend and Bend

## Backend

In order to create the application do the following

```sh
mkdir MoWhataAppy
cd MoWhatsAppy
composer create-project laravel/laravel Bend
mkdir Fend
cd Bend
composer require laravel/breeze --dev
php artisan breeze:install vue
```

- editing `.env DB_CONNECTION` to be `sqlite` instead of `mysql`
- > php artisan migrate
- > php artisan serve
- In other terminal same path
- > npm install
- > npm run dev

## Next we will implement all Needed APIs

- we will modify the user model where we will verify the registeration email if
  note

```php
   protected $casts = [
        'email_verified_at' => 'datetime',
    ];
```

- API Middleware: Laravel Breeze automatically includes the auth:sanctum
  middleware for API routes. Ensure your API routes are protected by this
  middleware. if not exists

```php
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiAuthController;

Route::post('register', [ApiAuthController::class, 'register']);
Route::post('login', [ApiAuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // Your other protected API routes here
});

```

- Edit kernel in http to make api include email verfication class middleware

```php
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
            // \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        ],

        'api' => [
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            // \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        ],
    ];
```

- for now we will ignore email verfication for simplisty but in the end of
  application will be activated
- if sanctum is not installed

```sh
composer require laravel/sanctum
php artisan sanctum:install
```

- Create API Controller: Create a new controller for handling API registration
  and login
- > php artisan make:controller ApiAuthController

```php
<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ApiAuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Send email verification notification if needed

        return response()->json(['message' => 'User registered successfully'], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        if (!$user->email_verified_at) {
            return response()->json(['error' => 'Email not verified'], 401);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user,
        ]);
    }
}

```

- now the time to create acontroller `UsersApisController` for the required
  endpoints for dealing wioth users as follows
  - getUserDetails
  - getAllUsers
  - checkIfUserExists
  - saveUserDetails -> update user

```php
<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\Response;
class UsersApisController extends Controller
{
    public function getUserDetails($userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($user);
    }

    public function getAllUsers()
    {
        $users = User::all();

        return response()->json($users);
    }

    public function checkIfUserExists($email)
    {
        $user = User::where('email', $email)->first();

        return response()->json(['exists' => !!$user]);
    }

    public function saveUserDetails(Request $request, $userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        // Update user details based on the request data
        $user->update($request->all());

        return response()->json(['message' => 'User details updated successfully']);
    }
}
```

- edit the field name to be two fields called firstName and lastName and
  required modifications for that
- create factory for users to test the end points
- create seeder for user calling User model in the run of the seeder to till it
  to fakly generate 10 users with the fielsed defined in the user factory

```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'firstName' => fake()->name(),
            'lastName' => fake()->name(),
            'photo' => fake()->imageUrl(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}

```

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        User::factory()->count(10)->create();
    }
}

```

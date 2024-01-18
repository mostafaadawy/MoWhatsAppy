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
    public function getUserDetails()
    {

    }

    public function getAllUsers()
    {

    }

    public function checkIfUserExists($search)
    {

    }

    public function saveUserDetails(Request $request, $userId)
    {

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

- now using postman or thunder to test the apis for the end points of the
  `UsersApisController`
- for the first endPoint there were many modifications
  - the guard for the api was not made by default so we need to add it in `auth`
    in `config`

```php
    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
        'api' => [
            'driver' => 'sanctum',
            'provider' => 'users',
            'hash' => false,
        ],
    ],
```

- The `UserApisController` after creating the end points

```php
<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
class UsersApisController extends Controller
{
    public function getUserDetails()
    {
        // Retrieve the currently authenticated user
        $user = Auth::user();

        if (!$user) {
            // Handle case where user is not authenticated
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Do something with the user details
        // ...

        return response()->json(['data' => $user]);
    }

    public function getAllUsers()
    {
        $users = User::all();

        return response()->json($users);
    }

    public function checkIfUserExists($search)
    {
        $users = User::where('email', 'like', '%' . $search . '%')
            ->orWhere('firstName', 'like', '%' . $search . '%')
            ->orWhere('lastName', 'like', '%' . $search . '%')
           ->get();
        // $user = User::where('email', 'like', '%' . $search . '%')
        //     ->orWhere('firstName', 'like', '%' . $search . '%')
        //     ->orWhere('lastName', 'like', '%' . $search . '%')
        //    ->first();

        // return response()->json(['exists' => !!$user]);
        return response()->json(['data' => $users]);
    }

    public function saveUserDetails(Request $request)
    {
        $user = User::find(Auth::user()->id);

        if (!$user) {
            // Handle case where user is not authenticated
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Update user details based on the request data
        $user->update($request->has('password') ? $request->all() : $request->except(['password']));


        return response()->json(['message' => 'User details updated successfully']);
    }
}

```

and the routes will be

```php
Route::middleware('auth:sanctum')->group(function () {
    // Your other protected API routes here
// Route::post('getUserDetails/{userId}', [UsersApisController::class,'getUserDetails'])->name('getUserDetails');
Route::get('getUserDetails', [UsersApisController::class,'getUserDetails'])->name('getUserDetails');
Route::get('getAllUsers', [UsersApisController::class,'getAllUsers'])->name('getAllUsers');
Route::get('checkIfUserExists/{search}', [UsersApisController::class,'checkIfUserExists'])->name('checkIfUserExists');
Route::post('saveUserDetails', [UsersApisController::class,'saveUserDetails'])->name('saveUserDetails');
});
```

- time to create our Chat Controller , model, and migration
- > php artisan make:model Chat -mrc
- edit the migration to contain possible fields

## Creating the chat actually is not that easy

there are important consderations that we have to consider such as every user
may have a chat and every chat may need a lot of meessages and each message has
status (who see it, who deliverd it).

the best senario from my point of view is to use Nosql relation where each chat
become a doc with its messages and interactive users on it, but for our design
as we use sql it can also be handeled by creating a chat table that has many
relation to messages table and chat and user-messages table, but why ?

imagine that messages is related to chat directly and there are agroup of users
for example 1000 user and someone from these users wrote a message so the
message recored will be repeated 1000 time (no of users) to just point to which
user deliver and which is not which will result in a messavie data redundancy in
DB

so the solution will be

- chat table that only containes id that is used to filterout the messages
  according to the chat
- messages table that will contain the message and its typeand chat_id for
  filtering
- message_user pivot table to allow many to many relationship between users and
  all messages that will have the status is it delivered submitted (when user is
  the owner), read and so on

## So This is the Senario

- In the begining there were no chat histories so for now user will find another
  user and when create his first message the chat will be created
- as there chat_id that is not null this allow the user to add messages to this
  chat
- when searching for a user (single user not group) we will search it there
  sinle one to one chat
-
- After creating chat histories with some users, user can select directly one of
  existing chats or find a new user for new chat
- creating groups will be implemented in the second phase
- > php artisan make:migration create_user_message_table
- now lets build it begining by the user_message table

```php
    public function up(): void
    {
        Schema::create('message_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('message_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['sent','delivered', 'seen'])->default('delivered');
            $table->timestamps();
        });
    }
```

- message table

```php
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_id')->constrained()->onDelete('cascade');;
            $table->string('content');
            $table->enum('type', ['text', 'photo', 'audio', 'video', 'document', 'location']);
            $table->timestamps();
        });
    }
```

- chat table

```php
public function up(): void
    {
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
    }
```

- chat controller

```php
use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function createChat(Request $request)
    {
        // Create a new chat
        $chat = Chat::create();

        // Add the authenticated user to the chat
        $user = Auth::user();
        $chat->users()->attach($user->id);

        // Send a message to the chat
        $message = $this->sendMessage($request->input('content'), $chat, $user);

        return response()->json(['chat' => $chat, 'message' => $message]);
    }

    public function sendMessageToChat(Request $request, $chatId)
    {
        // Find the chat
        $chat = Chat::findOrFail($chatId);

        // Get the authenticated user
        $user = Auth::user();

        // Send a message to the chat
        $message = $this->sendMessage($request->input('content'), $chat, $user);

        return response()->json(['chat' => $chat, 'message' => $message]);
    }

    public function editMessage(Request $request, $messageId)
    {
        // Find the message
        $message = Message::findOrFail($messageId);

        // Check if the authenticated user is the message owner
        $this->authorize('update', $message);

        // Update the message content
        $message->content = $request->input('content');
        $message->save();

        return response()->json(['message' => $message]);
    }

    public function deleteMessage($messageId)
    {
        // Find the message
        $message = Message::findOrFail($messageId);

        // Check if the authenticated user is the message owner
        $this->authorize('delete', $message);

        // Delete the message
        $message->delete();

        return response()->json(['message' => 'Message deleted successfully']);
    }

    // Helper function to create and send a message
    private function sendMessage($content, $chat, $user)
    {
        $message = new Message([
            'content' => $content,
            'type' => 'text', // Assuming it's a text message
        ]);

        // Save the message to the chat and associate it with the user
        $chat->messages()->save($message);
        $message->users()->attach($user->id, ['status' => 'delivered']);

        return $message;
    }
}

```

- create routes

```php
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\APIs\ApiAuthController;
use App\Http\Controllers\APIs\UsersApisController;
use App\Http\Controllers\APIs\ChatController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('register', [ApiAuthController::class, 'register']);
Route::post('login', [ApiAuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // Your other protected API routes here
// Route::post('getUserDetails/{userId}', [UsersApisController::class,'getUserDetails'])->name('getUserDetails');
Route::get('getUserDetails', [UsersApisController::class,'getUserDetails'])->name('getUserDetails');
Route::get('getAllUsers', [UsersApisController::class,'getAllUsers'])->name('getAllUsers');
Route::get('checkIfUserExists/{search}', [UsersApisController::class,'checkIfUserExists'])->name('checkIfUserExists');
Route::post('saveUserDetails', [UsersApisController::class,'saveUserDetails'])->name('saveUserDetails');
// Create a new chat and add message
Route::post('createChatSendMessage', [ChatController::class,'createChatSendMessage'])->name('createChatSendMessage');
// Send a message to an existing chat
Route::post('sendMessageToExistingChat', [ChatController::class,'sendMessageToExistingChat'])->name('sendMessageToExistingChat');
// Edit a message
Route::put('editMessage/{messageId}', [ChatController::class,'editMessage'])->name('editMessage');
// Delete a message
Route::delete('deleteMessage/{messageId}', [ChatController::class, 'deleteMessage'])->name('deleteMessage');
// Delete a chat along with its messages
Route::delete('deleteChat/{chatId}', [ChatController::class, 'deleteChat'])->name('deleteChat');
});

```

- in order to completye this step we need anothor pivot table for chat_user

```php

```

- > php artisan migrate:fresh --seed
- do not forget to add `->onDelete('cascade');` to any forign keys
- add celete chat to chat controller so chayt controller will look like

```php
<?php
namespace App\Http\Controllers\APIs;
use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
class ChatController extends Controller
{
    public function createChatSendMessage(Request $request)
    {
        // Create a new chat
        $chat = Chat::create();

        // Add the authenticated user to the chat
        $txUser = Auth::user();
        $rxUser_id= $request->input('rxUser_id');
        $chat->users()->attach($txUser->id);
        $chat->users()->attach($rxUser_id);

        // Send a message to the chat
        $message = $this->sendMessage($request->input('content'), $chat, $txUser, $rxUser_id);

        return response()->json(['chat' => $chat, 'message' => $message]);
    }

  public function sendMessageToExistingChat(Request $request)
    {
        // Find the chat
        $chat = Chat::findOrFail($request->input('chatId'));

        // Get the authenticated user
        $txUser = Auth::user();
        $rxUser_id= $request->input('rxUser_id');
        $chat->users()->attach($txUser->id);
        $chat->users()->attach($rxUser_id);
        // Send a message to the chat
        $message = $this->sendMessage($request->input('content'), $chat, $txUser, $rxUser_id);

        return response()->json(['chat' => $chat, 'message' => $message]);
    }

    public function editMessage(Request $request, $messageId)
    {
        // Find the message
        $message = Message::findOrFail($messageId);

        // Check if the authenticated user is the message owner
        $this->authorize('update', $message);

        // Update the message content
        $message->content = $request->input('content');
        $message->save();

        return response()->json(['message' => $message]);
    }

    public function deleteMessage($messageId)
    {
        // Find the message
        $message = Message::findOrFail($messageId);

        // Check if the authenticated user is the message owner
        $this->authorize('delete', $message);

        // Delete the message
        $message->delete();

        return response()->json(['message' => 'Message deleted successfully']);
    }

    // Helper function to create and send a message
    private function sendMessage($content, $chat, $txUser, $rxUser_id)
    {
        $message = new Message([
            'content' => $content,
            'type' => 'text', // Assuming it's a text message
        ]);

        // Save the message to the chat and associate it with the user
        $chat->messages()->save($message);
        $message->users()->attach($txUser->id, ['status' => 'sent']);
        $message->users()->attach($rxUser_id, ['status' => 'waiting']);

        return $message;
    }
    public function deleteChat($chatId)
    {
        // Find the chat
        $chat = Chat::findOrFail($chatId);

        // Delete the chat along with its messages
        $chat->messages()->delete();
        $chat->delete();

        return response()->json(['message' => 'Chat and messages deleted successfully']);
    }
}
```

- we need to add special delete method to allow users to delete the message
  permaenantly if they are the ownership or delet for themself if they are
  receivers so we need ownership and delete status

```php
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_id')->constrained()->onDelete('cascade');
            $table->string('content');
            $table->string('ownership');
            $table->enum('type', ['text', 'photo', 'audio', 'video', 'document', 'location']);
            $table->timestamps();
        });
```

```php
    public function up(): void
    {
        Schema::create('message_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('message_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['sent','waiting','delivered', 'seen','deleted'])->default('delivered');
            $table->timestamps();
        });
    }
```

- also ownership is used to allow only owners of message to edit the message
- do not forget `php artisan migrate:fresh --seed` then reg and login and use
  the token
- do not forget to edit the model `Message` to add in `fillable` the `ownership`
  field

## In order to authorize certain user to do certain action like add update edit and delete on certain table

- create `policy` for that table to till **_which action are allowed for whom_**

```sh
php artisan make:policy MessagePolicy
```

- Define Authorization Logic in MessagePolicy: Open the
  app/Policies/MessagePolicy.php file and define the authorization logic for
  updating messages
- policy is simply return boolean true or false if a condition is met

```php
<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Message;
class MessagePolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }
    public function update(User $user, Message $message)
    {
        return $user->id == $message->ownership;
    }
}

```

- Register the MessagePolicy: Open the app/Providers/AuthServiceProvider.php
  file and register the MessagePolicy in the policies array:
- Use authorize Method in Controller: In your ChatController, you can now use
  the authorize method:

```php
    public function editMessage(Request $request, $messageId)
    {
        // Find the message
        $message = Message::findOrFail($messageId);

        // Check if the authenticated user is the message owner
        $this->authorize('update', $message);
        // in case we need to do this authorization action without the policy comment the upper line and uncommint the next code snippet
        // if($message->ownership != Auth::user()->id)
        // {
        //     return response()->json(['message' => 'note authorized'])->status(403);
        // }
        // Update the message content
        $message->content = $request->input('content');
        $message->save();

        return response()->json(['message' => $message]);
    }
```

- WRT delete we have `deleteforme` and `deleteforall` every methoshave its
  policy where if i am not the message ctreator i can not delet for all
- also chat should have a policy where if any user delete the chat it shouldnot
  be totally removed from database except the creator himself if he want to
  delete it permentaly he can do this
- so the routing will be as next

```php

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\APIs\ApiAuthController;
use App\Http\Controllers\APIs\UsersApisController;
use App\Http\Controllers\APIs\ChatController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('register', [ApiAuthController::class, 'register']);
Route::post('login', [ApiAuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // Your other protected API routes here
// Route::post('getUserDetails/{userId}', [UsersApisController::class,'getUserDetails'])->name('getUserDetails');
Route::get('getUserDetails', [UsersApisController::class,'getUserDetails'])->name('getUserDetails');
Route::get('getAllUsers', [UsersApisController::class,'getAllUsers'])->name('getAllUsers');
Route::get('checkIfUserExists/{search}', [UsersApisController::class,'checkIfUserExists'])->name('checkIfUserExists');
Route::post('saveUserDetails', [UsersApisController::class,'saveUserDetails'])->name('saveUserDetails');
// Create a new chat and add message
Route::post('createChatSendMessage', [ChatController::class,'createChatSendMessage'])->name('createChatSendMessage');
// Send a message to an existing chat
Route::post('sendMessageToExistingChat', [ChatController::class,'sendMessageToExistingChat'])->name('sendMessageToExistingChat');
// Edit a message
Route::put('editMessage/{messageId}', [ChatController::class,'editMessage'])->name('editMessage');
// Delete a message
Route::delete('deleteMessageforMe/{messageId}', [ChatController::class, 'deleteMessageforMe'])->name('deleteMessageforMe');
Route::delete('deleteMessageforAll/{messageId}', [ChatController::class, 'deleteMessageforAll'])->name('deleteMessageforAll');
// Delete a chat along with its messages
Route::delete('deleteChat/{chatId}', [ChatController::class, 'deleteChat'])->name('deleteChat');
});
```

- so lets first create the chat policy

```sh
php artisan make:policy ChatPolicy
```

- edit the chat policy

```php
<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Chat;

class ChatPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }
    public function delete(User $user, Chat $chat){
        return $user->id == $chat->ownership;

    }
}

```

- now `ownership` field in chat doenot exist so we need to add it to migration
  and migrate and seed then add it to chat fillable, then register the policy in
  the authserviceprovider

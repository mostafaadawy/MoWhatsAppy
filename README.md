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
- > php artisan migrate:fresh --seed
- do not forgot to register again and login to use the token
- now we are ready to handel the delete method for a chat and as chat cascade on
  delete it should delte its messages so try it using postman or TDD or thunder

```php
    public function deleteChat($chatId)
    {
        // Find the chat
        $chat = Chat::findOrFail($chatId);
        $this->authorize('delete', $chat);
        // Delete the chat along with its messages
        $chat->messages()->delete();
        $chat->delete();

        return response()->json(['message' => 'Chat and messages deleted successfully']);
    }
```

- after reg login use token and creating many message we tried chat delete
- do not forget registring your policy in `AuthServiceProvider` and in creating
  the chat first time to save the ownership
- now time to make message delete methods

```php
public function deleteMessageforMe($messageId)
    {
        // Find the message
        $message = Message::findOrFail($messageId);

        // Check if the authenticated user is the message owner
        $this->authorize('delete_for_me', $message);

        // Delete the message
        $message->users()->updateExistingPivot(Auth::user()->id, ['status' => 'deleted']);

        return response()->json(['message' => 'Message deleted successfully']);
    }
    public function deleteMessageforAll($messageId)
    {
        // Find the message
        $message = Message::findOrFail($messageId);

        // Check if the authenticated user is the message owner
        $this->authorize('delete_for_all', $message);

        // Delete the message
        $message->delete();

        return response()->json(['message' => 'Message deleted successfully']);
    }

```

- add next methods to `MessagePolicy`

```php
    public function delete_for_me(User $user, Message $message)
    {
        // Add your authorization logic here
        // Example: Allow users to update their own messages
        return $user->id !=null;
    }
    public function delete_for_all(User $user, Message $message)
    {
        // Add your authorization logic here
        // Example: Allow users to update their own messages
        return $user->id == $message->ownership;
    }
```

- at this point almost all apis required for user and chat and messages are
  ready but we still need other apis

## How we can change message status from waiting to delivered then seeen ?

## Other Question is How to notify other users without firebase?

Next Section will Contain the answer

- first when we want to make some notifications for action that happen it can be
  done simply by laravel noticiation that we will show a code example for it.
  there are aother techniques for notifications such as using message brooker
  like laoravel broadcasting and pushers , where b rodcasting an event through
  channel and the frontend listen to this event. actually redis or rabbit or
  other message broker is mainly used for microservices integeration and
  communication but we can use these for our issue also.
- for more details lets check next section

## Implementing real-time features like message delivery and read receipts

Implementing real-time features like message delivery and read receipts often
involves using a combination of techniques such as websockets, event
broadcasting, and possibly a message broker like Redis.

Below is a simplified example using Laravel Echo, Laravel Broadcasting, and
Pusher for real-time features. Please note that this is just a basic
illustration, and a production implementation might need additional features and
security considerations.

- Install Laravel Echo and Pusher: Make sure you have Laravel Echo and Pusher
  installed.
- > composer require pusher/pusher-php-server
- Configure your .env file with Pusher credentials
- after installing we can find broadcasting.php file
- Broadcasting Configuration: Update your config/broadcasting.php file:

```php
'connections' => [
    'pusher' => [
        'driver' => 'pusher',
        'key' => env('PUSHER_APP_KEY'),
        'secret' => env('PUSHER_APP_SECRET'),
        'app_id' => env('PUSHER_APP_ID'),
        'options' => [
            'cluster' => env('PUSHER_APP_CLUSTER'),
            'useTLS' => true,
        ],
    ],
],

```

- Event Class: Create an event class for message status updates. Run
- > php artisan make:event MessageStatusUpdated

```php
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class MessageStatusUpdated implements ShouldBroadcast
{
    use SerializesModels;

    public $messageId;
    public $status;

    public function __construct($messageId, $status)
    {
        $this->messageId = $messageId;
        $this->status = $status;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('message.'.$this->messageId);
    }
}

```

- Update ChatController methods to broadcast the message status updates:

```php
use App\Events\MessageStatusUpdated;

// ...

public function sendMessageToChat(Request $request, $chatId)
{
    // ... Your existing code to send a message

    // Broadcast the message status update
    broadcast(new MessageStatusUpdated($message->id, 'waiting'))->toOthers();

    return response()->json(['chat' => $chat, 'message' => $message]);
}

public function deliverMessage(Request $request, $messageId)
{
    // ... Your existing code to handle delivering the message

    // Broadcast the message status update
    broadcast(new MessageStatusUpdated($message->id, 'delivered'))->toOthers();

    return response()->json(['message' => $message]);
}

public function seenMessage(Request $request, $messageId)
{
    // ... Your existing code to handle marking the message as seen

    // Broadcast the message status update
    broadcast(new MessageStatusUpdated($message->id, 'seen'))->toOthers();

    return response()->json(['message' => $message]);
}
```

- Listen for Events in Frontend: In your frontend, use Laravel Echo to listen
  for events and update the UI accordingly

```js
// Example in a Vue component mounted() { Echo.private('message.' +
this.messageId) .listen('MessageStatusUpdated', (event) => { // Handle the event
and update the UI console.log('Message status updated:', event.status); }); }
```

## more detailed example

a more detailed example with a realistic scenario. We'll implement a simple
messaging system with real-time features using Laravel Echo, Laravel
Broadcasting, and Pusher. In this example, we'll focus on updating the message
status when it's delivered and seen.

- > composer require pusher/pusher-php-server
- > npm install --save laravel-echo pusher-js
- Create an account on the Pusher website and obtain your API key, secret, and
  app ID.
- Update your .env file:

```js
BROADCAST_DRIVER = pusher;
PUSHER_APP_ID = your - app - id;
PUSHER_APP_KEY = your - app - key;
PUSHER_APP_SECRET = your - app - secret;
PUSHER_APP_CLUSTER = your - app - cluster;
```

- Update config/broadcasting.php

```php
'pusher' => [
    'driver' => 'pusher',
    'key' => env('PUSHER_APP_KEY'),
    'secret' => env('PUSHER_APP_SECRET'),
    'app_id' => env('PUSHER_APP_ID'),
    'options' => [
        'cluster' => env('PUSHER_APP_CLUSTER'),
        'useTLS' => true,
    ],
],

```

- Create an Event for Message Status Updates
- > php artisan make:event MessageStatusUpdated

```php
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class MessageStatusUpdated implements ShouldBroadcast
{
    use SerializesModels;

    public $messageId;
    public $status;

    public function __construct($messageId, $status)
    {
        $this->messageId = $messageId;
        $this->status = $status;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('message.' . $this->messageId);
    }
}

```

- Update MessageController:

```php
use App\Events\MessageStatusUpdated;

public function sendMessage(Request $request)
{
    // Validate and save the message

    // Broadcast the message status update
    broadcast(new MessageStatusUpdated($message->id, 'waiting'))->toOthers();

    return response()->json(['message' => $message]);
}

public function deliverMessage($messageId)
{
    // Update the message status to 'delivered'

    // Broadcast the message status update
    broadcast(new MessageStatusUpdated($message->id, 'delivered'))->toOthers();

    return response()->json(['message' => $message]);
}

public function seenMessage($messageId)
{
    // Update the message status to 'seen'

    // Broadcast the message status update
    broadcast(new MessageStatusUpdated($message->id, 'seen'))->toOthers();

    return response()->json(['message' => $message]);
}

```

- Frontend Integration:

```js
// resources/js/bootstrap.js

import Echo from 'laravel-echo';

window.Pusher = require('pusher-js');

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    encrypted: true,
});

// Example in a Vue component
mounted() {
    Echo.private('message.' + this.messageId)
        .listen('MessageStatusUpdated', (event) => {
            // Update the UI based on the received event.status
            console.log('Message status updated:', event.status);
        });
}

```

## how to use message broker in this case?

In the context of real-time messaging and notifications, a message broker is
often used to handle communication between different components of a system.
Laravel supports various message brokers, and one common choice is Redis. Redis
is an in-memory data structure store that can be used as a message broker for
real-time updates. Here are the steps to use Redis as a message broker in the
context of your Laravel application:

- Install Redis:
- > composer require predis/predis
- Configure Laravel to Use Redis: Update your .env
- > CACHE_DRIVER=redis
- > BROADCAST_DRIVER=redis 4.
- Update broadcasting.php Configuration `config/broadcasting.php`configuration
  file to use the Redis broadcast driver:

```php
'connections' => [
    'redis' => [
        'driver' => 'redis',
        'connection'=> 'default',
        ],
    ]
```

- Broadcasting Events: In your Laravel application,For example, you can create
  an event class:
- > php artisan make:event NewMessage
- Update the NewMessage event class:

```php
class NewMessage implements ShouldBroadcast { use
SerializesModels;

    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function broadcastOn()
    {
        return new Channel('chat.' . $this->message->chat_id);
    }

}
```

- This example assumes you have a Message model with a chat_id column.
- Broadcasting from Controller: In your controller, you can broadcast the event
  when a new message is sent:

```php
use App\Events\NewMessage;

public function sendMessage(Request $request) { // Validate and save the message

    // Broadcast the new message event
    broadcast(new NewMessage($message));

    return response()->json(['message' => $message]);

}
```

- Frontend Integration: In your frontend, use Laravel Echo to listen for
  events:in a Vue component

```js
mounted() {
    Echo.channel('chat.' + this.chatId) .listen('NewMessage', (event) => { // Handle
    the new message event console.log('New message received:', event.message); });
}
```

- Broadcasting to Redis: Ensure that your Laravel application is configured to
  broadcast events to Redis by running:
- > php artisan queue:listen
- This command will start the queue worker that will broadcast events to Redis.
- Test: Send a new message in your application, and you should see the "New
  message received" log in the console.
- This setup allows you to broadcast real-time events using Redis as a message
  broker. Adjust the event classes, channels, and logic based on your specific
  application requirements.
- If you have Laravel as the backend and Vue.js separately as the frontend, the
  real-time messaging and notification system can still be implemented using
  Laravel Broadcasting for real-time updates and Laravel Notifications for user
  notifications.

## what will be the deference in code if i used redis and the deference if i used rabbit?

The primary difference between using Redis and RabbitMQ as message brokers lies
in their design and functionality. Both are powerful tools, but they serve
different purposes.

- Redis is an in-memory data structure store, often used as a caching mechanism
  or a message broker for real-time updates. In the context of Laravel
  Broadcasting, Redis is commonly used to broadcast events to multiple
  subscribers.

- Laravel Broadcasting with Redis Example: Configure Laravel to Use Redis .env
  file to use Redis as the broadcasting driver:

```sh
BROADCAST_DRIVER=redis
```

- Update Broadcasting Configuration config/broadcasting.php

```php
'connections' => [
    'redis' => [
        'driver' => 'redis',
        'connection'=> 'default',
        ],
        ],
```

- Broadcast Events using Laravel Broadcasting:

```php
use Illuminate\Support\Facades\Broadcast;
// Example: Broadcasting a NewMessage event Broadcast::channel('chat.{chatId}',
function ($user, $chatId) { return true; // You can add custom logic to
determine who can listen to the channel });
```

- Handle Events in Vue.js In your Vue.js frontend, listen for events using
  Laravel Echo

```js
  mounted() {
    Echo.channel('chat.' + this.chatId)
    .listen('NewMessage', (event) => { // Handle the new message event
    console.log('New message received:', event.message); // Update your UI to display the new message
    });
    }
```

- RabbitMQ is a message broker that implements the Advanced Message. Queuing
  Protocol (AMQP). It is designed for distributed systems and supports complex
  messaging scenarios, including message queuing, routing, and more.
- Laravel Queues with RabbitMQ Example: Install Required Package Install the
  required package for Laravel to work with RabbitMQ:

```sh
composer require vladimir-yuldashev/laravel-queue-rabbitmq
```

- Configure Laravel to Use RabbitMQ .env file with RabbitMQ connection details:

```sh
QUEUE_CONNECTION=rabbitmq RABBITMQ_HOST=your-rabbitmq-host
RABBITMQ_PORT=5672 RABBITMQ_VHOST=your-rabbitmq-vhost
RABBITMQ_LOGIN=your-rabbitmq-username RABBITMQ_PASSWORD=your-rabbitmq-password
```

- Queue a Job In your Laravel application, you can use queues to offload tasks
  to RabbitMQ:

```php
// Example: Dispatching a job to the RabbitMQ queue dispatch(new
ProcessNewMessage($message));
```

- Create a job class that handles the dispatched task:

```sh
php artisan make:job ProcessNewMessage
```

- Update the ProcessNewMessage job class:

```php
public function handle() {
    // Handle the new message processinglogic
    // This could include broadcasting events or other tasks
    }
```

- Handle Events in Vue.js: If you still want to use Laravel Broadcasting for
  real-time updates, you can continue to use Laravel Broadcasting with Redis on
  the frontend.

## Comparison: Redis:

- Easier to set up for real-time updates using Laravel Broadcasting. Suitable
  for scenarios where real-time communication is a primary concern. Often used
  for simple use cases where message brokering is needed.
- RabbitMQ:More complex and powerful message broker designed for distributed
  systems. Suitable for scenarios where you need advanced queuing, routing, and
  reliability. Often used in enterprise-level applications with complex
  messaging requirements. Choose between Redis and RabbitMQ based on the
  specific requirements of your application. If you primarily need real-time
  updates and simplicity, Redis may be a more straightforward choice. If you
  need advanced messaging capabilities and are working on a larger, distributed
  system, RabbitMQ might be a more suitable option

## Using Laravel's built-in notifications system is another approach

to achieve real-time updates in your application. The difference lies in the way
notifications are delivered and handled. Here's a brief comparison between using
notifications and Laravel Broadcasting:

### Using Laravel Broadcasting:

- Real-Time Communication:Broadcasting is designed for real-time communication
  between the server and connected clients.
- It's suitable for scenarios where updates need to be pushed to clients
  immediately without them having to request the changes actively.
- Push Mechanism: Laravel Echo and broadcasting use a push mechanism to notify
  clients of updates. Events are broadcasted to channels, and clients (via
  Laravel Echo) listen for these events.
- Ease of Implementation Broadcasting is well-suited for scenarios where
  real-time updates are essential. It requires additional setup, such as
  configuring a broadcasting driver (like Pusher) and creating events and
  listeners.

### Using Laravel Notifications: Delayed Delivery:

- Laravel notifications are typically used for sending notifications over
  various channels (email, SMS, database, etc.). Notifications are often sent as
  a response to specific actions and may not be delivered in real-time.
- User-Initiated: Notifications are usually triggered by user actions, such as
  sending a message, and may not be suitable for real-time updates where instant
  delivery is critical.
- Channels Laravel notifications support various channels, including email, SMS,
  database, etc. It's suitable for scenarios where you want to notify users
  through multiple channels, not just real-time updates.

### Which to Choose: Real-Time Updates:

If you need real-time updates, such as notifying users about new messages
instantly, Laravel Broadcasting is more suitable. Notification for User Actions:
If you're notifying users about specific events that are not time-sensitive
(e.g., an email notification for a new message), Laravel Notifications might be
more appropriate.

# Although we target BroadCasting and Pusher But we will do also Notification just fro clearfying the deference Begining by Notifications and then we will use Broadcast

- First we need to configutre the QUeue Driver that will be used to save
  notifications jobs as we need to make notifications in the background to not
  affect the dataflow and performance of our frontend when we build it
- we can use many tech such as Database itself or we can use redis for the same
  purpose where redis is also db or store server
- in `.env`

```sh
QUEUE_CONNECTION=database
```

- note that if we dierectly allow notification we may suffer in bad performance
  in the frontend so we will do this notification in the background using queues
  and jobs for that issue we need to create the required tables where we will
  send notification through email and through DB
  - > QUEUE_CONNECTION=database
  - chancing it from `sync` to `database` where sync done through the same
    process but in bg but this may result in low performance because it is not
    totaly decoupled so we will use database insteade
  - create the required tables

```sh
php artisan queue:table
php artisan migrate
```

- this command will create jobs table in database and migrate it to the database
- then in order to handle notification we can use many methods but fro clean
  code we can simple create notifiction class that will notify the user by email
  and update database
- > php artisan make:notification MessageCreatedNotification
- > php artisan notifications:table
- > php artisan migrate: fresh --seed

```php
<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Message;
class MessageCreatedNotification extends Notification
{
    use Queueable;
    public $message;
    /**
     * Create a new notification instance.
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('A new message has been created.')
            ->action('View Message', url('/messages/' . $this->message->id))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
    public function toDatabase($notifiable)
    {
        return [
            'content' => 'A new message has been created.',
            'message_id' => $this->message->id,
        ];
    }
}

```

- after creating the initail notification we need to create the job that will
  call it this job will be called from an observer that keep watching our
  createmesaagae method so lets create the observer
- > php artisan make:observer MessageObserver --model=Message
- Register the Observer `app/Providers/AppServiceProvider.php`

```php
   public function boot()
    {
        // Register the MessageObserver for the Message model
        Message::observe(MessageObserver::class);
    }
```

- now lets create the job
- > php artisan make:job ProcessMessage

```php
<?php

<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Events\MessageNotificationEvent;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Notifications\MessageCreatedNotification;
class ProcessMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public $message;

    public function __construct(MessageNotificationEvent  $event)
    {
        $this->message = $event->message;
    }

    public function handle()
    {
        // Dispatch the event that will notify the user
        event(new MessageNotificationEvent($this->message));
    }
}
```

- Create a Notification Event
- > php artisan make:event MessageNotificationEvent

```php
<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Message;
class MessageNotificationEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}

```

- Create a Notification Listener
- > php artisan make:listener SendMessageNotification
  > --event=MessageNotificationEvent

```php
<?php

namespace App\Listeners;
use App\Notifications\MessageCreatedNotification;
use App\Events\MessageNotificationEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendMessageNotification
{use InteractsWithQueue;
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(MessageNotificationEvent $event)
    {
        // Notification logic here, including email and database notifications
        $event->message->user->notify(new MessageCreatedNotification($event->message));
    }
}
```

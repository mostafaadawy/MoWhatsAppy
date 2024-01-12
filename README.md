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

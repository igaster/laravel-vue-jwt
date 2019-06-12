https://dillinger.io/
This is a demo Laravel 5.8 application implementing JWT API autentication.

# A. Instructions:

- clone repository & serve ie under `laravel.test` local domain
- `composer install`
- `php artisan migrate --seed`

# B. Guide:

### 1. Lets visit an API endpoint that requires an authenticated user:

```
curl -X POST \
  http://laravel.test/api/auth/me \
  -H 'Accept: application/json'
 ```
 
 We will receive a JSON response
 
 ```json
 {
   "message":"Unauthenticated."
 }
 ```
 
#### 2. Authenticate with some valid user credentials. 

```
curl -X POST \
  http://laravel.test/api/auth/login \
  -F email=admin@admin.com \
  -F password=admin
  ```

Response:

```json
{
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sYXJhdmVsLnRlc3RcL2FwaVwvYXV0aFwvbG9naW4iLCJpYXQiOjE1NjAyNDc0NzQsImV4cCI6MTU2MDI1MTA3NCwibmJmIjoxNTYwMjQ3NDc0LCJqdGkiOiJxaDZGZThFeElkZUtYSmVyIiwic3ViIjoxLCJwcnYiOiI4N2UwYWYxZWY5ZmQxNTgxMmZkZWM5NzE1M2ExNGUwYjA0NzU0NmFhIn0.yaVncEfEtr5Stl1vutgVcBynv7PmzRNkbPhBWwsm3m0",
    "token_type": "bearer",
    "expires_in": 3600
}
```

- We should store the token (ie in JS local storage) and use it in future requests
- The token will expire after some time (by default 1h)

#### 3. Access API with the token

We can add the token into the Athorization Bearer header like:

```
curl -X POST \
  http://laravel.test/api/auth/me \
  -H 'Accept: application/json' \
  -H 'Authorization: Bearer PUT_YOUR_JWT_TOKEN_HERE'
```

Response

```json
{
    "id": 1,
    "name": "Admin User",
    "email": "admin@admin.com",
    "created_at": "2019-06-11 09:57:55",
    "updated_at": "2019-06-11 09:57:55"
}
```

#### 4. Logout (Invalidate token)

```
curl -X POST \
  http://laravel.test/api/auth/logout \
  -H 'Accept: application/json' \
  -H 'Authorization: Bearer PUT_YOUR_JWT_TOKEN_HERE'
```

Now the token has been invalidated and can not be used anymore

#### 5. Refresh Token

```
curl -X POST \
  http://laravel.test/api/auth/refresh \
  -H 'Accept: application/json' \
  -H 'Authorization: Bearer PUT_YOUR_JWT_TOKEN_HERE'
```

- We will receive a new token that we can use. The old one is invalid
- We should refresh our token before it expires (exchange old token with a fresh one)
- If we let the token expire then the user must login again to obtain a fresh token

# C. Application setup:

### 1. Configure jwt guard

At `config\auth.php` we configure a new `api-jwt` guard that uses the jwt driver

```php
    'guards' => [
        //...
        'api-jwt' => [
            'driver' => 'jwt',
            'provider' => 'users',
            'hash' => false,
        ],
    ],
```

### 2. Auth Routes:

- `routes\api,php`: All routes that are executing the `auth:api-jwt` middlware (guard) will verify that the request headers conatin a valid JWT token. 
- the `login` route should be excluded (obviously!)

```php

// Handle JWT Authentication
Route::group(['prefix' => 'auth'], function () {
    Route::post('login', 'AuthController@login');
    
    Route::group(['middleware' => 'auth:api-jwt'], function () {
        Route::post('logout', 'AuthController@logout');
        Route::post('refresh', 'AuthController@refresh');
        Route::post('me', 'AuthController@me');
    });
});

// Protected API routes:
Route::group(['middleware' => 'auth:api-jwt'], function () {
    // Put here all routes that require JWT authentication
});
```

### 3. Auth Controller:

- If we don't want to setup the JWT as the default guard, then we need to explicit retrieve it throgh the Auth Facade. ie use `auth('api-jwt')` instead of `auth()`  

View `app/Http/Controllers/AuthController.php` for reference

# D. Client (vue.js)

Client should handle 
- user login to get a JWT tokem 
- Store token on local storage or coockie 
- Add token to all API requests 
- Refresh the token bofore it expires
- Handle expired tokens (ie ask user to login again)


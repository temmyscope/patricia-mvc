## patricia-mvc api

***This project requires PHP 7.4***

```json
"require": { 
    "php": ">=7.4.0"
}
```

***All other requirements will be installed/updated after ```composer update``` is run***

- Create a PHP MVC Framework that has support for routing. 
- Use the router to create API endpoints that will allow users do the following:
- Login, Logout, Register

## Solution

- Created by Elisha Temiloluwa OYAWALE

## Code Structure

***The framework file directory is split into***

- app, cache, config, routes & tests folder
- The "storage" folder is a makeshift for a database [storing user data in json files]
- The vendor folder is created when developer runs `composer update` in the project folder
- after git cloning the framework

### EndPoints

- The available endpoints are what the specification prescribes:
- login, logout & Register

### Database && Storage

- The database has two schemas, session.json & users.json, which are just
- makeshift database schemas for storing data;
- users.json : stores users' data on sign up and retrieves from it during login
- session.json: stores user session on login and is deleted on logout;
- session in the above context is of course valid (unexpired) issued jwt token


### Router && Route

- The Router used in this framework is from 
***sevens/router library: which was developed by Elisha Temiloluwa OYAWALE(me)***

```php

- $router->post('login', user login Callable); 

- $router->post('register', user registration callable);

- $router->post('logout', logout Callable); 
# only available to users with set session token in request header

```

### Authorization

- Authorization is done using the Bearer token (JWT) present in the header of the request

- Authorization is revoked after user logs out

### Unit Testing

- Unit Testing the framework can be done using PHPUnit library which has been "dev-required" on this project

***phpdocumentor can also be used to generate documentation for the code***
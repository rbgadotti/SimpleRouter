# Simple Router
A simple PHP Router created in a single file differentiating verbs from requisition, simulating requests and work with url params

# What I Learned
* How a router works
* How to use regex to detected url params

## Usage

Create an index.php file with the following contents:

```php
<?php

require_once('SimpleRouter.php');

$router = new SimpleRouter();

// $router->setSimulatedRequest('POST', '/users/10/posts/rafael', 'page=1');

$router->any('/user/:id', function($id){
    echo '<h1>User id: '. $id . '</h1>';
});

$router->get('/hello', function(){
    echo '<h1>Hello World!</h1>';
});

$router->get('/profile/:id', 'ProfileController@showProfile');

$router->run();
```

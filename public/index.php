<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Bramasta\Belajar\PHP\MVC\App\Router;
use Bramasta\Belajar\PHP\MVC\config\Database;   
use Bramasta\Belajar\PHP\MVC\Controller\HomeController;
use Bramasta\Belajar\PHP\MVC\Controller\UserController;
use Bramasta\Belajar\PHP\MVC\Middleware\MustLoginMiddleware;
use Bramasta\Belajar\PHP\MVC\Middleware\MustNotLoginMiddleware;

Database::getConnection('prod');

//Home Controller
Router::add('GET', '/', HomeController::class, 'index',[]);

//User Controller
Router::add('GET', '/users/register', UserController::class, 'register', [MustNotLoginMiddleware::class]);
Router::add('POST', '/users/register', UserController::class, 'postRegister', [MustNotLoginMiddleware::class]); 
Router::add('GET', '/users/login', UserController::class, 'login', [MustNotLoginMiddleware::class]);
Router::add('POST', '/users/login', UserController::class, 'postLogin', [MustNotLoginMiddleware::class]); 
Router::add('GET', '/users/logout', UserController::class, 'logout', [MustLoginMiddleware::class]); 
Router::add('GET', '/users/profile', UserController::class, 'updateProfile', [MustLoginMiddleware::class]);
Router::add('POST', '/users/profile', UserController::class, 'postUpdateProfile', [MustLoginMiddleware::class]);
Router::add('GET', '/users/password', UserController::class, 'updatePassword', [MustLoginMiddleware::class]);
Router::add('POST', '/users/password', UserController::class, 'postUpdatePassword', [MustLoginMiddleware::class]);

Router::run();      
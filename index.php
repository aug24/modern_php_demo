<?php

require 'vendor/autoload.php';
require 'functions/hallo.php';

$app = new \Slim\App();

$app->get('/hallo/{name}', sayHallo);

// Run app
$app->run();

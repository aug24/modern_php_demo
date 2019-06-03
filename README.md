# Instructions to create this repository from scratch.

## Initialise, add basic dependencies
```
git init
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
mkdir bin functions tests
php composer-setup.php --install-dir=bin --filename=composer
rm composer-setup.php
bin/composer require slim/slim:^3.6.0
bin/composer require simpletest/simpletest:^1.1 --dev
bin/composer require squizlabs/php_codesniffer:^3 --dev

mv composer.json composer.json.orig
cat composer.json.orig \
  | jq '. + { "scripts": {
    "test": "php tests/alltests.php",
    "lint": "phpcs --ignore=vendor --standard=PSR2 .",
    "fix": "phpcbf --ignore=vendor --standard=PSR2 ."
  }}' \
  | tee composer.json 
rm composer.json.orig

bin/composer install
echo vendor >> .gitignore
cat >Dockerfile <<EOF
FROM php:5-apache
RUN docker-php-ext-install sockets pdo pdo_mysql mysqli
COPY . /var/www/html
EXPOSE 80
EOF
```

## Add code
```
cat >index.php <<EOF
<?php

require 'vendor/autoload.php';
require 'functions/hallo.php';

\$app = new \\Slim\\App();

\$app->get('/hallo/{name}', sayHallo);

// Run app
\$app->run();
EOF
```

```
cat >functions/hallo.php <<EOF
<?php

function sayHallo (\$request, \$response, \$args) { return \$response->write("Hallo lovely " . \$args['name']); }
EOF
```

```
cat >tests/alltests.php <<EOF
<?php
require_once('vendor/simpletest/simpletest/autorun.php');

class AllTests extends TestSuite {

  private \$directory = "tests";

  public function allTests() {
     \$tests = array_diff(scandir(\$this->directory), array('..', '.', 'alltests.php'));
     foreach( \$tests as \$file) {
        \$this->addFile(\$this->directory . "/" . \$file);
     }
  }
}
?>
EOF
```

## Add tests

```
cat >tests/hallotest.php <<EOF
<?php
require_once('vendor/simpletest/simpletest/autorun.php');
require_once('functions/hallo.php');

class DummyHalloResponse {
   private \$body;
   public function write(\$string) {\$this->body = \$string; }
   public function getBody() {return \$this->body;}
}

class TestHallo extends UnitTestCase {
    public function testHalloMessage() {
        \$response = new DummyHalloResponse();
        \$args = array();
        \$args['name'] = 'Justin';

        sayHallo (\$request, \$response, \$args);

        \$this->assertEqual(\$response->getBody(), "Hallo Lovely Justin");
    }
}
?>
EOF
```

## Run tests

```
bin/composer test
```

## Build and run

```
docker build -t test .
```
or
```
php -S localhost:8080
```

<?php

function sayHallo($request, $response, $args)
{
    return $response->write("Hallo lovely " . $args['name']);
}

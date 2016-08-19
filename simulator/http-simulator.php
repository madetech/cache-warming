<?php
require_once __DIR__ . '/../vendor/autoload.php';
$uri = $_SERVER['REQUEST_URI'];
switch (true) {
    case preg_match('/sitemap\.xml/', $uri, $matches):
        echo file_get_contents(__DIR__ . "/responses/sitemap.xml");
        break;
    default:
        break;
}
@mkdir(__DIR__ . '/requests/' . $uri, 0777, true);
file_put_contents(
    __DIR__ . '/requests/' . $uri . '/request-' . microtime(true) . '.serialised',
    serialize(['get' => $_GET, 'post' => $_POST, 'server' => $_SERVER])
);
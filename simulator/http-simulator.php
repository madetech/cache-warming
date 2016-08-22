<?php
require_once __DIR__ . '/../vendor/autoload.php';
$uri = $_SERVER['REQUEST_URI'];
switch (true) {
    case preg_match('~gb/en/sitemap\.xml~', $uri, $matches):
        echo file_get_contents(__DIR__ . "/responses/britishSiteMap.xml");
        break;
    case preg_match('~nl/nl/sitemap\.xml~', $uri, $matches):
        echo file_get_contents(__DIR__ . "/responses/dutchSiteMap.xml");
        break;
    case preg_match('/sitemap([0-9]?)\.xml/', $uri, $matches):
        $siteMapNumber = is_numeric($matches[1]) ? $matches[1] : '';
        echo file_get_contents(__DIR__ . "/responses/sitemap{$siteMapNumber}.xml");
        break;
    case preg_match('/timeout/', $uri, $matches):
        sleep(40);
        break;
    case preg_match('/alive/', $uri, $matches):
        echo 'ALIVE';
        exit;
        break;
    default:
        echo file_get_contents(__DIR__ . "/responses/index.html");
        break;
}
@mkdir(__DIR__ . '/requests/' . $uri, 0777, true);
file_put_contents(
    __DIR__ . '/requests/' . $uri . '/request-' . microtime(true) . '.serialised',
    serialize(['get' => $_GET, 'post' => $_POST, 'server' => $_SERVER])
);
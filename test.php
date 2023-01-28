<?php

namespace Hexlet\Code\Test;

$autoloadPath1 = __DIR__ . '/../../autoload.php';
$autoloadPath2 = __DIR__ . '/vendor/autoload.php';
if (file_exists($autoloadPath1)) {
    require_once $autoloadPath1;
} else {
    require_once $autoloadPath2;
}

use function  Hexlet\Code\Parsers\render;
use function Hexlet\Code\Utils\getFixtureFullPath;
use Symfony\Component\Yaml\Yaml;

$arr = render("tests/fixtures/file2.json");
// $arr = render(getFixtureFullPath('file1.json'));
$yaml = Yaml::dump($arr);
print_r($yaml);
// file_put_contents("tests/fixtures/file2.yaml", $yaml);

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
use function Hexlet\Code\Parsers\getFixtureFullPath;
use Symfony\Component\Yaml\Yaml;
use function Differ\Differ\genDiff;
use function Hexlet\Code\Formatters\stylish;
use function Hexlet\Code\Formatters\plain;

$arr1 = "tests/fixtures/file1.json";
$arr2 = "tests/fixtures/file2.json";
$result = genDiff($arr1, $arr2, "plain");
print_r($result);
// print_r(stylish($result));
// $arr = render(getFixtureFullPath('file1.json'));
// $yaml = Yaml::dump($arr);
// print_r($yaml);
// file_put_contents("tests/fixtures/file2.yaml", $yaml);

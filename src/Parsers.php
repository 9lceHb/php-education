<?php

namespace Hexlet\Code\Parsers;

use Symfony\Component\Yaml\Yaml;

function getExtention($path)
{
    $pathList = explode(".", $path);
    return end($pathList);
}

function getFixtureFullPath($fixtureName)
{
    // $parts = [__DIR__, 'tests', 'fixtures', $fixtureName];
    $parts = ['tests', 'fixtures', $fixtureName];
    return realpath(implode('/', $parts));
}

function render($path)
{
    $fullPath = realpath($path);
    $text = file_get_contents($fullPath);
    $extention = getExtention($path);
    if ($extention === 'json') {
        return json_decode($text, true);
    }
    return Yaml::parseFile($fullPath);
}

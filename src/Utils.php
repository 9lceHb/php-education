<?php

namespace Hexlet\Code\Utils;

use function Functional\flat_map;

function getNode($key, $value, $type)
{
    $node = [
        "key" => $key,
        "value" => $value,
        "type" => $type
    ];
    return $node;
}

function getValue($value)
{
    switch (gettype($value)) {
        case 'boolean':
            return $value ? 'true' : 'false';
        case 'NULL':
            return 'null';
        default:
            return $value;
    }
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
    return json_decode($text, true);
}

function genDiff($path1, $path2)
{
    $before = render($path1);
    $after = render($path2);
    $keysDeleted = array_keys(array_diff_key($before, $after));
    $keysAdded = array_keys(array_diff_key($after, $before));
    $keysIntersected = array_keys(array_intersect_key($before, $after));
    $deletedElem = array_map(fn($key) => getNode($key, $before[$key], 'deleted'), $keysDeleted);
    $addedElem = array_map(fn($key) => getNode($key, $after[$key], 'added'), $keysAdded);
    $sameKeyElem = flat_map($keysIntersected, function ($key) use ($before, $after) {
        if ($before[$key] === $after[$key]) {
            return [getNode($key, $before[$key], 'unchanged')];
        }
        return [getNode($key, $before[$key], 'changedFrom'), getNode($key, $after[$key], 'changedTo')];
    });
    // $sameKeyElem = flatten($sameKeyElem);
    $result = array_merge($deletedElem, $addedElem, $sameKeyElem);
    usort($result, function ($node1, $node2) {
        if ($node1["key"] !== $node2["key"]) {
            return $node1["key"] <=> $node2["key"];
        }
        if ($node1["type"] === 'changedFrom') {
            return -1;
        }
        if ($node1["type"] === 'changedTo') {
            return 1;
        }
        return 0;
    });
    $resultStrings = array_map(function ($node) {
        // $key = var_export($node["key"]);
        // $value = var_export($node['value']);
        $key = $node["key"];
        $value = getValue($node['value']);
        if ($node["type"] === 'deleted' || $node["type"] === 'changedFrom') {
            return "- {$key}: {$value}";
        }
        if ($node["type"] === 'added' || $node["type"] === 'changedTo') {
            return "+ {$key}: {$value}";
        }
        return "  {$key}: {$value}";
    }, $result);
    return implode("\n", $resultStrings);
}

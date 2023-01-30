<?php

namespace Hexlet\Code\Formatters;

use function Functional\flat_map;

function getValue($value, $formatter = 'stylish')
{
    switch (gettype($value)) {
        case 'boolean':
            return $value ? 'true' : 'false';
        case 'NULL':
            return 'null';
        case 'string':
            if ($formatter === 'plain') {
                return "'{$value}'";
            }
            return $value;
        default:
            return $value;
    }
}

function iterValue($array, $deph)
{
    $defaultIndentValues = '    ';
    $indent = str_repeat($defaultIndentValues, $deph);
    $bracketIndent = str_repeat($defaultIndentValues, $deph + 1);
    return flat_map(array_keys($array), function ($key) use ($indent, $array, $deph, $bracketIndent) {
        $value = getValue($array[$key]);
        // $value = $array[$key];
        if (is_array($value)) {
            $deph += 1;
            $newValue = iterValue($value, $deph);
            return ["{$indent}    {$key}: {", ...$newValue, "{$bracketIndent}}"];
        }
        return ["{$indent}    {$key}: {$value}"];
    });
}
function getSymbol($type)
{
    switch ($type) {
        case 'deleted':
            return '-';
        case 'changedFrom':
            return '-';
        case 'added':
            return '+';
        case 'changedTo':
            return '+';
        default:
            return ' ';
    }
}

function stylish($tree)
{
    $defaultIndent = '    ';
    function iterNode($tree, $deph, $defaultIndent)
    {
        $indent = str_repeat($defaultIndent, $deph);
        $bracketIndent = str_repeat($defaultIndent, $deph + 1);
        $result = flat_map($tree, function ($node) use ($indent, $deph, $defaultIndent, $bracketIndent) {
            $key = $node["key"];
            $value = getValue($node['value']);
            $children = $node["children"];
            $symbol = getSymbol($node["type"]);
            if (is_array($children)) {
                $deph += 1;
                $newNode = iterNode($children, $deph, $defaultIndent);
                return ["{$indent}  {$symbol} {$key}: {", ...$newNode, "{$bracketIndent}}"];
            }
            if (!is_array($value)) {
                return "{$indent}  {$symbol} {$key}: {$value}";
            }
            $deph += 1;
            $newValue = iterValue($value, $deph);
            return ["{$indent}  {$symbol} {$key}: {", ...$newValue, "{$bracketIndent}}"];
        });
        return $result;
    }
    $result = iterNode($tree, 0, $defaultIndent);
    return "{\n" . implode("\n", $result) . "\n}";
}

function plain($tree)
{
    function inner($tree, $path)
    {
        $result = flat_map($tree, function ($node) use ($path, $tree) {
            $key = $node["key"];
            $value = getValue($node['value'], 'plain');
            $children = $node["children"];
            $path = "{$path}.{$key}";
            if (is_array($children)) {
                $newNode = inner($children, $path);
                return [...$newNode];
            }
            if (is_array($value)) {
                $value = "[complex value]";
            }
            $path = substr($path, 1);
            switch ($node["type"]) {
                case 'deleted':
                    return "Property '{$path}' was removed";
                case 'added':
                    return "Property '{$path}' was added with value: {$value}";
                case 'unchanged':
                case 'changedTo':
                    return;
                default:
                    $newValue = getNewValue($tree, $key);
                    return "Property '{$path}' was updated. From {$value} to {$newValue}";
            }
        });
        return $result;
    }
    $result = inner($tree, '');
    return implode("\n", $result);
}

function getNewValue($tree, $key)
{
    $filtredArray = array_filter($tree, function ($innerNode) use ($key) {
        return ($key === $innerNode["key"] && $innerNode["type"] === 'changedTo');
    });
    $filtredArray = array_values(($filtredArray));
    return getValue($filtredArray[0]["value"], 'plain');
}

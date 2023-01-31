<?php

namespace Hexlet\Code\Formatters;

use function Functional\flat_map;

function getValue($value, string $formatter = 'stylish')
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

function iterValue(array $array, int $deph)
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
function getSymbol(string $type)
{
    $symbols = [
        'deleted' => '-',
        'changedFrom' => '-',
        'added' => '+',
        'changedTo' => '+',
    ];
    if (array_key_exists($type, $symbols)) {
        return $symbols[$type];
    }
    return ' ';
}

function iterNode2(array $tree, int $deph, string $defaultIndent)
{
    $indent = str_repeat($defaultIndent, $deph);
    $bracketIndent = str_repeat($defaultIndent, $deph + 1);
    return flat_map($tree, function ($node) use ($indent, $deph, $defaultIndent, $bracketIndent) {
        $key = $node["key"];
        $value = getValue($node['value']);
        $children = $node["children"];
        $symbol = getSymbol($node["type"]);
        if (is_array($children)) {
            $deph += 1;
            $newNode = iterNode2($children, $deph, $defaultIndent);
            return ["{$indent}  {$symbol} {$key}: {", ...$newNode, "{$bracketIndent}}"];
        }
        if (!is_array($value)) {
            return "{$indent}  {$symbol} {$key}: {$value}";
        }
        $deph += 1;
        $newValue = iterValue($value, $deph);
        return ["{$indent}  {$symbol} {$key}: {", ...$newValue, "{$bracketIndent}}"];
    });
}

function stylish(array $tree)
{
    $defaultIndent = '    ';
    $result = iterNode2($tree, 0, $defaultIndent);
    return "{\n" . implode("\n", $result) . "\n}";
}

function inner1(array $tree, string $path)
{
    return flat_map($tree, function ($node) use ($path, $tree) {
        $key = $node["key"];
        $value = is_array($node['value']) ? "[complex value]" : getValue($node['value'], 'plain');
        $children = $node["children"];
        $path = "{$path}.{$key}";
        $type = $node["type"];
        if (is_array($children)) {
            $newNode = inner1($children, $path);
            return [...$newNode];
        }
        $path = substr($path, 1);
        $resultString = getResultString($node, $path, $tree, $value);
        return $resultString;
    });
}

function plain(array $tree)
{
    $result = inner1($tree, '');
    return implode("\n", $result);
}

function getResultString(array $node, string $path, array $tree, $value)
{
    $key = $node["key"];
    $type = $node["type"];
    switch ($type) {
        case 'deleted':
            $resultString = "Property '{$path}' was removed";
            break;
        case 'added':
            $resultString = "Property '{$path}' was added with value: {$value}";
            break;
        case 'unchanged':
        case 'changedTo':
            return;
        default:
            $newValue = getNewValue($tree, $key);
            $resultString = "Property '{$path}' was updated. From {$value} to {$newValue}";
    }
    return $resultString;
}

function getNewValue(array $tree, string $key)
{
    $filtredArray = array_filter($tree, function ($innerNode) use ($key) {
        return ($key === $innerNode["key"] && $innerNode["type"] === 'changedTo');
    });
    $filtredArray = array_values(($filtredArray));
    $newValue = getValue($filtredArray[0]["value"], 'plain');
    return is_array($newValue) ? "[complex value]" : $newValue;
}

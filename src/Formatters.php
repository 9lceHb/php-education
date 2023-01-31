<?php

namespace Hexlet\Code\Formatters;

use function Functional\flat_map;

function getValue(mixed $value, string $formatter = 'stylish')
{
    switch (gettype($value)) {
        case 'boolean':
            // return $value ? 'true' : 'false';
            return var_export($value, true);
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
            $newValue = iterValue($value, $deph + 1);
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
            $newNode = iterNode2($children, $deph + 1, $defaultIndent);
            return ["{$indent}  {$symbol} {$key}: {", ...$newNode, "{$bracketIndent}}"];
        }
        if (!is_array($value)) {
            return "{$indent}  {$symbol} {$key}: {$value}";
        }
        $newValue = iterValue($value, $deph + 1);
        return ["{$indent}  {$symbol} {$key}: {", ...$newValue, "{$bracketIndent}}"];
    });
}

function stylish(array $tree)
{
    $defaultIndent = '    ';
    $result = iterNode2($tree, 0, $defaultIndent);
    return "{\n" . implode("\n", $result) . "\n}";
}

function plainInner1(array $tree, string $path)
{
    return flat_map($tree, function ($node) use ($path, $tree) {
        $key = $node["key"];
        $value = is_array($node['value']) ? "[complex value]" : getValue($node['value'], 'plain');
        $children = $node["children"];
        $newPath = "{$path}.{$key}";
        $type = $node["type"];
        if (is_array($children)) {
            $newNode = plainInner1($children, $newPath);
            return [...$newNode];
        }
        $fullPath = substr($newPath, 1);
        $resultString = getResultString($node, $fullPath, $tree, $value);
        return $resultString;
    });
}

function plain(array $tree)
{
    $result = plainInner1($tree, '');
    return implode("\n", $result);
}

function getResultString(array $node, string $path, array $tree, mixed $value)
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
    $filtredArray = array_values(array_filter($tree, function ($innerNode) use ($key) {
        return ($key === $innerNode["key"] && $innerNode["type"] === 'changedTo');
    }));
    $newValue = getValue($filtredArray[0]["value"], 'plain');
    return is_array($newValue) ? "[complex value]" : $newValue;
}

<?php

namespace Hexlet\Code\Stylish;

use function Functional\flat_map;

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

function stylish($tree)
{
    $defaultIndent = '    ';
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
    function iterNode($tree, $deph, $defaultIndent)
    {
        $indent = str_repeat($defaultIndent, $deph);
        $bracketIndent = str_repeat($defaultIndent, $deph + 1);
        $result = flat_map($tree, function ($node) use ($indent, $deph, $defaultIndent, $bracketIndent) {
            $key = $node["key"];
            $value = getValue($node['value']);
            // $value = $node['value'];
            $children = $node["children"];
            if (is_array($children)) {
                $deph += 1;
                $newNode = iterNode($children, $deph, $defaultIndent);
                return ["{$indent}    {$key}: {", ...$newNode, "{$bracketIndent}}"];
            }
            if ($node["type"] === 'deleted' || $node["type"] === 'changedFrom') {
                if (!is_array($value)) {
                    return "{$indent}  - {$key}: {$value}";
                }
                $deph += 1;
                $newValue = iterValue($value, $deph);
                return ["{$indent}  - {$key}: {", ...$newValue, "{$bracketIndent}}"];
            }
            if ($node["type"] === 'added' || $node["type"] === 'changedTo') {
                if (!is_array($value)) {
                    return "{$indent}  + {$key}: {$value}";
                }
                $deph += 1;
                $newValue = iterValue($value, $deph);
                return ["{$indent}  + {$key}: {", ...$newValue, "{$bracketIndent}}"];
            }
            if (!is_array($value)) {
                return "{$indent}    {$key}: {$value}";
            }
            $deph += 1;
            $newValue = iterValue($value, $deph);
            return ["{$indent}    {$key}: {", ...$newValue, "{$bracketIndent}}"];
        });
        return $result;
    }
    $result = iterNode($tree, 0, $defaultIndent);
    return "{\n" . implode("\n", $result) . "\n}";
}

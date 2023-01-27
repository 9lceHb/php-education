<?php

$autoloadPath1 = __DIR__ . '/../../autoload.php';
$autoloadPath2 = __DIR__ . '/vendor/autoload.php';
if (file_exists($autoloadPath1)) {
    require_once $autoloadPath1;
} else {
    require_once $autoloadPath2;
}

print_r(var_export(true));
print_r("\n");
print_r(var_export("abba"));
print_r("\n");
print_r("abba");
print_r("\n");
print_r(strval(true));

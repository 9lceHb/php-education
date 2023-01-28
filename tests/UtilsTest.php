<?php

namespace Hexlet\Utils\Tests;

use PHPUnit\Framework\TestCase;

use function Hexlet\Code\Utils\genDiff;
use function Hexlet\Code\Parsers\getFixtureFullPath;

// класс UtilsTest наследует класс TestCase
// имя класса совпадает с именем файла
class UtilsTest extends TestCase
{
    private $testAnswer1;

    public function setUp(): void
    {
        $this->testAnswer1 = <<<DOC
- follow: false
  host: hexlet.io
- proxy: 123.234.53.22
- timeout: 50
+ timeout: 20
+ verbose: true
DOC;
    }
    public function testGendiff(): void
    {
        $path1 = getFixtureFullPath("file1.json");
        $path2 = getFixtureFullPath("file2.json");
        $this->assertEquals($this->testAnswer1, genDiff($path1, $path2));
    }
}

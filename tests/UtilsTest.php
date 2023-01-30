<?php

namespace Hexlet\Utils\Tests;

use PHPUnit\Framework\TestCase;

use function Hexlet\Code\Utils\genDiff;
use function Hexlet\Code\Parsers\getFixtureFullPath;
use function Hexlet\Code\Stylish\stylish;
use function Hexlet\Code\Parsers\render;

// класс UtilsTest наследует класс TestCase
// имя класса совпадает с именем файла
class UtilsTest extends TestCase
{
    private $testAnswer1;

    public function setUp(): void
    {
        $this->testAnswer1 = <<<DOC
        {
            common: {
              + follow: false
                setting1: Value 1
              - setting2: 200
              - setting3: true
              + setting3: null
              + setting4: blah blah
              + setting5: {
                    key5: value5
                }
                setting6: {
                    doge: {
                      - wow: 
                      + wow: so much
                    }
                    key: value
                  + ops: vops
                }
            }
            group1: {
              - baz: bas
              + baz: bars
                foo: bar
              - nest: {
                    key: value
                }
              + nest: str
            }
          - group2: {
                abc: 12345
                deep: {
                    id: 45
                }
            }
          + group3: {
                deep: {
                    id: {
                        number: 45
                    }
                }
                fee: 100500
            }
        }
        DOC;
    }
    public function testGendiff(): void
    {
        $beforeFile = render(getFixtureFullPath("file1.json"));
        $afterFile = render(getFixtureFullPath("file2.json"));
        $this->assertEquals($this->testAnswer1, stylish(genDiff($beforeFile, $afterFile)));
    }
}

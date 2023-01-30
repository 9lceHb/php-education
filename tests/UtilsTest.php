<?php

namespace Hexlet\Utils\Tests;

use PHPUnit\Framework\TestCase;

use function Hexlet\Code\Utils\genDiff;
use function Hexlet\Code\Parsers\getFixtureFullPath;
use function Hexlet\Code\Formatters\stylish;
use function Hexlet\Code\Parsers\render;
use function Hexlet\Code\Formatters\plain;

// класс UtilsTest наследует класс TestCase
// имя класса совпадает с именем файла
class UtilsTest extends TestCase
{
    private $testAnswer1;
    private $testAnswer2;
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
        $this->testAnswer2 = <<<DOC
        Property 'common.follow' was added with value: false
        Property 'common.setting2' was removed
        Property 'common.setting3' was updated. From true to null
        Property 'common.setting4' was added with value: 'blah blah'
        Property 'common.setting5' was added with value: [complex value]
        Property 'common.setting6.doge.wow' was updated. From '' to 'so much'
        Property 'common.setting6.ops' was added with value: 'vops'
        Property 'group1.baz' was updated. From 'bas' to 'bars'
        Property 'group1.nest' was updated. From [complex value] to 'str'
        Property 'group2' was removed
        Property 'group3' was added with value: [complex value]
        DOC;
    }
    public function testGendiff(): void
    {
        $beforeFile = render(getFixtureFullPath("file1.json"));
        $afterFile = render(getFixtureFullPath("file2.json"));
        $this->assertEquals($this->testAnswer1, stylish(genDiff($beforeFile, $afterFile)));
        $this->assertEquals($this->testAnswer2, plain(genDiff($beforeFile, $afterFile)));
    }
}

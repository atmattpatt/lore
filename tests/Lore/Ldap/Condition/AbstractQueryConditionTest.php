<?php

namespace Lore\Ldap\Condition;

class AbstractQueryConditionTest extends \Lore\BaseTest
{
    /**
     * @dataProvider providesStrings
     * @param string $input
     * @param string $expected
     */
    public function testEscape($input, $expected)
    {
        $object = $this->getMock('\Lore\Ldap\Condition\AbstractQueryCondition');

        $actual = $this->invokeInternal($object, 'escape', $input);
        $this->assertEquals($expected, $actual);
    }

    public function providesStrings()
    {
        return array(
            array('cats & dogs', 'cats \26 dogs'),
            array('ACME (r)', 'ACME \28r\29'),
            array('up|down', 'up\7cdown'),
            array('e=mc2', 'e\3dmc2'),
            array('pie > cake', 'pie \3e cake'),
            array('i<3u', 'i\3c3u'),
            array('foo~', 'foo\7e'),
            array('rate**time', 'rate\2atime'),
            array('*@smith.com', '*@smith.com'),
            array('a/k/a', 'a\2fk\2fa'),
            array('c:\\', 'c:\5c'),
        );
    }
}

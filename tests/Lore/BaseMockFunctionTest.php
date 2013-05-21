<?php

namespace Lore;

abstract class BaseMockFunctionTest extends BaseTest
{

    /**
     * Test set up method
     *
     * Verifies that the runkit extension is loaded
     */
    protected function setUp()
    {
        if (!extension_loaded('runkit')) {
            $this->markTestSkipped('Extension runkit not available');
        }
    }

    /**
     * Shortcut to mock a function
     *
     * @param string $functionName
     * @param object $scopeObject
     * @return \PHPUnit_Extensions_MockFunction
     */
    protected function getMockFunction($functionName, $scopeObject)
    {
        return new \PHPUnit_Extensions_MockFunction($functionName, $scopeObject);
    }
}

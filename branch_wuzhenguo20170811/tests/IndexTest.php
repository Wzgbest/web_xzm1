<?php
/**
 * Created by messhair
 * Date: 17-2-20
 */
namespace tests;

use tests\TestCase;

class IndexTest extends TestCase
{
    public function testTest()
    {
        $this->visit('index/index/test')->see('hello world');
    }
}
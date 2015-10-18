<?php

use SmartCrowd\ExternalResource;

/**
 * @coversDefaultClass ExternalResource
 * @runTestsInSeparateProcesses
 */
class ExternalResourceTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers ::getResource
     */
    public function testGetResource() {
        $link = 'http://vk.com';
        $result = ExternalResource::init($link)->getResource();
        $this->assertNotEmpty($result);
        $this->assertNotContains("Ссылка недоступна", $result);

        $link = 'http://habrahabr.ru/kvakvakva/';
        $result = ExternalResource::init($link)->getResource();
        $this->assertContains("Ссылка недоступна 404", $result);

        $link = 'http://kvakvakva/';
        $result = ExternalResource::init($link)->getResource();
        $this->assertContains("Не удалось загрузить страницу", $result);
    }

    /**
     * @covers ::instagramHook
     */
    public function testInstagramHook()
    {
        $link = 'https://instagram.com/p/5FgJNwspx9/?taken-by=jasonstatham';
        $content = ExternalResource::instagramHook($link);
        $this->assertNotEquals($link, $content);
    }

    /**
     * @covers ::encodeUrl
     */
    public function testEncodeUrl()
    {
        $url = "http://москва.рф/index";
        $result = ExternalResource::encodeUrl($url);
        $this->assertEquals("http://xn--80adxhks.xn--p1ai/index", $result);
    }

}
 

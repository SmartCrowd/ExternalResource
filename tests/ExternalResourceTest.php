<?php

loadDepends("ExternalResource.php");

class ExternalResourceTest extends \PHPUnit_Framework_TestCase
{

    public function testUrlisUnavailable()
    {
        $link = 'http://bash.im';
        $content = \ExternalResource::getResource($link);
        $this->assertNotContains("Ссылка недоступна ", $content);

    }

    public function testUrlisavailable()
    {
        $link = 'http://habrahabr.ru/kvakvakva/';
        $content = \ExternalResource::getResource($link);
        $this->assertContains("Ссылка недоступна ", $content);

    }

    public function testInstagram()
    {
        $link = 'https://instagram.com/p/5FgJNwspx9/?taken-by=jasonstatham';

        $content = \ExternalResource::instagramHook($link);
        $this->assertNotEquals($link, $content);
    }

}
 

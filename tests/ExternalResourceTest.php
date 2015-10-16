<?php
namespace assets;

class AssetsManagerTest extends \PHPUnit_Framework_TestCase {

    public function testGetFilesFromDir()
    {
        $link='http://bash.im';
        $content = \ExternalResource::getResource($link);
        $this->assertNotEmpty($content);

    }

}
 

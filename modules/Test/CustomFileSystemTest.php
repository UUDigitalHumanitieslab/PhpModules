<?php

namespace modules\Test;

use modules\CustomFileSystem;

class CustomFileSystemTest extends \PHPUnit_Framework_TestCase
{
    /** @var CustomFileSystem */
    protected $filesystem;
    /** @var  string */
    protected $ref;

    protected function setUp()
    {
        $this->filesystem = new CustomFileSystem();
        $this->ref = "./modules/Test/Files/zipfiles.zip";
    }

    public function testListContent()
    {
        $files = $this->filesystem->listContent("./modules/Test/Files/listFiles");
        $this->assertEquals([$files[0]], ["test.txt"]);
    }

    public function testListContentZip()
    {
        $files = $this->filesystem->listContentZip($this->ref);
        $this->assertEquals(["zipfiles/", "zipfiles/zipTest.txt", "zipfiles/nestedFolder/"], $files);
    }

    public function testGetRealRootZip()
    {
        $realRoot = $this->filesystem->getRealRootZip($this->ref);
        $this->assertEquals("zipfiles/", $realRoot);
    }

    public function testGetContentNthLayer()
    {
        $filesFirstLayer = $this->filesystem->listContentZipNthLayer($this->ref, 0);
        $this->assertEquals(["zipfiles/"], $filesFirstLayer);
        $filesSecondLayer = $this->filesystem->listContentZipNthLayer($this->ref, 1);
        $this->assertEquals(["zipfiles/zipTest.txt", "zipfiles/nestedFolder/"], $filesSecondLayer);
    }

    public function testListFilesZip()
    {
        $files = $this->filesystem->listFilesZip($this->ref);
        $this->assertEquals(["zipfiles/zipTest.txt"], $files);

    }

    public function testListDirsZip()
    {
        $dirs = $this->filesystem->listDirsZip($this->ref);
        $this->assertEquals(["zipfiles/", "zipfiles/nestedFolder/"], $dirs);
    }


    public function testIsFile()
    {
        $file_ref = "this_is_a_file.txt";
        $folder_ref = "this_is_a_folder/folder/";
        $this->assertTrue($this->filesystem->isFile($file_ref));
        $this->assertFalse($this->filesystem->isFile($folder_ref));
    }

}

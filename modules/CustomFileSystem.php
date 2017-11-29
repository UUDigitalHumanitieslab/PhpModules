<?php


namespace modules;

use ZipArchive;

class CustomFileSystem
{
    /**
     * @param $ref : reference to the dir
     * @return array[str]
     */
    public function listContent($ref)
    {
        return scandir($ref, 1);
    }

    /**
     * Lists the content of a zip file
     * @param $ref : reference to the zip file
     * @return array[str]: array containing the contents of the zip file
     */
    public function listContentZip($ref)
    {
        $za = new ZipArchive();

        $za->open($ref);

        $result = array();
        for ($i = 0; $i < $za->numFiles; $i++) {
            $stat = $za->statIndex($i);
            array_push($result, $stat['name']);
        }


        return $result;
    }

    /**
     * List all the files in a zip file (excludes folders)
     * @param $ref
     * @return array[str]: array containing references to files
     */
    public function listFilesZip($ref)
    {
        $files = $this->listContentZip($ref);
        $result = array();
        foreach ($files as $file) {
            $loc = strrpos($file, "/");
            if ($loc != strlen($file) - 1) {
                array_push($result, $file);
            }
        }
        return $result;
    }

    /**
     * List all the folders in a zip file
     * @param $ref : the reference to the zip
     * @return array[str] : containing the dirs in the zip
     */
    public function listDirsZip($ref)
    {
        $contents = $this->listContentZip($ref);
        $result = array();
        foreach ($contents as $content) {
            $loc = strrpos($content, "/");
            if ($loc == strlen($content) - 1) {
                array_push($result, $content);
            }
        }
        return $result;
    }


    /**
     * List the files in a zip in the nth layer. Counting starts at zero
     * @param $ref : reference to the zip file
     * @param $layer : the layer number
     * @return array[str]: the content of the nth layer
     */
    public function listContentZipNthLayer($ref, $layer)
    {

        $files = $this->listFilesZip($ref);
        $result = Array();

        # First add the files
        foreach ($files as $file) {
            $count = substr_count($file, "/");
            if ($count == $layer) {
                array_push($result, $file);
            }
        }
        $dirs = $this->listDirsZip($ref);

        #Secondly add the dirs
        foreach ($dirs as $dir) {
            $count = substr_count($dir, "/");
            if ($count == $layer + 1) {
                array_push($result, $dir);
            }
        }
        return $result;
    }

    /**
     * Gets the real root of a zip file.
     * The real root is the first directory with two or more dirs beneath it or one or more files
     * @return string : the real "root" of a zip file. (equals "." if the first layer is the real root)
     */
    public function getRealRootZip($ref, $max_depth = 10)
    {
        $currentLayer = 0;
        $currentDir = ".";

        while ($currentLayer < $max_depth) {
            $content = $this->listContentZipNthLayer($ref, $currentLayer);
            $size = sizeof($content);
            if ($size > 1 || $size == 0) {
                return $currentDir;
            } elseif ($size == 1 and $this->isFile($content[0])) {
                return $currentDir;

            }
            $currentLayer += 1;
            $currentDir = $content[0];
        }
    }

    /***
     * Returns if a string represents a file.
     * @param str : the string that represents a file (or not)
     * @return boolean
     *
     */
    public function isFile($str)
    {
        $loc = strrpos($str, "/");
        return $loc != strlen($str) - 1;
    }

}
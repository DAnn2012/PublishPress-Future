<?php

namespace PublishPressFuture\Core;

class Paths
{
    private $baseDir;

    public function __construct($baseDir)
    {
        $this->baseDir = $baseDir;
    }

    public function getBaseDirPath()
    {
        return $this->baseDir;
    }

    public function getVendorDirPath()
    {
        return $this->getBaseDirPath() . '/vendor';
    }
}
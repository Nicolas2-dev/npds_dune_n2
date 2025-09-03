<?php

namespace Npds\Npds;


Class Npds 
{

    private string $basePath;


    public function __construct($basePath = null) 
    { 
        $this->setBasePath($basePath); 
    }

    public function setBasePath($basePath)
    {
        $this->basePath = rtrim($basePath ?: BASEPATH, '/\\');
    }

    public function basePath()
    {
        return $this->basePath;
    }

}
<?php

namespace App\HttpController;

use EasySwoole\Core\Http\AbstractInterface\Controller;

class Hello extends Controller
{
    function index()
    {
        $this->writeJson(200, ['name' => 'jack', 'age' => 20]);
    }

    public function say()
    {
        $this->writeJson(200, ['name' => 'hello say', 'age' => 20]);
    }
}
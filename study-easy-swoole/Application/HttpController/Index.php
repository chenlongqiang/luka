<?php
/**
 * Created by PhpStorm.
 * User: luka-chen
 * Date: 18/3/9
 * Time: 上午11:51
 */

namespace App\HttpController;

use EasySwoole\Core\Http\AbstractInterface\Controller;

class Index extends Controller
{
    function index()
    {
        // TODO: Implement index() method.
        $this->response()->write('this is controller index');
    }

    function test()
    {
        $this->response()->write('this is controller test');
    }

    function test2()
    {
        $this->response()->write('this is controller test2 and your id is '.$this->request()->getRequestParam('id'));
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: luka-chen
 * Date: 17/7/13
 * Time: 下午2:31
 */

include './vendor/autoload.php';

class A {
    public $params = 'a';
    public function say(){
        echo $this->params.PHP_EOL;
    }
}

class B {
    public $params = 'b';
    public function say(){
        echo $this->params.PHP_EOL;
    }
}

$container = new \Pimple\Container();

// define some services
$container['session'] = function ($c) {
    return new B();
};

// use
$session = $container['session'];
$session->say();
$session->params = 'bb';

$session2 = $container['session'];
$session2->say();

// ===============================================

// define some services
$container['session_storage'] = $container->factory(function ($c) {
    return new A();
});
// 对比
//$container['session_storage'] = function ($c) {
//    return new A();
//};

// use
$session_storage = $container['session_storage'];
$session_storage->say();
$session_storage->params = 'aa';

$session_storage2 = $container['session_storage'];
$session_storage2->say();


// define some parameters
$container['cookie_name'] = 'SESSION_ID';
$container['session_storage_class'] = 'SessionStorage';

$container['random_func'] = $container->protect(function () {
    return rand();
});

var_dump($container);

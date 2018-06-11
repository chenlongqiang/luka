### [项目下载](https://www.easyswoole.com/Manual/2.x/Cn/_book/Introduction/install.html#%E6%89%8B%E5%8A%A8%E5%AE%89%E8%A3%85%E6%A1%86%E6%9E%B6)
composer require easyswoole/easyswoole=2.x-dev
php vendor/bin/easyswoole install
php easyswoole start
中途没有报错的话，框架就安装完成了，此时可以访问 http://localhost:9501/ 看到框架的欢迎页面，表示框架已经安装成功

### 初始目录创建
```
----------------------------------
├─Application        应用目录
│  └─HttpController      应用的控制器目录
│     └─Index.php    默认控制器文件
----------------------------------
```

### Index.php
```php
<?php
namespace App\HttpController;
use EasySwoole\Core\Http\AbstractInterface\Controller;
class Index extends Controller
{
    function index()
    {
        // TODO: Implement index() method.
        $this->response()->write('hello world');
    }
}
```

### composer.json 注册应用的命名空间
```
{
    "autoload": {
        "psr-4": {
            "App\\": "Application/"
        }
    },
    "require": {
        "easyswoole/easyswoole": "2.x-dev"
    }
}
```

###
执行 composer dumpautoload 命令更新命名空间，框架已经可以自动加载 Application 目录下的文件了，此时框架已经安装完毕，可以开始编写业务逻辑






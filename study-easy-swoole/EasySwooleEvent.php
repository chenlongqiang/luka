<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/1/9
 * Time: 下午1:04
 */

namespace EasySwoole;

use \EasySwoole\Core\AbstractInterface\EventInterface;
use EasySwoole\Core\Component\Di;
use EasySwoole\Core\Component\SysConst;
use \EasySwoole\Core\Swoole\ServerManager;
use \EasySwoole\Core\Swoole\EventRegister;
use \EasySwoole\Core\Http\Request;
use \EasySwoole\Core\Http\Response;
use EasySwoole\Whoops\Runner;
use Whoops\Handler\PrettyPageHandler;

Class EasySwooleEvent implements EventInterface {

    public function frameInitialize(): void
    {
        // TODO: Implement frameInitialize() method.
        date_default_timezone_set('Asia/Shanghai');
        Di::getInstance()->set(SysConst::HTTP_EXCEPTION_HANDLER, \App\ExceptionHandler::class);
        Di::getInstance()->set(SysConst::CONTROLLER_MAX_DEPTH, 5);

        // 可以进行更多设置，默认为以下设置
        /*
        $options = [
            'auto_conversion' => true,                    // 开启AJAX模式下自动转换为JSON输出
            'detailed'        => true,                    // 开启详细错误日志输出
            'information'     => '发生内部错误,请稍后再试'   // 不开启详细输出的情况下 输出的提示文本
        ];
        $whoops  = new Runner($options);
        // 注册异常事件处理
        $whoops->pushHandler(new PrettyPageHandler);
        $whoops->register();
        */
    }

    public function mainServerCreate(ServerManager $server,EventRegister $register): void
    {
        // TODO: Implement mainServerCreate() method.
    }

    public function onRequest(Request $request,Response $response): void
    {
        // TODO: Implement onRequest() method.
    }

    public function afterAction(Request $request,Response $response): void
    {
        // TODO: Implement afterAction() method.
    }
}
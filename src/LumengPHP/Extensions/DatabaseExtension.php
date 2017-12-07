<?php

namespace LumengPHP\Extensions;

use LumengPHP\Db\ConnectionManager;
use LumengPHP\Kernel\Extension\AbstractExtension;

/**
 * 数据库扩展
 * 
 * 此扩展会执行以下动作：
 * 1，创建<b>连接管理器</b>并进行一些初始化操作，把<b>连接管理器</b>注册成名称为“dbConnManager”的服务
 * 2，把<b>默认连接</b>注册成名称为“dbConn”的服务
 * 
 * 此扩展要求配置格式如下：
 * [
 *     'database' => [
 *         'logger' => 'logger service name',
 *         'connections' => [
 *             ...
 *         ],
 * ]
 *
 * @author zhengluming <luming.zheng@shandjj.com>
 */
class DatabaseExtension extends AbstractExtension {

    public function load() {
        $dbConfig = $this->appContext->getConfig('database');
        if (is_null($dbConfig)) {
            _throw('数据库配置不存在~');
        }

        //用于数据库操作的日志组件
        $loggerName = $dbConfig['logger'] ?: '';
        $logger = null;
        if ($loggerName) {
            $logger = $this->appContext->getService($loggerName);
        }

        $container = $this->appContext->getServiceContainer();

        //创建连接管理器并进行一些初始化操作，把连接管理器注册成名称为“dbConnManager”的服务
        $connectionConfigs = $dbConfig['connections'];
        $connManager = ConnectionManager::create($connectionConfigs, $logger);
        $container->register('dbConnManager', $connManager);

        //把默认连接注册成名称为“dbConn”的服务
        $container->register('dbConn', function($container) {
            /* @var $connManager ConnectionManager */
            $connManager = $container->get('dbConnManager');
            return $connManager->getDefaultConnection();
        });
    }

}

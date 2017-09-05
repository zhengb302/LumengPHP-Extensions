<?php

namespace LumengPHP\Extensions;

use LumengPHP\Db\ConnectionManager;
use LumengPHP\Kernel\Extension\AbstractExtension;

/**
 * 数据库扩展<br />
 * 此扩展会执行以下动作：
 * 1，创建<b>连接管理器</b>并进行一些初始化操作，把<b>连接管理器</b>注册成名称为“dbConnManager”的服务
 * 2，把<b>默认连接</b>注册成名称为“dbConn”的服务
 *
 * @author zhengluming <luming.zheng@shandjj.com>
 */
class DatabaseExtension extends AbstractExtension {

    public function load() {
        $container = $this->appContext->getServiceContainer();

        //创建连接管理器并进行一些初始化操作，把连接管理器注册成名称为“dbConnManager”的服务
        $connectionConfigs = $this->appContext->getConfig('database');
        $connManager = ConnectionManager::create($connectionConfigs);
        $container->register('dbConnManager', $connManager);

        //把默认连接注册成名称为“dbConn”的服务
        $container->register('dbConn', function($container) {
            /* @var $connManager ConnectionManager */
            $connManager = $container->get('dbConnManager');
            return $connManager->getDefaultConnection();
        });
    }

}

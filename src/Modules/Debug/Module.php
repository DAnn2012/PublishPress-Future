<?php
/**
 * Copyright (c) 2022. PublishPress, All rights reserved.
 */

namespace PublishPressFuture\Modules\Debug;


use PublishPressFuture\Core\Framework\Logger\LoggerInterface;
use PublishPressFuture\Core\Framework\ModuleInterface;
use PublishPressFuture\Core\Framework\WordPress\Facade\HooksFacade;

class Module implements ModuleInterface
{
    /**
     * @var HooksFacade
     */
    private $hooks;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Controller
     */
    private $controller;

    /**
     * @param HooksFacade $hooks
     * @param LoggerInterface $logger
     */
    public function __construct(HooksFacade $hooks, LoggerInterface $logger)
    {
        $this->hooks = $hooks;
        $this->logger = $logger;

        $this->controller = $this->getController();
    }

    /**
     * @inheritDoc
     */
    public function initialize()
    {
        $this->controller->initialize();
    }

    private function getController()
    {
        return new Controller($this->hooks, $this->logger);
    }
}

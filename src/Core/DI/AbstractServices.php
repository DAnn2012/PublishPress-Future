<?php
/**
 * Copyright (c) 2022. PublishPress, All rights reserved.
 */

namespace PublishPressFuture\Core\DI;

abstract class AbstractServices
{
    const PLUGIN = 'plugin';

    const PLUGIN_VERSION = 'plugin.version';

    const PLUGIN_SLUG = 'plugin.slug';

    const PLUGIN_NAME = 'plugin.name';

    const DEFAULT_DATA = 'default.data';

    const DEFAULT_DATE_FORMAT = 'default.date.format';

    const DEFAULT_TIME_FORMAT = 'default.time.format';

    const DEFAULT_FOOTER_CONTENT = 'default.footer.content';

    const DEFAULT_FOOTER_STYLE = 'default.footer.style';

    const DEFAULT_FOOTER_DISPLAY = 'default.footer.display';

    const DEFAULT_EMAIL_NOTIFICATION = 'default.email.notification';

    const DEFAULT_EMAIL_NOTIFICATION_ADMINS = 'default.email.notification.admins';

    const DEFAULT_DEBUG = 'default.debug';

    const DEFAULT_EXPIRATION_DATE = 'default.expiration.date';

    const BASE_PATH = 'base.path';

    const BASE_URL = 'base.url';

    const HOOKS = 'hooks';

    const LEGACY_PLUGIN = 'legacy.plugin';

    const PATHS = 'paths';

    const DB = 'db';

    const SITE = 'site';

    const SETTINGS = 'settings';

    const LOGGER = 'logger';

    const CRON = 'cron';

    const ERROR = 'error';

    const DATETIME = 'datetime';

    const OPTIONS = 'options';

    const DEBUG = 'debug';

    const MODULES = 'modules';

    const EXPIRATION_SCHEDULER = 'expiration.scheduler';

    const EXPIRATION_RUNNER = 'expiration.runner';

    const MODULE_DEBUG = 'module.debug';

    const MODULE_INSTANCE_PROTECTION = 'module.instance_protection';

    const MODULE_EXPIRATOR = 'module.expirator';

    const MODULE_SETTINGS = 'module.settings';

    const POST_MODEL_FACTORY = 'post.model.factory';
}
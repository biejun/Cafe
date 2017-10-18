<?php
/**
 * AnyPHP Coffee
 *
 * This is a base configuration file that
 * defines the constants of a system.
 *
 * 这是一个用于定义系统常量的配置文件，你可以
 * 对其中一些配置进行修改。
 *
 */

/* 定义目录分隔符 (Non modifiable) */
define( 'DS', DIRECTORY_SEPARATOR );

/* 定义系统绝对路径 (Non modifiable) */
define( 'ABSPATH', realpath(__DIR__ . '/..') . DS );

/* 定义系统核心层目录 (Non modifiable) */
define( 'CORE', ABSPATH . 'coffee' );

/* 定义系统应用层目录 (Non modifiable) */
define( 'APP', ABSPATH . 'app' );

/* 定义系统配置目录 */
define( 'CONFIG', dirname(__FILE__) . DS );

/**
 * 定义系统运行环境
 *
 * 0 生产环境 1 开发环境
 */
define( 'IS_DEVELOPMENT', 0 );

/* 定义系统时区 */
define( 'TIMEZONE', 'PRC' );
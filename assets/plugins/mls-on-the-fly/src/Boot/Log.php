<?php
namespace Realtyna\MlsOnTheFly\Boot;

use Realtyna\Core\Utilities\Logger;

/**
 * Class Log
 *
 * A static logging interface for the MyPlugin plugin.
 *
 * @package Realtyna\MyPlugin
 */
class Log
{
    private static ?Logger $logger = null;

    /**
     * Initialize the Logger instance.
     *
     * @param string $logFilePath Path to the log file.
     * @param string $minLogLevel Minimum log level to record.
     * @return void
     */
    public static function init(string $logFilePath, string $minLogLevel = 'debug'): void
    {
        self::$logger = new Logger($logFilePath, $minLogLevel);
    }

    /**
     * Check if the logger is initialized.
     *
     * @return bool
     */
    private static function isLoggerInitialized(): bool
    {
        return self::$logger !== null;
    }

    public static function debug(string $message, array $context = []): void
    {
        if (self::isLoggerInitialized()) {
            self::$logger->debug($message, $context);
        }
    }

    public static function info(string $message, array $context = []): void
    {
        if (self::isLoggerInitialized()) {
            self::$logger->info($message, $context);
        }
    }

    public static function notice(string $message, array $context = []): void
    {
        if (self::isLoggerInitialized()) {
            self::$logger->notice($message, $context);
        }
    }

    public static function warning(string $message, array $context = []): void
    {
        if (self::isLoggerInitialized()) {
            self::$logger->warning($message, $context);
        }
    }

    public static function error(string $message, array $context = []): void
    {
        if (self::isLoggerInitialized()) {
            self::$logger->error($message, $context);
        }
    }

    public static function critical(string $message, array $context = []): void
    {
        if (self::isLoggerInitialized()) {
            self::$logger->critical($message, $context);
        }
    }

    public static function alert(string $message, array $context = []): void
    {
        if (self::isLoggerInitialized()) {
            self::$logger->alert($message, $context);
        }
    }

    public static function emergency(string $message, array $context = []): void
    {
        if (self::isLoggerInitialized()) {
            self::$logger->emergency($message, $context);
        }
    }
}


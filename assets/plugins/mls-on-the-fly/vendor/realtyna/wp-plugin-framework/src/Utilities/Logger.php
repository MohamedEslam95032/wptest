<?php
namespace Realtyna\Core\Utilities;
/**
 * Class Logger
 *
 * A simple logging utility for writing log messages to a file, including context such as URL, file, and line number.
 *
 * @package Realtyna\BasePlugin\Utilities
 */
class Logger
{
    private string $logDirectory;
    private string $logFilePath;
    private array $logLevels = [
        'debug' => 100,
        'info' => 200,
        'notice' => 300,
        'warning' => 400,
        'error' => 500,
        'critical' => 600,
        'alert' => 700,
        'emergency' => 800,
    ];
    private string $minLogLevel;

    /**
     * Logger constructor.
     *
     * @param string $logDirectory Path to the directory where logs are stored.
     * @param string $minLogLevel Minimum log level to record.
     */
    public function __construct(string $logDirectory, string $minLogLevel = 'debug')
    {
        $this->logDirectory = rtrim($logDirectory, DIRECTORY_SEPARATOR);
        $this->minLogLevel = $minLogLevel;
        $this->ensureLogDirectoryExists();
        $this->logFilePath = $this->getDailyLogFile();
        $this->rotateLogs();
    }

    /**
     * Ensure the log directory exists. Create it if it doesn't.
     *
     * @return void
     */
    private function ensureLogDirectoryExists(): void
    {
        if (!is_dir($this->logDirectory)) {
            mkdir($this->logDirectory, 0755, true);
        }
    }

    /**
     * Log a message at a specified level.
     *
     * @param string $level The log level.
     * @param string $message The log message.
     * @param array $context Additional context information.
     * @return void
     */
    private function log(string $level, string $message, array $context = []): void
    {
        if ($this->logLevels[$level] >= $this->logLevels[$this->minLogLevel]) {
            $context = $this->addDefaultContext($context);
            $date = date('Y-m-d H:i:s');
            $contextString = json_encode($context);
            $logMessage = "[{$date}] {$level}: {$message} {$contextString}\n";
            $this->writeLog($logMessage);
        }
    }

    /**
     * Add default context information such as URL, file, and line number.
     *
     * @param array $context The original context.
     * @return array The context with additional information.
     */
    private function addDefaultContext(array $context): array
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5);
        $context['url'] = $_SERVER['REQUEST_URI'] ?? 'CLI';
        $context['file'] = $backtrace[5]['file'] ?? 'unknown';
        $context['line'] = $backtrace[5]['line'] ?? 'unknown';
        return $context;
    }

    /**
     * Write the log message to the file.
     *
     * @param string $logMessage The formatted log message.
     * @return void
     */
    private function writeLog(string $logMessage): void
    {
        file_put_contents($this->logFilePath, $logMessage, FILE_APPEND);
    }

    /**
     * Get the log file path for the current day.
     *
     * @return string The full path to the log file.
     */
    private function getDailyLogFile(): string
    {
        $date = date('Y-m-d');
        return "{$this->logDirectory}/log-{$date}.log";
    }

    /**
     * Rotate log files, keeping only the most recent 20 logs.
     *
     * @return void
     */
    private function rotateLogs(): void
    {
        $files = glob("{$this->logDirectory}/log-*.log");
        if (count($files) > 20) {
            usort($files, function ($a, $b) {
                return filemtime($a) <=> filemtime($b);
            });
            while (count($files) > 20) {
                unlink(array_shift($files));
            }
        }
    }

    public function debug(string $message, array $context = []): void
    {
        $this->log('debug', $message, $context);
    }

    public function info(string $message, array $context = []): void
    {
        $this->log('info', $message, $context);
    }

    public function notice(string $message, array $context = []): void
    {
        $this->log('notice', $message, $context);
    }

    public function warning(string $message, array $context = []): void
    {
        $this->log('warning', $message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        $this->log('error', $message, $context);
    }

    public function critical(string $message, array $context = []): void
    {
        $this->log('critical', $message, $context);
    }

    public function alert(string $message, array $context = []): void
    {
        $this->log('alert', $message, $context);
    }

    public function emergency(string $message, array $context = []): void
    {
        $this->log('emergency', $message, $context);
    }
}

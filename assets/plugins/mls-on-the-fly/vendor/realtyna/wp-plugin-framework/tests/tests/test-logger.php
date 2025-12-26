<?php

use Realtyna\Core\Utilities\Logger;

class LoggerTest extends WP_UnitTestCase
{
    protected $logDirectory;
    protected $logger;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a unique temporary directory for logging
        $this->logDirectory = __DIR__ . '/logger_test';
        mkdir($this->logDirectory,0755, true);

        // Initialize the Logger class
        $this->logger = new Logger($this->logDirectory, 'info');
    }

    protected function tearDown(): void
    {
        // Clean up the log directory after each test
        array_map('unlink', glob("$this->logDirectory/*.*"));
        rmdir($this->logDirectory);

        parent::tearDown();
    }

    public function testLogDirectoryIsCreated()
    {
        $this->assertDirectoryExists($this->logDirectory);
    }

    public function testLogMessageIsWritten()
    {
        // Write a log message
        $this->logger->info('This is a test log message.');

        // Check that the log file is created
        $logFile = $this->getLogFile();

        $this->assertFileExists($logFile);
        // Check that the log message is written to the file
        $logContent = file_get_contents($logFile);

        $this->assertStringContainsString('This is a test log message.', $logContent);
    }

    public function testLogRespectsMinLogLevel()
    {
        // This message should not be logged because 'debug' is below 'info'
        $this->logger->debug('This debug message should not be logged.');

        // This message should be logged because 'error' is above 'info'
        $this->logger->error('This error message should be logged.');

        $logFile = $this->getLogFile();
        $logContent = file_get_contents($logFile);

        // Assert the debug message is not in the log
        $this->assertStringNotContainsString('This debug message should not be logged.', $logContent);

        // Assert the error message is in the log
        $this->assertStringContainsString('This error message should be logged.', $logContent);
    }


    protected function getLogFile()
    {
        return $this->logDirectory . '/log-' . date('Y-m-d') . '.log';
    }
}

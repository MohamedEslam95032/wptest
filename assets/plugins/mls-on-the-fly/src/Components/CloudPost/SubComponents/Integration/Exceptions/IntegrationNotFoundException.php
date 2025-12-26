<?php
namespace Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\Exceptions;

class IntegrationNotFoundException extends \Exception
{
    public function __construct()
    {
        parent::__construct('No supported integration found.');
    }
}
<?php

namespace Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\RFClient\SDK\RF\Entities;

class RFPropertyAdditionalInfo
{
    public function __construct($properties = array())
    {
        foreach ($properties as $key => $value) {
            $this->$key = $value;
        }
    }
}
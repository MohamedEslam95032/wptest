<?php

namespace Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\RFClient\SDK\RF\Entities;

use AllowDynamicProperties;

#[AllowDynamicProperties]
class RFLookup
{
    public function __construct($lookups = array())
    {
        foreach ($lookups as $key => $value) {
            $this->$key = $value;
        }
    }

}
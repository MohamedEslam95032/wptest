<?php

namespace Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\RFClient\SDK\RF\Entities;

use AllowDynamicProperties;

#[AllowDynamicProperties]
class RFProperty
{
    public mixed $post_id;
    public mixed $post_author;

    public function __construct($properties = array())
    {
        foreach ($properties as $key => $value) {
            $this->$key = $value;
        }
    }

}
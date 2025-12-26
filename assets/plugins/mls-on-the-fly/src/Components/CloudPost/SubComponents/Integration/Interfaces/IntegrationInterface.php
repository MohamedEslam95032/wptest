<?php

namespace Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\Interfaces;

interface IntegrationInterface
{
    public function checkMetaToSave($check, $metaKey): bool|null;


    public function getMappingDir(): string;
}
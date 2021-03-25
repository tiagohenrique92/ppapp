<?php
namespace PPApp\Utils;

use Ramsey\Uuid\Uuid as ThirdPartyUuid;

class Uuid
{
    /**
     * create
     *
     * @return string
     */
    public static function create(): string
    {
        $uuid = ThirdPartyUuid::uuid6();
        return $uuid;
    }
}

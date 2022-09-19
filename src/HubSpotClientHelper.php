<?php

namespace WCHub;

use HubSpot\Factory;

class HubSpotClientHelper
{
    public static function createFactory($access_token)
    {
        if (!empty($access_token)) {
            return Factory::createWithAccessToken($access_token);
        }
    }
}
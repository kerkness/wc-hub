<?php

namespace WCHub;


class HubContacts
{
    use HubObject;

    public function basicApi()
    {
        return $this->client->crm()->contacts()->basicApi();
    }

    public function searchApi()
    {
        return $this->client->crm()->contacts()->searchApi();        
    }


}
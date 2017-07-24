<?php

namespace Highco\ApiConsumerBundle\Events;

interface GuzzleEventListenerInterface
{
    public function setServiceName($serviceName);
}

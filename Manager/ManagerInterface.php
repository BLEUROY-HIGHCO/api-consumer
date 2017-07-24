<?php

namespace Highco\ApiConsumerBundle\Manager;

interface ManagerInterface
{
    public function getRepository(string $class);
}

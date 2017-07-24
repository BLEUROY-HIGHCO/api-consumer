<?php

namespace Highco\ApiConsumerBundle\Manager;

use GuzzleHttp\Client;
use Highco\ApiConsumerBundle\Exception\InvalidArgumentException;
use Highco\ApiConsumerBundle\Exception\InvalidResourceException;
use Highco\ApiConsumerBundle\Repository\RepositoryInterface;

class Manager implements ManagerInterface
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var array
     */
    private $repositories;

    /**
     * Available entities
     *
     * @var array
     */
    private $entities = [];

    public function __construct(Client $client, array $entities = [])
    {
        $this->client       = $client;
        $this->repositories = [];
        foreach ($entities as $entity) {
            $this->entities[$entity['class']] = [
                'route_prefix'     => $entity['route_prefix'],
                'repository_class' => $entity['repository_class'],
            ];
        }
    }

    /**
     * @param string $class
     *
     * @return RepositoryInterface
     * @throws InvalidResourceException
     */
    public function getRepository(string $class): RepositoryInterface
    {
        if (!array_key_exists($class, $this->entities)) {
            throw new InvalidArgumentException();
        }

        if (!array_key_exists($class, $this->repositories)) {
            $repositoryClass    = $this->entities[$class]['repository_class'];
            $repositoryInstance = new $repositoryClass($this->client, $class, $this->entities[$class]['route_prefix']);
            if ($repositoryInstance instanceof RepositoryInterface === false) {
                throw new InvalidResourceException('Provided repository should implements Highco\ApiConsumerBundle\Repository\RepositoryInterface');
            }
            $this->repositories[$class] = $repositoryInstance;
        }

        return $this->repositories[$class];
    }

}

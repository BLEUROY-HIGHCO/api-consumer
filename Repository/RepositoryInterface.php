<?php

namespace Highco\ApiConsumerBundle\Repository;

interface RepositoryInterface
{
    public function getById(int $id);

    public function getCollection();

    public function call(string $route, string $type, string $method = 'GET', array $data = [], bool $withDeserialization = true);

    public function save($entity, array $groups = []);

    public function serialize($entity, string $format = 'json', array $groups = []);

    public function deserialize(string $content, string $class, string $format = 'json', array $groups = []);

    public function delete(int $id);
}

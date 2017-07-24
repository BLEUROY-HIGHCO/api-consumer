<?php

namespace Highco\ApiConsumerBundle\Repository;

use GuzzleHttp\Client;
use Highco\ApiConsumerBundle\Exception\InvalidArgumentException;
use Highco\ApiConsumerBundle\Serializer\JsonldEncoder;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Doctrine\Common\Annotations\AnnotationReader;

class Repository implements RepositoryInterface
{
    const TYPE_COLLECTION = 'TYPE_COLLECTION';
    const TYPE_ONE        = 'TYPE_ONE';

    const ACCEPT_CONTENT_TYPE = ['json', 'ld+json'];

    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $class;

    /**
     * @var string
     */
    private $routePrefix;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * Repository constructor.
     *
     * @param Client $client
     * @param string $class
     * @param string $routePrefix
     */
    function __construct(Client $client, string $class, string $routePrefix)
    {
        $this->client         = $client;
        $this->class          = $class;
        $this->routePrefix    = $routePrefix;
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $this->serializer     = new Serializer([new PropertyNormalizer($classMetadataFactory), new ObjectNormalizer(), new ArrayDenormalizer()], [new JsonldEncoder(), new JsonEncoder()]);
    }

    public function getById(int $id)
    {
        return $this->call('/'.$id, self::TYPE_ONE);
    }

    public function getCollection()
    {
        return $this->call('', self::TYPE_COLLECTION);
    }

    /**
     * @param string $route
     * @param string $type
     * @param string $method
     * @param array  $data
     * @param bool   $withDeserialization
     *
     * @return bool|object
     */
    public function call(string $route = '', string $type = self::TYPE_ONE, string $method = 'GET', array $data = [], bool $withDeserialization = true)
    {
        if (!in_array($type, [self::TYPE_ONE, self::TYPE_COLLECTION])) {
            throw new InvalidArgumentException();
        }

        if ($route === '') {
            $calculatedRoute = sprintf('/%s', $this->routePrefix);
        } else {
            $calculatedRoute = sprintf('/%s%s', $this->routePrefix, $route);
        }

        $query = $this->client->request($method, $calculatedRoute, $data);
        $this->checkStatusCode($query);

        $deserializeType = $type === self::TYPE_COLLECTION ? $this->class.'[]' : $this->class;

        return $withDeserialization ? $this->deserialize($query->getBody()->getContents(), $deserializeType, $this->getFormatFromHeader($query->getHeader('Content-Type'))) : true;
    }

    /**
     * @param        $entity
     * @param string $format
     * @param array  $groups
     *
     * @return string
     */
    public function serialize($entity, string $format = 'json', array $groups = null)
    {
        if (!in_array($format, self::ACCEPT_CONTENT_TYPE)) {
            throw new InvalidArgumentException();
        }

        $context = $groups !== null ? ['groups' => $groups] : [];

        return $this->serializer->serialize($entity, $format, $context);
    }

    /**
     * @param string $content
     * @param string $class
     * @param string $format
     *
     * @param array  $groups
     *
     * @return object
     */
    public function deserialize(string $content, string $class, string $format = 'json', array $groups = null)
    {
        if (!in_array($format, self::ACCEPT_CONTENT_TYPE)) {
            throw new InvalidArgumentException();
        }

        $context = $groups !== null ? ['groups' => $groups] : [];

        return $this->serializer->deserialize($content, $class, $format, $context);
    }

    /**
     * @param       $entity
     * @param array $groups
     *
     * @return bool|object
     */
    public function save($entity, array $groups = null)
    {
        if (!method_exists($entity, 'getId')) {
            throw new UnprocessableEntityHttpException();
        }

        $method = 'POST';
        $route  = '';
        if (null !== $entity->getId()) {
            $method = 'PUT';
            $route  .= '/'.$entity->getId();
        }

        $data = $this->serialize($entity, 'json', $groups);

        return $this->call($route, self::TYPE_ONE, $method, ['body' => $data]);
    }

    /**
     * @param int $id
     *
     * @return bool|object
     */
    public function delete(int $id)
    {
        return $this->call('/'.$id, self::TYPE_ONE, 'DELETE', [], false);
    }

    /**
     * @param array $contentType
     *
     * @return string
     */
    private function getFormatFromHeader(array $contentType): string
    {
        if (preg_match_all('/application\/([a-zA-Z\+]+)/', $contentType[0], $matches) && in_array($matches[1][0], self::ACCEPT_CONTENT_TYPE)) {
            return $matches[1][0];
        }

        return 'json';
    }

    /**
     * @param ResponseInterface $query
     */
    private function checkStatusCode(ResponseInterface $query): void
    {
        $statusCode = $query->getStatusCode();
        if (!preg_match('/^20[0-9]$/', $statusCode)) {
            if ($statusCode === 404) {
                throw new NotFoundHttpException();
            } else {
                throw new HttpException($statusCode, $query->getBody()->getContents());
            }
        }
    }
}

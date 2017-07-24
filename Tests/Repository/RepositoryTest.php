<?php

namespace Highco\ApiConsumerBundle\Tests\Repository;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;
use Highco\ApiConsumerBundle\Exception\InvalidArgumentException;
use Highco\ApiConsumerBundle\Repository\Repository;
use Highco\ApiConsumerBundle\Tests\DependencyInjection\Fixtures\FakeEntity;
use Highco\ApiConsumerBundle\Tests\Entity\Dealer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class RepositoryTest extends TestCase
{
    public function testGetById()
    {
        $client   = $this->createMock(Client::class);
        $response = $this->createMock(Response::class);
        $response->method('getStatusCode')->willReturn(200);
        $response->method('getHeader')->with('Content-Type')->willReturn(['application/json']);
        $stream = $this->createMock(Stream::class);
        $stream->method('getContents')->willReturn(
            <<<EOT
    {
        "@context": "/contexts/Dealer",
        "@id": "/dealers/8",
        "@type": "Dealer",
        "id": 8,
        "username": "NIS70711",
        "name": "Autobedrijf Stormvogels B.V.",
        "code": "70711"
      }
EOT
        );
        $response->method('getBody')->willReturn($stream);
        $client->method('request')->withAnyParameters()->willReturn($response);
        $repository = new Repository(
            $client,
            Dealer::class,
            '/dealers'
        );

        $dealer = $repository->getById(8);
        $this->assertInstanceOf(Dealer::class, $dealer);
        $this->assertEquals(8, $dealer->getId());
        $this->assertNull($dealer->getLanguage());
    }

    public function testGetCollection()
    {
        $client   = $this->createMock(Client::class);
        $response = $this->createMock(Response::class);
        $response->method('getStatusCode')->willReturn(200);
        $response->method('getHeader')->with('Content-Type')->willReturn(['application/ld+json']);
        $stream = $this->createMock(Stream::class);
        $stream->method('getContents')->willReturn(
            <<<EOT
    {
        "id": 1,
        "@context": "/contexts/Dealer",
        "@id": "/dealers",
        "@type": "hydra:Collection",
        "hydra:member": [
          {
            "@id": "/dealers/3",
            "@type": "Dealer",
            "id": 3,
            "username": "string",
            "name": "string",
            "code": "string",
            "language": "str"
          },
          {
            "@id": "/dealers/4",
            "@type": "Dealer",
            "id": 4,
            "username": "NIS70716",
            "name": "Autobedrijf Ten Boekel & Bakker Schagen B.V.",
            "code": "70716",
            "language": "NL"
          },
          {
            "@id": "/dealers/5",
            "@type": "Dealer",
            "id": 5,
            "username": "NIS70743",
            "name": "Autobedrijf Ten Boekel & Bakker Alkmaar B.V.",
            "code": "70743",
            "language": "NL"
          },
          {
            "@id": "/dealers/6",
            "@type": "Dealer",
            "id": 6,
            "username": "NIS70732",
            "name": "AutoBrockhoff Noord B.V.",
            "code": "70732",
            "language": "NL"
          }
          ],
        "hydra:totalItems": 175,
        "hydra:view": {
          "@id": "/dealers?page=1",
          "@type": "hydra:PartialCollectionView",
          "hydra:first": "/dealers?page=1",
          "hydra:last": "/dealers?page=18",
          "hydra:next": "/dealers?page=2"
        }
      }
EOT
        );
        $response->method('getBody')->willReturn($stream);
        $client->method('request')->withAnyParameters()->willReturn($response);
        $repository = new Repository(
            $client,
            Dealer::class,
            '/dealers'
        );

        $dealers = $repository->getCollection();
        $this->assertTrue(is_array($dealers));
        $this->assertEquals(4, count($dealers));
    }

    public function testInvalidArgumentExceptionCall()
    {
        $client = $this->createMock(Client::class);

        $repository = new Repository(
            $client,
            Dealer::class,
            '/dealers'
        );

        $this->expectException(InvalidArgumentException::class);
        $repository->call('', 'Hello');
    }

    public function testUnprocessableEntityHttpExceptionSave()
    {
        $client = $this->createMock(Client::class);

        $repository = new Repository(
            $client,
            Dealer::class,
            '/dealers'
        );

        $this->expectException(UnprocessableEntityHttpException::class);
        $repository->save(null);
    }

    private function getBasicRepository()
    {
        $client = $this->createMock(Client::class);

        return new Repository(
            $client,
            Dealer::class,
            '/dealers'
        );
    }

    public function testSerialize()
    {
        $repo = $this->getBasicRepository();
        $this->expectException(InvalidArgumentException::class);
        $repo->serialize(new FakeEntity(), 'xml');
    }

    public function testDeserialize()
    {
        $repo = $this->getBasicRepository();
        $this->expectException(InvalidArgumentException::class);
        $repo->deserialize('', FakeEntity::class, 'xml');
    }

    public function testSave()
    {
        $client   = $this->createMock(Client::class);
        $response = $this->createMock(Response::class);
        $response->method('getStatusCode')->willReturn(200);
        $response->method('getHeader')->with('Content-Type')->willReturn(['application/json']);
        $stream = $this->createMock(Stream::class);
        $stream->method('getContents')->willReturn(
            <<<EOT
    {
        "@context": "/contexts/Dealer",
        "@id": "/dealers/8",
        "@type": "Dealer",
        "id": 8,
        "username": "NIS70711",
        "name": "Autobedrijf Stormvogels B.V.",
        "code": "70711"
      }
EOT
        );
        $response->method('getBody')->willReturn($stream);
        $client->method('request')->withAnyParameters()->willReturn($response);
        $repository = new Repository(
            $client,
            Dealer::class,
            '/dealers'
        );

        $dealer = $repository->save(new Dealer());
        $this->assertInstanceOf(Dealer::class, $dealer);
        $this->assertEquals(8, $dealer->getId());
    }

    public function testDelete()
    {
        $client   = $this->createMock(Client::class);
        $response = $this->createMock(Response::class);
        $response->method('getStatusCode')->willReturn(200);
        $response->method('getHeader')->with('Content-Type')->willReturn(['application/json']);
        $client->method('request')->withAnyParameters()->willReturn($response);
        $repository = new Repository(
            $client,
            Dealer::class,
            '/dealers'
        );

        $result = $repository->delete(8);
        $this->assertTrue($result);
    }

    public function testNotFoundExceptionCheckStatusCode()
    {
        $client   = $this->createMock(Client::class);
        $response = $this->createMock(Response::class);
        $response->method('getStatusCode')->willReturn(404);

        $client->method('request')->withAnyParameters()->willReturn($response);
        $repository = new Repository(
            $client,
            Dealer::class,
            '/dealers'
        );

        $this->expectException(NotFoundHttpException::class);
        $repository->getById(1);
    }

    public function testHttpExceptionCheckStatusCode()
    {
        $client   = $this->createMock(Client::class);
        $response = $this->createMock(Response::class);
        $response->method('getStatusCode')->willReturn(403);
        $stream = $this->createMock(Stream::class);
        $stream->method('getContents')->willReturn('');
        $response->method('getBody')->willReturn($stream);

        $client->method('request')->withAnyParameters()->willReturn($response);
        $repository = new Repository(
            $client,
            Dealer::class,
            '/dealers'
        );

        $this->expectException(HttpException::class);
        $repository->getById(1);
    }
}

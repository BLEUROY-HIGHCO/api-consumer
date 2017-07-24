<?php

namespace Highco\ApiConsumerBundle\Tests\Serializer;

use Highco\ApiConsumerBundle\Serializer\JsonldEncoder;
use PHPUnit\Framework\TestCase;

class JsonldEncoderTest extends TestCase
{
    public static function providerEntityCollection()
    {
        return [
            [
                '{
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
                },
                {
                    "@id": "/dealers/7",
                    "@type": "Dealer",
                    "id": 7,
                    "username": "NIS70830",
                    "name": "Abswoude B.V.",
                    "code": "70830",
                    "language": "NL"
                },
                {
                    "@id": "/dealers/8",
                    "@type": "Dealer",
                    "id": 8,
                    "username": "NIS70711",
                    "name": "Autobedrijf Stormvogels B.V.",
                    "code": "70711",
                    "language": "NL"
                },
                {
                    "@id": "/dealers/9",
                    "@type": "Dealer",
                    "id": 9,
                    "username": "NIS70721",
                    "name": "Autobedrijf Nieuwendijk",
                    "code": "70721",
                    "language": "NL"
                },
                {
                    "@id": "/dealers/10",
                    "@type": "Dealer",
                    "id": 10,
                    "username": "NIS70752",
                    "name": "Rustman\'s Automobielbedrijf B.V.",
                    "code": "70752",
                    "language": "NL"
                },
                {
                    "@id": "/dealers/11",
                    "@type": "Dealer",
                    "id": 11,
                    "username": "NIS70733",
                    "name": "Autobedrijf Hoeke B.V.",
                    "code": "70733",
                    "language": "NL"
                },
                {
                    "@id": "/dealers/12",
                    "@type": "Dealer",
                    "id": 12,
                    "username": "NIS70870",
                    "name": "Automobielbedrijf Oudshoorn B.V.",
                    "code": "70870",
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
            }',
                10,
            ],
        ];
    }

    public static function providerEntityOne()
    {
        return [
            [
                '{
            "@context": "/contexts/Dealer",
            "@id": "/dealers/8",
            "@type": "Dealer",
            "id": 8,
            "username": "NIS70711",
            "name": "Autobedrijf Stormvogels B.V.",
            "code": "70711",
            "language": "NL"}',
                8,
            ],
        ];
    }

    /**
     * @dataProvider providerEntityCollection
     *
     * @param string $data
     * @param int    $numberItems
     */
    public function testDecodeCollection(string $data, int $numberItems)
    {
        $encoder = new JsonldEncoder();

        $result = $encoder->decode($data, 'ld+json');

        $this->assertTrue(is_array($result));
        $this->assertTrue(is_int($result[0]['id']));
        $this->assertCount($numberItems, $result);
    }

    /**
     * @dataProvider providerEntityOne
     *
     * @param string $data
     * @param int    $numberItems
     */
    public function testDecode(string $data, int $numberItems)
    {
        $encoder = new JsonldEncoder();

        $result = $encoder->decode($data, 'ld+json');

        $this->assertTrue(is_array($result));
        $this->assertTrue(is_int($result['id']));
        $this->assertCount($numberItems, $result);
    }
//
//    /*
//     *
//     */
//    public function testSupportsDecoding()
//    {
//        // todo
//    }
//
//    /**
//     *
//     */
//    public function testEncode()
//    {
//        // todo
//    }
//
//    /**
//     *
//     */
//    public function testSupportsEncoding()
//    {
//        // todo
//    }
}

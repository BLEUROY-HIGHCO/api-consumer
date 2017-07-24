<?php

namespace Highco\ApiConsumerBundle\Serializer;

use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;

class JsonldEncoder implements EncoderInterface, DecoderInterface
{
    const FORMAT = 'ld+json';

    public function decode($data, $format, array $context = [])
    {
        $decodedData = json_decode($data, true);
        if (array_key_exists('hydra:member', $decodedData)) {
            return $decodedData['hydra:member'];
        } else {
            return $decodedData;
        }
    }

    public function supportsDecoding($format)
    {
        return $format === self::FORMAT;
    }

    public function encode($data, $format, array $context = [])
    {
        return json_encode($data);
    }

    public function supportsEncoding($format)
    {
        return $format === self::FORMAT;
    }

}

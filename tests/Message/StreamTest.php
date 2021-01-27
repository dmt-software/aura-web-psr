<?php

namespace DMT\Test\Aura\Psr\Message;

use DMT\Aura\Psr\Factory\StreamFactory;
use DMT\Aura\Psr\Message\Stream;
use Http\Psr7Test\StreamIntegrationTest;
use Psr\Http\Message\StreamInterface;

/**
 * Class StreamTest
 *
 * @package DMT\Test\Aura\Psr\Message
 */
class StreamTest extends StreamIntegrationTest
{
    /**
     * Create stream.
     *
     * @param StreamInterface|resource|string $data
     * @return Stream|StreamInterface
     */
    public function createStream($data): Stream
    {
        $factory = new StreamFactory();

        if (is_resource($data)) {
            return $factory->createStreamFromResource($data);
        }

        if (is_file($data)) {
            return $factory->createStreamFromFile($data);
        }

        return $factory->createStream($data);
    }
}

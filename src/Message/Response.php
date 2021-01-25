<?php

namespace DMT\Aura\Psr\Message;

use Aura\Web\Exception\InvalidStatusCode;
use Aura\Web\Response as AuraResponse;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Response
 *
 * @package DMT\Aura\Psr\Message
 */
class Response implements ResponseInterface
{
    use MessageTrait;

    /** @var AuraResponse $object */
    private $object;

    /**
     * Response constructor.
     * @param AuraResponse $response
     */
    public function __construct(AuraResponse $response)
    {
        $this->object = $response;
    }

    /**
     * @return AuraResponse
     */
    public function getInnerObject(): AuraResponse
    {
        return $this->object;
    }

    /**
     * Gets the response status code.
     *
     * @return int Status code.
     */
    public function getStatusCode(): int
    {
        return $this->getInnerObject()->status->getCode();
    }

    /**
     * Return an instance with the specified status code and, optionally, reason phrase.
     *
     * @param int $code The 3-digit integer result code to set.
     * @param string $reasonPhrase The reason phrase to use.
     * @return static
     * @throws \InvalidArgumentException
     */
    public function withStatus($code, $reasonPhrase = ''): self
    {
        if (!is_int($code)) {
            throw new \InvalidArgumentException('invalid status code given');
        }

        try {
            $status = clone($this->getInnerObject()->status);
            $status->set($code, $reasonPhrase);
        } catch (InvalidStatusCode $exception) {
            throw new \InvalidArgumentException('invalid status code given');
        }

        return $this->newInstanceWith(compact('status'));
    }

    /**
     * Gets the response reason phrase associated with the status code.
     *
     * @return string
     */
    public function getReasonPhrase(): string
    {
        return $this->getInnerObject()->status->getPhrase() ?? '';
    }

    /**
     * Get the header container.
     *
     * @param array $headerValues
     * @return array
     */
    protected function getHeaderContainer(array $headerValues = []): array
    {
        $headers = clone($this->getInnerObject()->headers);

        $headerValues = array_filter(
            $headerValues,
            function ($values, $name) use ($headers) {
                return $headers->get($name) !== $values;
            },
            ARRAY_FILTER_USE_BOTH
        );

        foreach ($headerValues as $header => $values) {
            $value = is_array($values) ? array_shift($values) : $values;
            $headers->set($header, $value);

            if (is_array($values)) {
                foreach ($values as $value) {
                    $headers->add($header, $value);
                }
            }
        }

        return compact('headers');
    }

    /**
     * Ensure the immutability of the response.
     *
     * @param array $override
     * @return self
     */
    protected function newInstanceWith(array $override = []): self
    {
        $innerResponse = $this->getInnerObject();

        $response = new AuraResponse(
            $override['status'] ?? clone($innerResponse->status),
            $override['headers'] ?? clone($innerResponse->headers),
            clone($innerResponse->cookies),
            $override['content'] ?? clone($innerResponse->content),
            clone($innerResponse->cache),
            clone($innerResponse->redirect)
        );

        return new static($response);
    }
}
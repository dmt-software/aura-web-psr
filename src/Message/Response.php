<?php /** @noinspection PhpRedundantCatchClauseInspection */

namespace DMT\Aura\Psr\Message;

use Aura\Web\Exception\InvalidStatusCode;
use Aura\Web\Response as AuraResponse;
use Aura\Web\WebFactory;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Response
 *
 * @package DMT\Aura\Psr\Message
 */
class Response implements ResponseInterface
{
    use MessageTrait;

    /**
     * Response constructor.
     *
     * @param int $code
     * @param string $reasonPhrase
     */
    public function __construct(int $code = 200, string $reasonPhrase = '')
    {
        $this->getInnerObject()->status->set($code, $reasonPhrase);
    }

    /**
     * @return AuraResponse
     */
    public function getInnerObject(): AuraResponse
    {
        if (!$this->object) {
            $this->object = (new WebFactory([]))->newResponse();
        }
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
     * @throws InvalidArgumentException
     */
    public function withStatus($code, $reasonPhrase = ''): self
    {
        if (!is_int($code)) {
            throw new InvalidArgumentException('invalid status code given');
        }

        try {
            $instance = clone($this);
            $instance->getInnerObject()->status->set($code, $reasonPhrase);

            return $instance;
        } catch (InvalidStatusCode $exception) {
            throw new InvalidArgumentException('invalid status code given');
        }
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
}
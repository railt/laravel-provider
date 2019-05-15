<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\LaravelProvider\Http;

use Illuminate\Http\Request;
use Railt\Http\Provider\ProviderInterface;

/**
 * Class LaravelProvider
 */
class LaravelProvider implements ProviderInterface
{
    /**
     * @var Request
     */
    private $request;

    /**
     * LaravelProvider constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return array
     */
    public function getQueryArguments(): array
    {
        return $this->request->query->all();
    }

    /**
     * @return array
     */
    public function getPostArguments(): array
    {
        return $this->request->request->all();
    }

    /**
     * @return string|null
     */
    public function getContentType(): ?string
    {
        return $this->request->getContentType();
    }

    /**
     * @return string
     * @throws \LogicException
     */
    public function getBody(): string
    {
        return (string)$this->request->getContent(false);
    }
}

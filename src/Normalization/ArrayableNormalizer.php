<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\LaravelProvider\Normalization;

use Illuminate\Contracts\Support\Arrayable;
use Railt\Normalization\Context\ContextInterface;
use Railt\Normalization\NormalizerInterface;

/**
 * Class ArrayableNormalizer
 */
class ArrayableNormalizer implements NormalizerInterface
{
    /**
     * @param mixed $result
     * @param ContextInterface $context
     * @return array|bool|float|int|mixed|string
     */
    public function normalize($result, ContextInterface $context)
    {
        if (! $result instanceof Arrayable || $context->isScalar()) {
            return $result;
        }

        return $result->toArray();
    }
}

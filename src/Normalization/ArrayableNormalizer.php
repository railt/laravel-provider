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
use Railt\Extension\Normalization\Context\ContextInterface;
use Railt\Extension\Normalization\NormalizerInterface;

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
        if ($context->isScalar()) {
            return $result;
        }

        if ($result instanceof Arrayable) {
            return $result->toArray();
        }

        if (\is_array($result)) {
            \array_walk_recursive($result, static function (&$data) {
                if ($data instanceof Arrayable) {
                    $data = $data->toArray();
                }
            });
        }

        return $result;
    }
}

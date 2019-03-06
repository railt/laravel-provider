<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace App\Http\Controllers\GraphQL;

use Illuminate\Support\Str;

/**
 * Class EchoController
 */
class EchoController
{
    /**
     * @param string $message
     * @param bool $upper
     * @return string
     */
    public function say(string $message, ?bool $upper): string
    {
        $message = $upper ? Str::upper($message) : $message;

        return \sprintf('You wrote me a message "%s"', $message);
    }
}

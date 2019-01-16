<?php
/**
 * This file is part of Railt Laravel Adapter package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace App\Http\Controllers\GraphQL;

use Illuminate\Support\Str;
use Railt\Http\InputInterface;

/**
 * Class EchoController
 */
class EchoController
{
    /**
     * @param string $message
     * @param bool $upper
     * @param InputInterface $input
     * @return string
     */
    public function say(string $message, bool $upper = false, InputInterface $input): string
    {
        $result = [
            'Your message is "' . ($upper ? Str::upper($message) : $message) . '"',
            'Path is "' . $input->getPath() . '"',
            'Alias is "' . $input->getAlias() . '"',
            'Query is "' . $input->request()->getQuery() . '"'
        ];

        return \implode('; ', $result);
    }
}

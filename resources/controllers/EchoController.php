<?php
/**
 * This file is part of Railt Laravel Adapter package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace App\GraphQL\Controllers;

use Illuminate\Support\Str;
use Railt\Http\InputInterface;

/**
 * Class EchoController
 */
class EchoController
{
    /**
     * @param InputInterface $input
     * @return string
     */
    public function say(InputInterface $input): string
    {
        $result = 'Your message is: ' . $input->get('message');

        return $input->get('upper') ? Str::upper($result) : $result;
    }
}

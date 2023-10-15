<?php

declare(strict_types=1);

namespace Railt\LaravelProvider\Compiler;

final class DirectoryLoader
{
    /**
     * @var list<non-empty-string>
     */
    private const DEFAULT_EXTENSIONS = [
        'graphqls',
        'graphql',
    ];

    /**
     * @psalm-taint-sink file $directory
     * @param non-empty-string $directory
     * @param list<non-empty-string> $extensions
     */
    public function __construct(
        private readonly string $directory,
        private readonly array $extensions = self::DEFAULT_EXTENSIONS,
    ) {}

    /**
     * @param non-empty-string $type
     */
    public function __invoke(string $type): ?\SplFileInfo
    {
        foreach ($this->extensions as $extension) {
            $pathname = $this->directory . '/' . $type . '.' . $extension;

            if (\is_file($pathname)) {
                return new \SplFileInfo($pathname);
            }
        }

        return null;
    }
}

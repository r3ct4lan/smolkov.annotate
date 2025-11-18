<?php

namespace Orm\Annotate\Helpers;

use Orm\Annotate\Module;

class Output
{
    private string $value;
    const DEFAULT_VALUE = 'annotation.php';

    const DIR_SEPARATOR = '/';
    const FILE_EXTENSION = '.php';

    public function __construct(?string $value = null)
    {
        $this->value = trim((string)$value);
        $this->normalize();
    }

    public function getValue(): string
    {
        return $this->value;
    }

    protected function normalize(): void
    {
        $this->normalizeEmpty();
        $this->normalizeSeparators();
        $this->normalizeRoot();
        $this->normalizeWindowsPath();
        $this->normalizeFilename();
        $this->normalizeExtension();
    }

    private function normalizeEmpty(): void
    {
        if ($this->value === '') {
            $this->value = $this::DEFAULT_VALUE;
        }
    }

    private function normalizeSeparators(): void
    {
        $this->value = $this->replaceSeparator($this->value);
    }

    public function replaceSeparator(string $value): string
    {
        return str_replace(['\\', '/'], self::DIR_SEPARATOR, $value);
    }

    private function normalizeRoot(): void
    {
        if (!str_starts_with($this->value, static::DIR_SEPARATOR)) {
            $documentRoot = $this->replaceSeparator(Module::getDocRoot());
            $documentRoot = rtrim($documentRoot, static::DIR_SEPARATOR);

            if (!str_starts_with($this->value, $documentRoot)) {
                $this->value = $documentRoot . static::DIR_SEPARATOR . ltrim($this->value, static::DIR_SEPARATOR);
            }
        }
    }

    private function normalizeWindowsPath(): void
    {
        if (preg_match('#^[A-Za-z]:#', $this->value) === 1) {
            $value = substr($this->value, 2);

            if ($value === '' || $value[0] !== static::DIR_SEPARATOR) {
                $value = static::DIR_SEPARATOR . $value;
            }

            $this->value = $value;
        }
    }

    private function normalizeFilename(): void
    {
        if (str_ends_with($this->value, static::DIR_SEPARATOR)) {
            $this->value .= static::DEFAULT_VALUE;
        }
    }

    private function normalizeExtension(): void
    {
        if (!str_ends_with($this->value, static::FILE_EXTENSION)) {
            $this->value .= static::FILE_EXTENSION;
        }
    }
}
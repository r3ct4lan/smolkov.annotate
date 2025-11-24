<?php

namespace Orm\Annotate\Helpers;

use Orm\Annotate\Helpers\Module as ModuleHelper;
use Orm\Annotate\Module;

class Output
{
    private string $value;
    private string $moduleName;

    const DEFAULT_VALUE = 'annotate.php';

    const DIR_SEPARATOR = '/';
    const FILE_EXTENSION = '.php';

    public function __construct(?string $value = null)
    {
        $this->value = trim((string)$value);
    }

    public function getValue(): string
    {
        $this->normalize();
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
            if ($this->moduleName) {
                $this->value = $this->getValueByModuleName();
            } else {
                $this->value = $this::DEFAULT_VALUE;
            }
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

    public function setModuleName(string $moduleName): static
    {
        $this->moduleName = $moduleName;

        return $this;
    }

    private function getValueByModuleName(): string
    {
        if (!$this->moduleName) {
            return $this->value;
        }

        $moduleHelper = new ModuleHelper($this->moduleName);
        return $moduleHelper->getDirectory() . self::DIR_SEPARATOR . self::DEFAULT_VALUE;
    }
}
<?php

namespace Ksfraser\Prefs\Config;

use RuntimeException;

/**
 * Simple INI-backed configuration for Prefs.
 */
class IniConfig
{
    /** @var string */
    private $filePath;

    /** @var array<string,array<string,mixed>> */
    private $data = [];

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
        $this->reload();
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }

    public function reload(): void
    {
        if (!is_file($this->filePath)) {
            $this->data = [];
            return;
        }

        $parsed = parse_ini_file($this->filePath, true, INI_SCANNER_RAW);
        if ($parsed === false || !is_array($parsed)) {
            throw new RuntimeException('Failed to parse INI file: ' . $this->filePath);
        }

        // Normalize to sectioned array
        $out = [];
        foreach ($parsed as $section => $values) {
            if (is_array($values)) {
                $out[(string)$section] = $values;
            }
        }
        $this->data = $out;
    }

    /**
     * @return array<string,array<string,mixed>>
     */
    public function all(): array
    {
        return $this->data;
    }

    /**
     * @param mixed $default
     * @return mixed
     */
    public function get(string $section, string $key, $default = null)
    {
        if (!isset($this->data[$section]) || !array_key_exists($key, $this->data[$section])) {
            return $default;
        }
        return $this->data[$section][$key];
    }

    /**
     * @param mixed $value
     */
    public function set(string $section, string $key, $value): void
    {
        if (!isset($this->data[$section])) {
            $this->data[$section] = [];
        }
        $this->data[$section][$key] = $value;
    }

    /**
     * @param array<string,mixed> $values
     */
    public function setSection(string $section, array $values): void
    {
        $this->data[$section] = $values;
    }

    public function save(): void
    {
        $dir = dirname($this->filePath);
        if ($dir && !is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        $ini = $this->renderIni($this->data);

        $tmp = $this->filePath . '.tmp.' . bin2hex(random_bytes(6));
        $bytes = @file_put_contents($tmp, $ini, LOCK_EX);
        if ($bytes === false) {
            throw new RuntimeException('Failed to write temp INI file');
        }

        if (is_file($this->filePath)) {
            @unlink($this->filePath);
        }

        if (!@rename($tmp, $this->filePath)) {
            @unlink($tmp);
            throw new RuntimeException('Failed to replace INI file');
        }
    }

    /**
     * @param array<string,array<string,mixed>> $data
     */
    private function renderIni(array $data): string
    {
        $lines = [];
        foreach ($data as $section => $values) {
            $lines[] = '[' . $section . ']';
            foreach ($values as $k => $v) {
                $lines[] = $this->renderIniEntry((string)$k, $v);
            }
            $lines[] = '';
        }
        return implode("\n", $lines);
    }

    /**
     * @param mixed $value
     */
    private function renderIniEntry(string $key, $value): string
    {
        if (is_array($value)) {
            $out = [];
            foreach ($value as $item) {
                $out[] = $key . '[] = ' . $this->renderIniScalar($item);
            }
            return implode("\n", $out);
        }

        return $key . ' = ' . $this->renderIniScalar($value);
    }

    /**
     * @param mixed $value
     */
    private function renderIniScalar($value): string
    {
        if ($value === null) {
            return '""';
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_int($value) || is_float($value)) {
            return (string)$value;
        }

        $s = (string)$value;
        $s = str_replace('"', '\\"', $s);
        return '"' . $s . '"';
    }
}

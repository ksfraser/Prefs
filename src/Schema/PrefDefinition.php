<?php

namespace Ksfraser\Prefs\Schema;

class PrefDefinition
{
    /** @var string */
    private $key;

    /** @var mixed */
    private $defaultValue;

    /** @var bool */
    private $required;

    /** @var string|null */
    private $description;

    /** @param mixed $defaultValue */
    public function __construct(string $key, $defaultValue = null, bool $required = false, ?string $description = null)
    {
        $this->key = $key;
        $this->defaultValue = $defaultValue;
        $this->required = $required;
        $this->description = $description;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    /** @return mixed */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
}

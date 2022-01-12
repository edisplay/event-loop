<?php

namespace Revolt\EventLoop\Internal;

use Revolt\EventLoop\FiberLocal;
use WeakMap;

final class FiberStorage
{
    private ?\WeakMap $weakMap = null;

    public function get(FiberLocal $fiberLocal): mixed
    {
        if (!$this->weakMap || !isset($this->weakMap[$fiberLocal])) {
            return null;
        }

        return $this->weakMap[$fiberLocal];
    }

    public function set(FiberLocal $fiberLocal, mixed $value): void
    {
        $weakMap = ($this->weakMap ??= new WeakMap());
        $weakMap[$fiberLocal] = $value;
    }

    public function clear(): void
    {
        $this->weakMap = null;
    }
}

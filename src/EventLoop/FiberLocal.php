<?php

namespace Revolt\EventLoop;

/**
 * Fiber local storage.
 *
 * Each instance stores data separately for each fiber. Usage examples include contextual logging data.
 *
 * @template T
 */
final class FiberLocal
{
    /** @var \Fiber|null Dummy fiber for {main} */
    private static ?\Fiber $mainFiber = null;
    private static ?\WeakMap $localStorage = null;

    /** @internal */
    public static function getFiberStorage(): Internal\FiberStorage
    {
        $fiber = self::getFiber();
        $localStorage = self::getLocalStorage();

        if (!isset($localStorage[$fiber])) {
            $storage = new Internal\FiberStorage();
            $localStorage[$fiber] = $storage;
            return $storage;
        }

        return $localStorage[$fiber];
    }

    private static function getLocalStorage(): \WeakMap
    {
        return self::$localStorage ??= new \WeakMap();
    }

    private static function getFiber(): \Fiber
    {
        $fiber = \Fiber::getCurrent();

        if ($fiber === null) {
            $fiber = self::$mainFiber ??= new \Fiber(static function () {
                // dummy fiber for main, as we need some object for the WeakMap
            });
        }

        return $fiber;
    }

    /**
     * @param T $value
     */
    public function __construct(mixed $value)
    {
        $this->set($value);
    }

    /**
     * @param T $value
     */
    public function set(mixed $value): void
    {
        self::getFiberStorage()->set($this, $value);
    }

    /**
     * @return T
     */
    public function get(): mixed
    {
        return self::getFiberStorage()->get($this);
    }
}

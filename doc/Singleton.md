# `Singleton`

```php
use axy\patterns\Singleton;

class MySingleton
{
    use Singleton;

    protected function __construct()
    {
    }
}

$instance = MySingleton::getInstance();
$instance === MySingleton::getInstance();
```

Define only public static method `getInstance()`.
If you need protected constructor that you can do yourself.

## Inheritance

All heirs share the same object.

```
use axy\patterns\Singleton;

class Parent
{
    use Singleton;
}

class Child extends Parent
{
}

(Parent::getInstance() === Child::getInstance()); // TRUE
```

If a child needs the own singleton you should define it directly:

```php
class Child extends Parent
{
    use Singleton;
}
```

## `getSingletonInstance()`

The disadvantage of that way: you cannot write a phpdoc for `getInstance()` and will be not autocomplete in IDE.
You can do like this:

```php
class MySingleton
{
    /**
     * @return MySingleton
     */
    public static function getInstance()
    {
        return self::getSingletonInstance();
    }
}
```

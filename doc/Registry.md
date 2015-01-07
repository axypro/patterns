# The Registry

The registry provides a standard features:

* Write a data with a name.
* Read a data by the name.

In additional:

* Constants.
* Read-only mode.
* Lazy load.

### Constants

The variable can be marked as constant.
Such variable cannot be changed and removed.
It can only be read.

### Readonly mode

The registry can be marked as read-only.
After this change or delete existing variables or create new ones will fail.

For example, a system registry filled system services on stage initialization.
User controllers can have access to them but modification must be forbidden.

### The variables with lazy load

For a lazy variable set a function-loader instead of direct value.
The value is created after the first access.

As callbacks can be used [axy/callbacks](https://github.com/axypro/callbacks/blob/master/doc/format.md).

## The interface

### The constructor

```php
__construct([array $vars [, array $lazy [, bool $readonly = FALSE])
```

* `$vars`: the initial list of variables (a key => a value).
* `$lazy`: the initial list of lazy variables (a key => a callback).
* `$readonly`: you can set read-only mode immediately.

All variables that are set by the constructor are not constants.

### Setters

##### `set(string $name, mixed $value [, boolean $const = FALSE])`

Sets a variable `$name` to `$value`.
If specified `$const` marks the variable as constant.

If there is a variable with the same name then it is change.
If it is constant then throw error `PropertyReadOnly`.
If the registry is read-only then throw error `ContainerReadOnly`.

```php
$registry->set('x', 1);
$registry->set('x', 2, true);
$registry->set('x', 3); // PropertyReadOnly
```

##### `setLazy(string $name, callable $creator [, boolean const = false])`

A lazy variable can be marked as constant too.

```php
$loader = function () {
    return file_get_contents('file.txt');
};

$registry->setLazy('filecontent', $loader);

echo $registry->filecontent; // the file will be loaded here
```

Or with axy/callback:

```php
$registry->setLazy('filecontent', [null, 'file_get_contents', ['file.txt']]);
```

If there is a variable with the same name then it is change.
If it is constant then throw error `PropertyReadOnly`.
If the registry is read-only then throw error `ContainerReadOnly`.

##### `setVars(array $vars = null, array $lazy = null)`

Save many variables. 
Normal and lazy.

##### `remove(string $name)`

Removes a variable.
If it is not constant and the container is not read-only.

##### `markAsConstant(string $name)`

Marks an existing variable as a constant.

Returns `FALSE` if variable was the constant.

If variable is not exists returns `FALSE` and do nothing.

### Getters

##### `get(string $name [, mixed $default = NULL [, $load = TRUE]):mixed

Returns a value of a variable by the `$name`.
If the variable is not found then returns `$default`.

If this variable is lazy and it is not loaded yet and `$load` is `FALSE` then it will not loading.
Just return `$default`.

##### `getAllVars([$load = TRUE])`

Returns a dictionary with all variables (a key => a value).

By default, loaded all lazy variables, which was not yet loaded.
But if `$load` is `FALSE` then returns only loading at the moment.

##### `exists(string $name):bool`

Checks if a variable with the `$name` is defined (loaded or not not matter).

```php
$registry->set('x', 1);
$registry->setLazy('y', 'callback');

$registry->exists('x'); // TRUE
$registry->exists('y'); // TRUE, y is not loaded but defined
$registry->exists('z'); // FALSE
```

##### `isConstant(string $name):bool`

Checks if a variable with the `$name` is constant.

##### `isLoaded(string $name):bool`

Checks if a variable with the `$name` is loaded.
Normal (not lazy) variable is always "loaded".

### Magic

Can use the magic methods:

```php
$registry->x = 10;
if (isset($registry->y)) {
    echo $registry->y;
    unset($registry->y);
}
```

And the array syntax:

```php
$registry['a'] = $registry['b'] + $registry['c'];
```

The registry implements `Countable` and `Traversable`.
Before `foreach()` all lazy variables will be loaded.

### Read-only mode

##### `toReadOnly(void):boolean`

The read-only mode can be enabled in the constructor.

```php
$registry = new Registry(['x' => 1], null, true);
```

Or after:
```php
$registry = new Registry();
$registry->x = 1;
$registry->y = 2;
$registry->toReadOnly();
```

`toReadOnly()` returns `TRUE` if mode switching occurred in a presently.
Or `FALSE` if the registry was in the read-only mode already.

Switch to read-only is irreversible.

##### `isReadOnly(void):bool`

Checks if the registry is read-only.

### Errors

Registry methods throws the following errors (from `axy\patterns\errors` namespace):

* `ContainerReadOnly`: the attempt write to a read-only registry.
* `PropertyReadOnly`: the attempt change a value for a constant (if the registry is not read-only).
* Any other exception from loaders.

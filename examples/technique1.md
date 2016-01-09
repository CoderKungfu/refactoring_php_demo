# Refactoring Technique 1:

## Change names to communicate intent.

### Example A - Mystery Variables

```php
<?php

$a = 'Michael';
$b = 'Cheng';

printf('Hello, my name is %s %s', $a, $b);
```

What does `$a` and `$b` stand for? How do you change it to give it *meaning*?

**The refactored code:**

```php
<?php

$first_name = 'Michael';
$last_name = 'Cheng';

printf('Hello, my name is %s %s', $first_name, $last_name);
```

### Example B - Mystery functions

```php
function foo($first_name, $last_name) {
  return sprintf('Hello, my name is %s %s', $first_name, $last_name);
}

echo foo('Luke', 'Skywalker');
```

What does `foo` stand for? How do you change it to give it *meaning*?

```php
function greetings_from($first_name, $last_name) {
  return sprintf('Hello, my name is %s %s', $first_name, $last_name);
}

echo greetings_from('Luke', 'Skywalker');
```
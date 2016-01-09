# Refactoring Like a Pro

## What is refactoring?

- A change made to the internal structure of software to make it easier to understand and cheaper to modify without changing its observable behavior.
- To restructure software by applying a series of refactorings without changing its observable behavior.
- Software left alone will decay.

## Why do we Refactor?

- Makes it cheaper to make changes.
- Make it easier to understand.
- Improves the design of the code.

## Refactoring techniques:

### 1) Change names to communicate intent.

#### Example A - Mystery Variables

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

#### Example B - Mystery functions

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

### 2) No magic numbers / variables.

```php
class Currency {
  public static function in_usd($amount) {
    return $amount * 1.4;
  }
}

echo Currency::in_usd(100);
```

What is `1.4`? Bring out the meaning with a variable or a constant.

```php
class Currency {
  private static $usd_exchange_rate = 1.4;

  public static function in_usd($amount) {
    return $amount * self::$usd_exchange_rate;
  }
}

echo Currency::in_usd(100);
```

### 3) One responsibility per function / class

#### Example: Doing too many things!

```php
<?php
function add_member($first_name, $last_name, $email){
  $fp = fopen('members.csv', 'r+');

  while (($data = fgetcsv($fp)) !== FALSE) {
    if ($data[2] == $email) {
      fclose($fp);
      return false;
    }
  }

  fputcsv($fp, [$first_name, $last_name, $email]);
  fclose($fp);
}

var_dump(add_member('Michael', 'Cheng', 'miccheng@gmail.com'));
```

How many things is `add_member` doing?

Abstract away implementation details into separate functions.<br/>
What if we want to check for duplicate names as well?

```php
<?php
function get_members() {
  $fp = fopen('members.csv', 'r');

  $members = [];
  while (($values = fgetcsv($fp)) !== FALSE) {
    array_push($members, $values);
  }

  fclose($fp);
  return $members;
}

function check_record($user){
  $user_values = array_values($user);
  foreach(get_members() as $member) {
    if (array_intersect($user_values, $member)) {
      return true;
    }
  }
  return false;
}

function add_member($user){
  if (check_record($user)) return false;

  $fp = fopen('members.csv', 'a');
  fputcsv($fp, array_values($user));
  fclose($fp);

  return true;
}

$new_user = [
  'first_name'=>'Leia',
  'last_name'=>'Organa',
  'email'=>'organa@newrepublic.com'
];

var_dump(add_member($new_user));
```

### 4) Do not be obsessed with primitives

- Use classes / models to encapsulate behavior. 
- Stop passing associative arrays around.
- Switch to compositional approach.
- Use objects that work together.

```php
<?php
class User {
  public $first_name;
  public $last_name;
  public $email;

  public function __construct($first_name, $last_name, $email) {
    $this->first_name = $first_name;
    $this->last_name = $last_name;
    $this->email = $email;
  }

  public function same_as(User $other_user) {
    if ($other_user->email == $this->email)
      return true;

    if (
      $other_user->first_name == $this->first_name &&
      $other_user->last_name == $this->last_name
    )
      return true;

    return false;
  }

  public function values() {
    return [ $this->first_name, $this->last_name, $this->email ];
  }
}

class ClubMembership {
  private static $membership_file = 'members.csv';
  private $members = [];
  private $file_handle;

  public function __construct() {
    $this->file_handle = fopen(self::$membership_file, 'r+');
  }

  public function get_members() {
    if (!empty($this->members)) return $this->members;

    while (($values = fgetcsv($this->file_handle)) !== FALSE) {
      $user = new User($values[0], $values[1], $values[2]);
      array_push($this->members, $user);
    }

    return $this->members;
  }

  public function check_record(User $user){
    foreach($this->get_members() as $member) {
      if ($member->same_as($user))
        return true;
    }
    return false;
  }

  public function add_member(User $user) {
    if ($this->check_record($user)) return false;

    fputcsv($this->file_handle, $user->values());
    array_push($this->members, $user);

    return true;
  }
}

$new_user = new User('Michael', 'Jordan', 'mj@nba.com');

$membership = new ClubMembership();

var_dump($membership->add_member($new_user));

```


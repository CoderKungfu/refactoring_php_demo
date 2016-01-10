class: center, middle

# How To Refactor Like a ~~Pro~~ Boss 

### Michael Cheng / @coderkungfu
---

# Agenda

1. What is refactoring? Why do we refactor?
--

2. Common refactoring techniques
---

# What is refactoring? Why do we do it?
--

- Software left alone will decay
--

- A change made to the internal structure of software to make it:

--

    1.  Easier to understand
--

	2.  Cheaper to make future changes
--

- This does not change the observable behavior of the software
--

- Improves the design of the code
--

- Tests help ensure that we didn't change behavior
---

# Common refactoring techniques:
--

1. Change names to communicate intent
--

2. No magic numbers / variables
--

3. One responsibility per function / class
--

4. Do not be obsessed with primitives
---
class: center, middle

# Refactoring Technique 1:

# Change names to communicate intent
---

## Technique 1: Change names to communicate intent

### Example A - Mystery Variables
--

```php
$a = 'Michael';
$b = 'Cheng';

printf('Hello, my name is %s %s', $a, $b);
```
--

What does `$a` and `$b` stand for? How do you change it to give it *meaning*?
---
## Technique 1: Change names to communicate intent

### Example A - Mystery Variables

```php
*$first_name = 'Michael';
*$last_name = 'Cheng';

printf('Hello, my name is %s %s', $first_name, $last_name);
```
---

## Technique 1: Change names to communicate intent

### Example B - Mystery functions
--

```php
function foo($first_name, $last_name) {
  return sprintf('Hello, my name is %s %s', $first_name, $last_name);
}

echo foo('Luke', 'Skywalker');
```
--

What does `foo` stand for? How do you change it to give it *meaning*?
---

## Technique 1: Change names to communicate intent

### Example B - Mystery functions

```php
*function greetings_from($first_name, $last_name) {
  return sprintf('Hello, my name is %s %s', $first_name, $last_name);
}

echo greetings_from('Luke', 'Skywalker');
```
---
class: center, middle

# Refactoring Technique 2:

# No magic numbers / variables
---

## Technique 2: No magic numbers / variables

### Example - Mystery number
--

```php
function amount_in_usd($amount) {
  return $amount * 1.4;
}

echo amount_in_usd(100);
```
--

What is `1.4`?
---
## Technique 2: No magic numbers / variables

### Example - Mystery number

Bring out the meaning with a variable or a constant.

```php
*define('USD_EXCHANGE_RATE', 1.4);

function amount_in_usd($amount) {
  return $amount * USD_EXCHANGE_RATE;
}

echo amount_in_usd(100);
```
---
## Technique 2: No magic numbers / variables

### Example - Mystery number in a `class`

```php
class Currency {
* const USD_EXCHANGE_RATE = 1.4;

  public static function in_usd($amount) {
    return $amount * self::USD_EXCHANGE_RATE;
  }
}

echo Currency::in_usd(100);
```
---
class: center, middle

# Refactoring Technique 3:

# One responsibility per function / class
---
name: technique3_header1

## Technique 3: One responsibility per function / class

### Example: Club Membership Register
---

template: technique3_header1

#### Background

You are a software guy at a country club and you maintain a register of club members in `CSV` format:

--
```csv
Michael,Cheng,miccheng@gmail.com
Luke,Skywalker,luke.skywalker@jedi.com
```
---

template: technique3_header1

Your manager wants you to make a PHP script to manage the members register.
--

#### Feature Story

> "I want to add a new member to the end of the file.
> If the email is already in the list, ignore the new entry."

.center.emoji[ 🤓🤓🤓 ]
---

### Technique 3: One responsibility per function / class

You quickly whip together a proof-of-concept PHP function to meet the above requirement:

```php
function add_member($new_member){
  $fp = fopen('members.csv', 'r+');

  while (($data = fgetcsv($fp)) !== FALSE) {
    if ($data[2] == $new_member[2]) {
      fclose($fp);
      return false;
    }
  }

  fputcsv($fp, $new_member);
  fclose($fp);
}

$new_member = ['Michael', 'Cheng', 'miccheng@gmail.com'];
var_dump(add_member($new_member));
```
---

class: center, middle

# It works! 😎😎😎

![Fuck yeah!](http://lovelivegrow.com/wp-content/uploads/2012/11/Fuck-Yeah.jpg)

---
### Technique 3: One responsibility per function / class

### Questions 🤔
--

- How many things is `add_member` doing?

--

    1. Opens CSV file
--
    2. Checks for duplicate email(?)
--
    3. Ignores if its already there
--
    4. Adds it in if its not
--
- Is it too much? Ideally, each function should only have 1 responsibility.
--

- And this code isn't very readable - you might come back in 2 months and forget what the code means.
---

### Technique 3: One responsibility per function / class

### What should you do?
--

- Abstract away implementation details into separate functions.
--

- Ask your self: ***"What if..."*** questions.
---

### Technique 3: One responsibility per function / class

### Step 1: Identify the different behavior
--

1. <strike>Opens CSV file</strike> Get List of members
--

2. Checks <strike>for duplicate email</strike> if member is already on the list
--

3. <strike>Ignores if its already there</strike> (this is result of 2)
--

4. Adds <strike>it in if its not</strike> new member to the list
---

name: refactor_separate_functions

### Technique 3: One responsibility per function / class

### Step 2: Break into separate functions
---

template: refactor_separate_functions

#### a. Pull out the code (extract) that "gets list of members" into a function

```php
function get_members() {
  $fp = fopen('members.csv', 'r');

  $members = [];
  while (($values = fgetcsv($fp)) !== FALSE) {
    array_push($members, $values);
  }

  fclose($fp);
  return $members;
}
```
---

template: refactor_separate_functions

#### b. Extract "Checks if member is already on the list" into a function

```php
function is_member_in_list($members, $new_member){
  foreach($members as $member) {
    if ($member[2] == $new_member[2]) // Email field
      return true;
  }
  return false;
}
```
---

template: refactor_separate_functions

#### c. Extract "Adds new member to the list" into a function

```php
function add_member_to_file($user) {
  $fp = fopen('members.csv', 'a');
  fputcsv($fp, $user);
  fclose($fp);
}
```
---

### Technique 3: One responsibility per function / class

### Step 3: Refactor `add_member` to use the new functions

```php
function add_member($new_member){
  $current_members = get_members();
  if (is_member_in_list($current_members, $new_member))
    return false;

  add_member_to_file($new_member);
  return true;
}

$new_member = ['Michael', 'Cheng', 'miccheng@gmail.com']
var_dump(add_member($new_member));
```
--

.center.emoji[ 😁😁😁 ]

---
class: center, middle

# After 2 months...

---

### Technique 3: One responsibility per function / class

### Feature Change

--

> "I want this to also check that the name of the user is not in the list and ignore that too."

.center.emoji[ 😣😣😣 ]

--

People with same name but different email addresses are also being added to the list. You have to help him prevent this in the future.
---

### Technique 3: One responsibility per function / class

### The Code Change

--
#### Functions list:

- get_members()
- is_member_in_list($members, $new_member)
- add_member_to_file($user)
- add_member($new_member)

--

#### Where do you think the code change should happen?
---

### Technique 3: One responsibility per function / class

### The Code Change

#### Functions list:

- get_members()
- `is_member_in_list($members, $new_member)`
- add_member_to_file($user)
- add_member($new_member)

#### Where do you think the code change should happen?

---

### Technique 3: One responsibility per function / class

#### Main function

```php
function add_member($new_member){
  $current_members = get_members();
* if (is_member_in_list($current_members, $new_member))
    return false;

  add_member_to_file($new_member);
  return true;
}
```

Update `is_member_in_list` function so that it checks for full name.
---
### Technique 3: One responsibility per function / class

The original `is_member_in_list` function

```php
function is_member_in_list($members, $new_member){
  foreach($members as $member) {
    if ($member[2] == $new_member[2]) // Email field
      return true;
  }
  return false;
}
```

---
### Technique 3: One responsibility per function / class

Updated `is_member_in_list` function that checks for full name.

```php
function is_member_in_list($members, $new_member){
  foreach($members as $member) {
    if ($member[2] == $new_member[2]) // Email field
      return true;
*   if (
*       $member[0] == $new_member[0] && // First name
*       $member[1] == $new_member[1]    // Last name
*   ) {
*   	return true;
*   }
  }
  return false;
}
```
---

### Technique 3: One responsibility per function / class

### What have we learnt?
--

- Readable code FTW!
--

- How to break up a long function into separate functions - each with 1 responsibility
--

- Future code changes can be easily made by changing one of the functions without affecting the main function's API 
---

class: center, middle

# Refactoring Technique 4:

# Do not be obsessed with primitives
---

class: center, middle

# To be continued...

### (Next month?)

---

class: center, middle

# Meanwhile... 

## Check out the sample codes.

### https://github.com/CoderKungfu/refactoring_php_demo

---

class: center, middle

# Questions?

## Twitter: @CoderKungfu

### https://github.com/CoderKungfu/refactoring_php_demo
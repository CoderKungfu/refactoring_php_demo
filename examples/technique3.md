# Refactoring Technique 3:

## One responsibility per function / class

# Example: Club Membership Register

## Background

You are a software guy at a country club and you are told to maintain a register of club members in `CSV` format:

```csv
Michael,Cheng,miccheng@gmail.com
Luke,Skywalker,luke.skywalker@jedi.com
```
Your manager wants you to make a PHP script to manage the members register.

## Feature Story
> I want to add a new member to the end of the file.
> If the email is already in the list, ignore the new entry.

## First Implementation

You quickly whip together a quick PHP function to meet the above requirement:

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

### Questions

How many things is `add_member` doing?

1. Opens CSV file
2. Checks for duplicate email(?)
3. Ignores if its already there
4. Adds it in if its not

Is it too much? Ideally, each function should only have 1 responsibility. And this code isn't very readable - you might come back in 2 months and forget what the code means.

### What should you do?

- Abstract away implementation details into separate functions.
- What if we want to check for duplicate names as well?

## Refactoring

### Step 1: Identify the different behavior

1. <strike>Opens CSV file</strike> Get List of members
2. Checks <strike>for duplicate email</strike> if member is already on the list
3. <strike>Ignores if its already there</strike> (this is result of 2)
4. Adds <strike>it in if its not</strike> new member to the list

### Step 2: Break into separate functions

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

#### c. Extract "Adds new member to the list" into a function

```php
function add_member_to_file($user) {
  $fp = fopen('members.csv', 'a');
  fputcsv($fp, $user);
  fclose($fp);
}
```

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

## The Final Code

Great! The code looks neater now.

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

function is_member_in_list($members, $new_member){
  foreach($members as $member) {
    if ($member[2] == $new_member[2]) // Email field
      return true;
  }
  return false;
}

function add_member_to_file($new_member) {
  $fp = fopen('members.csv', 'a');
  fputcsv($fp, $new_member);
  fclose($fp);
}

function add_member($new_member){
  $current_members = get_members();
  if (is_member_in_list($current_members, $new_member))
    return false;

  add_member_to_file($new_member);
  return true;
}

$new_member = ['Michael', 'Cheng', 'miccheng@gmail.com'];
var_dump(add_member($new_member));
```

## Feature Change

So your manager came back to you after 2 months and tells you that there's a bug in the code.

People with same name but different email addresses are also being added to the list. You have to help him prevent this in the future.

> I want this to also check that the name of the user is not in the list and ignore that too.

## The Code Change

Because you have not looked at this code in a while, you might have forgotten how it was implemented in the first place.

But luckily, you did spent some time refactoring the code. You can now quickly regain your understanding of the code by looking at the main function and skimming the name of other functions being used.

### Main function

```php
function add_member($new_member){
  $current_members = get_members();
  if (is_member_in_list($current_members, $new_member))
    return false;

  add_member_to_file($new_member);
  return true;
}
```

### Functions list:

- `get_members()`
- `is_member_in_list($members, $new_member)`
- `add_member_to_file($user)`
- `add_member($new_member)`

### Where do you think the code change should happen?

Updated `is_member_in_list` function that checks for full name.

```php
function is_member_in_list($members, $new_member){
  foreach($members as $member) {
    if ($member[2] == $new_member[2]) // Email field
      return true;
    if (
        $member[0] == $new_member[0] && // First name
        $member[1] == $new_member[1]    // Last name
    ) {
    	return true;
    }
  }
  return false;
}
```

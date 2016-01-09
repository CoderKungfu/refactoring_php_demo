# Refactoring Technique 3:

## One responsibility per function / class

### Example: Doing too many things!

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
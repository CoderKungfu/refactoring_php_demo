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
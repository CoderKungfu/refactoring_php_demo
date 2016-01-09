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

function is_member_in_list($members, $new_member){
  foreach($members as $member) {
    if ($member[2] == $new_member[2]) // Email field
      return true;
  }
  return false;
}

function add_member_to_file($user) {
  $fp = fopen('members.csv', 'a');
  fputcsv($fp, $user);
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
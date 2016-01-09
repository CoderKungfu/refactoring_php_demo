<?php
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
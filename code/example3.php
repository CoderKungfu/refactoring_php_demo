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
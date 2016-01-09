<?php
function foo($first_name, $last_name) {
  return sprintf('Hello, my name is %s %s', $first_name, $last_name);
}

echo foo('Luke', 'Skywalker');
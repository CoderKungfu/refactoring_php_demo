<?php
require_once dirname(dirname(__FILE__)) . '/example4.php';

class MemberTest extends PHPUnit_Framework_TestCase
{
  public function testConstructor()
  {
    $first_name = 'Michael';
    $last_name = 'Cheng';
    $email = 'miccheng@gmail.com';

    $options = ['first_name'=>$first_name, 'last_name'=>$last_name, 'email'=>$email];
    $member = new Member($options);

    $this->assertEquals($first_name, $member->first_name);
    $this->assertEquals($last_name, $member->last_name);
    $this->assertEquals($email, $member->email);
  }

  public function testEquals() {
    $first_name = 'Michael';
    $last_name = 'Cheng';
    $email = 'miccheng@gmail.com';
    $member = new Member(['first_name'=>$first_name, 'last_name'=>$last_name, 'email'=>$email]);

    $same_email = new Member(['first_name'=>'Peter', 'last_name'=>'Paul', 'email'=>$email]);
    $this->assertTrue($member->equals($same_email));

    $same_name = new Member(['first_name'=>$first_name, 'last_name'=>$last_name, 'email'=>'test@example.com']);
    $this->assertTrue($member->equals($same_name));

    $diff_person = new Member(['first_name'=>'1', 'last_name'=>'2', 'email'=>'test@example.com']);
    $this->assertFalse($member->equals($diff_person));
  }
}
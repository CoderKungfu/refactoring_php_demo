<?php
require_once dirname(dirname(__FILE__)) . '/example4.php';

class ClubMembershipRegisterTest extends PHPUnit_Framework_TestCase
{
  protected $data_store;
  protected $membership_register;

  public function setUp() {
    $options = ['file'=> __DIR__ . '/members.csv'];
    $this->data_store = new CSVDataStore($options);
    $this->membership_register = new ClubMembershipRegister($this->data_store);
  }

  public function tearDown() {
    file_put_contents(__DIR__ . '/members.csv', "Jim,Henson,j.henson@muppets.com\r\n");
  }

  public function testAllMembers()
  {
    $members = $this->membership_register->all_members();

    $this->assertTrue(is_a($members[0], 'Member'));
    $this->assertEquals($members[0]->last_name, 'Henson');
    $this->assertEquals($members[0]->email, 'j.henson@muppets.com');
  }

  public function testIsExistingMember() {
    $first_name = 'Michael';
    $last_name = 'Cheng';
    $email = 'miccheng@gmail.com';

    $options1 = ['first_name'=>$first_name, 'last_name'=>$last_name, 'email'=>$email];
    $new_member = new Member($options1);
    $this->assertFalse($this->membership_register->is_in_register($new_member), 'Should not be in register');

    $options2 = ['first_name'=>$first_name, 'last_name'=>$last_name, 'email'=>'j.henson@muppets.com'];
    $same_email = new Member($options2);
    $this->assertTrue($this->membership_register->is_in_register($same_email), 'Not same email');

    $options3 = ['first_name'=>'Jim', 'last_name'=>'Henson', 'email'=>$email];
    $same_name = new Member($options3);
    $this->assertTrue($this->membership_register->is_in_register($same_name), 'Not same name');
  }

  public function testAddMember() {
    $first_name = 'Michael';
    $last_name = 'Cheng';
    $email = 'miccheng@gmail.com';

    $new_member = new Member(['first_name'=>$first_name, 'last_name'=>$last_name, 'email'=>$email]);
    $this->assertTrue($this->membership_register->add_member($new_member), 'Should be able to add');
    $this->assertEquals(2, count($this->membership_register->all_members()));

    $this->assertFalse($this->membership_register->add_member($new_member), 'Should not be able to add again');
  }
}
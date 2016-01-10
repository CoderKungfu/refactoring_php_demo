<?php
class Member {
  public $first_name;
  public $last_name;
  public $email;

  private static $known_fields = [ 'first_name', 'last_name', 'email' ];

  public function __construct($options) {
    foreach(self::$known_fields as $field_name) {
      if (isset($options[$field_name]))
        $this->$field_name = $options[$field_name];
    }
  }

  public static function init_member($record) {
    $options = array_combine(self::$known_fields, $record);
    return new Member($options);
  }

  public function equals(Member $query_member) {
    if ($query_member->email == $this->email) return true;

    if (
      $query_member->first_name == $this->first_name &&
      $query_member->last_name == $this->last_name
    )
      return true;

    return false;
  }

  public function values() {
    return get_object_vars($this);
  }
}

class CSVDataStore {
  private static $csv_file;
  private $file_handle;

  public function __construct($options=array()) {
    self::$csv_file = empty($options['file']) ? __DIR__ . '/members.csv' : $options['file'];
    $this->file_handle = fopen(self::$csv_file, 'a+');
  }

  public function read_all() {
    rewind($this->file_handle);
    $data = [];
    while (($values = fgetcsv($this->file_handle)) !== FALSE) {
      array_push($data, $values);
    }
    return $data;
  }

  public function append($data) {
    return fputcsv($this->file_handle, $data) > 0;
  }
}

class ClubMembershipRegister {
  private $data_store;

  public function __construct($data_store=null) {
    $this->data_store = (empty($data_store)) ? new CSVDataStore() : $data_store;
  }

  public function all_members() {
    $all_records = $this->data_store->read_all();
    return array_map('Member::init_member', $all_records);
  }

  public function is_in_register(Member $query_member){
    foreach($this->all_members() as $member) {
      if ($member->equals($query_member))
        return true;
    }
    return false;
  }

  public function add_member(Member $new_member) {
    if ($this->is_in_register($new_member)) return false;

    $new_record = $new_member->values();
    $this->data_store->append($new_record);

    return true;
  }
}

// $new_member = new Member(['first_name'=>'Michael', 'last_name'=>'Jordan', 'email'=>'mj@nba.com']);
// $membership = new ClubMembershipRegister();
// var_dump($membership->add_member($new_member));
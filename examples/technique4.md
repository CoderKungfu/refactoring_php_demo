# Refactoring Technique 4:

## Do not be obsessed with primitives

- Use classes / models to encapsulate behavior.
- Stop passing associative arrays around.
- Switch to compositional approach.
- Use objects that work together.

# Example: Club Membership Register v2.0

## Feature change

Your manager came to you again, rubbing his hands, he says:

> I am thinking of adding a new field to the register. I'm getting confirmation from the directors. But in any case, can you make sure your script can support an address field in the future?

## More Refactoring

So before we can add the new field, let's refactor the code such that it can support new fields in the future.

You could just add a new item in the input array:

```php
$new_member = ['Tony', 'Blair', 't.blair@gov.uk', '1, Downing Street'];
var_dump(add_member($new_member));
```

But what if they add a phone number in the future?

```php
$new_member = ['Tony', 'Blair', 't.blair@gov.uk', '1, Downing Street', '1234567'];
var_dump(add_member($new_member));
```

How about when they decide to change the order of the fields? :(

## Think in terms of business logic

Instead of modeling the `$new_member` as an `array`, let's imagine it as an `object` with attributes and behaviors. We should also imagine the Club Membership Register as an `object` with attributes and behaviors.

### Class Responsibility & Collaborators (CRC)

To do this, we prepare some CRC cards:

```
Class: Member
---------------------------------------------
Member particulars         |


Class: ClubMembershipRegister
---------------------------------------------
Who is a current member    |  Member
Adds a member              |  DataStore


Class: DataStore
---------------------------------------------
Read from CSV file         |
Writes to CSV file         |
```

### From CRC to Class

```php
class Member {
  public $first_name;
  public $last_name;
  public $email;
}

class ClubMembership {
  private $members = [];
  
  public function get_members(){ }
  public function is_member_in_list($new_member) { }
  public function add_member_to_file($new_member) { }
  public function add_member($new_member) { }
}

class DataStore {
  public function read_all() { }
  public function append($data) { }
}
```

## The Refactored Code

```php
class Member {
  public $first_name;
  public $last_name;
  public $email;

  public function __construct($options) {
    $this->first_name = $options['first_name'];
    $this->last_name = $options['last_name'];
    $this->email = $options['email'];
  }

  public static function init_member($record) {
    $options = array_combine(['first_name','last_name', 'email'], $record);
    return new Member($options);
  }

  public function equals(Member $query_member) {
    if ($query_member->email == $this->email)
      return true;

    if (
      $query_member->first_name == $this->first_name &&
      $query_member->last_name == $this->last_name
    )
      return true;

    return false;
  }

  public function values() {
    return [ $this->first_name, $this->last_name, $this->email ];
  }
}

class CSVDataStore {
  private static $membership_file;
  private $file_handle;

  public function __construct($options=array()) {
    echo self::$membership_file;

    self::$membership_file = empty($options['file']) ? __DIR__ . '/members.csv' : $options['file'];


    $this->file_handle = fopen(self::$membership_file, 'a+');
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

$new_member = new Member(['first_name'=>'Michael', 'last_name'=>'Jordan', 'email'=>'mj@nba.com']);
$membership = new ClubMembershipRegister();
var_dump($membership->add_member($new_member));
```
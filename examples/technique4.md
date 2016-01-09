# Refactoring Technique 4:

## Do not be obsessed with primitives

- Use classes / models to encapsulate behavior.
- Stop passing associative arrays around.
- Switch to compositional approach.
- Use objects that work together.

```php
class User {
  public $first_name;
  public $last_name;
  public $email;

  public function __construct($first_name, $last_name, $email) {
    $this->first_name = $first_name;
    $this->last_name = $last_name;
    $this->email = $email;
  }

  public function same_as(User $other_user) {
    if ($other_user->email == $this->email)
      return true;

    if (
      $other_user->first_name == $this->first_name &&
      $other_user->last_name == $this->last_name
    )
      return true;

    return false;
  }

  public function values() {
    return [ $this->first_name, $this->last_name, $this->email ];
  }
}

class ClubMembership {
  private static $membership_file = 'members.csv';
  private $members = [];
  private $file_handle;

  public function __construct() {
    $this->file_handle = fopen(self::$membership_file, 'r+');
  }

  public function get_members() {
    if (!empty($this->members)) return $this->members;

    while (($values = fgetcsv($this->file_handle)) !== FALSE) {
      $user = new User($values[0], $values[1], $values[2]);
      array_push($this->members, $user);
    }

    return $this->members;
  }

  public function check_record(User $user){
    foreach($this->get_members() as $member) {
      if ($member->same_as($user))
        return true;
    }
    return false;
  }

  public function add_member(User $user) {
    if ($this->check_record($user)) return false;

    fputcsv($this->file_handle, $user->values());
    array_push($this->members, $user);

    return true;
  }
}

$new_user = new User('Michael', 'Jordan', 'mj@nba.com');

$membership = new ClubMembership();

var_dump($membership->add_member($new_user));

```
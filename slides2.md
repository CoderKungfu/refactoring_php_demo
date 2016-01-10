class: center, middle

# How To Refactor Like a ~~Pro~~ Boss

# (Part 2)

### Michael Cheng / @coderkungfu
---
class: center, middle

# Recap from Part 1
---
# What is refactoring? Why do we do it?
--

- Software left alone will decay
--

- A change made to the internal structure of software to make it:

--

    1.  Easier to understand
--

	2.  Cheaper to make future changes
--

- This does not change the observable behavior of the software
--

- Improves the design of the code
--

- Tests help ensure that we didn't change behavior
---

# Common refactoring techniques:
--

1. Change names to communicate intent
--

2. No magic numbers / variables
--

3. One responsibility per function / class
--

4. **Do not be obsessed with primitives**
---

class: center, middle

# Refactoring Technique 4:

# Do not be obsessed with primitives
---

## Technique 4: Do not be obsessed with primitives

### Why?
--

- Array & Associative Array are awesome as generic data containers
--

- But they do not communicate intent
--

- Brittle - breaks easily if your API changes
---
## Technique 4: Do not be obsessed with primitives

### Alternative
--

- Use classes / models to encapsulate behavior
--

- Switch to compositional approach
--

- Use objects that work together
---

## Technique 4: Do not be obsessed with primitives

### Example: Club Membership Register v2.0
--

Your manager came to you again, rubbing his hands, he says:
--

> "I am thinking of adding a new field to the register. I'm getting confirmation from the directors. But in any case, can you make sure your script can support an address field in the future?"

.center.emoji[ 🤓🤓🤓 ]
---
name: technique4_header

### Technique 4: Do not be obsessed with primitives

---
template: technique4_header

### So how can we do this?
--

You could just add a new item in the input array:
--

```php
$new_member = ['Tony', 'Blair', 't.blair@gov.uk',
                '1, Downing Street'];
var_dump(add_member($new_member));
```
--

But what if they add a phone number in the future?
--

```php
$new_member = ['Tony', 'Blair', 't.blair@gov.uk',
                '1, Downing Street', '1234567'];
var_dump(add_member($new_member));
```
--
How about when they decide to change the order of the fields?

--
.emoji.center[ 😱😱😱 ]
---

template: technique4_header

### Think in terms of business logic
--

- Instead of modeling the `$new_member` as an `array`, let's imagine it as an `object` with attributes and behaviors.
--

- To do this, we prepare some **Class Responsibility & Collaborators (CRC)** cards


--
```bash
Class name
---------------------------------------------
Responsibilities         |  Collaborators
(what the class knows)   |  (other classes it works with) 
                         |
```
---

template: technique4_header

### Class Responsibility & Collaborators (CRC)
--

```markdown
Class: Member
---------------------------------------------
Member particulars         |
```
--

```markdown
Class: ClubMembershipRegister
---------------------------------------------
List of members            |  Member
Who is a current member    |
Adds a member              |
Reads CSV file             |
Writes new member to CSV   |
```
--

Hmm... .emoji[ 🤔 ] the `ClubMembershipRegister` class seems to know too much about how we store the data
---
template: technique4_header

```markdown
Class: Member
---------------------------------------------
Member particulars         |
```

```markdown
Class: ClubMembershipRegister
---------------------------------------------
List of members            |  Member
Who is a current member    |  DataStore
Adds a member              |
```
--

#### Let's split the storage bits into a separate class

```markdown
Class: DataStore
---------------------------------------------
Read from CSV file         |
Writes to CSV file         |
```
---
template: technique4_header

#### From CRC to Class

```php
class Member {
  public $first_name;
  public $last_name;
  public $email;
}

class ClubMembershipRegister {
  public function all_members(){ }
  public function is_in_register($new_member) { }
  public function add_member($new_member) { }
}

class DataStore {
  public function read_all() { }
  public function append($data) { }
}
```
---
class: center, middle

# Let's write some tests!

---

template: technique4_header

### Test driven development (TDD)

--

- Use tests to guide the development of features
--

- Tests are important in ensuring that your code changes do not affect existing behavior when you refactor
--

- Red &raquo; Green &raquo; Refactor

.center.emoji.big[ 🚦 ]

---

template: technique4_header

#### Step 1: Write a test

```php
class MemberTest extends PHPUnit_Framework_TestCase
{
  public function testConstructor()
  {
    $first_name = 'Michael';
    $last_name = 'Cheng';
    $email = 'miccheng@gmail.com';

*    $options = [ 'first_name'=>$first_name,
*                 'last_name'=>$last_name,
*                 'email'=>$email ];
    $member = new Member($options);

    $this->assertEquals($first_name, $member->first_name);
    $this->assertEquals($last_name, $member->last_name);
    $this->assertEquals($email, $member->email);
  }
}
```
---

template: technique4_header

#### Step 2: Run the test and see the failure

```bash
➜  sample_codes git:(master) ✗ ./phpunit ./code/tests/MemberTest.php
PHPUnit 4.8.21 by Sebastian Bergmann and contributors.

F

Time: 84 ms, Memory: 11.75Mb

There were 1 failure:

*1) MemberTest::testConstructor
Failed asserting that null matches expected 'Michael'.

/Users/miccheng/projects/refactoring/sample_codes/code/tests/MemberTest.php:15
```
---

template: technique4_header

#### Step 3: Write minimal code to pass the test

```php
class Member {
  public $first_name;
  public $last_name;
  public $email;

*  public function __construct($options) {
*    $this->first_name = $options['first_name'];
*    $this->last_name = $options['last_name'];
*    $this->email = $options['email'];
*  }
}
```

We implement the constructor method

---

template: technique4_header

#### Step 4: Run and pass the test

```bash
➜  sample_codes git:(master) ✗ ./phpunit ./code/tests/MemberTest.php --color
PHPUnit 4.8.21 by Sebastian Bergmann and contributors.

.

Time: 91 ms, Memory: 11.75Mb

*OK (1 tests, 3 assertions)
```
---

template: technique4_header

#### Step 5: Refactor
--

What can we do to make this code better?

```php
public function __construct($options) {
  $this->first_name = $options['first_name'];
  $this->last_name = $options['last_name'];
  $this->email = $options['email'];
}
```
--

Do you think its cumbersome to have to set the ***field names*** explicitly?

--
```php
public function __construct($options) {
  $this->first_name = $options['first_name'];
  $this->last_name = $options['last_name'];
  $this->email = $options['email'];
* $this->address = $options['address'];
* $this->mobile = $options['mobile'];
* $this->gender = $options['gender'];
  ...
}
```

---

template: technique4_header

#### Step 5: Refactor

Let's yank out the list of fields into a class variable

```php
*private static $known_fields = [ 'first_name', 'last_name', 'email' ];

public function __construct($options) {
  foreach(self::$known_fields as $field_name) {
    if (isset($options[$field_name]))
      $this->$field_name = $options[$field_name];
  }
}
```
--

Or a clever alternative. Why would this not make sense?

```php
public function __construct($options) {
  array_walk( self::$known_fields, function($field_name) use ($options){
      $this->$field_name = $options[$field_name];
    }
  );
}
```
---
class: center, middle

# After some time...

## Rinse and repeat for all classes

---

template: technique4_header

#### The new classes: `Member`

```php
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
  
  ...
```
---

template: technique4_header

#### The new classes: `Member`

```php
  ...

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
```
---

template: technique4_header

#### The new classes: `CSVDataStore`

```php
class CSVDataStore {
  private static $csv_file;
  private $file_handle;

  public function __construct($options=array()) {
    self::$csv_file = empty($options['file'])
                        ? __DIR__ . '/members.csv'
                        : $options['file'];

    $this->file_handle = fopen(self::$csv_file, 'a+');
  }

  ...
```
---

template: technique4_header

#### The new classes: `CSVDataStore`

```php
  ...

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
```
---

template: technique4_header

#### The new classes: `ClubMembershipRegister`

```php
class ClubMembershipRegister {
  private $data_store;

  public function __construct($data_store=null) {
    $this->data_store = (empty($data_store)) ? new CSVDataStore() : $data_store;
  }

  public function all_members() {
    $all_records = $this->data_store->read_all();
    return array_map('Member::init_member', $all_records);
  }

  ...
```
---

template: technique4_header

#### The new classes: `ClubMembershipRegister`

```php
  ...

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
```
---

template: technique4_header

#### And finally... the revised `add_member` function

```php
$membership_register = new ClubMembershipRegister();

function add_member(Array $member_data) {
  global $membership_register;

  $new_member = Member::init_member($member_data);

  return $membership_register->add_member($new_member);
}

$new_member = ['Michael', 'Cheng', 'miccheng@gmail.com'];
var_dump(add_member($new_member));
```
---

template: technique4_header

#### A cleaner approach to revising the `add_member` function
 
Change the actual implementation of the system:
--

```php
$new_member = new Member([ 'first_name'=>'Michael',
                           'last_name'=>'Jordan', 
                           'email'=>'mj@nba.com' ]);

$membership = new ClubMembershipRegister();
var_dump($membership->add_member($new_member));
```

Possibly when your manager confirms the changes?
---

## Technique 4: Do not be obsessed with primitives

### What have we learnt?
--

- Use **Class Responsibilities & Collaborators (CRC)** to identify boundaries around domain entities / models
  
--

- Use **Test Driven Development (TDD)** to guide you

--

    - Ensure your code changes didn't change behavior in subsequent refactoring attempts
--

    - Ensure you didn't introduce any bugs
--

    - Tests allows you to make changes with confidence
--

- Only introduce backward incompatible **API** changes as last resort 

--

    - eg. Replace the `add_member` function with a cleaner implementation 
---

class: center, middle

# Questions?

## Twitter: @CoderKungfu

### https://github.com/CoderKungfu/refactoring_php_demo
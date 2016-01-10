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

- Software left alone will decay.
--

- A change made to the internal structure of software to make it:
--

    1.  Easier to understand.
	2.  Cheaper to make future changes.
--

- This does not change the observable behavior of the software.
--

- Improves the design of the code.
--

- Tests help ensure that we didn't change behavior.
---

# Common refactoring techniques:
--

1. Change names to communicate intent
--

2. No magic numbers / variables
--

3. One responsibility per function / class
--

4. Do not be obsessed with primitives
---

class: center, middle

# Refactoring Technique 4:

# Do not be obsessed with primitives
---

## Technique 4: Do not be obsessed with primitives

### Why?
--

- Array & Associative Array are awesome as generic data containers. 
--

- But they do not communicate intent. 
--

- Brittle - breaks easily if your API changes.
---
## Technique 4: Do not be obsessed with primitives

### Alternative
--

- Use classes / models to encapsulate behavior.
--

- Switch to compositional approach.
--

- Use objects that work together.
---

## Technique 4: Do not be obsessed with primitives

### Example: Club Membership Register v2.0
--

Your manager came to you again, rubbing his hands, he says:

> I am thinking of adding a new field to the register. I'm getting confirmation from the directors. But in any case, can you make sure your script can support an address field in the future?
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

- To do this, we prepare some **Class Responsibility & Collaborators (CRC)** cards.


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

Hmm... the `ClubMembershipRegister` class seems to know too much about how we store the data.
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

#### Let's split the storage bits into a separate class.

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

We implement the constructor method.

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

What can we do to make this code better?
--

```php
public function __construct($options) {
  $this->first_name = $options['first_name'];
  $this->last_name = $options['last_name'];
  $this->email = $options['email'];
}
```
--

Do you think its brittle to have to set these things explicitly?
---

template: technique4_header

#### Step 5: Refactor

```php
private static $known_fields = [ 'first_name', 'last_name', 'email' ];

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
<?php
require_once dirname(dirname(__FILE__)) . '/example4.php';

class CSVDataStoreTest extends PHPUnit_Framework_TestCase
{
  protected $data_store;

  public function setUp() {
    $options = ['file'=> __DIR__ . '/members.csv'];
    $this->data_store = new CSVDataStore($options);
  }

  public function tearDown() {
    file_put_contents(__DIR__ . '/members.csv', "Jim,Henson,j.henson@muppets.com\r\n");
  }

  public function testReadFile()
  {
    $result = $this->data_store->read_all();

    $this->assertEquals(1, count($result), 'Does not contain 1 record');
    $this->assertEquals(3, count($result[0]), 'Does not contain 3 fields');
    $this->assertEquals('Jim', $result[0][0], 'The first name is invalid');
  }

  public function testAppend()
  {
    $new_record = ['Jim', 'Lee', 'jlee@dccomics.com'];

    $this->assertTrue($this->data_store->append($new_record));

    $records = $this->data_store->read_all();

    $this->assertEquals(2, count($records), 'Does not contain 2 record');
    $this->assertEquals(3, count($records[1]), 'Does not contain 3 fields');
    $this->assertEquals('Lee', $records[1][1], 'The last name is invalid');
  }
}
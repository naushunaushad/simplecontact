<?php

namespace Drupal\Tests\simplecontact\Unit;

use Drupal\simplecontact\ContactsValidator;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\simplecontact\ContactsValidator
 *  @group simplecontact
 */
class SimplecontactValidatorTest extends UnitTestCase {

    /**
     *
     * @var \Drupal\simplecontact\ContactsValidator 
     */
    protected $validator;

    /**
     * {@inheritdoc}
     */
    protected function setUp() {
        $this->validator = new ContactsValidator();
    }

    /**
     * @covers \Drupal\simplecontact\ContactsValidator::validateStreetNumber
     */
    public function testvalidateStreetNumber() {
        $this->assertTrue($this->validator->validateStreetNumber(9), "Valid Street Number");
    }

    /**
     * @covers \Drupal\simplecontact\ContactsValidator::validateZip
     */
    public function testvalidateZip() {
        $this->assertTrue($this->validator->validateZip(689505), "Valid Zip code");
    }

    /**
     * @covers \Drupal\simplecontact\ContactsValidator::validatePhoneNumber
     */
    public function testvalidatePhoneNumber() {
        $this->assertTrue($this->validator->validatePhoneNumber(919744657468), "Valid Phone Number");
    }

}

<?php

use PHPUnit\Framework\TestCase;
use App\ValidateFormData;
use App\LatitudeValidator;
use App\LongitudeValidator;

class ValidateFormDataTest extends TestCase
{

    const LAT_FROM = 'latitude_from';
    const LAT_TO   = 'latitude_to';
    const LON_FROM = 'longitude_from';
    const LON_TO   = 'longitude_to';

    const FORM_FIELDS = [self::LAT_FROM, self::LAT_TO, self::LON_FROM, self::LON_TO];

    const EXAMPLE_FORM_DATA = [
        'latitude_from' => '1',
        'latitude_to' => '90',
        'longitude_from' => '2',
        'longitude_to' => '50'
    ];

    public function testInstantiating()
    {
        $formData = [];
        $validatorInstance = new ValidateFormData($formData);

        $this->assertIsObject($validatorInstance);
        $this->assertInstanceOf(ValidateFormData::class, $validatorInstance);
        $this->assertObjectHasAttribute('errors', $validatorInstance);
        $this->assertObjectHasAttribute('results', $validatorInstance);
        $this->assertObjectHasAttribute('formData', $validatorInstance);
        $this->assertObjectHasAttribute('latValues', $validatorInstance);
        $this->assertObjectHasAttribute('lonValues', $validatorInstance);

        $reflectionObject = new ReflectionClass($validatorInstance);
        $this->assertEquals($reflectionObject->getConstant('LAT'), 'latitude');
        $this->assertEquals($reflectionObject->getConstant('LON'), 'longitude');
        $this->assertEquals($reflectionObject->getConstant('REQUIRED_KEYS'),
            self::FORM_FIELDS
        );

    }

    public function testCheckFormDataMethodWithInvalidFormData() {

        $formData = [];
        $validatorInstance = new ValidateFormData($formData); // construct with empty array

        $results = $validatorInstance->getResults();
        $errors = $validatorInstance->getErrors();

        $this->assertIsArray($results);
        $this->assertCount(0, $results);

        $this->assertIsArray($errors);
        $this->assertCount(count(self::FORM_FIELDS), $errors);

        foreach (self::FORM_FIELDS as $key => $value) {
            $this->assertArrayHasKey($value, $errors);
        }

        foreach (self::FORM_FIELDS as $key => $value) {
            $this->assertEquals($errors[$value], 'Form field missing');
        }
    }

    public function testCheckFormDataMethodWithValidFormData() {

        $validatorInstance = new ValidateFormData(self::EXAMPLE_FORM_DATA);

        $errors = $validatorInstance->getErrors();
        $results = $validatorInstance->getResults();

        $this->assertIsArray($errors);
        $this->assertCount(0, $errors);


        $this->assertIsArray($results);
        $this->assertCount(count(self::FORM_FIELDS), $results);

        foreach (self::EXAMPLE_FORM_DATA as $key => $value) {
            $this->assertArrayHasKey($key, $results);
        }
    }

    public function testSortFormDataMethodWithInvalidFormData() {

        $validatorInstance = new ValidateFormData([]); // construct with empty array

        /* should not reach sortFormData(), will fill errors array and return */
        $validatorInstance->checkFormData();
        $errors = $validatorInstance->getErrors();
        $this->assertNotCount(0, $errors);
        /* */

        $latValues = $validatorInstance->getLatValues();
        $lonValues = $validatorInstance->getLonValues();

        $this->assertIsArray($latValues);
        $this->assertCount(0, $latValues);

        $this->assertIsArray($lonValues);
        $this->assertCount(0, $lonValues);
    }
    public function testSortFormDataMethod() {

        $validatorInstance = new ValidateFormData(self::EXAMPLE_FORM_DATA);
        $validatorInstance->checkFormData();

        $latValues = $validatorInstance->getLatValues();
        $lonValues = $validatorInstance->getLonValues();

        $this->assertCount(2, $latValues);
        $this->assertArrayHasKey(self::LAT_FROM, $latValues);
        $this->assertArrayHasKey(self::LAT_TO, $latValues);

        $this->assertCount(2, $lonValues);
        $this->assertArrayHasKey(self::LON_FROM, $lonValues);
        $this->assertArrayHasKey(self::LON_TO, $lonValues);

    }

    public function testValidateValuesMethodWithInvalidValues() {

        $invalidFormData = [
            'latitude_from' => '$this',
            'latitude_to' => '! is !',
            'longitude_from' => '<alert>SO</alert>',
            'longitude_to' => "INSERT INTO sentence VALUES ('wrong')"
        ];

        $validatorInstance = new ValidateFormData($invalidFormData);
        $results = $validatorInstance->getResults();
        $errors = $validatorInstance->getErrors();


        $this->assertCount(0, $results);
        $this->assertCount(4, $errors);
        foreach ($errors as $key => $value) {
            $this->assertArrayHasKey($key, $errors);
            $this->assertEquals($errors[$key], "Incorrect value");
        }
    }

    public function testValidateValuesMethodWithValidValues() {

        $validFormData = [
            'latitude_from' => '1',
            'latitude_to' => '2',
            'longitude_from' => '3',
            'longitude_to' => "4"
        ];

        $validatorInstance = new ValidateFormData($validFormData);
        $results = $validatorInstance->getResults();
        $errors = $validatorInstance->getErrors();


        $this->assertCount(4, $results);
        foreach ($results as $key => $value) {
            $this->assertArrayHasKey($key, $results);
        }
        foreach ($validFormData as $key => $value) {
            $this->assertEquals($results[$key], $validFormData[$key]);
        }

        $this->assertCount(0, $errors);

    }

    public function testLatitudeValidator() {
        $latValidator = new LatitudeValidator();

        $invalidValue = 'surely wrong, isn`t it';
        $this->assertFalse($latValidator::validate($invalidValue));
        $invalidValue = -90.00000001;
        $this->assertFalse($latValidator::validate($invalidValue));
        $invalidValue = 90.01;
        $this->assertFalse($latValidator::validate($invalidValue));

        $validValue = '1';
        $this->assertTrue($latValidator::validate($validValue));
        $validValue = '90';
        $this->assertTrue($latValidator::validate($validValue));
        $validValue = -90;
        $this->assertTrue($latValidator::validate($validValue));
        $validValue = +90.0000;
        $this->assertTrue($latValidator::validate($validValue));
        $validValue = '-0.000000';
        $this->assertTrue($latValidator::validate($validValue));
    }

    public function testLongitudeValidator() {
        $lonValidator = new LongitudeValidator();

        $invalidValue = 'must be wrong, right';
        $this->assertFalse($lonValidator::validate($invalidValue));
        $invalidValue = 'must be correct, right';
        $this->assertFalse($lonValidator::validate($invalidValue));
        $invalidValue = '-181';
        $this->assertFalse($lonValidator::validate($invalidValue));
        $invalidValue = '-180.0001';
        $this->assertFalse($lonValidator::validate($invalidValue));
        $invalidValue = -181;
        $this->assertFalse($lonValidator::validate($invalidValue));
        $invalidValue = -180.00001;
        $this->assertFalse($lonValidator::validate($invalidValue));

        $validValue = 0;
        $this->assertTrue($lonValidator::validate($validValue));
        $validValue = "0";
        $this->assertTrue($lonValidator::validate($validValue));
        $validValue = "180.0000";
        $this->assertTrue($lonValidator::validate($validValue));
        $validValue = 180.000;
        $this->assertTrue($lonValidator::validate($validValue));
        $validValue = '0';
        $this->assertTrue($lonValidator::validate($validValue));
        $validValue = 0;
        $this->assertTrue($lonValidator::validate($validValue));
    }
}
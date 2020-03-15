<?php

namespace App;


class LatitudeValidator {

    public static function validate($lat) {
        if (is_numeric($lat)) {
            return is_finite($lat) && abs($lat) <= 90;
        }
        return false;
    }
}

class LongitudeValidator {
    public static function validate($lon) {
        if (is_numeric($lon)) {
            return is_finite($lon) && abs($lon) <= 180;
        }
        return false;
    }
}

class ValidateFormData  {

    const LAT = 'latitude';
    const LON = 'longitude';

    const REQUIRED_KEYS = [
        'latitude_from',
        'latitude_to',
        'longitude_from',
        'longitude_to'
    ];

    private $errors = [];
    private $results = [];

    private $formData;

    private $latValues = [];
    private $lonValues = [];


    public function __construct($formData)
    {
        $this->formData = $formData;
    }

    public function getFormData()
    {
        return $this->formData;
    }

    public function getLatValues(): array
    {
        return $this->latValues;
    }

    public function getLonValues(): array
    {
        return $this->lonValues;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getResults(): array
    {
       $this->checkFormData();
       return $this->results;
    }

    public function checkFormData() {
        $missing = array_diff_key(array_flip(self::REQUIRED_KEYS), $this->getFormData() );

        if (count($missing) > 0) {
            foreach ($missing as $key => $value) {
                $this->errors[$key] = 'Form field missing';

            }
            return;

        } else { $this->sortFormData(); }
    }

    public function sortFormData()
    {

        foreach($this->getFormData() as $key => $value) {

            if (strpos($key, self::LAT) !== false ) {
                $this->latValues[$key] = $value;
            }
            else if (strpos($key, self::LON) !== false ) {
                $this->lonValues[$key] = $value;
            }
        }
        return $this->validateValues();

    }


    public function validateValues()
    {
        foreach ($this->getLatValues() as $key => $value) {
            $latValidateResult = LatitudeValidator::validate($value);

            if ($latValidateResult) {
                $this->results[$key] = $value;
            } else {
                $this->errors[$key] = 'Incorrect value';
            }
        }

        foreach ($this->getLonValues() as $key => $value) {
            $lonValidateResult = LongitudeValidator::validate($value);

            if ($lonValidateResult) {
                $this->results[$key] = $value;
            } else {
                $this->errors[$key] = 'Incorrect value';
            }

        }
        return $this;
    }




}
<?php

namespace App;

require "../vendor/autoload.php";

use App\ValidateFormData as Validator;
use App\DistanceCalculator as DistCalc;

$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';

if ($contentType === "application/json") {

    //Receive the RAW post data.
    $payload = trim(file_get_contents("php://input"));

    $decoded = json_decode($payload, true);
    if (!is_array($decoded)) {
        http_response_code(400);
        echo json_encode(['message' => 'Form processing error']);
        return ;
    }


    $validatedContent = new Validator($decoded);
    $validationResults = $validatedContent->getResults();

    $validationErrors = $validatedContent->getErrors();
    if ($validationErrors !== []) {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid form values']);
        return;
    }

    $calculatedDistance = DistCalc::vincentyGreatCircleDistance($validationResults);

    echo json_encode([
       'message' => $calculatedDistance,
    ]);
}
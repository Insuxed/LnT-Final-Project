<?php
require 'vendor/autoload.php';

use Google\Cloud\Firestore\FirestoreClient;

// Path to the service account JSON file downloaded from Firebase Console
$serviceAccountPath = 'form-e010c-firebase-adminsdk-xo8bg-bd83a691e3.json';

// Initialize Firestore client
$firestore = new FirestoreClient([
    'keyFilePath' => $serviceAccountPath
]);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get the POST data
    $name = $_POST["name"];
    $email = $_POST["email"];
    $phoneNumber = $_POST["phoneNumber"];
    $event = $_POST["Events"];

    // Validation
    $errorMessages = [];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessages[] = "Please input a valid email";
    }

    if (strlen($name) <= 3) {
        $errorMessages[] = "Please input more than 3 characters for Name";
    }

    if (!preg_match('/^08\d{1,12}$/', $phoneNumber)) {
        $errorMessages[] = "Please input a valid phone number (starting with 08 and less than 14 characters)";
    }

    // If there are validation errors, return them
    if (!empty($errorMessages)) {
        foreach ($errorMessages as $errorMessage) {
            echo $errorMessage . "<br>";
        }
    } else {
        // Collection name in Firestore
        $collectionName = 'registrations';

        // Reference to the Firestore collection
        $collection = $firestore->collection($collectionName);

        // Add a new document to the collection
        $newDocRef = $collection->add([
            'name' => $name,
            'email' => $email,
            'phoneNumber' => $phoneNumber,
            'event' => $event,
            'timestamp' => Firestore::timestamp()
        ]);

        // Get the document ID of the newly added document
        $docId = $newDocRef->id();

        echo "Document added with ID: $docId";
    }
} else {
    // If the request method is not POST, return an error message
    echo "Invalid request method";
}
?>

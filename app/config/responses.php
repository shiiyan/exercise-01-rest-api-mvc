<?php
use Phalcon\Http\Response;

// Create a response
$response = new Response();

// Check if the insertion was successful
if ($success) {
	// Change the HTTP status
	$response->setStatusCode(201, 'Created');

	//$product->id= $status->getModel()->id;

	
	$response->setJsonContent(
		[
			'status' => 'OK',
			'data' => $product

		], JSON_UNESCAPED_SLASHES
	);
} else {
	// Change the HTTP status
	$response->setStatusCode(409, 'Conflict');

	// Send errors to the client
	$errors = [];

	foreach($status->getMessages() as $message) {
		$errors[] = $message->getMessage();
	}

	$response->setJsonContent(
		[
			'status' => 'ERROR',
			'messages' => $errors
		]
	);
}
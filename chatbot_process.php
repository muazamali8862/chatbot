<?php
// chatbot_process.php

// Load necessary libraries
require "vendor/autoload.php"; // Ensure you have the Gemini API client installed via Composer

use GeminiAPI\Client;
use GeminiAPI\Resources\Parts\TextPart;

// Get the incoming JSON data
$data = json_decode(file_get_contents('php://input'));

// Check if the message is set
if (!isset($data->message)) {
    echo json_encode(['error' => 'No message provided']);
    exit;
}

$message = $data->message;

// Initialize Gemini API client
$geminiApiKey = 'AIzaSyBzLwUeTVRe7DHAp8pbGvCs64xg6MIQK1w'; // Replace with your actual Gemini API key
$client = new Client($geminiApiKey);

try {
    // Send the message to Gemini API
    $response = $client->GeminiPro()->generateContent(new TextPart($message));

    // Extract the response text
    $botResponse = $response->text(); // Assuming this method returns the response text

    // Send the response back to the frontend
    echo json_encode(['response' => $botResponse]);

} catch (Exception $e) {
    // Handle any errors
    echo json_encode(['error' => 'Error communicating with Gemini: ' . $e->getMessage()]);
}
?>
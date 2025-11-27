<?php
// contact.php - Simple contact form handler

// Set response header to JSON
header('Content-Type: application/json');

// Enable error reporting for debugging (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Initialize response array
$response = array(
    'success' => false,
    'message' => ''
);

// Check if form is submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Sanitize and validate input data
    $name = isset($_POST['name']) ? trim(htmlspecialchars($_POST['name'])) : '';
    $email = isset($_POST['email']) ? trim(htmlspecialchars($_POST['email'])) : '';
    $subject = isset($_POST['subject']) ? trim(htmlspecialchars($_POST['subject'])) : '';
    $message = isset($_POST['message']) ? trim(htmlspecialchars($_POST['message'])) : '';
    
    // Validation
    $errors = array();
    
    if (empty($name)) {
        $errors[] = "Name is required";
    }
    
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    if (empty($subject)) {
        $errors[] = "Subject is required";
    }
    
    if (empty($message)) {
        $errors[] = "Message is required";
    }
    
    // If no errors, process the form
    if (empty($errors)) {
        
        // Email configuration
        $to = "ashwini@example.com"; // Replace with actual email
        $email_subject = "Portfolio Contact: " . $subject;
        
        // Email body
        $email_body = "You have received a new message from your portfolio contact form.\n\n";
        $email_body .= "Name: $name\n";
        $email_body .= "Email: $email\n";
        $email_body .= "Subject: $subject\n\n";
        $email_body .= "Message:\n$message\n";
        
        // Email headers
        $headers = "From: $email\r\n";
        $headers .= "Reply-To: $email\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();
        
        // Send email
        if (mail($to, $email_subject, $email_body, $headers)) {
            
            // Save to messages.txt file as backup
            $log_entry = date('Y-m-d H:i:s') . " | $name | $email | $subject\n";
            file_put_contents('messages.txt', $log_entry, FILE_APPEND);
            
            $response['success'] = true;
            $response['message'] = "Thank you! Your message has been sent successfully.";
            
        } else {
            $response['message'] = "Sorry, there was an error sending your message. Please try again later.";
        }
        
    } else {
        // Return validation errors
        $response['message'] = implode(", ", $errors);
    }
    
} else {
    $response['message'] = "Invalid request method";
}

// Return JSON response
echo json_encode($response);
?>

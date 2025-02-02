<?php
/**
 * File: PostEmail.php
 * Purpose: Contact form handler with TOR verification
 */
session_start();
$pageTitle = 'Contact Form Submission';
require_once '../includes/header.php';
require_once '../includes/tor_challenge.php';
//require_once '../includes/force_tor_challenge.php';
// Initialize messages
$errorMessage = '';
$successMessage = '';

function handleError($message) {
    global $errorMessage;
    $errorMessage = $message;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $torChallenge = new TORChallengeHandler();
    $userIP = $_SERVER['REMOTE_ADDR'];

    // Handle TOR users
    if ($torChallenge->isTorUser($userIP)) {
        if (!isset($_POST['tor_answer'])) {
            echo '<section id="mainBody" class="fade-in">';
            echo $torChallenge->renderChallengeForm($_POST);
            echo '</section>';
            include '../includes/footer.php';
            exit;
        } 
        
        if (!$torChallenge->validateAnswer($_POST['tor_answer'])) {
            echo '<section id="mainBody" class="fade-in">';
            echo "<div class='message-container'><p class='error-message'>Incorrect answer. Please try again.</p></div>";
            echo $torChallenge->renderChallengeForm($_POST);
            echo '</section>';
            include '../includes/footer.php';
            exit;
        }
    }

    // Process form submission
    $name = htmlspecialchars($_POST["Name"] ?? '', ENT_QUOTES, 'UTF-8');
    $email = filter_var($_POST["Email"] ?? '', FILTER_SANITIZE_EMAIL);
    $message = htmlspecialchars($_POST["Message"] ?? '', ENT_QUOTES, 'UTF-8');

    // Validate inputs
    if (empty($name) || strlen($name) > 100) {
        handleError("Please provide a valid name (maximum 100 characters).");
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        handleError("Please provide a valid email address.");
    } elseif (empty($message)) {
        handleError("Please provide a message.");
    } else {
        $config = require '../includes/config.php';
        
        // Log the contact submission
        $filePath = $config['paths']['data'] . '/contact.txt';
        $dateTime = date("Y-m-d H:i:s");
        $logEntry = sprintf(
            "Date: %s, Name: %s, Email: %s, IP: %s, TOR: %s\n",
            $dateTime,
            $name,
            $email,
            $userIP,
            $torChallenge->isTorUser($userIP) ? "Yes" : "No"
        );

        file_put_contents($filePath, $logEntry, FILE_APPEND | LOCK_EX);

        // Prepare and send email
        $to = $config['email']['admin_email'];
        $subject = "New Contact Form Message";
        $emailBody = "You have received a new message:\n\n";
        $emailBody .= "Name: " . $name . "\n";
        $emailBody .= "Email: " . $email . "\n";
        $emailBody .= "Message:\n" . $message . "\n\n";
        $emailBody .= "IP Address: " . $userIP . "\n";
        $emailBody .= "TOR User: " . ($torChallenge->isTorUser($userIP) ? "Yes" : "No") . "\n";
        
        $headers = "From: " . $config['email']['noreply_email'] . "\r\n";
        $headers .= "Reply-To: " . $email . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();

        // Send main email
        if (mail($to, $subject, $emailBody, $headers)) {
            // Send confirmation to user
            $userSubject = "Thank you for contacting us";
            $userMessage = "Dear " . $name . ",\n\n";
            $userMessage .= "Thank you for your message. We have received your inquiry and will respond shortly.\n\n";
            $userMessage .= "Best regards,\n";
            $userMessage .= $config['app']['site_name'] . " Team";
            
            $userHeaders = "From: " . $config['email']['noreply_email'] . "\r\n";
            $userHeaders .= "Reply-To: " . $config['email']['support_email'] . "\r\n";
            $userHeaders .= "X-Mailer: PHP/" . phpversion();

            mail($email, $userSubject, $userMessage, $userHeaders);
            
            $successMessage = "Thank you for your message. We'll get back to you soon!";
        } else {
            handleError("Sorry, there was an error sending your message. Please try again later.");
            error_log("Failed to send email from contact form: " . $email);
        }
    }
}
?>

<section id="mainBody" class="fade-in">
    <div class="message-container">
        <?php if ($errorMessage): ?>
            <div class="error-message">
                <p><?php echo htmlspecialchars($errorMessage); ?></p>
                <p><a hrer="javascript:history.back()" class="back-link">Go Back</a></p>
            </div>
        <?php endif; ?>
        
        <?php if ($successMessage): ?>
            <div class="success-message">
                <p><?php echo htmlspecialchars($successMessage); ?></p>
                <p><a href="/" class="home-link">Return to Home</a></p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include '../includes/footer.php'; ?>

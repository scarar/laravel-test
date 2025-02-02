<?php
session_start();
require_once '../includes/tor_challenge.php';

$pageTitle = 'Contact Form Submission';
$errorMessage = '';
$successMessage = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $torChallenge = new TORChallengeHandler();

    // Force display of the challenge for all submissions
    if (!isset($_POST['tor_answer'])) {
        echo $torChallenge->renderChallengeForm();
        include '../includes/footer.php';  // Ensure footer is included
        exit;
    }

    // Validate the Tor challenge answer
    if (!$torChallenge->validateAnswer($_POST['tor_answer'])) {
        error_log("Tor challenge failed. Answer: " . htmlspecialchars($_POST['tor_answer']));
        echo "<p style='color:red;'>Incorrect answer. Please try again.</p>";
        echo $torChallenge->renderChallengeForm();
        include '../includes/footer.php';  // Include footer on failure
        exit;
    }

    // If the challenge is passed, process the form data
    $name = htmlspecialchars($_POST["Name"] ?? '', ENT_QUOTES, 'UTF-8');
    $email = filter_var($_POST["Email"] ?? '', FILTER_SANITIZE_EMAIL);
    $message = htmlspecialchars($_POST["Message"] ?? '', ENT_QUOTES, 'UTF-8');

    if (empty($name) || strlen($name) > 100) {
        $errorMessage = "Please provide a valid name (maximum 100 characters).";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = "Please provide a valid email address.";
    } elseif (empty($message)) {
        $errorMessage = "Please provide a message.";
    } else {
        $successMessage = "Thank you for your message. We'll get back to you soon!";
    }
}
?>
<section id="mainBody">
    <?php if ($errorMessage): ?>
        <p class="error"><?php echo htmlspecialchars($errorMessage); ?></p>
    <?php endif; ?>
    <?php if ($successMessage): ?>
        <p class="success"><?php echo htmlspecialchars($successMessage); ?></p>
    <?php endif; ?>
</section>
<?php include '../includes/footer.php'; ?>

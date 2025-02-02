<?php
session_start();
require_once '../includes/tor_challenge.php';

$pageTitle = 'Newsletter Signup';
$errorMessage = '';
$successMessage = '';
$submissionsFile = '../data/submissions/submissions.txt';  // Path to submissions file

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $torChallenge = new TORChallengeHandler();

    // Force display of the challenge for all submissions
    if (!isset($_POST['tor_answer'])) {
        include '../includes/header.php';  // Include header
        echo $torChallenge->renderChallengeForm();  // Render only the challenge form
        include '../includes/footer.php';  // Include footer
        exit;  // Stop further execution
    }

    // Validate the Tor challenge answer
    $userAnswer = $_POST['tor_answer'] ?? '';
    if (!$torChallenge->validateAnswer($userAnswer)) {
        include '../includes/header.php';  // Include header
        echo "<p style='color:red;'>Incorrect answer. Please try again.</p>";
        echo $torChallenge->renderChallengeForm();  // Render challenge form with error
        include '../includes/footer.php';  // Include footer
        exit;  // Stop further execution
    }

    // Check if the form data is provided
    if (empty($_POST["Name"]) || empty($_POST["Email"])) {
        include '../includes/header.php';  // Include header
        echo "<p style='color:red;'>Please complete the form before submitting.</p>";
        echo $torChallenge->renderChallengeForm();  // Re-render challenge form with error
        include '../includes/footer.php';  // Include footer
        exit;  // Stop further execution
    }

    // If the challenge is passed, process the form data
    $name = htmlspecialchars($_POST["Name"] ?? '', ENT_QUOTES, 'UTF-8');
    $email = filter_var($_POST["Email"] ?? '', FILTER_SANITIZE_EMAIL);

    if (empty($name) || strlen($name) > 100) {
        $errorMessage = "Please provide a valid name (maximum 100 characters).";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = "Please provide a valid email address.";
    } else {
        $successMessage = "You have successfully signed up for the newsletter!";

        // Append user information to submissions.txt
        $submissionData = "Date: " . date("Y-m-d H:i:s") . ", Name: $name, Email: $email\n";
        if (file_put_contents($submissionsFile, $submissionData, FILE_APPEND | LOCK_EX) === false) {
            $errorMessage = "Failed to save your submission. Please try again later.";
        } else {
            error_log("Form processed and saved for email: $email");
        }
    }
}
?>
<?php include '../includes/header.php'; ?>
<section id="mainBody">
    <?php if ($errorMessage): ?>
        <p class="error"><?php echo htmlspecialchars($errorMessage); ?></p>
    <?php endif; ?>
    <?php if ($successMessage): ?>
        <p class="success"><?php echo htmlspecialchars($successMessage); ?></p>
    <?php endif; ?>
</section>
<?php include '../includes/footer.php'; ?>

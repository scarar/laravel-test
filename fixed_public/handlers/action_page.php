<?php
session_start();
require_once '../includes/tor_challenge.php';

$torChallenge = new TORChallengeHandler();
$userIP = $_SERVER['REMOTE_ADDR'];
$errorMessage = '';
$successMessage = '';
$showChallenge = false;
$challengeError = '';

// Process form and TOR challenge
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Check for TOR challenge requirement
    if ($torChallenge->requiresChallenge($userIP)) {
        if (!isset($_POST['tor_answer'])) {
            $showChallenge = true;
        } elseif (!$torChallenge->validateAnswer($_POST['tor_answer'])) {
            $showChallenge = true;
            $challengeError = "Incorrect answer. Please try again.";
        }
    }

    // Only process form if TOR challenge is passed or not required
    if (!$showChallenge) {
        $name = htmlspecialchars($_POST["Name"] ?? '', ENT_QUOTES, 'UTF-8');
        $email = filter_var($_POST["Email"] ?? '', FILTER_SANITIZE_EMAIL);

        if (empty($name) || strlen($name) > 100) {
            $errorMessage = "Please provide a valid name (maximum 100 characters).";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errorMessage = "Please provide a valid email address.";
        } else {
            $filePath = __DIR__ . '/../data/submissions/newsletter.txt';
            $dateTime = date("Y-m-d H:i:s");
            $data = sprintf(
                "Date: %s, Name: %s, Email: %s, IP: %s, TOR: %s\n",
                $dateTime,
                $name,
                $email,
                $userIP,
                $torChallenge->isTorUser($userIP) ? "Yes" : "No"
            );

            if (file_put_contents($filePath, $data, FILE_APPEND | LOCK_EX)) {
                $successMessage = "Thank you, " . htmlspecialchars($name) . ". You've been successfully subscribed!";
            } else {
                $errorMessage = "Sorry, there was an error processing your subscription.";
            }
        }
    }
}

// Start page output
include '../includes/header.php';
?>

<section id="mainBody" class="fade-in">
    <?php if ($showChallenge): ?>
        <?php if ($challengeError): ?>
            <div class="message-container">
                <div class="error-message">
                    <p><?php echo htmlspecialchars($challengeError); ?></p>
                </div>
            </div>
        <?php endif; ?>
        <?php echo $torChallenge->renderChallengeForm($_POST); ?>
    <?php else: ?>
        <div class="message-container">
            <?php if ($errorMessage): ?>
                <div class="error-message">
                    <p><?php echo htmlspecialchars($errorMessage); ?></p>
                    <p><a href="javascript:history.back()" class="back-link">Go Back</a></p>
                </div>
            <?php endif; ?>
            
            <?php if ($successMessage): ?>
                <div class="success-message">
                    <p><?php echo htmlspecialchars($successMessage); ?></p>
                    <p><a href="/" class="home-link">Return to Home</a></p>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</section>

<?php include '../includes/footer.php'; ?>

<?php
session_start();

class TORChallengeHandler {
    // Render the Tor challenge form
    public function renderChallengeForm() {
        return <<<HTML
        <div class="tor-challenge-container">
            <h2>Tor Challenge</h2>
            <form method="post" class="tor-challenge-form">
                <p class="challenge-question">What is 2 + 2?</p>
                <input type="text" name="tor_answer" placeholder="Your answer" required>
                <input type="hidden" name="form_data" value="{$_SESSION['form_data']}">
                <button type="submit" name="verify_tor" class="submit-btn">Submit</button>
            </form>
        </div>
HTML;
    }

    // Validate the answer to the challenge
    public function validateAnswer($answer) {
        $correctAnswer = '4'; // Replace with your dynamic logic
        return $answer === $correctAnswer;
    }
}

// Check if the challenge needs to be presented
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['verify_tor'])) {
    $_SESSION['form_data'] = base64_encode(serialize($_POST)); // Save form data for later
    $torChallenge = new TORChallengeHandler();
    echo $torChallenge->renderChallengeForm();
    exit;
}

// Handle challenge verification
if (isset($_POST['verify_tor'])) {
    $torChallenge = new TORChallengeHandler();
    $torAnswer = $_POST['tor_answer'] ?? '';
    if (!$torChallenge->validateAnswer($torAnswer)) {
        echo "<p style='color:red;'>Incorrect answer. Please try again.</p>";
        echo $torChallenge->renderChallengeForm();
        exit;
    }

    // Restore form data and continue
    $_POST = unserialize(base64_decode($_POST['form_data']));
}
?>

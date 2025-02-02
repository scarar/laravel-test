<?php
class TORChallengeHandler {
//    private $cachePath = '/mnt/data/unzipped_public/public/cache/tor_exit_nodes.cache';

    // Force the display of the challenge form
    public function renderChallengeForm($data = []) {
        error_log("Rendering the Tor challenge form.");
        return <<<HTML
        <div class="tor-challenge-container">
            <h2>Tor Challenge</h2>
            <form method="post" class="tor-challenge-form">
                <p class="challenge-question">What is 2 + 2?</p>
                <input type="text" name="tor_answer" placeholder="Your answer" required>
                <button type="submit" class="submit-btn">Submit</button>
            </form>
        </div>
HTML;
    }

    // Validate the answer to the challenge
    public function validateAnswer($answer) {
        error_log("Validating answer: $answer.");
        $correctAnswer = '4';
        return $answer === $correctAnswer;
    }
}
?>

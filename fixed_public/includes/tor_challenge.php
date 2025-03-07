<?php
/**
 * File: tor_challenge.php
 * Purpose: Handles TOR detection and math CAPTCHA implementation
 * Version: Production with Testing Mode
 */
class TORChallengeHandler {
    private $session;
    private $formData;
    private const MAX_ATTEMPTS = 3;
    private const CHALLENGE_TIMEOUT = 300; // 5 minutes
    private const SESSION_KEY = 'tor_challenge_completed';
    private $debugMode = true;
    
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->initializeSession();
    }
    
    private function initializeSession() {
        if (!isset($_SESSION['tor_challenge_attempts'])) {
            $_SESSION['tor_challenge_attempts'] = 0;
        }
        
        if (!isset($_SESSION['tor_challenge_timestamp'])) {
            $_SESSION['tor_challenge_timestamp'] = time();
        }
        
        if (!isset($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = false;
        }
    }
    
    private function logDebug($message) {
        if ($this->debugMode) {
            error_log("TOR Challenge Debug: " . $message);
        }
    }
    
    public function getClientIP() {
        $headers = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];

        foreach ($headers as $header) {
            if (isset($_SERVER[$header])) {
                $ips = array_map('trim', explode(',', $_SERVER[$header]));
                foreach ($ips as $ip) {
                    if (filter_var($ip, FILTER_VALIDATE_IP,
                        FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                        return $ip;
                    }
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'];
    }
    
    public function isTorUser($ip) {
        // For testing purposes - always return true
        return true;
    }
    
    public function requiresChallenge($ip) {
        if (!isset($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = false;
        }
        
        $this->logDebug("Challenge check - TOR: Yes, Session Status: " . 
                       ($_SESSION[self::SESSION_KEY] ? 'Completed' : 'Required'));
        
        return $this->isTorUser($ip) && !$_SESSION[self::SESSION_KEY];
    }
    
    public function generateChallenge() {
        $num1 = rand(1, 20);
        $num2 = rand(1, 20);
        $operations = ['+', '-', '*'];
        $operation = $operations[array_rand($operations)];
        
        $question = "$num1 $operation $num2";
        
        switch ($operation) {
            case '+':
                $answer = $num1 + $num2;
                break;
            case '-':
                $answer = $num1 - $num2;
                break;
            case '*':
                $answer = $num1 * $num2;
                break;
            default:
                $answer = 0;
        }
        
        $_SESSION['tor_challenge'] = [
            'question' => $question,
            'answer' => $answer,
            'timestamp' => time()
        ];
        
        $this->logDebug("Generated challenge: $question = $answer");
        return $question;
    }
    
    public function validateAnswer($userAnswer) {
        $this->logDebug("Validating answer: $userAnswer");
        
        if (!isset($_SESSION['tor_challenge']['answer'])) {
            $this->logDebug("No answer in session");
            return false;
        }
        
        if (time() - $_SESSION['tor_challenge_timestamp'] > self::CHALLENGE_TIMEOUT) {
            $this->logDebug("Challenge timed out");
            $this->resetChallengeStatus();
            return false;
        }
        
        $userAnswer = (int) $userAnswer;
        $correctAnswer = (int) $_SESSION['tor_challenge']['answer'];
        
        $this->logDebug("Comparing answers - User: $userAnswer, Correct: $correctAnswer");
        
        $isCorrect = ($userAnswer === $correctAnswer);
        
        if ($isCorrect) {
            $this->logDebug("Answer correct - Processing submission");
            $this->resetChallengeStatus();
        } else {
            $_SESSION['tor_challenge_attempts']++;
            $this->logDebug("Incorrect answer - Attempt {$_SESSION['tor_challenge_attempts']}");
            
            if ($_SESSION['tor_challenge_attempts'] >= self::MAX_ATTEMPTS) {
                $this->resetChallengeStatus();
            }
        }
        
        return $isCorrect;
    }
    
    public function renderChallengeForm($formData = []) {
        $this->formData = $formData;
        $question = $this->generateChallenge();
        
        ob_start();
        ?>
        <div class="tor-challenge-container">
            <h2>Security Verification Required</h2>
            <p>You are accessing this site through TOR. Please solve this math problem to continue:</p>
            
            <?php if ($_SESSION['tor_challenge_attempts'] > 0): ?>
                <div class="challenge-attempts">
                    Attempts remaining: <?php echo self::MAX_ATTEMPTS - $_SESSION['tor_challenge_attempts']; ?>
                </div>
            <?php endif; ?>
            
            <form method="post" action="" class="tor-challenge-form">
                <label for="math-answer" class="challenge-question">
                    <?php echo htmlspecialchars($question); ?> = ?
                </label>
                
                <input type="number" 
                       id="math-answer"
                       name="tor_answer" 
                       required 
                       autocomplete="off" 
                       class="challenge-input"
                       aria-label="Enter your answer">
                
                <?php $this->renderHiddenFields(); ?>
                
                <button type="submit" class="submit-btn">Submit Answer</button>
            </form>
            
            <div class="challenge-info">
                <p>This math challenge helps prevent automated access through TOR.</p>
                <p>You have <?php echo self::CHALLENGE_TIMEOUT / 60; ?> minutes to complete this challenge.</p>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    private function renderHiddenFields() {
        foreach ($this->formData as $key => $value) {
            if ($key !== 'tor_answer' && !is_array($value)) {
                echo '<input type="hidden" name="' . 
                     htmlspecialchars($key) . 
                     '" value="' . 
                     htmlspecialchars($value) . 
                     '">';
            }
        }
    }
    
    public function resetChallengeStatus() {
        // Clear all challenge-related session data
        unset($_SESSION[self::SESSION_KEY]);
        unset($_SESSION['tor_challenge']);
        unset($_SESSION['tor_challenge_attempts']);
        unset($_SESSION['tor_challenge_timestamp']);
        
        // Reinitialize session with fresh values
        $this->initializeSession();
        
        $this->logDebug("Challenge status completely reset");
        session_write_close();
        session_start();
    }
}

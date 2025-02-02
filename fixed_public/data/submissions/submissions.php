<?php
/**
 * Submissions Handler
 * Manages form submissions and data storage
 */

class SubmissionsHandler {
    private $submissionsFile = 'submissions.txt';
    private $maxRetries = 3;
    
    /**
     * Save submission to file
     */
    public function saveSubmission($data) {
        $attempts = 0;
        $saved = false;
        
        while (!$saved && $attempts < $this->maxRetries) {
            try {
                $this->writeToFile($data);
                $saved = true;
            } catch (Exception $e) {
                $attempts++;
                if ($attempts >= $this->maxRetries) {
                    throw new Exception("Failed to save submission after {$this->maxRetries} attempts");
                }
                usleep(100000); // Wait 0.1 seconds before retrying
            }
        }
        
        return $saved;
    }
    
    /**
     * Write data to file with proper formatting
     */
    private function writeToFile($data) {
        $formattedData = $this->formatSubmissionData($data);
        
        if (file_put_contents(
            $this->submissionsFile,
            $formattedData,
            FILE_APPEND | LOCK_EX
        ) === false) {
            throw new Exception("Failed to write to submissions file");
        }
        
        return true;
    }
    
    /**
     * Format submission data for storage
     */
    private function formatSubmissionData($data) {
        $timestamp = date('Y-m-d H:i:s');
        
        return sprintf(
            "[%s] Name: %s, Email: %s, IP: %s, TOR: %s\n",
            $timestamp,
            htmlspecialchars($data['name'] ?? 'Unknown'),
            htmlspecialchars($data['email'] ?? 'Unknown'),
            htmlspecialchars($data['ip'] ?? 'Unknown'),
            $data['is_tor'] ? 'Yes' : 'No'
        );
    }
    
    /**
     * Validate submission data
     */
    public function validateSubmission($data) {
        $errors = [];
        
        // Validate name
        if (empty($data['name'])) {
            $errors[] = "Name is required";
        } elseif (strlen($data['name']) > 100) {
            $errors[] = "Name is too long (maximum 100 characters)";
        }
        
        // Validate email
        if (empty($data['email'])) {
            $errors[] = "Email is required";
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format";
        }
        
        return $errors;
    }
    
    /**
     * Clean old submissions (optional maintenance)
     */
    public function cleanOldSubmissions($daysToKeep = 30) {
        if (!file_exists($this->submissionsFile)) {
            return;
        }
        
        $lines = file($this->submissionsFile);
        $cutoffDate = strtotime("-{$daysToKeep} days");
        $newLines = [];
        
        foreach ($lines as $line) {
            if (preg_match('/^\[(.*?)\]/', $line, $matches)) {
                $submissionDate = strtotime($matches[1]);
                if ($submissionDate > $cutoffDate) {
                    $newLines[] = $line;
                }
            }
        }
        
        file_put_contents($this->submissionsFile, implode('', $newLines), LOCK_EX);
    }
}
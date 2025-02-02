<?php
/**
 * File: index.php
 * Purpose: Homepage with newsletter signup
 */
$pageTitle = 'Home';
include 'includes/header.php';
//include 'includes/force_tor_challenge.php';
?>

<section id="mainBody" class="fade-in">
    <div id="welcomeDiv">
        <h2>Welcome to my page</h2>
        <p>Hope you have a lovely stay while viewing my content; and please do let me know what you think...</p>
    </div>

    <div id="newsLetter" class="myFadeOut">
        <form action="handlers/action_page.php" method="POST">
            <input type="text" 
                   name="Name" 
                   placeholder="Enter your name" 
                   required 
                   maxlength="100">
            <br>
            <input type="email" 
                   name="Email" 
                   placeholder="Enter your email" 
                   required 
                   pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}" 
                   title="Please enter a valid email address">
            <br><br>
            <input type="submit" name="Submit" value="Submit">
        </form>
    </div>

    <div id="leftDiv">
        <a href="https://tinyurl.com/cybria-TOR">Visit Cybria.net on TOR</a>
        <p>Hello and welcome.</p>
        <p>Thank you for stopping by.</p>
        <p>Have a wonderful day!</p>
    </div>

    <div id="rightDiv">
        <a href="#">
            <img src="/assets/images/Harbour1.jpg" alt="Just Do It Logo">
        </a>
        <p>Experience our unique location and services.</p>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

<?php include 'security_headers.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Just Do It'; ?></title>
    <link rel="stylesheet" type="text/css" href="/assets/css/styles.css">
</head>
<body>
    <div class="container">
        <header>
            <div id="logo" class="fade-in">
                <a href="/"><img src="/assets/images/JustDoIt.png" alt="Just Do It Logo"></a>
            </div>
            <ul class="nav_menu fade-in">
                <li><a href="/">Home</a></li>
                <li><a href="/about.html">About Me</a></li>
                <li><a href="/contact.php">Contact Us</a></li>
            </ul>
            <div class="clear"></div>
        </header>
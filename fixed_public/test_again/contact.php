<?php
/**
 * File: contact.php
 * Purpose: Contact form page
 */
$pageTitle = 'Contact Us';
include 'includes/header.php';
?>

<section id="mainBody" class="fade-in">
    <div id="welcomeDiv">
        <form id="contact_form" name="contact_form" method="post" action="handlers/PostEmail.php">
            <fieldset>
                <legend>Contact Us</legend>

                <table>
                    <tr>
                        <td><label for="Name">Name</label></td>
                        <td>
                            <input type="text" 
                                   name="Name" 
                                   id="Name" 
                                   placeholder="Enter Name" 
                                   required 
                                   aria-label="Enter your name" 
                                   maxlength="100"
                                   autofocus />
                        </td>
                    </tr>

                    <tr>
                        <td><label for="Email">Email</label></td>
                        <td>
                            <input type="email" 
                                   name="Email" 
                                   id="Email" 
                                   placeholder="Email Address" 
                                   required 
                                   pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}" 
                                   title="Please enter a valid email address" 
                                   maxlength="254" 
                                   spellcheck="false" 
                                   aria-label="Enter your email address" />
                        </td>
                    </tr>

                    <tr>
                        <td><label for="Message">Message</label></td>
                        <td>
                            <textarea name="Message" 
                                     id="Message" 
                                     cols="45" 
                                     rows="5" 
                                     placeholder="Enter Message" 
                                     required 
                                     aria-label="Enter your message"></textarea>
                        </td>
                    </tr>

                    <tr>
                        <td></td>
                        <td>
                            <input type="reset" name="Reset" id="Reset" value="Reset" />
                            <input type="submit" name="Send" id="Send" value="Submit" />
                        </td>
                    </tr>
                </table>
            </fieldset>
        </form>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
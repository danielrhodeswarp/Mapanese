<?php


//PRINT_R($_POST);

$emailBody = '';

if(!empty($_POST['email']) and !empty($_POST['message']))
{
	// In case any of our lines are larger than 70 characters, we should use wordwrap()
$message = wordwrap($_POST['message'], 60);

	$emailBody = <<<TEXT
Sender email: {$_POST['email']}
Contact reason: {$_POST['reason']}
Message: {$message}
TEXT;
	
	mail('yourEmailAddress@example.com', 'Mapanese contact form', $emailBody);
	
}



header('Location: /mapanese_contact_thanks.html');
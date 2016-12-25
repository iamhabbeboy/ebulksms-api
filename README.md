EbulkSMS Easy Library
use this class to connect easily to EbulkSMS platform to your project

<?php
require_once "./app/EbulkSmsApi.php";

$sender = "Testing";
$msgtxt = "Hello, World; Am using EbulkSMS library developed by @iamhabbeboy";
$phone = "07087322191";
$sms = new EbulkSmsApi( 'GENERATED_KEY', 'YOUR_EMAIL');
$sms -> send( $sender, $msgtxt, PHONE_NUMBER); 

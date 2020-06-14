<?php

namespace App\Traits;

use App\Mail\NotificationMail;
use App\Mail\PasswordResetMail;
use App\Mail\AccountVerificationMail;
use Illuminate\Support\Facades\Mail;

trait EmailSenderTrait
{
    /**
     * Prepares an email with verification token to be sent.
     *
     * @param string $email
     * @param string $topic
     * @param string $message
     * 
     * @return boolean
     */
    public function sendEmail($email, $topic, $message=null)
    {
        // Email parameters
        if (!$email || !$topic) {
            return false;
        }

        // Get an ovsettings value
        $mock_mail = config('ovsettings.mock_mail');


        // Test mail, comment out to send real mail
        if ($mock_mail) {
            return self::sendMockMail('Email- '.$email.' Topic- '.$topic.' Message-'.$message);
        }

        // Send an email to user containing the appropriate email subject
        switch ($topic) {
            case config('constants.mail.verification'):

                Mail::to($email)->send(new AccountVerificationMail($message));
                break;

            case config('constants.mail.reset'):
                
                Mail::to($email)->send(new PasswordResetMail($message));
                break;

            case config('constants.mail.info'):

                Mail::to($email)->send(new NotificationMail($message));
                break;

            default:
                self::sendMockMail('Email- '.$email.' Topic- '.$topic.' Message-'.$message);
                break;
        }

        // Return success
        return true;
    }

    /**
     * Send a mock email to a specified address
     * 
     * @param string $message
     * @return boolean
     */
    public static function sendMockMail($message)
    {
        try {

            // Check if folder exists
            if (!file_exists('./../public/storage/mockmail')) {
                mkdir('./../public/storage/mockmail', 0777, true);
            }

            // Write a message to text file
            $filename = "./../public/storage/mockmail/mockmail.txt";
            file_put_contents($filename, "\n".$message, FILE_APPEND | LOCK_EX);

        } catch (\Throwable $th) {
            return false;
        }

        return true;
    }
}
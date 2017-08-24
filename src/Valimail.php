<?php

namespace Valimail;

use Valimail\ValidationMethod\SMTP_Validation;

class Valimail
{
    public $email;

    public function __construct($email)
    {
        $this->email = $email;
    }

    public function validateMXRecord()
    {
        list($name, $domain) = explode('@', $this->email);
        if (!checkdnsrr($domain, 'MX')) {
            return false;
        } else {
            return true;
        }

    }

    public function validateSMTP()
    {
        $smtp = new SMTP_Validation();
        $smtp->SMTPValidate($this->email);
    }


}



<?php

namespace Valimail;

use Valimail\ValidationMethod\SMTP_Validation;
use Valimail\ValidationMethod\Syntax_Vlidation;

class Valimail
{
    public $email;

    public function __construct($email)
    {
        $this->email = $email;
    }

    public function validateAllMethod()
    {
        $validation["validateSyntax"] = $this->validateSyntax();
        $validation["validateMXRecord"] = $this->validateMXRecord();
        $validation["validateSMTP"] = $this->validateSMTP();

        $countTrue = 0;
        foreach ($validation as $method) {
            if ($method) {
                $countTrue++;
            }
        }
        $status = count($validation) == $countTrue;

        return array($status, $validation);
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
        return $smtp->SMTPValidate($this->email);
    }

    public function validateSyntax()
    {
        $syntax = new Syntax_Vlidation();
        return $syntax->syntaxValidate($this->email);
    }


}



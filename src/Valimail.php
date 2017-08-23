<?php

namespace Valimail;

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
      //  $this->email;
    }


}



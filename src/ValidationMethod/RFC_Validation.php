<?php
 

namespace Valimail\ValidationMethod;
use Valimail\ValidationMethod\Standard\RFC822;

class RFC_Validation extends RFC822
{


    public function validateRfcStandard($email){

        return $this->is_valid_email_address($email);

    }
}
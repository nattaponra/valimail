<?php

namespace Valimail\ValidationMethod;


class Syntax_Vlidation
{
    public function syntaxValidate($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        } else {
            return true;

        }
    }

}
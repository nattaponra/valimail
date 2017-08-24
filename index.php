<?php
require_once __DIR__.'/app/autoload.php';
use Valimail\Valimail;




$validate = new Valimail("nattapon_arty@hotmail.com");

$validate->validateSMTP();




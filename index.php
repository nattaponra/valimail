<?php
require_once __DIR__.'/app/autoload.php';
use Valimail\Valimail;

$email = $argv[1];

echo $email;
$validate = new Valimail($email);

$result = $validate->validateAllMethod();
print_r($result);




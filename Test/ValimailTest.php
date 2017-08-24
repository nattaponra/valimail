<?php
require_once __DIR__ . '/../app/autoload.php';
use PHPUnit\Framework\TestCase;
use Valimail\Valimail;


class ValimailTest extends TestCase
{

    function testValidEmail()
    {
        /** Example Data */
        $validEmail = array(
            "nattapon.rakthong@gmail.com",
            "nattapon@3dsinteractive.com",
            "nattapon_arty@hotmail.com"
        );

        $countGoodEmail = 0;
        foreach ($validEmail as $email) {
            $validate = new  Valimail($email);
            if ($validate->validateMXRecord()) {
                $countGoodEmail++;
            }

        }
        $this->assertEquals($countGoodEmail, count($validEmail));
    }

    function testInvalidEmail()
    {
        /** Example Data */
        $invalidEmail = array(
            "xxx@com",
            "asdasdas@ffffasdad.com",
            "fungger@reasdq1sdasdas11.com",
            "fuunggu@kaisong.xco"
        );

        $countBedEmail = 0;
        foreach ($invalidEmail as $email) {
            $validate = new  Valimail($email);
            if (!$validate->validateMXRecord()) {
                $countBedEmail++;
            }

        }
        $this->assertEquals($countBedEmail, count($invalidEmail));
    }

    public function testSMTPEmail(){

        /** Example Data */
        $validEmail = array(
            "nattapon.rakthong@gmail.com",
            "nattapon@3dsinteractive.com",
            "nattapon_arty@hotmail.com"
        );
        $validate = new  Valimail("nattapon.rakthong@gmail.com");

       echo $validate->validateSMTP();
    }


}
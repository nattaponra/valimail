<?php
require_once __DIR__ . '/../app/autoload.php';
use PHPUnit\Framework\TestCase;
use Valimail\Valimail;


class ValimailTest extends TestCase
{

    private $validEmail;
    private $invalidEmail;

    public function __construct($name = null, array $data = [], $dataName = '')
    {

        parent::__construct($name, $data, $dataName);

        $this->validEmail = array(
            "nattapon.rakthong@gmail.com",
            "nattapon@3dsinteractive.com",
            "nattapon_arty@hotmail.com"
        );

        $this->invalidEmail = array(
            "xxx@gmail.com",
            "asdasdas@ffffasdad.com",
            "fungger@reasdq1sdasdas11.com",
            "fuunggu@kaisong.xco"
        );
    }

    //testTrackPageView_givenAnonymouseIsTrue_expectSeeLeadInDatabase
    function testVidateMXrecord_giveInputIsValidEmailArray_expectEmailsAreGood()
    {

        $countGoodEmail = 0;
        foreach ($this->validEmail as $email) {
            $validate = new  Valimail($email);
            if ($validate->validateMXRecord()) {
                $countGoodEmail++;
            }

        }
        $this->assertEquals($countGoodEmail, count($this->validEmail));
    }

    function testVidateMXrecord_giveInputIsInvalidEmailArray_expectAllEmailsAreBad()
    {


        $countBadEmail = 0;
        foreach ($this->invalidEmail as $email) {
            $validate = new  Valimail($email);
            if (!$validate->validateMXRecord()) {
                $countBadEmail++;
            }

        }
        $this->assertEquals($countBadEmail, count($this->invalidEmail));
    }

//    public function testVidateSMTP_giveInputIsValidEmailArray_expectAllEmailsFoundSMTPServer()
//    {
//        $countFoundSMTPServer = 0;
//        foreach ($this->validEmail as $email) {
//            $validate = new  Valimail($email);
//
//            if (!$validate->validateSMTP()) {
//                $countFoundSMTPServer++;
//            }
//
//        }
//        $this->assertEquals($countFoundSMTPServer, count($this->validEmail));
//
//    }


    public function testVidateEmailSyntax_giveInputIsValidEmailArray_expectAllCorrectEmailSyntax()
    {
        $countCorrectSyntax = 0;
        foreach ($this->validEmail as $email) {
            $validate = new  Valimail($email);

            if ($validate->validateSyntax()) {
                $countCorrectSyntax++;
            }

        }

        $this->assertEquals($countCorrectSyntax, count($this->validEmail));


    }


    public function testAllMethodValidate_giveInputIsInvalidEmailArray_expectAllEmailsAreBadEmail()
    {

        $countBadEmail = 0;
        foreach ($this->invalidEmail as $email) {
            $validate = new  Valimail($email);
            list($status, $result) = $validate->validateAllMethod();
            if (!$status) {
                $countBadEmail++;
            }

        }

        $this->assertEquals($countBadEmail, count($this->invalidEmail));

    }

}
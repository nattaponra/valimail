<?php


namespace Valimail\ValidationMethod\Standard;


class SMTP_Command
{
    private $host;
    private $port;
    private $sock;
    private $timeout;

    function __construct($host, $port)
    {
        $this->host = $host;
        $this->port = $port;
        $this->timeout = 10;
    }


    public function setTimeOut($second)
    {
        $this->timeout = $second;
    }

    public function SMTPConnect()
    {
        if ($this->sock = @fsockopen($this->host, $this->port, $errno, $errstr, (float)$this->timeout)) {
            stream_set_timeout($this->sock, $this->timeout);
            $reply = fread($this->sock, 2082);
            return $this->getResponseCode($reply);
        } else {


            return false;
        }
    }

    private function execCommand($msg)
    {
        fwrite($this->sock, $msg . "\r\n");
        if (strtoupper($msg) == "QUIT") {

            return fclose($this->sock);
        } else {
            $reply = fread($this->sock, 2082);
            return $this->getResponseCode($reply);
        }

    }


    private function getResponseCode($reply)
    {
        $smtpResponse = new SMTP_Response();
        if ($reply) {
            preg_match('/^([0-9]{3}) /ims', $reply, $matches);
            $code = isset($matches[1]) ? $matches[1] : '';
            $smtpResponse->responseCode = $code;
            $smtpResponse->replyMessages = $reply;
        }

        return $smtpResponse;
    }

    public function HELO($domain)
    {

        $msg = "HELO " . $domain;
        return $this->execCommand($msg);
    }


    public function MAIL_FROM($email)
    {


        $msg = "MAIL FROM: <" . $email . ">";
        return $this->execCommand($msg);

    }


    public function RCPT_TO($email)
    {
        $msg = "RCPT TO: <" . $email . ">";
        return $this->execCommand($msg);
    }

    public function QUIT()
    {
        $msg = "quit";
        return $this->execCommand($msg);

    }


}
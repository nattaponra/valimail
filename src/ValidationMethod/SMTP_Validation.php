<?php

namespace Valimail\ValidationMethod;


class SMTP_Validation
{


    private function getDescriptionStatusCode($code)
    {
        $description = array(
            "200" => "(nonstandard success response, see rfc876)",
            "211" => "System status, or system help reply",
            "214" => "Help message",
            "220" => "<domain> Service ready",
            "221" => "<domain> Service closing transmission channel",
            "250" => "Requested mail action okay, completed",
            "251" => "User not local; will forward to <forward-path>",
            "252" => "Cannot VRFY user, but will accept message and attempt delivery",
            "354" => "Start mail input; end with <CRLF>.<CRLF>",
            "421" => "<domain> Service not available, closing transmission channel",
            "450" => "Requested mail action not taken: mailbox unavailable",
            "451" => "Requested action aborted: local error in processing",
            "452" => "Requested action not taken: insufficient system storage",
            "500" => "Syntax error, command unrecognised",
            "501" => "Syntax error in parameters or arguments",
            "502" => "Command not implemented",
            "503" => "Bad sequence of commands",
            "504" => "Command parameter not implemented",
            "521" => "<domain> does not accept mail (see rfc1846)",
            "530" => "Access denied (???a Sendmailism)",
            "550" => "Requested action not taken: mailbox unavailable",
            "551" => "User not local; please try <forward-path>",
            "552" => "Requested mail action aborted: exceeded storage allocation",
            "553" => "Requested action not taken: mailbox name not allowed",
            "554" => "Transaction failed"
        );

        return isset($description[$code]) ? $description[$code] : "";
    }


    private function explodeEmail($email)
    {
        list($name, $domain) = explode('@', $email);
        return array($name, $domain);
    }

    public function getMXRecord($domain)
    {
        $mxweights = array();
        $hosts = array();
        getmxrr($domain, $hosts, $mxweights);
        $mxs = array_combine($hosts, $mxweights);
        if (count($mxs) != 0) {
            asort($mxs, SORT_NUMERIC);
        }
        return $mxs;
    }

    public function getResponseCode($reply)
    {
        preg_match('/^([0-9]{3}) /ims', $reply, $matches);
        $code = isset($matches[1]) ? $matches[1] : '';
        return $code;
    }

    public function SMTPValidate($email)
    {
        $max_conn_time = 30;
        $sock = '';
        $port = 25;
        $max_read_time = 5;
        $messages = "";
        $result = false;
        list($name, $domain) = $this->explodeEmail($email);

        /** retrieve SMTP Server by server domain */
        $mxs = $this->getMXRecord($domain);
        if (count($mxs) == 0) {
            return false;
        }
        $mxs[$domain] = 100;
        $timeout = $max_conn_time / count($mxs);

        /** Try to connect SMTP server */

        while (list($host) = each($mxs)) {

            #connect to SMTP server

            if ($sock = @fsockopen($host, $port, $errno, $errstr, (float)$timeout)) {

                stream_set_timeout($sock, $max_read_time);

                break;
            }
        }

        # did we get a TCP socket

        if ($sock) {

            $reply = fread($sock, 2082);
            if ($code = $this->getResponseCode($reply) != '220') {
                return array("status" => $result, "messages" => $this->getDescriptionStatusCode($code));
            }


            /** initiate smtp conversation */
            $msg = "HELO " . $domain;
            fwrite($sock, $msg . "\r\n");
            $reply = fread($sock, 2082);
            if ($code = $this->getResponseCode($reply) != '250') {
                return array("status" => $result, "messages" => $this->getDescriptionStatusCode($code));
            }


            /** tell of sender */
            $msg = "MAIL FROM: <" . $name . '@' . $domain . ">";
            fwrite($sock, $msg . "\r\n");
            $reply = fread($sock, 2082);
            if ($code = $this->getResponseCode($reply) != '250') {
                return array("status" => $result, "messages" => $this->getDescriptionStatusCode($code));
            }

            /**   ask of recepient */
            $msg = "RCPT TO: <" . $name . '@' . $domain . ">";
            fwrite($sock, $msg . "\r\n");
            $reply = fread($sock, 2082);
            $code = $this->getResponseCode($reply);



            /** quit smtp connection */
            $msg = "QUIT";
            fwrite($sock, $msg . "\r\n");
            # close socket
            fclose($sock);


            if ($code == '250' || $code == '451' || $code == '452') {

                return array("status" => true, "messages" => $this->getDescriptionStatusCode($code));
            }


        }

        return array("status" => false, "messages" => "Can't connect SMTP server.");;

    }

}
<?php

namespace Valimail\ValidationMethod;


class SMTP_Validation
{
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

    function SMTPValidate($email)
    {
        $max_conn_time = 30;
        $sock = '';
        $port = 25;
        $max_read_time = 5;

        $result = false;
        list($name, $domain) = $this->explodeEmail($email);

        /** retrieve SMTP Server by server domain */
        $mxs = $this->getMXRecord($domain);
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

            preg_match('/^([0-9]{3}) /ims', $reply, $matches);

            $code = isset($matches[1]) ? $matches[1] : '';


            if ($code != '220') {

                # MTA gave an error...
                return $result;

            }


            # initiate smtp conversation
            $msg = "HELO " . $domain;

            fwrite($sock, $msg . "\r\n");

            $reply = fread($sock, 2082);


            # tell of sender

            $msg = "MAIL FROM: <" . $name . '@' . $domain . ">";


            fwrite($sock, $msg . "\r\n");

            $reply = fread($sock, 2082);


            #ask of recepient

            $msg = "RCPT TO: <" . $name . '@' . $domain . ">";


            fwrite($sock, $msg . "\r\n");

            $reply = fread($sock, 2082);


            #get code and msg from response

            preg_match('/^([0-9]{3}) /ims', $reply, $matches);


            $code = isset($matches[1]) ? $matches[1] : '';


            if ($code == '250') {

                #you received 250 so the email address was accepted

                $result = true;

            } elseif ($code == '451' || $code == '452') {

                #you received 451 so the email address was greylisted

                #or some temporary error occured on the MTA) - so assume is ok


                $result = true;

            } else {

                $result = false;

            }


            #quit smtp connection

            $msg = "quit";
            fwrite($sock, $msg . "\r\n");
            # close socket
            fclose($sock);

        }

        return $result;

    }

}
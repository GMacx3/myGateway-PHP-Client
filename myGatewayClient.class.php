<?php

/*
 * The MIT License
 *
 * Copyright 2015 Lukas Kämmerling.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace lkdev\myGateway;

/**
 * A class that provieds Functions for the work with myGateway
 *
 * @author Lukas Kämmerling
 * @version 1.0.0
 * @todo provide Passiv Auth functions
 */
class myGatewayClient {

    /**
     * Contains the API URL.
     */
    CONST Api_Url = "https://mein-mittelsmann.de/sms/api.web.php";

    /**
     * API CURL Request 
     */
    CONST API_REQUEST_CURL = "curl";

    /**
     * FOR File_get_contents Request
     */
    CONST API_REQUEST_FGC = "fgc";

    /**
     * Contains the Projekt Token
     * @var string
     */
    private $Client_Token;

    /**
     * Contains the Name of the Project
     * @var string
     */
    private $Project_Name;

    /**
     * Name of the Projekt Holder
     * @var string
     */
    private $Project_Holder;

    /**
     * Method for the Request
     * @var string
     */
    private $Api_Request_Typ;

    /**
     *  Contains the Return from one Request
     * @var stdclass
     */
    private $Api_Return;

    public function __construct() {
        $this->Client_Token = '';
        $this->Project_Name = '';
        $this->Project_Holder = '';
        $this->Api_Request_Typ = '';
    }

    public function __destruct() {
        $this->Client_Token = null;
        $this->Project_Name = null;
        $this->Project_Holder = null;
        $this->Api_Request_Typ = null;
    }

    /**
     * Returns the Request Typ ( CURL OR file_get_contents)
     * @return string
     */
    public function getApiRequestTyp() {
        return $this->Api_Request_Typ;
    }

    /**
     * Sets The Request Typ
     * @param string $RequestTyp
     * @return boolean
     * @throws myGatewayClientException
     */
    public function setApiRequestTyp($RequestTyp) {
        if (is_string($RequestTyp) && empty($RequestTyp) === false) {
            $this->Api_Request_Typ = $RequestTyp;
            return true;
        } else {
            throw new myGatewayClientException("Project Holder can't be empty and musst be an string");
        }
    }

    /**
     * Return the Project Holder
     * @return string
     */
    public function getProjectHolder() {
        return $this->Project_Holder;
    }

    /**
     * Set the new Project Holder, Overwrites the old in the storage
     * @param string $ProjectHolder
     * @return boolean
     * @throws myGatewayClientException
     */
    public function setProjectHolder($ProjectHolder) {
        if (is_string($ProjectHolder) && empty($ProjectHolder) === false) {
            $this->Project_Holder = $ProjectHolder;
            return true;
        } else {
            throw new myGatewayClientException("Project Holder can't be empty and musst be an string");
        }
    }

    /**
     * Returns the Project Name
     * @return string
     */
    public function getProjectName() {
        return $this->Project_Name;
    }

    /**
     * Set the new Project Name, Overwrites the old in the storage
     * @param string $ProjectName
     * @return boolean
     * @throws myGatewayClientException
     */
    public function setProjectName($ProjectName) {
        if (is_string($ProjectName) && empty($ProjectName) === false) {
            $this->Project_Name = $ProjectName;
            return true;
        } else {
            throw new myGatewayClientException("Project Name can't be empty and musst be an string");
        }
    }

    /**
     * Return the Client Token
     * @return string
     */
    public function getClientToken() {
        return $this->Client_Token;
    }

    /**
     * Set the new Client Token. Overwrites the old
     * @param string $ClientToken
     * @return boolean
     * @throws myGatewayClientException
     */
    public function setClientToken($ClientToken) {
        if (is_string($ClientToken) && empty($ClientToken) === false) {
            $this->Client_Token = $ClientToken;
            return true;
        } else {
            throw new myGatewayClientException("Client Token can't be empty and musst be an string");
        }
    }

    /**
     * Sends a new SMS to the API
     * @param string $SmsRecipient
     * @param string $SmsSendText
     * @param int $SmsExpiration
     * @throws myGatewayClientException
     * @return boolean
     */
    public function sendSMS($SmsRecipient, $SmsSendText, $SmsExpiration = 172800) {
        $data = array("action" => "add", "sms_recipient" => $SmsRecipient, "sms_sendtext" => $SmsSendText, "sms_expiration" => (time() +$SmsExpiration));
        try {
            $this->sendRequest($data);
            return $this->checkApiReturn();
        } catch (myGatewayClientException $ex) {
            throw new myGatewayClientException($ex->getMessage());
        }
        return false;
    }

    /**
     * Returns the Status of the SMS - Request with SMS Id
     * @param int $SmsId
     * @return boolean|stdClass
     * @throws myGatewayClientException
     */
    public function checkSMSwithId($SmsId) {
        $data = array("action" => "check", "type" => "id", "sms_id" => $SmsId);
        try {
            $this->sendRequest($data);
            return $this->checkApiReturn();
        } catch (myGatewayClientException $ex) {
            throw new $ex;
        }
        return false;
    }

    /**
     * Return the Status of the SMS - Request with SMS Recipent
     * @param string $SmsRecipent
     * @return boolean|stdClass
     * @throws myGatewayClientException
     */
    public function checkSMSwithRecipent($SmsRecipent) {
        $data = array("action" => "check", "type" => "recipient", "sms_recipient" => $SmsRecipent);
        try {
            $this->sendRequest($data);
            return $this->checkApiReturn();
        } catch (myGatewayClientException $ex) {
            throw new $ex;
        }
        return false;
    }

    /**
     * Delete one SMS
     * @param int $SmsId
     * @return boolean|string
     * @throws type
     */
    public function deleteSMS($SmsId) {
        $data = array("action" => "remove", "sms_id" => $SmsId);
        try {
            $this->sendRequest($data);
            return $this->checkApiReturn();
        } catch (myGatewayClientException $ex) {
            throw new $ex;
        }
        return false;
    }
    /**
     * Checks the POST Answer
     * @return \stdClass
     * @throws myGatewayClientException
     */
    public function checkPostAnswer(){
        if($_SERVER['HTTP_USER_AGENT'] === "SMS Gateway Client (".$this->getProjectHolder().";".$this->getProjectName()."')"){
            $return = new \stdClass();
            $return->status = $_POST['status'];
            $return->id = $_POST['id'];
            return $return;
        } else {
       
            throw new myGatewayClientException("Post Answer Auth(Passiv) wasn't successfuly");
        }
    }

    /**
     * Check the Return from the API
     * @return stdClass
     * @throws myGatewayClientException
     */
    private function checkApiReturn() {
        if ($this->Api_Return->success === 0) {
            throw new myGatewayClientException($this->Api_Return->error->message . " " . $this->Api_Return->error->description);
        } else if ($this->Api_Return->success === 1) {
            return $this->Api_Return->data;
        }
    }

    /**
     * Sends The Real Request
     * @param array $data
     * @param int $RequestTyp
     * @return boolean
     * @throws myGatewayClientException
     */
    private function sendRequest(array $data) {
        if ($this->getApiRequestTyp() == "") {
            $this->setApiRequestTyp(self::API_REQUEST_FGC);
        }
        try {
            $this->checkIfCUrlIsLoaded($this->getApiRequestTyp());
            switch ($this->getApiRequestTyp()) {
                case self::API_REQUEST_CURL:
                    $this->sendCurlRequest($data);
                    break;
                case self::API_REQUEST_FGC:
                    $this->sendFGCRequest($data);
                    break;
            }
            return true;
        } catch (myGatewayClientException $ex) {
            throw new $ex;
        }
    }

    /**
     * Sends a Request with CURL
     * @param array $data
     * @return boolean
     */
    private function sendCurlRequest(array $data) {
        $data["client_token"] = $this->getClientToken();
        $data["client_alias"] = $this->getProjectName();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::Api_Url);
        curl_setopt($ch, CURLOPT_POST, count($data));
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $this->Api_Return = json_decode(curl_exec($ch));
        curl_close($ch);
        return true;
    }

    /**
     * Sends a Request with file_get_contents
     * @param array $data
     * @return boolean
     */
    private function sendFGCRequest(array $data) {
        $data["client_token"] = $this->getClientToken();
        $data["client_alias"] = $this->getProjectName();
        $fgc_data = http_build_query($data);
        $options = array("http" =>
            array(
                "method" => "POST",
                'header' => "Content-type: application/x-www-form-urlencoded\r\n"
                . "Content-Length: " . strlen($fgc_data) . "\r\n",
                "content" => $fgc_data,
            )
        );
        $context = stream_context_create($options);
        $this->Api_Return = json_decode(file_get_contents(self::Api_Url, false, $context));
        return true;
    }

    /**
     * Check if CURL is loaded
     * @param int $RequestTyp
     * @return boolean
     * @throws myGatewayClientException
     */
    private function checkIfCUrlIsLoaded($RequestTyp) {
        if ($RequestTyp == self::API_REQUEST_CURL) {
            if (!extension_loaded("curl")) {
                throw new myGatewayClientException("CURL Extension isn't loading.");
            }
        }
        return true;
    }

}

class myGatewayClientException extends \Exception {
    
}

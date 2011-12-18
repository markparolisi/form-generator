<?php

class CurlSend {

    var $channel;
    var $postData = array();
    var $apiURL = 'http://api.compassknowledge.com/lead-import/lead-form-submission?';
    var $message = 'Messages';

    function __construct($postData = null) {

        //Return error message if no POST data provided.
        if (!$postData)
            return 'No Post Data';

        //Setup cURL
        $this->setOptions();

        //Add additional fields to the POST data.
        $postData['timestamp'] = time();
        $postData['ip_address'] = $_SERVER['REMOTE_ADDR'];
        $this->postData = $postData;
    }

    private function setOptions() {
        $this->channel = curl_init();
        curl_setopt($this->channel, CURLOPT_HEADER, true);
        curl_setopt($this->channel, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($this->channel, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->channel, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->channel, CURLOPT_COOKIEJAR, 'cookie.txt');
        curl_setopt($this->channel, CURLOPT_COOKIEFILE, 'cookie.txt');
    }

    public function writeLog() {
        $postData = $this->postData;
        $today = date('Ymd');
        $log_file_name = SUCCESS_LOG_DIR . $today . ".tsv";
        if (isset($postData['error']))
            $log_file_name = ERROR_LOG_DIR . $today . ".tsv";

        //Loop throught the POST array and set it for CSV file
        $content = '';
        foreach ($postData as $key => $value) {
            $content .= $key . " = " . $value . "\t";
        }
        $content .= "\n";

        //Check if log file exists to append, or create new
        if (is_file($log_file_name)) {
            $log_file = fopen($log_file_name, 'a+');
            if (!$log_file)
                $this->message = 'Cannot open existing file ' . $log_file_name;
        }else {
            $log_file = fopen($log_file_name, 'w');
            if (!$log_file)
                $this->message = 'Cannot open new file ' . $log_file_name;
        }
        return fwrite($log_file, $content);
    }

    public function sendToApi() {
        $request = $this->makeRequest('get', $this->apiURL, $this->postData);
        return $request;
    }

    function makeRequest($method, $url, $postData) {
        if (strtolower($method) == 'post') {
            if (is_array($postData)) {
                $postData = implode('&', $postData);
            }
            curl_setopt($this->channel, CURLOPT_POST, false);
            curl_setopt($this->channel, CURLOPT_POSTFIELDS, $postData);
            curl_setopt($this->channel, CURLOPT_URL, $url);
        } else {
            curl_setopt($this->channel, CURLOPT_URL, $url . http_build_query($postData));
        }
        return curl_exec($this->channel);
    }

}

// End CurlSend Class

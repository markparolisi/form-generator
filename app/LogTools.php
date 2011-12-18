<?php

require('form_init.php');

class LogTools {

    var $logDate;
    var $logFile;
    var $formattedLeads = array();
    var $message = 'Messages';

    function __construct($theDate = null, $theAction = null) {
        $this->logDate = $theDate;
        $this->setLogFile($theAction);
        $this->parseFields();
    }

    public function setLogFile($theAction) {
        $logDir = SUCCESS_LOG_DIR;
        if ($theAction == 'errors') {
            $logDir = ERROR_LOG_DIR;
        }
        $this->logFile = $logDir . $this->logDate . '.tsv';
        return $this->logFile;
    }

    public function parseFields() {
        $handle = fopen($this->logFile, 'r');
        if (!$handle) {
            return false;
        } else {
            while (($data = fgetcsv($handle, 1000, "\t")) !== FALSE) {
                $tmpArray = array();
                foreach ($data as $val) {
                    $strArray = explode(" = ", $val);
                    if (isset($strArray[0]) && !empty($strArray[0]))
                        $valKey = trim($strArray[0]);
                    if (isset($strArray[1]))
                        $valValue = trim($strArray[1]);
                    $tmpArray[$valKey] = $valValue;
                }
                array_push($this->formattedLeads, $tmpArray);
            }
        }
        fclose($handle);
        return $this->formattedLeads;
    }

    public function sendRequest() {
        foreach ($this->formattedLeads as $formattedLead) {
            $curl = new CurlSend($formattedLead);
            echo $curl->sendToAPI();
        }
    }

    public function showErrors() {
        foreach ($this->formattedLeads as $formattedLead) {
            print_r($formattedLead);
        }
    }

}

// End LogTools Class
echo '<pre>';
$theDate = (empty($_REQUEST['date'])) ? date('Ymd') : $_REQUEST['date'];
$theAction = (empty($_REQUEST['action'])) ? null : $_REQUEST['action'];
$tools = new LogTools($theDate, $theAction);
switch ($theAction) {
    case 'import':
        $tools->sendRequest();
        break;
    case 'errors':
        $tools->showErrors();
        break;
    default:
        break;
}



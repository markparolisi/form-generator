<?php

class FormInputs {

    public $errors = array();
    public $inputs = '';
    public $formValidation = array();

    public function __construct($iniArray = null) {
        $this->setInputs($iniArray);
        $this->inputs = $this->convertToUTF8($this->inputs);
    }

    public function setInputs($iniArray=null) {
        if (count($iniArray) > 1) {
            $this->inputs .= "<fieldset>\n";
            $stage_counter = 1;
            foreach ($iniArray as $key => $value) { //loop through all of the parent arrays of our ini
                if (!empty($value['stage'])) {
                    if ($value['stage'] > $stage_counter) {
                        $stage_counter++;
                        $this->inputs .= "</fieldset>\n<fieldset>\n";
                    }
                }
                if (!empty($value['type'])) {

                    if ($value['type'] == 'text') {
                        $this->createTextInput($value);
                    }
                    if ($value['type'] == 'textarea') {
                        $this->createTextarea($value);
                    }
                    if ($value['type'] == 'select') {
                        $this->createSelect($value);
                    }
                    if ($value['type'] == 'radio') {
                        $this->createRadio($value);
                    }
                    if ($value['type'] == 'checkbox') {
                        $this->createCheckbox($value);
                    }
                    if ($value['type'] == 'submit') {
                        $this->createSubmit($value);
                    }
                    if ($value['type'] == 'button') {
                        $this->createButton($value);
                    }
                    if ($value['type'] == 'label') {
                        $this->createLabel($value);
                    }
                    if ($value['type'] == 'hidden') {
                        $this->createHiddenInput($value);
                    }
                    if (!empty($value['validation'])) {
                        $this->formValidation[$value['name']] = $value['validation'];
                    }
                }
            }
            $this->inputs .= '<input type="hidden" name="stage" id="stage" value="' . $stage_counter . '"/>';
            $this->inputs .= '</fieldset>' . "\n";
        } else {
            $this->inputs = false;
        }
    }

    public function checkInputErrors($required, $accepted, $input_array) {
        foreach ($required as $key => $value) {
            if (!array_key_exists($value, $input_array)) {
                $this->errors[] = $value . ' is required for' . $key;
            }
        }
        foreach ($input_array as $key => $value) {
            if (!in_array($key, $accepted)) {
                $this->errors[] = $key . ' of '. $value .' not valid for this type';
            } else {
                if (in_array($key, $required)) {
                    if (empty($value)) {
                        $this->errors[] = $key . ' has no value';
                    }
                }
            }
        }
    }

    public function checkNullInputs($input_array) {
        $clean_array = array();
        $check_vals = array("name", "id", "class", "value", "rows", "cols", "displayname", "for");
        foreach ($check_vals as $check_val) {
            $input_array[$check_val] = (!empty($input_array[$check_val])) ? $input_array[$check_val] : '';
        }
        return $input_array;
    }

    public function createTextInput($input_array) {
        $accepted = array('type', 'name', 'id', 'value', 'class', 'stage', 'validation');
        $required = array('type', 'name', 'id');
        $this->checkInputErrors($required, $accepted, $input_array);
        $input_array = $this->checkNullInputs($input_array);
        $this->inputs .= '<input type="text" name="' . $input_array["name"] . '" id="' . $input_array["id"] . '" title="' . $input_array["value"] . '" value="' . $input_array["value"] . '" class="' . $input_array["class"] . '" />' . "\n";
    }

    public function createTextarea($input_array) {
        $accepted = array('type', 'name', 'id', 'value', 'class', 'stage', 'rows', 'cols', 'validation');
        $required = array('type', 'name', 'id');
        $this->checkInputErrors($required, $accepted, $input_array);
        $input_array = $this->checkNullInputs($input_array);
        $this->inputs.= '<textarea name="' . $input_array["name"] . '" id="' . $input_array["id"] . '" class="' . $input_array["class"] . ' "title="' . $input_array["value"] . '" cols="' . $input_array["cols"] . '" rows="' . $input_array["rows"] . '" >' . $input_array["value"] . '</textarea>          ';
    }

    public function createSelect($input_array) {
        $accepted = array('type', 'name', 'id', 'value', 'class', 'stage', 'rows', 'cols', 'optvalue', 'optdisplayname', 'title', 'validation');
        $required = array('type', 'name', 'id', 'optvalue', 'optdisplayname');
        $this->checkInputErrors($required, $accepted, $input_array);
        $name = (!empty($input_array['name'])) ? $input_array['name'] : '';
        $id = (!empty($input_array['id'])) ? $input_array['id'] : '';
        $value = (!empty($input_array['value'])) ? $input_array['value'] : '';
        $class = (!empty($input_array['class'])) ? $input_array['class'] : '';
        $title = (!empty($input_array['title'])) ? $input_array['title'] : '';
        $this->inputs .='<select name="' . $name . '" id="' . $id . '" "title="' . $title . '" class="' . $class . '" >' . "\n";
        if (!empty($input_array['optdisplayname'])) {
            $optdisplayname = $input_array['optdisplayname'];
            foreach ($input_array['optvalue'] as $key => $input_array) {
                $this->inputs .= '<option value="' . $input_array . '">' . $optdisplayname[$key] . '</option> ' . "\n";
            }
        }
        $this->inputs .= '</select>' . "\n";
    }

    public function createRadio($input_array) {
        $accepted = array('type', 'name', 'id', 'value', 'class', 'stage', 'displayname', 'validation');
        $required = array('type', 'name', 'id', 'value');
        $this->checkInputErrors($required, $accepted, $input_array);
        $input_array = $this->checkNullInputs($input_array);
        $this->inputs .= '<input type="radio" value="' . $input_array["value"] . '" id="' . $input_array["id"] . '"  name="' . $input_array["name"] . '" class="' . $input_array["class"] . '" />' . $input_array["displayname"];
    }

    public function createCheckbox($input_array) {
        $accepted = array('type', 'name', 'id', 'value', 'class', 'stage', 'displayname', 'title', 'validation');
        $required = array('type', 'name', 'id', 'value');
        $this->checkInputErrors($required, $accepted, $input_array);
        $input_array = $this->checkNullInputs($input_array);
        $this->inputs .= '<input type="checkbox" value="' . $input_array["value"] . '" id="' . $input_array["id"] . '" title="' . $input_array["value"] . '" name="' . $input_array["name"] . '" class="' . $input_array["class"] . '" />' . $input_array["displayname"] . "\n";
    }

    public function createSubmit($input_array) {
        $accepted = array('type', 'name', 'id', 'value', 'class', 'stage', 'validation');
        $required = array('type', 'name', 'id');
        $this->checkInputErrors($required, $accepted, $input_array);
        $input_array = $this->checkNullInputs($input_array);
        $this->inputs.= '<input type="submit" name="' . $input_array["name"] . '" id="' . $input_array["id"] . '" class="' . $input_array["class"] . '" value="' . $input_array["value"] . '" />' . "\n";
    }

    public function createButton($input_array) {
        $accepted = array('type', 'name', 'id', 'value', 'class', 'stage', 'validation');
        $required = array('type', 'name', 'id');
        $this->checkInputErrors($required, $accepted, $input_array);
        $input_array = $this->checkNullInputs($input_array);
        $this->inputs.= '<input type="button" name="' . $input_array["name"] . '" id="' . $input_array["id"] . '" class="' . $input_array["class"] . '" value="' . $input_array["value"] . '" />';
    }

    public function createHiddenInput($input_array) {
        $accepted = array('type', 'name', 'id', 'value', 'class', 'stage', 'validation');
        $required = array('type', 'name', 'id');
        $this->checkInputErrors($required, $accepted, $input_array);
        $input_array = $this->checkNullInputs($input_array);
        $this->inputs .= '<input type="hidden" name="' . $input_array["name"] . '" id="' . $input_array["id"] . '" value="' . $input_array["value"] . '" class="' . $input_array["class"] . '" />' . "\n";
    }

    public function createLabel($input_array) {
        $accepted = array('type', 'for', 'id', 'value', 'class', 'stage');
        $required = array('type', 'value');
        $this->checkInputErrors($required, $accepted, $input_array);
        $input_array = $this->checkNullInputs($input_array);
        $this->inputs.= '<label for="' . $input_array["for"] . '" id="' . $input_array["id"] . '" class="' . $input_array["class"] . '" >' . $input_array["value"] . '</label>' . "\n";
    }

    public function convertToUTF8($inputs) {
        if (mb_detect_encoding($inputs, "UTF-8, ISO-8859-1, GBK") != "UTF-8") {
            return mb_convert_encoding($inputs, "HTML-ENTITIES", "UTF-8");
        } else {
            return $inputs;
        }
    }

    public function getFormInputs() { //send back our input string
        if (!empty($this->errors)) {
            $errorString = implode(", ", $this->errors);
            $fullInput = $errorString . $this->inputs;
        } else {
            $fullInput = $this->inputs;
        }
        return $fullInput;
    }

}

//  End FormInputs Class

<?php
class DbpResponse {
    public $data;
    public $result;
    public $state;
    public $message;
    public $title;
    public function __construct($_data,$_result=true,$_state=1,$_message='',$_title=''){
        $this->data = $_data;
        $this->result = $_result;
        $this->state = $_state;
        $this->message = $_message;
        $this->title = $_title;
    }
}
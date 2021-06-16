<?php
class PdoResponse{
    public $data;
    public $result;
    public $message;
    public $title;
    public function __construct($_result=true,$_message='',$_title=''){
        $this->result = $_result;
        $this->message = $_message;
        $this->title = $_title;
    }
}
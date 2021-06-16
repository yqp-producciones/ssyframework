<?php
class MyTable extends Base
{
    public function __construct(){
        $this->onInit();
    }
    public function selects($data=null){
        try {
            $result;
            switch ($data['op']) {
                case 'sel-fill': 
                    $this->query("select * from my_table");
                    $result = $this->resultset();
                    break;
                case 'sel-like':
                    $this->query("select * from my_table where my_table.column like :search");
                    $this->bind(':search','%'.(isset($data['search']) ? str_replace(' ','%',$data['search'])  : '').'%');
                    $result = $this->resultset();
                    break;
                default: return $this->invalid_option();
            }
        } catch (\Exception $ex) { $result = new DbpResponse([],false,0,$ex->getMessage(),'Error interno de sistema'); }
        return $result;
    }

    public function inserts($data){
        $result;
        try {
            switch ($this->getValue($data,'op','ins-default')) {
                case 'ins-default':
                    $this->query("insert into my_table (column) values (:column_value)");
                    $this->bind(':column_value',$this->getValue($data,'column_value',null));
                    $a = $this->execute();
                    if($a->result){ $result = new DbpResponse([],true,1,'Nuevo elemento registrado conrrectamente.','Registro exitoso'); } 
                    else { $result = new DbpResponse([],false,0,$a->message,$a->title);}
                break;
                default: return $this->invalid_option();
            }
        } catch (\Exception $ex) { $result = new DbpResponse([],false,0,$ex->getMessage(),'Error al registrar elemento'); }
        return $result;
    }

    public function updates($data){
        $result;
        try {
            switch ($this->getValue($data,'op','udp-default')) {
                case 'udp-default':
                    $this->query("update my_table set column_value=:column_value where id =:id");
                    $this->bind(':id',$this->getValue($data,'id',null));
                    $this->bind(':column_value',$this->getValue($data,'column_value',null));
                    $a = $this->execute();
                    if($a->result){ $result = new DbpResponse([],true,1,'Datos  guardados correctamente.','Datos modificados'); } 
                    else { $result = new DbpResponse([],false,0,$a->message,$a->title);}
                break;
                default: return $this->invalid_option();
            }
        } catch (\Exception $ex) { $result = new DbpResponse([],false,0,$ex->getMessage(),'Error al guardar elemento'); }
        return $result;
    }

    public function deletes($data){
        $result;
        try {
            switch ((isset($data['op']) ? $data['op'] :'del-default')) {
                case 'del-default':
                    $this->query("delete from my_table where id= :id");
                    $this->bind(':id',$this->getValue($data,'id',null));
                    $a = $this->execute();
                    if($a->result){ $result = new DbpResponse([],true,1,'dato eliminado del sistema.','elemento eliminado'); } 
                    else { $result = new DbpResponse([],false,0,$a->message,$a->title);}
                break;
                default: return $this->invalid_option();
            }
        } catch (\Exception $ex) { return $this->response($ex->getMessage(),0);} 
        return $result;
    }
}
?>
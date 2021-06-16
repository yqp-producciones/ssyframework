<?php 
class Base
{
    public $pdo;
    public $stmt;
    public $error;

    public function onInit(){
        $opcion = array(
            PDO::ATTR_PERSISTENT=>true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        );
        try
        {
            $this->pdo = new PDO("mysql:host=".BDaccess["Host"].";port=3306;dbname=".BDaccess["DataBase"].";charset=utf8",BDaccess["User"],BDaccess["Password"]);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch(PDOException $ex){
            $this->error = $ex->getMessage();
        }
    }
    //prepara el comando a ejecutar
    public function query($sql){
        $this->stmt = $this->pdo->prepare($sql);
    }

    //crea parametros
    public function bind($parameters,$values,$tipes=null){
        if(is_null($tipes)){
            switch(true){
                case is_int($values):
                    $tipo = PDO::PARAM_INT;
                break;
                case is_bool($values):
                    $tipo = PDO::PARAM_BOOL;
                break;
                case is_null($values):
                    $tipo = PDO::PARAM_NULL;
                break;
                default:
                $tipo = PDO::PARAM_STR;
            }
        }
        $this->stmt->bindvalue($parameters,$values,$tipo);
    }

    //ejecuta una instruccion sql
    public function execute(){
        $result = new PdoResponse();
        try
        {
            $result = new PdoResponse($this->stmt->execute(),'','');
        }
        catch(PDOException $ex)
        {
            $result = new PdoResponse(false,$ex->getMessage(),'Error al ejecutar comando');
            $this->error = $ex->getMessage();
        }
        return $result;
    }

    //retorna la lista obtenida de la consulta
    public function resultset(){
        $result = new DbpResponse([]);
        try {
            $r = $this->Execute();
            if($r->result){
                $result = new DbpResponse($this->stmt->fetchALL(),true,1,$r->message,$r->title);
            } else {
                $result = new DbpResponse([],false,0,$r->message,$r->title);
            }
        } catch (\Exception $ex) {
            $result = new DbpResponse([],false,0,$ex->getMessage(),'Error al obtener lista');
        }
        return $result;
    }

    //devuelve la cantidad de filas
    public function RowsCount(){
        return $this->stmt->rowCount();
    }
    public function response($data=[],$result=true,$state = 1,$message='',$title='Información'){
        return ['state'=>$state,'result'=>$state,'message'=>$message,'title'=>$title,'data'=>$data];
    }

    public function invalid_option(){
        return  new DbpResponse([],false,0,'Opcion inválida, por favor comuníquese con el desarrollador del software para mayor información','Error al obtener lista');
      
    }

    public function toRow($array= null){
        $r = null;
        foreach ($array as $row) {
           $r = $row;break;
        }
        return $r;
    }

    public function isData($result){
        if($result != null){
            if(count($result) > 0){return true;}
            else{return false;}
        }else{return false;}
    }
    public function model($model){
       /* $clase =null;
        if (!class_exists($model,false)) {
            $mi_clase = new $model();
        }*/
        require_once DirApp.DirModels.$model.php;
        return new $model();
    }

    public function getQueryx($querys=[],$rubro= 1,$nombre=''){
        $r = null;
        foreach($querys as $query){
            if($query['rubro_id'] == $rubro && $query['nombre'] == $nombre){
                $r = $query;
                break;
            }
        }
        return $r;
    }

    public function getQuery($rubro = 0,$nombre = '', $entidad=''){
        $this->query("select * from query where entidad =:entidad and nombre =:nombre and rubro_id=:rubro_id");
        $this->bind(':nombre',$nombre);
        $this->bind(':rubro_id',$rubro);
        $this->bind(':entidad',$entidad);
        return $this->toRow($this->resultset());
    }

    /*devuelve el valor de un array por palabra clave */
    public function getValue($data,$colname,$default= null){
        $result = $default;
            if(isset($data[$colname])){
                if(strtolower($data[$colname]) != 'null'){
                    $result =$data[$colname];
                }
            }
        return $result;
    }
    public function getCorrelativo($entidad = null){
        $result = $this->response('error',0);
        if($entidad != null){
            try {
                $x = $this->toRow($this->model('Correlativo')->selects(['op'=>'sel-entidad','entidad'=>$entidad]));
                if($x != null){
                    return $this->response($x,1);
                } else {
                    $this->response('No se pudo encontrar correlativo o no está registrado',0);
                }
            } catch (\Exception $ex) { $this->response($ex->getmessage(),0); }
        }
        return $result;
    }

    /* genera un codigo atravez del correlativo y el prefijo */
    public function generateCode($correlativo = 0,$pref='COD',$length = 8){
        $result = $pref.str_pad($correlativo,($length - strlen($pref)),'0',STR_PAD_LEFT);
        return $result;
    }

    /* actualiza el correlavito de la entidad y incrementa en 1 su posicion */
    public function setCorrelativo($correlativo = []){
        $result = false;
        try {
            if($correlativo != null){
                if(count($correlativo) > 0){
                    $this->model('Correlativo')->updates(['op'=>'udp-contador','contador'=>($correlativo['contador'] + 1),'id'=>$correlativo['id']]);
                    $result = true;
                }
            }
        } catch (\Exception $ex) {
            
        }
        
        return $result;
    }

   
}
?>
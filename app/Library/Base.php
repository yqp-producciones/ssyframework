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
            $this->pdo = new PDO("mysql:host=".BDaccess["Host"].";dbname=".BDaccess["DataBase"].";charset=utf8",BDaccess["User"],BDaccess["Password"]);
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

    //ejecuta la funcion
    public function execute(){
        $result;
        try
        {
           $result = $this->stmt->execute();
        }
        catch(PDOException $ex)
        {
            $result = false;
            $this->error = $ex->getMessage();
        }
        return $result;
    }
    //retorna un array de valores
    public function resultset(){
        $this->Execute();
        return $this->stmt->fetchALL();
    }
    public function model($model){
        require_once DirApp.DirModels.$model.php;
        return new $model();
    }
}
?>
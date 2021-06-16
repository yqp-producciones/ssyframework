<?php 
class Controller
{
    public $current=[];
    public function model($model){
        require_once DirApp.DirModels.$model.php;
        return new $model();
    }
    public function view($view, $data=[]){
        setCurrentData($data);
       // echo DirApp.DirViews.$view.php;
        if(file_exists(DirApp.DirViews.$view.'/'.str_replace('/','.',$view).php)){
            require_once DirApp.DirViews.$view.'/'.str_replace('/','.',$view).php;
            system::includeJS($view.'/'.str_replace('/','.',$view));
        }
        else
        {
            echo  DirApp.DirViews.$view.'/'.str_replace('/','.',$view).'<br>';
           die(' no existe vista ');
        }
        
    }
    public function response($data=[],$result=true,$state = 1,$message='',$title='Información'){
        return ['state'=>$state,'result'=>$state,'message'=>$message,'title'=>$title,'data'=>$data];
    }
    public function json($array){
        return json_encode($array);
    }
    public function jsonp($array){
        echo json_encode($array);
    }
    public function toRow($array= null){
        $r = null;
        foreach ($array as $row) {
           $r = $row;break;
        }
        return $r;
    }
    public function redirectTo($ruta){
        print("<script> document.location.href='".SiteUrl.$ruta."';</script>");
    }
    public function redirect(){
        $ruta ="";
        if($this->is_session_company()){
            $ruta ="company/index";
        }else if($this->is_session_branch()){
            $ruta ="branch/index";
        } else{
            $ruta ="company/index";
        }
        print("<script> document.location.href='".SiteUrl.$ruta."';</script>");
    }
    /* determina las sessiones de usuario o multiusuario */
    public function is_session(){
        if(isset($_SESSION[SsName])){
           return true;
        }
        else{ return false;}
    }
    public function is_session_company(){
        if(isset($_SESSION[csn])){
           return true;
        }
        else{ return false;}
    }
    /* verifica la sesion de la filiar */
    public function is_session_branch(){
        if(isset($_SESSION[ssn])){
           return true;
        }
        else{ return false;}
    }
    public function go_admin(){
        $this->redirectTo('admin/index');
    }


    public function go_branch(){
        $this->redirectTo('branch/index');
    }

    public function session_admin(){
        if(isset($_SESSION[SSP])){
           return true;
        }
        else{ return false;}
    }

    public function session_client(){
        if(isset($_SESSION[SSC])){
           return true;
        }
        else{ return false;}
    }

    public function getCurrentMenu($nivel,$menu,$submenu){
        return ['nivel'=>$nivel,'menu'=>$menu,'submenu'=>$submenu];
    }

    public function getAPI($method, $url, $data){
        $curl = curl_init();
        switch ($method){
           case "POST":
              curl_setopt($curl, CURLOPT_POST, 1);
              if ($data)
                 curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
              break;
           case "PUT":
              curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
              if ($data)
                 curl_setopt($curl, CURLOPT_POSTFIELDS, $data);			 					
              break;
           default:
              if ($data)
                 $url = sprintf("%s?%s", $url, http_build_query($data));
        }
        // OPTIONS:
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
           'Pk:'.PK,
           'Sk:'.SK,
           'Content-Type: application/json',
        ));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        // EXECUTE:
        $result = curl_exec($curl);
        if(!$result){die("Connection Failure");}
        curl_close($curl);
        return $result;
    }
    public function getApiKey($string = 'public'){
        $a = getallheaders();
        $b = [];
        if($string == 'public'){
            $b = explode('&%&',$a['Pk']);
        } else {
            $b = explode('&%&',$a['Sk']);
        }
        $e =[];
        
        for ($i=0; $i < count($b); $i++) {
            array_push($e,system::decrypt($b[$i],system::cryptkey()));
        }
        return $e;
    }
    public function getPost(){
        return json_decode(file_get_contents('php://input'), true);
    }

    /*funcion especifica para controlar sesion de apis */
    public function isAccessApi(){
        $key = $this->getApiKey('public');
        if(isset($key)){
            if(isset($key[0])){
                if(isset($key[1])){
                    $user = trim($key[0]);
                    $pass = system::decrypt(trim($key[1]),system::cryptkey());
                    $empresa = $this->model('Empresa')->selects(['op'=>'checkout','user'=>$user,'pass'=>$pass]);
                    if($empresa != null){
                        if(count($empresa) > 0){
                            return true;
                        } else { return false; }
                    } else { return false; }
                } else { return false; }
            } else { return false; }
        } else { return false; }
    }

    /*devuelve el valor de un array por palabra clave */
    public function getValue($data,$colname,$default){
        $result = $default;
            if(isset($data[$colname])){
                if(strtolower($data[$colname]) != 'null'){
                    $result =$data[$colname];
                }
            }
        return $result;
    }
}
?>
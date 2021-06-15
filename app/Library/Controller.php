<?php 
class Controller
{
    public $current=[];
    public function model($model){
        require_once DirApp.DirModels.$model.php;
        return new $model();
    }
    public function view($view, $data=[]){
       // echo DirApp.DirViews.$view.php;
        if(file_exists(DirApp.DirViews.$view.'/'.str_replace('/','.',$view).php)){
            require_once DirApp.DirViews.$view.'/'.str_replace('/','.',$view).php;
            system::includeJS($view.'/'.str_replace('/','.',$view));
        }
        else
        {
            echo  DirApp.DirViews.$view.'/'.str_replace('/','.',$view);
            die('no existe vista');
        }
        
    }
    public function response($message='',$type = 1){
        return json_encode(['result'=>$type,'message'=>$message]);
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
    public function redirect($ruta){
        print("<script> document.location.href='".SiteUrl.$ruta."';</script>");
    }

    /* determina las sessiones de usuario o multiusuario */
    public function session($param='US'){
        $param = strtoupper($param);
        if(isset($_SESSION[$param])){
           return true;
        }
        else{ return false;}
    }
    
    public function admin(){
        if(isset($_SESSION['isadmin'])){
           return true;
        }
        else{ return false;}
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
        if(!$result){return 'error';}
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
}
?>
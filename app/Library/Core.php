<?php 
    /* archivo principal de la aplicacion  */
    class Core
    {
        protected $controller = "IndexController"; //default
        protected $method = "index"; //default
        protected $parameters = []; //default

        public function __construct(){
            $url = $this->getUrl();

            /* CONFIGURACION DE CONTROLADOR */
            //verficar si existe el controlador o el archivo del controlador
            if(file_exists(DirApp.DirControllers.ucwords($url[0]).'Controller'.php)){
                $this->controller = ucwords($url[0]).'Controller';
                unset($url[0]);
                
            }
            require_once DirApp.DirControllers.$this->controller.php;
            $this->controller = new $this->controller;
            
            /* CONFIGURACION DE METODO */
            //verficar si existe metodo
            if(isset($url[1])){
                if(method_exists($this->controller,$url[1])){
                    $this->method = $url[1];
                    unset($url[1]);
                }
            }

            /* CONFIGURACION DE PARAMETROS */
            $this->parameters= $url ? array_values($url):[];
            
            /* INVOCAR LA FUNCION */
            call_user_func_array([$this->controller,$this->method],$this->parameters);
        }

        //devuelve un array ordenado de la url
        public function getUrl(){
            if(isset($_GET['url'])){
                $url = rtrim($_GET['url'],'/');
                $url = filter_var($url,FILTER_SANITIZE_URL);
                $url = explode('/',$url);
                return $url;
            }
        }
    }
?>
<?php
use Peru\Sunat\RucFactory;
use Peru\Jne\DniFactory;
class ConsultaController extends Controller
{
    
    public function __construct(){ }
    
    public function ruc($ruc=''){
        if($ruc != ''){
            /*opcion1*/
           /*$result = json_decode($this->getAPI('GET','https://api.sunat.cloud/ruc/'.$ruc,false),true);
            if($result !=''){
                $retorno =[
                'razon_social'=>$result['razon_social'],
                'domicilio_fiscal'=>$result['domicilio_fiscal'],
                'estado'=>$result['contribuyente_estado']
                ];
                echo json_encode(['result'=>1,'message'=>$retorno],true);
            }else{
                echo json_encode(['result'=>0,'message'=>'Número de documento no existe o es inválido']);
            }
            */
            /*opcion2*/
            /*
            $result = json_decode($this->getAPI('GET','https://dniruc.apisperu.com/api/v1/ruc/'.$ruc.'?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6InlvcmRpY2l0bzIwMTRAZ21haWwuY29tIn0.x7Cz4LfNnDoRPz-dYc4cCvQJdSVlryehagpZ5Ai6c4Q',false),true);
            if($result != null){
                $retorno =[
                'razon_social'=>$result['razonSocial'],
                'domicilio_fiscal'=>$result['direccion'],
                'estado'=>$result['estado']
                ];
                echo json_encode(['result'=>1,'message'=>$retorno],true);
            }else{
                echo json_encode(['result'=>0,'message'=>'Número de documento no existe o es inválido']);
            }*/
            
            /*opcion 3*/
            
            require 'vendor/autoload.php';
            $factory = new RucFactory();
            $cs = $factory->create();
            $company = (array)$cs->get($ruc);
            if ($company != null) {
                $retorno = [
                    'razon_social'=>$company['razonSocial'],
                    'domicilio_fiscal'=>$company['direccion'],
                    'estado'=>$company['estado']
                    ];
                    echo json_encode(['result'=>1,'message'=>$retorno],true);
            }else{
                /*verifica reniec*/
                $factory = new DniFactory();
                $cs = $factory->create();
                $person = (array)$cs->get($ruc);
                if($person != null){
                    $retorno = [
                        'razon_social'=>$person['nombres'].' '.$person['apellidoPaterno'].' '.$person['apellidoMaterno'],
                        'domicilio_fiscal'=>'-',
                        'estado'=>''
                        ];
                        echo json_encode(['result'=>1,'message'=>$retorno],true);
                } else {
                    echo json_encode(['result'=>0,'message'=>'Número de documento no existe o es inválido']);
                }
            }
        } else {
            echo json_encode(['result'=>0,'message'=>'Tienes que ingresar el numero de ruc']);
        }   
    }
    
    public function dni($dni=''){
        require 'vendor/autoload.php';
        $factory = new DniFactory();
        $cs = $factory->create();
        $person = (array)$cs->get($dni);
        if($person != null){
            $retorno = [
                'razon_social'=>$person['nombres'].' '.$person['apellidoPaterno'].' '.$person['apellidoMaterno'],
                'domicilio_fiscal'=>'-',
                'estado'=>''
                ];
                echo json_encode(['result'=>1,'message'=>$retorno],true);
        }
    }
    
    public function tipocambio($mes ='',$anio=''){
        include('html_dom/simple_html_dom.php');
        $result = $this->getAPI('GET','http://www.sunat.gob.pe/cl-at-ittipcam/tcS01Alias?mes='.$mes.'&anho='.$anio,false);
        //$html = file_get_html('http://www.sunat.gob.pe/cl-at-ittipcam/tcS01Alias?mes='.$mes.'&anho='.$anio);
        
        try {
            $r =  str_get_html($result);
            $ctable=1;
            $coleccion = [];
            $cabeceras = [];
            $items =[];
            foreach($r->find('table') as $row) {
                /*seleccionar tabla de lista de tipos*/
                if($ctable == 2){
                   $count = 0;
                    foreach($row->find('tr') as $tr) {
                        /*generar la cabecera*/
                        if($count == 0){
                            $ccabecera = 1;
                            foreach($tr->find('td') as $td) {
                                if($ccabecera <= 3){
                                    array_push($cabeceras,$td->innertext);
                                }
                                $ccabecera++;
                            }
                        }
                        /*generar el cuerpo*/
                        else{
                            $citems = 0;
                            $climit = 0;
                            $group = [];
                            foreach($tr->find('td') as $td) {
                                if($climit <= 2){
                                    array_push($group, str_replace('</strong>','',str_replace('<strong>','',trim($td->innertext))));
                                }
                                
                                if($climit == 2){
                                    array_push($items,$group);
                                    $climit = 0;
                                    $group = [];
                                }else{
                                    $climit++;
                                }
                                $citems++;
                            }
                        }
                        $count++;
                    } 
                    /*generar array bidimencional*/
                    foreach($items as $item){
                        $a = [
                            'dia'=>str_replace('</strong>','',str_replace('<strong>','',trim($item[0]))),
                            'compra'=>trim($item[1]),
                            'venta'=>trim($item[2]),
                        ];
                        array_push($coleccion,$a);
                    }
                }
                $ctable++;
            }
            sort($coleccion);
            if(count($coleccion) > 0){
                echo json_encode(['result'=>1,'message'=>$coleccion],JSON_UNESCAPED_UNICODE);
            }else{
                echo json_encode(['result'=>0,'message'=>'No existe Información que mostrar. Puede Consultar otro Periodo.'],JSON_UNESCAPED_UNICODE);
            }
            
        } catch (Exception $e) {
            echo json_encode(['result'=>0,'message'=>'Error al realizar consulta, vuelva a intentarlo.']);
        }
    }
}

<?php
//const SiteUrl ="https://knetsistemas.com/";
//const ApiUrl = "https://clientes.shirlasoft.store/";
const SiteUrl ="http://localhost/knetsistemas/";
const ApiUrl = "http://localhost/admin/";

const PK = "lZiPnp2TnZ6Pl4ud&%&joSSgoJwmo2BkWdn&%&mp+MlpON";
const SK = "lZiPnp2TnZ6Pl4ud&%&joSSgoJwmo2BkWdn&%&nY+NnI+e";
const Key = 7;
const SiteName = "Universal";

/* usa los scripts y css comprimidos ejemplo.min.js */
const UseCompress = false;
/* default index empresa */
const DeInEm = "empresa/index";

//rutas de carpetas de la aplicacion
const DirBack = "../";
const DirApp = DirBack."app/";
const DirControllers = "Controllers/";
const DirHelpers = "Helpers/";
const DirModels = "Models/";
const DirViews = "Views/";
const DirLibrary = "Library/";
const DirImg= "img/";
const DirLayout= DirApp."Views/Layout/";
//extensiones
const php = ".php";
const html = ".html";

//error_reporting(0);
//ini_set('display_errors', 0);
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

?>
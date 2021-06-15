<?php
/* funcion que devuelve el link generado para ver un producto */
function product_link($producto = null){
    if($producto != null){
       return SiteUrl.'index/producto?d='.str_replace(' ','-',str_replace(' - ','-',$producto['descripcion'])).'&p='.system::encrypt($producto['cdproducto'],system::cryptkey()).'&e='.$producto['id'];
    } else {return '';}
}

/* calcula el precio de producto validando la oferta */
function product_price($producto = null){
    if($producto != null){
       return ($producto['isoferta'] == 1) ? $producto['preciou'] - (($producto['cientoo']/100) * $producto['preciou']) : $producto['preciou'];
    }else{return '0';}
}
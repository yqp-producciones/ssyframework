<?php
class Format{
    const button = "<button %s >%s</button>";
    const input = "<input %s />";
    const label ="<label %s >%s</label>";
    const select = "<select %s >%s</select>";
    const option = "<option %s >%s</option>";
    const a ="<a %s >%s</a>";
    const h3 = "<h3 %s >%s</h3>";
    const td = "<td %s >%s</td>";
    const tr = "<tr %s >%s</tr>";
    const th = "<th %s >%s</th>";
    const tbody = "<tbody %s >%s</tbody>";
    const thead = "<thead %s >%s</thead>";
    const table = "<table %s >%s</table>";
    const script = "<script %s type='text/javascript'>%s</script>";

    //jquery format
    const JQready = "$(document).ready(function(){%s});";
    const JQalert = "alert(%s);";
    const JQdatatable = "$('%s').DataTable(%s);";
    //simbolos
    const comilla ="'"; //comlilla simple 
}

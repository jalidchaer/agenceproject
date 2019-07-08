<?php 


/*
   Retorna el último día del mes
*/
function getLastDayMonth($month, $year){
    $firstday = date('01-' . $month . '-'.$year);
    $lastday = date(date('t', strtotime($firstday)) .'-' . $month . '-'.$year);
    $lastday = date("d",strtotime($lastday));
    return $lastday;
}

/*
   Retorn la lista de meses por un rango seleccionado, recibe como parámetro el mes y el año
*/
function getMonths($fromDate,$endDate){
    
    $year = date("Y",strtotime($endDate));
    $month = date("m",strtotime($endDate));
    $lastDay = getLastDayMonth($month, $year);

    $fromDate = '01'.'-'.$fromDate;
    $endDate = $lastDay.'-'.$endDate;

    $f1 = new DateTime($fromDate);
    $f2 = new DateTime($endDate);

    $cant_meses = $f2->diff($f1);
    $cant_meses = $cant_meses->format('%m'); //devuelve el numero de meses entre ambas fechas.
    $listaMeses = array($f1->format('Y-m'));

    for ($i=0; $i<$cant_meses; $i++) {

        $ultimaFecha = end($listaMeses);
        $ultimaFecha = new DateTime($ultimaFecha);
        $nuevaFecha = $ultimaFecha->add(new DateInterval("P1M"));
        $nuevaFecha = $nuevaFecha->format('Y-m');

        array_push($listaMeses, $nuevaFecha) ;

    }
    return $listaMeses;
 }
 /*
   Retorna el nombre de los meses según la posición seleccionada
 */
  function getNameMonths(){
     $month = array('01' => 'Janeiro', '02'=> 'Fevereiro', '03' =>'Março', '04'=> 'Abril', '05'=>'Maio', 
        '06'=> 'Junho', '07'=>'Julho', '08'=>'Agosto', '09'=> 'Setembro', '10'=>"Outubro", '11'=>'Novembro', '12'=>'Dezembro'); 
     return $month;
  }

 /*
     Retorna el calculo de la comisión
 */

  function calculateComissao($valor, $total_imp_inc, $comissao_cn){
      
      $valorComissao = ($valor - ($valor*($total_imp_inc/100)))*($comissao_cn/100);
      return $valorComissao;
  }
/*
   Retorna el calculo del valor neto
*/
  function calculateValorNeto($valor, $total_imp_inc){
       $valorNeto = ($valor - ($valor*($total_imp_inc/100)));
       return $valorNeto;
  }

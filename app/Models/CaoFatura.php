<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use DB;


class CaoFatura
{
   /*  
       Retorna las facturas asociadas a un consultor en un rango de fecha
   */
   public static function getFaturasByUser($co_usuario, $from_date, $end_date)
   {  

    $year = date("Y",strtotime($end_date));
    $month = date("m",strtotime($end_date));
    $lastDay = getLastDayMonth($month, $year);

    $fromDate = '01'.'-'.$from_date;
    $endDate = $lastDay.'-'.$end_date;
      
    $from_date = date("Y-m-d",strtotime($fromDate));
    $end_date =  date("Y-m-d",strtotime($endDate));

   	 $facturas = DB::table('cao_fatura')
            ->join('cao_sistema', 'cao_fatura.co_cliente', '=', 'cao_sistema.co_cliente')
            ->join('cao_os', 'cao_fatura.co_os', '=', 'cao_os.co_os')
             ->whereColumn([
                    ['cao_sistema.co_usuario', '=', 'cao_os.co_usuario'],
                    ['cao_sistema.co_sistema', '=', 'cao_fatura.co_sistema'],
                ])
               ->where('cao_os.co_usuario', '=', $co_usuario)
               ->whereBetween('cao_fatura.data_emissao', [$from_date, $end_date])
               ->select('cao_fatura.num_nf','cao_fatura.valor','cao_fatura.total_imp_inc','cao_fatura.comissao_cn','cao_fatura.data_emissao')
               ->get();
         return $facturas;
   }

}


<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use DB;


class CaoSalario
{
  /* 
      Retorna el salario de un consultor
  */
   public static function getSalarioByUser($co_usuario)
   {
   	 $salario = DB::table('cao_salario')
            ->where('co_usuario', '=', $co_usuario)
            ->select('brut_salario')
            ->get();

            return $salario;
   }

}
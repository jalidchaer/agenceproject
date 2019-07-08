<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use DB;


class CaoUsuario 
{
   /*
      Retorna todos los consultores los cuales son de tipo de 0,1,2
   */
   public static function geUsersConsultores()
   {
   	 $users = DB::table('cao_usuario')
            ->join('permissao_sistema', 'cao_usuario.co_usuario', '=', 'permissao_sistema.co_usuario')
            ->where('permissao_sistema.in_ativo', '=', 'S')
            ->whereIn('permissao_sistema.co_tipo_usuario', [0,1,2])
            ->select('cao_usuario.co_usuario','cao_usuario.no_usuario')
            ->get();

            return $users;
   }

  /*
     Retorna los datos asociados a un consultor
  */
   public static function getConsultor($user){
      $user = DB::table('cao_usuario')
            ->where('cao_usuario.co_usuario', '=', $user)
            ->select('cao_usuario.co_usuario','cao_usuario.no_usuario')
            ->get();

      return $user;
   }

}
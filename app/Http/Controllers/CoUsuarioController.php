<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CaoUsuario;



class CoUsuarioController extends Controller {

   /*
      Retorna la lista de consultores
   */
	public function getConsultores()
	{
		$users = CaoUsuario::geUsersConsultores();
		return response()->json($users);
	}

}
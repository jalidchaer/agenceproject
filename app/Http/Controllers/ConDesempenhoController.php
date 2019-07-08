<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use App\models\CaoFatura;
use App\models\CaoSalario;
use App\models\CaoUsuario;

class ConDesempenhoController extends Controller {

 /*
    Retorna la vista con_desempenho
 */
 public function index(){
     return view('con_desempenho');
 }

 /*
    Retorna el valor neto la comisión y las ganancias por mes para cada consultor en un periodo
    determinado
 */
	public function getDesempenhoCosultor($user, $fromDate, $endDate){
		$facturas = CaoFatura::getFaturasByUser($user,$fromDate,$endDate);
    $cuxtoFixo = CaoSalario::getSalarioByUser($user);
    if(!empty($cuxtoFixo[0])){
       $cuxtoFixo = $cuxtoFixo[0]->brut_salario;
     }else {
       $cuxtoFixo = 0;
     }
   
    $data = [];
		foreach ($facturas as $key => $value) {
        $totalComissao = 0;
        $totalValorNeto = 0;
		    $monthYear = date("Y-m",strtotime($value->data_emissao));
        $year = date("Y",strtotime($value->data_emissao));
        $month = date("m",strtotime($value->data_emissao));
           $arr[$monthYear][] = $value;

           for ($i=0; $i<count($arr[$monthYear]) ; $i++) { 
                
                $valor = $arr[$monthYear][$i]->valor;
                $comissao_cn = $arr[$monthYear][$i]->comissao_cn;
                $total_imp_inc = $arr[$monthYear][$i]->total_imp_inc;
                $valorNeto =  calculateValorNeto($valor, $total_imp_inc);
                $valorComissao = calculateComissao($valor, $total_imp_inc, $comissao_cn);

                $totalComissao  +=$valorComissao;
                $totalValorNeto +=$valorNeto;
                $lucro = ($totalValorNeto-($cuxtoFixo+$totalComissao));
              }
            
            $data[$monthYear] = array('mes'=>getNameMonths()[$month].' de '.$year, 'totalValorNetoMes'=>number_format($totalValorNeto,2),'totalComissaoMes'=>number_format($totalComissao,2), 'cuxtoFixoMes'=>$cuxtoFixo, 'lucroMes'=>number_format($lucro,2));
            
          }
        return $data;
    }

/*
    Retorna el valor neto total la comisión total y las ganancias totales para cada consultor en un periodo
    determinado
 */
    public function getTotalDesempenhoCosultor($user, $fromDate, $endDate){
       $facturas = CaoFatura::getFaturasByUser($user,$fromDate,$endDate);
       $cuxtoFixo = CaoSalario::getSalarioByUser($user);
        if(!empty($cuxtoFixo[0])){
          $cuxtoFixo = $cuxtoFixo[0]->brut_salario;
       }else {
          $cuxtoFixo = 0;
        }
        $totalComissao = 0;
        $totalValorNeto = 0;
        $totalCuxtoFixo = 0;

       foreach ($facturas as $key => $value) {

         $valor = $value->valor;
         $total_imp_inc = $value->total_imp_inc;
         $comissao_cn = $value->comissao_cn;

         $valorNeto =   calculateValorNeto($valor, $total_imp_inc);
         $valorComissao = calculateComissao($valor, $total_imp_inc, $comissao_cn);
         
         
         $totalValorNeto += $valorNeto;
         $totalComissao  += $valorComissao;
         $totalCuxtoFixo += $cuxtoFixo;

       }
        $totalLucro = ($totalValorNeto-($totalCuxtoFixo+$totalComissao));
       $data = array('totalValorNeto'=>number_format($totalValorNeto,2), 'totalComissao'=>number_format($totalComissao,2), 'totalCuxtoFixo'=>number_format($totalCuxtoFixo,2), 'totalLucro'=>number_format($totalLucro,2));
       return $data;
    }


 /*
     Retorna el listado de cada uno de los consultores con la información asociada a su desempeño
 */
  public function relatorio(Request $request){
        
        
         $validator = Validator::make($request->all(), [
            'fromDate' => 'required',
            'endDate' => 'required',
            'consultores' => 'required'
        ]);
        if ($validator->fails()) {
            $data = array('status'=>false,'message'=>$validator->errors()->all());
            return  response()->json($data);
        }
        $fromDate = $request->fromDate;
        $endDate  = $request->endDate;
        $user = $request->consultores;
        $data = [];
        for ($i=0; $i<count($user); $i++) { 
           $consultor = $user[$i];
           $dataConsultor = CaoUsuario::getConsultor($consultor);
           $dataDesempenho = $this->getDesempenhoCosultor($consultor, $fromDate, $endDate);
           $dataTotaDesempenho = $this->getTotalDesempenhoCosultor($consultor, $fromDate, $endDate);

           $data[] = array('dataUser'=>$dataConsultor,'dataDesempenho'=>$dataDesempenho, 'dataTotaDesempenho'=>$dataTotaDesempenho);
        
        }

        return response()->json($data);
	}
  /*
     Retorna los datos del desempeño de cada consultor por mes
  */
   public function grafico(Request $request){
        
        $validator = Validator::make($request->all(), [
            'fromDate' => 'required',
            'endDate' => 'required',
            'consultores' => 'required'
        ]);
        if ($validator->fails()) {
            $data = array('status'=>false,'message'=>$validator->errors()->all());
            return  response()->json($data);
        }
        $fromDate = $request->fromDate; // fecha de inicio
        $endDate  = $request->endDate; // fecha de final
        $user = $request->consultores; // lista de consultores
        $countUser = count($user);  // cantidad de consultores
        $totalCuxtoFixo = 0;   // inicializamos el costo fijo
        $months = getMonths($fromDate,$endDate); // obtenemos la lista de meses
        // obtenemos por cada consultor el valor neto y el salario
        for ($i=0; $i<$countUser; $i++) { 
          $dataValorNetoMes = array();
           $consultor = $user[$i];
           $dataConsultor = CaoUsuario::getConsultor($consultor);
           $dataDesempenho = $this->getDesempenhoCosultor($consultor, $fromDate, $endDate);
           $cuxtoFixo = CaoSalario::getSalarioByUser($user);
           if(!empty($cuxtoFixo[0])){
            $cuxtoFixo = $cuxtoFixo[0]->brut_salario;
           }else {
            $cuxtoFixo = 0;
           }
            $dataMonthYear = array();
           // guardamos por cada mes el total del valor neto de cada consultor
           foreach ($months as $key => $value) {
             $totalValorNetoMes = 0;
            if(!empty($dataDesempenho[$value])){
               $totalValorNetoMes = str_replace(",", "", $dataDesempenho[$value]["totalValorNetoMes"]);
            }
            $month = substr($value, 5, 2 );
            $year = substr($value, 0, 4 );
            $name = getNameMonths()[$month];
            $monthYear = $name.' - '.$year;
              array_push($dataMonthYear, $monthYear);
              array_push($dataValorNetoMes, $totalValorNetoMes) ;
            }
            $dataUser[] = $dataConsultor;
            $dataValores[]= $dataValorNetoMes;
            $totalCuxtoFixo +=$cuxtoFixo;
            $cuxtoFixoMedio = $totalCuxtoFixo/$countUser;
          
        }
       
        $data = array('dataSets'=>$dataValores, 'months'=>$dataMonthYear, 'dataUser'=>$dataUser, 'cuxtoFixoMedio'=>$cuxtoFixoMedio);
        return response()->json($data);
  }


 /*
    Retona el total de las ganancias netas de todos los consultores
 */
  public function calculateTotalReceitaLiquida($user, $fromDate, $endDate){
        $totalDesempenho = 0;
        for ($i=0; $i<count($user); $i++){ 
           $consultor = $user[$i];
           $dataConsultor = CaoUsuario::getConsultor($consultor);
           $dataTotalDesempenho = $this->getTotalDesempenhoCosultor($consultor, $fromDate, $endDate);
           $totalValorNeto = str_replace(",", "",  $dataTotalDesempenho["totalValorNeto"]);
           $totalDesempenho += $totalValorNeto;

         }
         return $totalDesempenho;
  }

 /* 
     Retorna el porcentaje de las ganancias netas por cada consultor
 */
  public function percentageReceitaLiquida(Request $request){

        $validator = Validator::make($request->all(), [
            'fromDate' => 'required',
            'endDate' => 'required',
            'consultores' => 'required'
        ]);
        if ($validator->fails()) {
            $data = array('status'=>false,'message'=>$validator->errors()->all());
            return  response()->json($data);
        }
        $fromDate = $request->fromDate;
        $endDate  = $request->endDate;
        $user = $request->consultores;
        
        $totalReceitaLiquida = $this->calculateTotalReceitaLiquida($user, $fromDate, $endDate);
        $totalDesempenho = 0;
        $data = [];
        for ($i=0; $i<count($user); $i++){ 
           $consultor = $user[$i];
           $dataConsultor = CaoUsuario::getConsultor($consultor);
           $dataTotalDesempenho = $this->getTotalDesempenhoCosultor($consultor, $fromDate, $endDate);
           $totalValorNeto = str_replace(",", "",  $dataTotalDesempenho["totalValorNeto"]);
           if($totalReceitaLiquida != 0){
             $percentaje = round(($totalValorNeto/$totalReceitaLiquida)*100,2);
           }else{
              $percentaje = 0;
           }
           $dataPercentaje[] = $percentaje;
           $dataUser[] = $dataConsultor;
        }
        $data = array('dataPercentaje'=>$dataPercentaje, 'dataUser'=>$dataUser);
        return response()->json($data);
  }



}
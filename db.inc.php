<?php
class conectdb{
 
	private static $conexionActiva = null;
 
	public function db(){
 
		// Si ya existe una conexión abierta y sigue viva, reutilízala
		if(self::$conexionActiva !== null && self::$conexionActiva instanceof mysqli){
			// ping() confirma que la conexión sigue viva; si el server la cerró, reconecta
			if(@mysqli_ping(self::$conexionActiva)){
				return self::$conexionActiva;
			}
		}
 
		$ambiente = $this->ambiente();
 
		if($ambiente == 'QA'){
			$db_server='127.0.0.1';
			$db_username='u492963066_epeventos';
			$db_password='12Epc@@^9A0';
			$db_name='u492963066_epeventos';
		}elseif($ambiente == 'localhost'){
			$db_server='127.0.0.1';
			$db_username='root';
			$db_password='';
			$db_name='epeventos_crm6';
		}elseif($ambiente == 'PROD2'){
			$db_server='127.0.0.1';
			$db_username='u492963066_prod';
			$db_password='EPc@@ñ^IN722480';
			$db_name='u492963066_prod';
		}elseif($ambiente == 'PROD'){
			$db_server='127.0.0.1';
			$db_username='u492963066_prod01';
			$db_password='8W$j3mnLC0';
			$db_name='u492963066_prod01';
		}
 
		$conn = mysqli_connect($db_server,$db_username,$db_password,$db_name);
 
		if(!$conn){
		
			error_log('Error de conexión MySQL: '.mysqli_connect_error());
			die('No se pudo conectar a la base de datos. Intenta de nuevo en unos minutos.');
		}
 
		mysqli_set_charset($conn,"utf8");
 
		self::$conexionActiva = $conn;
 
		return $conn;
	}
 

	public function cerrarConexion(){
		if(self::$conexionActiva !== null){
			mysqli_close(self::$conexionActiva);
			self::$conexionActiva = null;
		}
	}
 
}
?>
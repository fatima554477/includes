<?php
/*
clase EPC INNOVA
CREADO : 10/OCTUBRE/2022
fecha sandor: 25/JUNIO/2023
fecha fatima: 03/JUNIO/2026
*/
	date_default_timezone_set('America/Mexico_City');
	define('__ROOT2__', dirname(dirname(__FILE__)));
	require __ROOT2__."/includes/error_reporting.php";
	require __ROOT2__."/includes/ambientetrabajo.php";

	
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\SMTP;
	use PHPMailer\PHPMailer\Exception;
	require  __ROOT2__.'/PHPMailer-master/vendor/autoload.php';	
class herramientas extends PHPMailer{

	private static $smtpCacheByRelacion = array();
	private static $smtpCacheById = array();


	public function array_smtp_ID($conn,$id){
		if(isset(self::$smtpCacheByRelacion[$id])){
			return self::$smtpCacheByRelacion[$id];
		}

		$variablequery = "select * from 03datossmtp where idRelacion = '".$id."' limit 1 ";
		$arrayquery = mysqli_query($conn,$variablequery);
		$row = mysqli_fetch_array($arrayquery, MYSQLI_ASSOC);
		if(!$row){
			self::$smtpCacheByRelacion[$id] = array();
			return self::$smtpCacheByRelacion[$id];
		}
		$host['Host'] = $row['Host'];
		$host['Username'] = $row['Username'];
		$host['Passwordd'] = $row['Passwordd'];
		$host['SMTPSecure'] = $row['SMTPSecure'];
		$host['Port'] = $row['Port'];
		$host['setFrom1'] = $row['setFrom1'];
		$host['setFrom2'] = $row['setFrom2'];
	    $host['prefijo'] = $row['prefijo'];
		$host['idRelacion'] = $row['idRelacion'];
		self::$smtpCacheByRelacion[$id] = $host;
		return $host;
	}

	public function array_smtp_PREFIJO($conn,$id){
		if(isset(self::$smtpCacheById[$id])){
			return self::$smtpCacheById[$id];
		}

		$variablequery = "select * from 03datossmtp where id = '".$id."' limit 1 ";
		$arrayquery = mysqli_query($conn,$variablequery);
		$row = mysqli_fetch_array($arrayquery, MYSQLI_ASSOC);
		if(!$row){
			self::$smtpCacheById[$id] = array();
			return self::$smtpCacheById[$id];
		}
		$host['Host'] = $row['Host'];
		$host['Username'] = $row['Username'];
		$host['Passwordd'] = $row['Passwordd'];
		$host['SMTPSecure'] = $row['SMTPSecure'];
		$host['Port'] = $row['Port'];
		$host['setFrom1'] = $row['setFrom1'];
		$host['setFrom2'] = $row['setFrom2'];
	    $host['prefijo'] = $row['prefijo'];
		$host['idRelacion'] = $row['idRelacion'];
		self::$smtpCacheById[$id] = $host;
		return $host;
	}
	
	public function variables_informacionfiscal_logo2_ID($conn,$idRelacion){



		$variablequery = "select id,ADJUNTAR_LOGO_INFORMACION from 03docs_info_fiscal where idRelacion = '".$idRelacion."' ";
		$arrayquery = mysqli_query($conn,$variablequery);
		$row = mysqli_fetch_array($arrayquery, MYSQLI_ASSOC);
		if($row['id']!=''){
		return $row['ADJUNTAR_LOGO_INFORMACION'];
		}else{

		$variablequery1 = "select id from 03datosdelaempresa where NCE_OBSERVACIONES = 'EP'  ";
		$arrayquery1 = mysqli_query($conn,$variablequery1);
		$row = mysqli_fetch_array($arrayquery1, MYSQLI_ASSOC);			
			
		$variablequery = "select ADJUNTAR_LOGO_INFORMACION from 03docs_info_fiscal where idRelacion = '".$row['id']."'  ";
		$arrayquery = mysqli_query($conn,$variablequery);
		$row = mysqli_fetch_array($arrayquery, MYSQLI_ASSOC);
		return $row['ADJUNTAR_LOGO_INFORMACION'];			
		}
	}

	public function email($emails1nombre, $html, $adjuntos, $embebida, $Subject=FALSE, $arrayhost=false){
	try {
		$this->clearAllRecipients();
		$this->clearAttachments();
    //Server settings
    //$this->SMTPDebug = SMTP::DEBUG_SERVER;
		$this->isSMTP();
		$this->SMTPAuth   = true;
		//PRINT_R($arrayhost);
		$Host= isset($arrayhost['Host'])?$arrayhost['Host']:'';		
	if($Host != '' ){
		
		if($_SESSION["NOMBREUSUARIO33"]==true){
			$setFrom2 = $_SESSION["NOMBREUSUARIO33"];
		}else{
			$setFrom2 = $arrayhost['setFrom2'];
		}		
		
		$Username = $arrayhost['Username'];
		$Passwordd= $arrayhost['Passwordd'];
		$SMTPSecure= $arrayhost['SMTPSecure'];
		$Port = $arrayhost['Port'];
		$setFrom1 = $arrayhost['setFrom1'];
		//$setFrom2 = $arrayhost['setFrom2'];
		$prefijo = $arrayhost['prefijo'];
		$idRelacion = $arrayhost['idRelacion'];
	}else{
		
		if($_SESSION["NOMBREUSUARIO33"]==true){
			$setFrom2 = $_SESSION["NOMBREUSUARIO33"];
		}else{
			$setFrom2 = 'EPConvenciones';
		}		

	}



		if($Host != ''){
		$this->Host       = $Host;
		$this->Username   = $Username;
		$this->Password   = $Passwordd;
		$this->SMTPSecure = $SMTPSecure;
		$this->Port       = $Port;
		$this->setFrom($setFrom1, $setFrom2);
		}else{
$conexionsmtp = new ambientetrabajo();
	$host_smtp = $this->array_smtp_PREFIJO($conexionsmtp->db(),'1');
	if(empty($host_smtp)){
		return 'No se encontró configuración SMTP';
	}
			

		
		$this->Host       = $host_smtp['Host'];//'smtp.office365.com';
		$this->Username   = $host_smtp['Username'];//'epconvenciones@epconvenciones.com.mx';
		$this->Password   = $host_smtp['Passwordd'];//'Nub414151';
		$this->SMTPSecure = $host_smtp['SMTPSecure'];//'tls';
		$this->Port       = $host_smtp['Port'];// 587;
		$this->setFrom($host_smtp['setFrom1'],$host_smtp['setFrom2']);
		}
		//echo $this->Username;
       foreach($emails1nombre as $etiqueta => $valor){
		$explotado = explode(';',$etiqueta);
		$cuenta = count($explotado);
		if($cuenta>1){
		for($ioa=0;$ioa<$cuenta;$ioa++){
			$destinatario = trim($explotado[$ioa]);
			if($destinatario !== ''){
				$this->addAddress($destinatario);
			}
		}
		}else{
			$destinatario = trim($etiqueta);
			if($destinatario !== ''){
				$this->addAddress($destinatario, $valor);
			}
		}
		}

		foreach($adjuntos as $etiqueta1 => $valor1){
			$this->addAttachment($etiqueta1, $valor1);
		}

		foreach($embebida as $etiqueta2 => $valor2){
			$this->AddEmbeddedImage($etiqueta2, $valor2, $etiqueta2);
		}
		if($Subject==false){
			$Subject1='Favor de Completar el Formulario';
			}else{
			$Subject1=$Subject;				
			}
		
		$this->isHTML(true);
		$this->CharSet = 'UTF-8';    
		$this->Subject = $Subject1;
		$this->Body    = $html;
		$this->send();
    //return 'Correo enviado';
	}
		catch (Exception $e) {
		return "El email no pudo ser enviado, Error: {$this->ErrorInfo}";
	}
	return 'Correo enviado';
	/*if($this->ErrorInfo==''){
	return 'Correo enviado';	
	}else{
	return $this->ErrorInfo;	
	}*/
	
	}


	public function guardatxt($datos){
		$errorEmail = $datos.' '.chr(13).chr(10);
		$file = fopen(__ROOT2__.'/verinicio/logMailgun.txt', "a+");
		fwrite($file, $errorEmail);
		fclose($file);
		$errorEmail = '';
	}

public function lectorxml($valor){
    
    if(!file_exists($valor)) return array();
    
    $xmlString = file_get_contents($valor);
    $xmlString = preg_replace('/^\xEF\xBB\xBF/', '', $xmlString);
    
libxml_use_internal_errors(true);
$xmlDoc = simplexml_load_string($xmlString);

if($xmlDoc === false) {
    error_log('lectorxml FALLO en: ' . $valor);
    foreach (libxml_get_errors() as $error) {
        error_log('libxml: ' . trim($error->message));
    }
    libxml_clear_errors();
    return array();
}
    
    $regreso = array();
    
    // Detectar namespace automaticamente (cfdi/3 o cfdi/4)
    $namespacesXML = $xmlDoc->getNamespaces(true);
    $cfdi_ns = 'http://www.sat.gob.mx/cfd/4';
    foreach($namespacesXML as $prefix => $uri){
        if(strpos($uri, 'sat.gob.mx/cfd') !== false){
            $cfdi_ns = $uri;
        }
    }
    
    $xmlDoc->registerXPathNamespace('cfdi', $cfdi_ns);
    $xmlDoc->registerXPathNamespace('tfd', 'http://www.sat.gob.mx/TimbreFiscalDigital');
    $xmlDoc->registerXPathNamespace('pago10', 'http://www.sat.gob.mx/Pagos');
    $xmlDoc->registerXPathNamespace('aerolineas', 'http://www.sat.gob.mx/aerolineas');

    foreach ($xmlDoc->xpath('//cfdi:Comprobante') as $autor) {
        if ((string)$autor['Version'] != '')           $regreso['Version']           = rtrim((string)$autor['Version']);
        if ((string)$autor['Sello'] != '')             $regreso['sello']             = rtrim((string)$autor['Sello']);
        if ((string)$autor['Certificado'] != '')       $regreso['Certificado']       = rtrim((string)$autor['Certificado']);
        if ((string)$autor['NoCertificado'] != '')     $regreso['noCertificado']     = rtrim((string)$autor['NoCertificado']);
        if ((string)$autor['Fecha'] != '')             $regreso['fecha']             = rtrim((string)$autor['Fecha']);
        if ((string)$autor['TipoDeComprobante'] != '') $regreso['tipoDeComprobante'] = rtrim((string)$autor['TipoDeComprobante']);
        if ((string)$autor['TipoDeComprobante'] != '') $regreso['TipoDeComprobante'] = rtrim((string)$autor['TipoDeComprobante']);
        if ((string)$autor['MetodoPago'] != '')        $regreso['metodoDePago']      = rtrim((string)$autor['MetodoPago']);
        if ((string)$autor['FormaPago'] != '')         $regreso['formaDePago']       = rtrim((string)$autor['FormaPago']);
        if ((string)$autor['CondicionesDePago'] != '') $regreso['condicionesDePago'] = rtrim((string)$autor['CondicionesDePago']);
        if ((string)$autor['SubTotal'] != '')          $regreso['subTotal']          = rtrim((string)$autor['SubTotal']);
        if ((string)$autor['Descuento'] != '')         $regreso['Descuento']         = rtrim((string)$autor['Descuento']);
        if ((string)$autor['TipoCambio'] != '')        $regreso['TipoCambio']        = rtrim((string)$autor['TipoCambio']);
        if ((string)$autor['Moneda'] != '')            $regreso['Moneda']            = rtrim((string)$autor['Moneda']);
        if ((string)$autor['Total'] != '')             $regreso['total']             = rtrim((string)$autor['Total']);
        if ((string)$autor['Serie'] != '')             $regreso['serie']             = rtrim((string)$autor['Serie']);
        if ((string)$autor['Folio'] != '')             $regreso['folio']             = rtrim((string)$autor['Folio']);
        if ((string)$autor['NumCtaPago'] != '')        $regreso['NumCtaPago']        = rtrim((string)$autor['NumCtaPago']);
        if ((string)$autor['LugarExpedicion'] != '')   $regreso['LugarExpedicion']   = rtrim((string)$autor['LugarExpedicion']);
    }  

foreach ($xmlDoc->xpath('//cfdi:Emisor') as $autor1) {
    if ((string)$autor1['Rfc'] != '')          $regreso['rfcE']     = trim(str_replace('"', '', (string)$autor1['Rfc']));
    if ((string)$autor1['Nombre'] != '')        $regreso['nombreE']  = trim(str_replace('"', '', (string)$autor1['Nombre']));
    if ((string)$autor1['RegimenFiscal'] != '') $regreso['regimenE'] = (string)$autor1['RegimenFiscal'];
}

    foreach ($xmlDoc->xpath('//cfdi:Receptor') as $autor4) {
        if ((string)$autor4['Rfc'] != '')                    $regreso['rfcR']                    = rtrim((string)$autor4['Rfc']);
        if ((string)$autor4['Nombre'] != '')                  $regreso['nombreR']                 = rtrim((string)$autor4['Nombre']);
        if ((string)$autor4['UsoCFDI'] != '')                 $regreso['UsoCFDI']                 = (string)$autor4['UsoCFDI'];
        if ((string)$autor4['DomicilioFiscalReceptor'] != '')  $regreso['DomicilioFiscalReceptor'] = (string)$autor4['DomicilioFiscalReceptor'];
        if ((string)$autor4['RegimenFiscalReceptor'] != '')   $regreso['RegimenFiscalReceptor']   = (string)$autor4['RegimenFiscalReceptor'];
    }

    if(isset($regreso['tipoDeComprobante']) && $regreso['tipoDeComprobante'] == 'P'){
        foreach ($xmlDoc->xpath('//pago10:Pagos/pago10:Pago') as $pago10DATOS1) {
            $regreso['FechaPago']    = (string)$pago10DATOS1['FechaPago'];
            $regreso['FormaDePagoP'] = (string)$pago10DATOS1['FormaDePagoP'];
            $regreso['MonedaP']      = (string)$pago10DATOS1['MonedaP'];
            $regreso['Monto']        = (string)$pago10DATOS1['Monto'];
        }
        $intp = 0;
        foreach ($xmlDoc->xpath('//pago10:DoctoRelacionado') as $pago10DATOS) {
            $regreso['pagos10'][$intp]['IdDocumento']      = (string)$pago10DATOS['IdDocumento'];
            $regreso['pagos10'][$intp]['Serie']            = (string)$pago10DATOS['Serie'];
            $regreso['pagos10'][$intp]['Folio']            = (string)$pago10DATOS['Folio'];
            $regreso['pagos10'][$intp]['MonedaDR']         = (string)$pago10DATOS['MonedaDR'];
            $regreso['pagos10'][$intp]['MetodoDePagoDR']   = (string)$pago10DATOS['MetodoDePagoDR'];
            $regreso['pagos10'][$intp]['NumParcialidad']   = (string)$pago10DATOS['NumParcialidad'];
            $regreso['pagos10'][$intp]['ImpSaldoAnt']      = (string)$pago10DATOS['ImpSaldoAnt'];
            $regreso['pagos10'][$intp]['ImpPagado']        = (string)$pago10DATOS['ImpPagado'];
            $regreso['pagos10'][$intp]['ImpSaldoInsoluto'] = (string)$pago10DATOS['ImpSaldoInsoluto'];
            $intp++;
        }
    }

    foreach ($xmlDoc->xpath('//cfdi:Concepto') as $cfdiConcepto) {
        $regreso['Cantidad']         = (string)$cfdiConcepto['Cantidad'];
        $regreso['ValorUnitario']    = (string)$cfdiConcepto['ValorUnitario'];
        $regreso['Importe']          = (string)$cfdiConcepto['Importe'];
        $regreso['ClaveProdServ']    = (string)$cfdiConcepto['ClaveProdServ'];
        $regreso['Unidad']           = (string)$cfdiConcepto['Unidad'];
        $regreso['Descripcion']      = (string)$cfdiConcepto['Descripcion'];
        $regreso['ClaveUnidad']      = (string)$cfdiConcepto['ClaveUnidad'];
        $regreso['NoIdentificacion'] = (string)$cfdiConcepto['NoIdentificacion'];
        break;
    }

    foreach ($xmlDoc->xpath('//tfd:TimbreFiscalDigital') as $cfdiTimbreFiscalDigital) {
        $regreso['UUID']             = (string)$cfdiTimbreFiscalDigital['UUID'];
        $regreso['selloCFD']         = (string)$cfdiTimbreFiscalDigital['SelloCFD'];
        $regreso['noCertificadoSAT'] = (string)$cfdiTimbreFiscalDigital['NoCertificadoSAT'];
        $regreso['FechaTimbrado']    = (string)$cfdiTimbreFiscalDigital['FechaTimbrado'];
        $regreso['selloSAT']         = (string)$cfdiTimbreFiscalDigital['SelloSAT'];
        $regreso['RfcProvCertif']    = (string)$cfdiTimbreFiscalDigital['RfcProvCertif'];
    }

    foreach ($xmlDoc->xpath('//cfdi:Retencion') as $impueRdesglosado){
        if((string)$impueRdesglosado['Impuesto']=='001') $regreso['impueRdesglosado001'] = (string)$impueRdesglosado['Importe'];
        if((string)$impueRdesglosado['Impuesto']=='002') $regreso['impueRdesglosado002'] = (string)$impueRdesglosado['Importe'];
    }

    foreach ($xmlDoc->xpath('//cfdi:Impuestos') as $impueR){
        $regreso['TImpuestosRetenidos']   = (string)$impueR['TotalImpuestosRetenidos'];
        $regreso['TImpuestosTrasladados'] = (string)$impueR['TotalImpuestosTrasladados'];
    }

    foreach ($xmlDoc->xpath('//aerolineas:Aerolineas') as $TUA){
        $regreso['TUA'] = (string)$TUA['TUA'];
    }
    foreach ($xmlDoc->xpath('//aerolineas:OtrosCargos') as $TotalCargos){
        $regreso['TuaTotalCargos'] = (string)$TotalCargos['TotalCargos'];
    }

    return $regreso;
}

			

//		$session = isset($_SESSION['idPROV'])?$_SESSION['idPROV']:''; 
	public function guardar_db_xml($url,$session,$ultimo_id,$conn,$tipo_comprobante=false){

		
			if( file_exists($url) ){
	$regreso = $this->lectorxml($url);
			}   	
	$Version = isset($regreso['Version'])?$regreso['Version']:'';
	$sello = isset($regreso['sello'])?$regreso['sello']:'';
	$Certificado = isset($regreso['Certificado'])?$regreso['Certificado']:'';
	$noCertificado = isset($regreso['noCertificado'])?$regreso['noCertificado']:'';
	$fecha = isset($regreso['fecha'])?$regreso['fecha']:'';
	$tipoDeComprobante = isset($regreso['tipoDeComprobante'])?$regreso['tipoDeComprobante']:'';
	$metodoDePago = isset($regreso['metodoDePago'])?$regreso['metodoDePago']:'';
	$formaDePago = isset($regreso['formaDePago'])?$regreso['formaDePago']:'';
	$condicionesDePago = isset($regreso['condicionesDePago'])?$regreso['condicionesDePago']:'';
	$subTotal = isset($regreso['subTotal'])?$regreso['subTotal']:'';
	$TipoCambio = isset($regreso['TipoCambio'])?$regreso['TipoCambio']:'';
	$Moneda = isset($regreso['Moneda'])?$regreso['Moneda']:'';
	$total = isset($regreso['total'])?$regreso['total']:'';
	$serie = isset($regreso['serie'])?$regreso['serie']:'';
	$folio = isset($regreso['folio'])?$regreso['folio']:'';
	$LugarExpedicion = isset($regreso['LugarExpedicion'])?$regreso['LugarExpedicion']:'';
	
	$rfcE = isset($regreso['rfcE'])?$regreso['rfcE']:'';					
	$nombreE = isset($regreso['nombreE'])?$regreso['nombreE']:'';	
	$regimenE = isset($regreso['regimenE'])?$regreso['regimenE']:'';
	
	$rfcR = isset($regreso['rfcR'])?$regreso['rfcR']:'';
	$nombreR = isset($regreso['nombreR'])?$regreso['nombreR']:'';
	$UsoCFDI = isset($regreso['UsoCFDI'])?$regreso['UsoCFDI']:'';
	$DomicilioFiscalReceptor = isset($regreso['DomicilioFiscalReceptor'])?$regreso['DomicilioFiscalReceptor']:'';
	$RegimenFiscalReceptor = isset($regreso['RegimenFiscalReceptor'])?$regreso['RegimenFiscalReceptor']:'';
	
	$UUID = isset($regreso['UUID'])?$regreso['UUID']:'';
	$selloCFD = isset($regreso['selloCFD'])?$regreso['selloCFD']:'';
	$noCertificadoSAT = isset($regreso['noCertificadoSAT'])?$regreso['noCertificadoSAT']:'';
	$FechaTimbrado = isset($regreso['FechaTimbrado'])?$regreso['FechaTimbrado']:'';
	$RfcProvCertif = isset($regreso['RfcProvCertif'])?$regreso['RfcProvCertif']:'';	
	$TImpuestosRetenidos = isset($regreso['TImpuestosRetenidos'])?$regreso['TImpuestosRetenidos']:'';
	$TImpuestosTrasladados = isset($regreso['TImpuestosTrasladados'])?$regreso['TImpuestosTrasladados']:'';
//ClaveProdServ
	$CantidadConcepto = isset($regreso['Cantidad'])?$regreso['Cantidad']:'';
	$ValorUnitarioConcepto = isset($regreso['ValorUnitario'])?$regreso['ValorUnitario']:'';
	$ImporteConcepto = isset($regreso['Importe'])?$regreso['Importe']:'';
	$ClaveProdServConcepto = isset($regreso['ClaveProdServ'])?$regreso['ClaveProdServ']:'';
	$UnidadConcepto = isset($regreso['Unidad'])?$regreso['Unidad']:'';
	$DescripcionConcepto = isset($regreso['Descripcion'])?$regreso['Descripcion']:'';
	$ClaveUnidadConcepto = isset($regreso['ClaveUnidad'])?$regreso['ClaveUnidad']:'';
	$NoIdentificacionConcepto = isset($regreso['NoIdentificacion'])?$regreso['NoIdentificacion']:'';
	$TuaTotalCargos = isset($regreso['TuaTotalCargos'])?$regreso['TuaTotalCargos']:'';	
	$TUA = isset($regreso['TUA'])?$regreso['TUA']:'';		
	$Descuento = isset($regreso['Descuento'])?$regreso['Descuento']:'';

$var3 = "INSERT INTO `02XML` (
`id`, `Version`, `fechaTimbrado`, `tipoDeComprobante`, 
`metodoDePago`, `formaDePago`, `condicionesDePago`, `subTotal`, 
`TipoCambio`, `Moneda`, `totalf`, `serie`, 
`folio`, `LugarExpedicion`, `rfcE`, `nombreE`, 
`regimenE`, `rfcR`, `nombreR`, `UsoCFDI`, 
`DomicilioFiscalReceptor`, `RegimenFiscalReceptor`, `UUID`, `TImpuestosRetenidos`, 
`TImpuestosTrasladados`, `idRelacion`, `ultimo_id`,

CantidadConcepto,
ValorUnitarioConcepto,
ImporteConcepto,
ClaveProdServConcepto,
UnidadConcepto,
DescripcionConcepto,
ClaveUnidadConcepto,
NoIdentificacionConcepto,
TuaTotalCargos,
TUA,
Descuento,
tipo_comprobante

) VALUES (
'', '".$Version."', '".$FechaTimbrado."', '".$tipoDeComprobante."', 
'".$metodoDePago."', '".$formaDePago."', '".$condicionesDePago."', '".$subTotal."', 
'".$TipoCambio."', '".$Moneda."', '".$total."', '".$serie."', 
'".$folio."', '".$LugarExpedicion."', '".$rfcE."', '".$nombreE."', 
'".$regimenE."', '".$rfcR."', '".$nombreR."', '".$UsoCFDI."', 
'".$DomicilioFiscalReceptor."', '".$RegimenFiscalReceptor."', '".$UUID."', '".$TImpuestosRetenidos."', 
'".$TImpuestosTrasladados."', '".$session."', '".$ultimo_id."',


'".$CantidadConcepto."',
'".$ValorUnitarioConcepto."',
'".$ImporteConcepto."',
'".$ClaveProdServConcepto."',
'".$UnidadConcepto."',
'".$DescripcionConcepto."',
'".$ClaveUnidadConcepto."',
'".$NoIdentificacionConcepto."',
'".$TuaTotalCargos."',
'".$TUA."',
'".$Descuento."',
'".$tipo_comprobante."'

);  ";	
		mysqli_query($conn,$var3) or die('P156'.mysqli_error($conn));
		//return "1";	
		
		
	}



//		$session = isset($_SESSION['idPROV'])?$_SESSION['idPROV']:''; 
	public function guardar_db_xml2($url,$session,$ultimo_id,$conn,$tipo_comprobante=false){

		
			if( file_exists($url) ){
	$regreso = $this->lectorxml($url);
			}   	
	$Version = isset($regreso['Version'])?$regreso['Version']:'';
	$sello = isset($regreso['sello'])?$regreso['sello']:'';
	$Certificado = isset($regreso['Certificado'])?$regreso['Certificado']:'';
	$noCertificado = isset($regreso['noCertificado'])?$regreso['noCertificado']:'';
	$fecha = isset($regreso['fecha'])?$regreso['fecha']:'';
	$tipoDeComprobante = isset($regreso['tipoDeComprobante'])?$regreso['tipoDeComprobante']:'';
	$metodoDePago = isset($regreso['metodoDePago'])?$regreso['metodoDePago']:'';
	$formaDePago = isset($regreso['formaDePago'])?$regreso['formaDePago']:'';
	$condicionesDePago = isset($regreso['condicionesDePago'])?$regreso['condicionesDePago']:'';
	$subTotal = isset($regreso['subTotal'])?$regreso['subTotal']:'';
	$TipoCambio = isset($regreso['TipoCambio'])?$regreso['TipoCambio']:'';
	$Moneda = isset($regreso['Moneda'])?$regreso['Moneda']:'';
	$total = isset($regreso['total'])?$regreso['total']:'';
	$serie = isset($regreso['serie'])?$regreso['serie']:'';
	$folio = isset($regreso['folio'])?$regreso['folio']:'';
	$LugarExpedicion = isset($regreso['LugarExpedicion'])?$regreso['LugarExpedicion']:'';
	
	$rfcE = isset($regreso['rfcE'])?$regreso['rfcE']:'';					
	$nombreE = isset($regreso['nombreE'])?$regreso['nombreE']:'';	
	$regimenE = isset($regreso['regimenE'])?$regreso['regimenE']:'';
	
	$rfcR = isset($regreso['rfcR'])?$regreso['rfcR']:'';
	$nombreR = isset($regreso['nombreR'])?$regreso['nombreR']:'';
	$UsoCFDI = isset($regreso['UsoCFDI'])?$regreso['UsoCFDI']:'';
	$DomicilioFiscalReceptor = isset($regreso['DomicilioFiscalReceptor'])?$regreso['DomicilioFiscalReceptor']:'';
	$RegimenFiscalReceptor = isset($regreso['RegimenFiscalReceptor'])?$regreso['RegimenFiscalReceptor']:'';
	
	$UUID = isset($regreso['UUID'])?$regreso['UUID']:'';
	$selloCFD = isset($regreso['selloCFD'])?$regreso['selloCFD']:'';
	$noCertificadoSAT = isset($regreso['noCertificadoSAT'])?$regreso['noCertificadoSAT']:'';
	$FechaTimbrado = isset($regreso['FechaTimbrado'])?$regreso['FechaTimbrado']:'';
	$RfcProvCertif = isset($regreso['RfcProvCertif'])?$regreso['RfcProvCertif']:'';	
	$TImpuestosRetenidos = isset($regreso['TImpuestosRetenidos'])?$regreso['TImpuestosRetenidos']:'';
	$TImpuestosTrasladados = isset($regreso['TImpuestosTrasladados'])?$regreso['TImpuestosTrasladados']:'';
//ClaveProdServ
	$CantidadConcepto = isset($regreso['Cantidad'])?$regreso['Cantidad']:'';
	$ValorUnitarioConcepto = isset($regreso['ValorUnitario'])?$regreso['ValorUnitario']:'';
	$ImporteConcepto = isset($regreso['Importe'])?$regreso['Importe']:'';
	$ClaveProdServConcepto = isset($regreso['ClaveProdServ'])?$regreso['ClaveProdServ']:'';
	$UnidadConcepto = isset($regreso['Unidad'])?$regreso['Unidad']:'';
	$DescripcionConcepto = isset($regreso['Descripcion'])?$regreso['Descripcion']:'';
	$ClaveUnidadConcepto = isset($regreso['ClaveUnidad'])?$regreso['ClaveUnidad']:'';
	$NoIdentificacionConcepto = isset($regreso['NoIdentificacion'])?$regreso['NoIdentificacion']:'';
	$TuaTotalCargos = isset($regreso['TuaTotalCargos'])?$regreso['TuaTotalCargos']:'';	
	$TUA = isset($regreso['TUA'])?$regreso['TUA']:'';		
	$Descuento = isset($regreso['Descuento'])?$regreso['Descuento']:'';

$var3 = "INSERT INTO `11XML` (
`id`, `Version`, `fechaTimbrado`, `tipoDeComprobante`, 
`metodoDePago`, `formaDePago`, `condicionesDePago`, `subTotal`, 
`TipoCambio`, `Moneda`, `totalf`, `serie`, 
`folio`, `LugarExpedicion`, `rfcE`, `nombreE`, 
`regimenE`, `rfcR`, `nombreR`, `UsoCFDI`, 
`DomicilioFiscalReceptor`, `RegimenFiscalReceptor`, `UUID`, `TImpuestosRetenidos`, 
`TImpuestosTrasladados`, `idRelacion`, `ultimo_id`,

CantidadConcepto,
ValorUnitarioConcepto,
ImporteConcepto,
ClaveProdServConcepto,
UnidadConcepto,
DescripcionConcepto,
ClaveUnidadConcepto,
NoIdentificacionConcepto,
TuaTotalCargos,
TUA,
Descuento,
tipo_comprobante

) VALUES (
'', '".$Version."', '".$FechaTimbrado."', '".$tipoDeComprobante."', 
'".$metodoDePago."', '".$formaDePago."', '".$condicionesDePago."', '".$subTotal."', 
'".$TipoCambio."', '".$Moneda."', '".$total."', '".$serie."', 
'".$folio."', '".$LugarExpedicion."', '".$rfcE."', '".$nombreE."', 
'".$regimenE."', '".$rfcR."', '".$nombreR."', '".$UsoCFDI."', 
'".$DomicilioFiscalReceptor."', '".$RegimenFiscalReceptor."', '".$UUID."', '".$TImpuestosRetenidos."', 
'".$TImpuestosTrasladados."', '".$session."', '".$ultimo_id."',


'".$CantidadConcepto."',
'".$ValorUnitarioConcepto."',
'".$ImporteConcepto."',
'".$ClaveProdServConcepto."',
'".$UnidadConcepto."',
'".$DescripcionConcepto."',
'".$ClaveUnidadConcepto."',
'".$NoIdentificacionConcepto."',
'".$TuaTotalCargos."',
'".$TUA."',
'".$Descuento."',
'".$tipo_comprobante."'

);  ";	
		mysqli_query($conn,$var3) or die('P156'.mysqli_error($conn));
		//return "1";	
		
		
	}





	public function fechaEs($fecha) {
	$fecha = substr($fecha, 0, 10);
	$numeroDia = date('d', strtotime($fecha));
	$dia = date('l', strtotime($fecha));
	$mes = date('F', strtotime($fecha));
	$anio = date('Y', strtotime($fecha));
	$dias_ES = array("Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo");
	$dias_EN = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
	$nombredia = str_replace($dias_EN, $dias_ES, $dia);
	$meses_ES = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
	$meses_EN = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
	$nombreMes = str_replace($meses_EN, $meses_ES, $mes);
	return $nombredia." ".$numeroDia." de ".$nombreMes." de ".$anio;
	}



	public function idempermiso($idempermiso,$conn) {
		//$conn = $this->db();		
			$pregunta = 'select *,01empresa.id as idddd from 01empresa JOIN 01adjuntoscolaboradores ON 01empresa.id = 01adjuntoscolaboradores.idRelacion where 01empresa.id = "'.$idempermiso.'" AND ESTATUS_CRM_ACTIVOBAJA = "ACTIVO" ';

			$preguntaQ = mysqli_query($conn,$pregunta) or die('P1533'.mysqli_error($conn));
			$ROWP = MYSQLI_FETCH_ARRAY($preguntaQ, MYSQLI_ASSOC);

			if($ROWP['idddd']!=0){
			return $ROWP['PERMISOS'];
			}


	}


	}


	class listadocolaboradores extends ambientetrabajo{

	public function MANDA_INFORMACION2($parametros,$titulos=false,$nombretabla,$limites=false){
		/*liberada pero no les gustó, trae registros en forma natural en vertical*/
		$explotado = explode(',',$titulos);
		$cuenta = count($explotado);
		$valores .= '<hr/><br/><table border="0" width="100%" align="center">';
		
		if($titulos!=false){
		$valores .= '<tr >';
		for($rrr=0;$rrr<=$cuenta;$rrr++){
		 $valores .= '<td style="border-bottom:1px solid  #000;border-right:1px solid  #000;"> <strong>'.$explotado[$rrr].' </strong></td>';
		}
		$valores .= '</tr>';
		}
		
		$conn = $this->db();
		$var1 = 'select '.$parametros.' from '.$nombretabla.' '.$limites;
		$query = mysqli_query($conn,$var1) or die('P44'.mysqli_error($conn));

		while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
			
			$valores .= '<tr >';
			foreach($row as $etiqueta => $valor){
				$valores .= '<td style="border-bottom:1px solid  #000; border-right:1px solid  #000;"> '. $valor.'</td>';
			}
					$valores .= '</tr>';
		}
		$valores .= '</table><br/><hr/>';
	return $valores ;
	
	
	}


	public function MANDA_INFORMACION($parametros3,$titulos=false,$nombretabla,$limites=false){
		/*liberada en horizontal y en espera de ser aprobada*/
		$explotado = explode(',',$titulos);

		$parametros2 = str_replace(' ','',$parametros3);
		$parametros = preg_replace("[\n|\r|\n\r]", "", $parametros2);		
		$explotadoP = explode(',',$parametros);
		$cuenta2 = count($explotadoP) - 1;

		$conn = $this->db();
		$var1 = 'select id,'.$parametros.' from '.$nombretabla.' '.$limites;
		$query = mysqli_query($conn,$var1) or die('P44'.mysqli_error($conn));
		$OOO=0;	
		//$valores2 .= '<table border="0"><tr><td>';
		$valores2 = '';
		while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
		$valores2 .= '<table border="0" style="margin:15px;">';
		
		for($rrr=0;$rrr<=$cuenta2;$rrr++){
		$inicio = '<tr><td style="text-align: left;"><strong>'.$explotado[$rrr].' : </strong></td>';

		$valores2 .= $inicio.'<td style="text-align: left;">'.strtoupper($row[$explotadoP[$rrr]]).'</td></tr>';
		//echo trim($row[$explotadoP[$rrr]]);
		//$OOO++;
		}
					$valores2 .= '</table><hr/>';
		}
	return $valores2 ;
	}




	public function MANDA_INFORMACION3($parametros,$titulos=false,$nombretabla,$limites=false){
		/*en construccion*/
		$explotado = explode(',',$titulos);
		$cuenta = count($explotado) - 1;
		//$valores .= '<hr/><br/><table border="0" width="100%" align="center">';
		$valores=array();
		if($titulos!=false){
		//$valores .= '<tr >';
		for($rrr=0;$rrr<=$cuenta;$rrr++){
		$valores['ETIQUETA'][$rrr] = $explotado[$rrr];
		}
		//$valores .= '</tr>';
		}

		$explotadoP = explode(',',$parametros);
		$cuenta2 = count($explotadoP) - 1;

		$conn = $this->db();
		$var1 = 'select id,'.$parametros.' from '.$nombretabla.' '.$limites;
		$query = mysqli_query($conn,$var1) or die('P44'.mysqli_error($conn));
$valores2 .= '<table border="1">';		$OOO=0;	
		while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
		
			//$valores .= '<tr >';
			
		for($rrr=0;$rrr<=$cuenta2;$rrr++){
			if($OOO<=$cuenta2){
				$inicio = '<tr><td>'.$cuenta.' '.$explotado[$rrr].'</td>';
				$tr= '</tr>';
			}else{
				$inicio = '';	$tr= '</tr>';				
			}
		//ECHO $valores['VALOR'][$row['id']][$OOO] = $row[$explotadoP[$rrr]];
		//$valores['VALOR'][][$OOO] = $valores['ETIQUETA'][$rrr].' '.$row[$explotadoP[$rrr]];
		$valores2 .= $inicio.'<td>'.$row[$explotadoP[$rrr]].'</td>'.$tr;
		$OOO++;
		}
		//$valores2 .= '</tr>';
			/*foreach($row as $etiqueta => $valor){
				$valores['VALORES'][$OOO] = $valor;
				$OOO++;
			}*/
			
					//$valores .= '</tr>';
		}
		
$valores2 .= '</table>';		
		
		//$valores .= '</table><br/><hr/>';
	return $valores2 ;
	}


	public function ADJUNTA_IMAGENES_EMAIL($parametros,$nombretabla,$limites=false){
		
		$explotado = explode(',',$parametros);
		$cuenta = count($explotado);
		$valores[] = '';

		//$valores;
		$conn = $this->db();
		$var1 = 'select '.$parametros.' from '.$nombretabla.' '.$limites;
		$query = mysqli_query($conn,$var1) or die('P44'.mysqli_error($conn));
		/*excluir texto de imagenes*/
	$adjuntos = array('../manuales/6c84aa69-a18a-4ff0-886a-4644bef7bd42-1.pdf'=>'manual2.pdf');

		while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
			
			
			foreach($row as $etiqueta => $valor){
				if($valor==true){
				$valores[__ROOT2__.'/includes/archivos/'.$valor] = $valor;
				}
			}
			
			
		if($parametros!=false){

		/*for($rrr=0;$rrr<=$cuenta;$rrr++){
		 $valores[] =  array('includes/archivos/'.$row[$explotado[$rrr]],$row[$explotado[$rrr]]);
		}*/

		}
	//$valores = array($row[]);
	
		}

	return $valores ;
	
	
	}


	public function masiva($idr1){
	$conn = $this->db();
	$varquery = 'select F_ACTA_NACIMIENTO, F_COMPROBANTE_DOMICILIO, F_CONSTANCIA_SITUACION_FISCAL, F_RFC, F_CURP, F_ALTA_SEGURO_SOCIAL, F_ALTA_SEGURO_SOCIAL_EPC, F_INE_FRENTE, F_INE_REVERSO, F_FOTO_ESTADO_CUENTA_BANCARIO, F_DOCUMENTO_CREDITO_INFONAVIT, F_CARTA_DE_RECOMENDACION_1, F_CARTA_DE_RECOMENDACION_2, F_CARTA_DE_RECOMENDACION_3, F_CONSTANCIA_DE_CURSOS, F_CONSTANCIA_ESTUDIOS, F_CERTIFICADO_VACUNA_1, F_CERTIFICADO_VACUNA_2, F_CERTIFICADO_VACUNA_3, F_CERTIFICADO_VACUNA_4, F_CURRICULUM_VITAE, F_LICENCIA_CONDUCIR, F_PASAPORTE, F_VISA_AMERICANA, F_LICENCIA_DE_CONDUCIR, F_FM3_EN_CASO_DE_SER_EXTRANJERO, F_CARTILLA_LIBERADA, F_FOTO_ACTUAL, F_CONTRATO_LABORAL_EPC_1, F_CONTRATO_LABORAL_EPC_2, F_CONTRATO_LABORAL_EPC_3, F_CONTRATO_LABORAL_EPC_4, F_CONTRATO_LABORAL_EPC_5, F_CONTRATO_LABORAL_EPC_6, F_CONTRATO_LABORAL_EPC_7, F_CONTRATO_LABORAL_EPC_8, F_CONTRATO_CONF_ENTRADA_EPC, F_REGLAMENTO_FIRMADO, F_CODIGO_CONDUCTA_FIRMADO, F_POLITICAS_EMPRESA_FIRMADO, F_CARTA_RENUNCIA_1, F_CARTA_RENUNCIA_2, F_CALCULO_FINIQUITO_1, F_CALCULO_FINIQUITO_2, F_CALCULO_FINIQUITO_3, F_CONTRATO_CONF_SALIDA, F_DOCUMENTOS_LEGALES_1, F_DOCUMENTOS_LEGALES_2, F_DOCUMENTOS_LEGALES_3, F_DOCUMENTOS_LEGALES_4, F_OTROS_DOCUMENTOS_1, F_OTROS_DOCUMENTOS_2, F_OTROS_DOCUMENTOS_3, F_OTROS_DOCUMENTOS_4 from 01adjuntoscolaboradores where idRelacion = "'.$idr1.'" ';
	$varquery1a =mysqli_query($conn,$varquery);
	$fecha=date("Y-m-d-h-i-s").'.zip';
	$zip = new ZipArchive;
	$zip->open($fecha,ZipArchive::CREATE);
	$i=0;
	$obj=mysqli_fetch_array($varquery1a,MYSQLI_NUM);
	$cuenta = count($obj) - 1;
	$nombre_carpeta=__ROOT2__.'/includes/archivos/';
	for($ia=0;$ia<=$cuenta;$ia++){
		if($obj[$ia]!=''){
			$zip->addFile($nombre_carpeta.$obj[$ia],$ia.'_'.$obj[$ia]);
		}
	}
	$zip->close();
	header('Content-Type: application/zip');
	header('Content-Disposition: attachment; filename="'.$fecha.'"');
	readfile($fecha);
	@unlink($fecha);
	exit;
	}


 
	public function html($linkgenerado, $idwebc, $empresa = false) {
		return '<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Bienvenida - ' . $empresa . '</title>
</head>
<body style="margin:0; padding:0; background-color:#f2f2f2; font-family:Calibri, Arial, sans-serif;">
 
<table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color:#f2f2f2; padding:30px 0;">
<tr>
<td align="center">
 
	<table role="presentation" width="600" border="0" cellspacing="0" cellpadding="0" style="background-color:#ffffff; border-radius:8px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,0.08);">
 
		<!-- Imagen de cabecera -->
		<tr>
			<td align="center" style="background-color:#ffffff;">
				<img src="cid:ver" style="display:block; width:100%; max-width:600px;" alt="' . $empresa . '">
			</td>
		</tr>
 
		<!-- Cuerpo -->
		<tr>
			<td style="padding:35px 40px; text-align:center; color:#333333; font-size:16px; line-height:1.6;">
 
				<h2 style="color:#1a1a1a; font-size:22px; margin:0 0 20px 0;">
					¡Bienvenid@ a ' . $empresa . '!
				</h2>
 
				<p style="margin:0 0 20px 0;">
					Te pedimos por favor llenes el siguiente formulario para darte de alta en nuestro sistema de gestión de eventos y crear tu perfil y expediente interno.
				</p>
 
				<p style="margin:0 0 8px 0; font-weight:bold;">
					Te recomendamos copiar tu Usuario y tu Password:
				</p>
				<p style="margin:0 0 25px 0; background-color:#f5f5f5; border-radius:6px; padding:12px 16px; display:inline-block; font-size:15px; color:#1a1a1a;">
					' . $idwebc . '
				</p>
 
				<p style="margin:0 0 15px 0;">
					Da clic en el siguiente botón y sigue cada una de las instrucciones:
				</p>
 
				<table role="presentation" border="0" cellspacing="0" cellpadding="0" style="margin:0 auto 30px auto;">
					<tr>
						<td align="center" style="border-radius:6px; background-color:#0057b8;">
							<a href="' . $linkgenerado . '" target="_blank" style="display:inline-block; padding:14px 32px; font-size:16px; font-weight:bold; color:#ffffff; text-decoration:none; border-radius:6px;">
								Ir al sitio
							</a>
						</td>
					</tr>
				</table>
 
				<hr style="border:none; border-top:1px solid #e0e0e0; margin:30px 0;">
 
				<h3 style="color:#1a1a1a; font-size:17px; margin:0 0 12px 0;">
					¿Tienes algún problema?
				</h3>
 
				<p style="margin:0 0 20px 0; font-size:15px;">
					Escanea el código QR para enviarnos un WhatsApp, o escríbenos a alguno de los siguientes correos:<br>
					<a href="mailto:MARTHAB@EPCONVENCIONES.COM.MX" style="color:#0057b8; text-decoration:none;">MARTHAB@EPCONVENCIONES.COM.MX</a><br>
					<a href="mailto:ANABEL@EPCONVENCIONES.COM.MX" style="color:#0057b8; text-decoration:none;">ANABEL@EPCONVENCIONES.COM.MX</a><br>
					<a href="mailto:OSCAR@EVENTOS520.COM.MX" style="color:#0057b8; text-decoration:none;">OSCAR@EVENTOS520.COM.MX</a>
				</p>
 
				<p style="margin:0 0 20px 0; font-size:14px; color:#666666;">
					Horario de atención: 9am a 7pm, de lunes a viernes.<br>
					Te sugerimos tomar una foto del error que te apareció para poder ayudarte más rápido.
				</p>
 
				<img src="cid:qrproveedores" style="width:180px; margin-bottom:10px;" alt="Código QR de soporte">
 
				<p style="margin:25px 0 0 0; font-size:14px; color:#666666;">
					Asegúrate de guardar la liga en tu navegador, para que puedas entrar de forma directa de aquí en adelante.
				</p>
 
				<p style="margin:20px 0 0 0; font-size:15px; color:#1a1a1a;">
					Te deseamos una larga estancia con nosotros.
				</p>
 
			</td>
		</tr>
 
		<!-- Imagen decorativa -->
		<tr>
			<td align="center" style="background-color:#ffffff; padding:0 0 10px 0;">
				<img src="cid:munecos" style="max-width:100%;" alt="">
			</td>
		</tr>
 
		<!-- Footer -->

 
	</table>
 
</td>
</tr>
</table>
 
</body>
</html>';
	}
 


	public function html2($EXTRA1, $EXTRA2){
		return '<!DOCTYPE html>
<html>
<head>
    <title>Enviar correo HTML+CSS+Imagen+Adjunto desde Localhost</title>
	<style type="text/css">
table {text-align: center;}
tr {text-align: center;}
td {text-align: center;}
p {text-align: center;}
div {text-align: center;}
body{color:#000; font-family:Calibri; font-weight: bold; text-align:center; font-size:18px; background-color:white;}
</style>
</head>
<body>

<div class="es-content">
<table width="100%" border="0" height="100%" >
<tr>
<td><img src="cid:ver" style="width:450px;"></td>
</tr>

<tr><td style="padding-bottom:30px; padding-top:30px;">

Has recibido este correo porque solicitaste esta información.<BR/>
'.$EXTRA1.' '. $EXTRA2.'

</td></tr>


<tr><td  style="padding-bottom:30px; padding-top:30px;">

EVENTOS PROMOCIONES Y CONVENCIONES.<br>
Insurgentes Sur 1377 - 3 Col. Insurgentes Mixcoac Benito Juárez, CDMX, México C.P. 03920<br>
</td></tr>

</table>
</div>
</body>
</html>';
	}

	public function htmltoken($linkgenerado, $idwebc,$empresa=false){
	
		return '<!DOCTYPE html>
<html>
<head>
    <title>Enviar correo HTML+CSS+Imagen+Adjunto desde Localhost</title>
	<style type="text/css">
table {text-align: center;}
tr {text-align: center;}
td {text-align: center;}
p {text-align: center;}
div {text-align: center;}
body{color:#000; font-family:Calibri; font-weight: bold; text-align:center; font-size:18px; background-color:white;}
</style>
</head>
<body>

<div class="es-content">
<table width="100%" border="0" height="100%" >
<tr>
<td><img src="cid:ver"></td>
</tr>

<tr><td style="padding-bottom:30px; padding-top:30px;">

HAS RECIBIDO ESTE CORREO DEBIDO UNA DOBLE AUTENTICACIÓN.<BR>
ABAJO TE ADJUNTAMOS EL TOKEN QUE DEBERÁS DE PEGAR EN EL CRM <BR>

TOKEN: <STRONG> '.$idwebc.' </STRONG>
<BR>
SI TIENES ALGÚN PROBLEMA, MANDA UN CORREO A oscar@eventos520.com.mx Y CON GUSTO TE APOYAREMOS, TE SUGERIMOS TOMAR UNA FOTO DEL ERROR QUE TE SALIÓ.
<BR>
GRACIAS.



</td></tr>


<tr>
<td><img src="cid:munecos"></td>
</tr>


<tr><td  style="padding-bottom:30px; padding-top:30px;">

EVENTOS PROMOCIONES Y CONVENCIONES.<br>
Insurgentes Sur 1377 - 3 Col. Insurgentes Mixcoac Benito Juárez, CDMX, México C.P. 03920<br>
</td></tr>

</table>
</div>
</body>
</html>';
	}
		
public function revisar_usuario($conn,$USUARIO_CRM,$id){
    if($id == ''){ return ''; }
    $var1 = 'select id from 01empresa where id = "'.$id.'" ';
    $query = mysqli_query($conn,$var1) or die('P44'.mysqli_error($conn));
    $row = mysqli_fetch_array($query, MYSQLI_ASSOC);
    return $row['id'];
}	

	public function revisar_usuario_existente($conn,$USUARIO_CRM){
		$var1 = 'select id from 01empresa where USUARIO_CRM =  "'.trim($USUARIO_CRM).'" limit 1';
		$query = mysqli_query($conn,$var1) or die('P44'.mysqli_error($conn));
		$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
		return $row['id'];
	}	

	public function revisar_01informacionpersonal01($conn,$id){
		$var1 = 'select idRelacion from 01informacionpersonal where idRelacion =  "'.$id.'" ';
		$query = mysqli_query($conn,$var1) or die('P44'.mysqli_error($conn));
		$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
		return $row['idRelacion'];
	}


	public function revisar_01adjuntoscolaboradores01($conn,$id){
		$var1 = 'select idRelacion from 01adjuntoscolaboradores where idRelacion =  "'.$id.'" ';
		$query = mysqli_query($conn,$var1) or die('P44'.mysqli_error($conn));
		$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
		return $row['idRelacion'];
	}

	public function revisar_01empresapertenece($conn,$idreviwcoordina,$id_empresa){
		$var1 = 'select idRelacionC from 01empresapertenece where idRelacionC =  "'.$idreviwcoordina.'" ';
		$query = mysqli_query($conn,$var1) or die('P44'.mysqli_error($conn));
		$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
		return $row['idRelacionC'];
	}

	public function revisar_01empresapertenece2($id){
		$conn = $this->db();
		$var1 = 'select idRelacionE from 01empresapertenece where idRelacionC =  "'.$id.'" ';
		$query = mysqli_query($conn,$var1) or die('P44'.mysqli_error($conn));
		$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
		return $row['idRelacionE'];
	}

	public function revisar_01empresapertenece3($id){
	
	$conn = $this->db();
		
	if($this->ambiente()=='PROD'){
	$link_generado = 'https://epcinn.com/crm/sistemaPROD/includes/archivos/';
	}
	elseif($this->ambiente()=='PROD2'){
	$link_generado = 'https://epcinn.com/crm/sistemaPROD2/includes/archivos/';
	}
	elseif($this->ambiente()=='PROD3'){
	$link_generado = 'https://epcinn.com/crm/SISTEMA_PRUEBAS/includes/archivos/';		
	}
	else{
	$link_generado = 'https://epcinn.com/pruebas/crm2/main-files/syn-ui/sistemaPRUEBAS/includes/archivos/';	
	}
		
		$var1 = 'select imagen, idRelacionE from 01empresapertenece where idRelacionC =  "'.$_SESSION["idempermiso"].'" ';
		$query = mysqli_query($conn,$var1) or die('P44'.mysqli_error($conn));
		$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
		$rowimagen = ISSET($row['imagen'])?$row['imagen']:'';
		$idRelacionE = ISSET($row['idRelacionE'])?$row['idRelacionE']:'';
		
		if( $idRelacionE == ''){
			
		$variablequery1 = "select id from 03datosdelaempresa where NCE_OBSERVACIONES = 'EP' or NCE_OBSERVACIONES = 'EPC'  ";
		$arrayquery1 = mysqli_query($conn,$variablequery1);
		$row2 = mysqli_fetch_array($arrayquery1, MYSQLI_ASSOC);
		
		$variablequery = "select ADJUNTAR_LOGO_INFORMACION from 03docs_info_fiscal where idRelacion = '".$row2['id']."'  ";
		$arrayquery = mysqli_query($conn,$variablequery);
		$row3 = mysqli_fetch_array($arrayquery, MYSQLI_ASSOC);
		return $link_generado.$row3['ADJUNTAR_LOGO_INFORMACION'];
		
		}else{
		$variablequerylogo="select ADJUNTAR_LOGO_INFORMACION from 03docs_info_fiscal where idRelacion = '".$idRelacionE."' ";
		$arrayquery = mysqli_query($conn,$variablequerylogo);
		$row3logo = mysqli_fetch_array($arrayquery, MYSQLI_ASSOC);

		return $link_generado.$row3logo['ADJUNTAR_LOGO_INFORMACION'];
		}
		

	}

	public function variablesusuario($id){
		$conn = $this->db();
		$var = 'select *,01empresa.id as id1 from 
		01empresa, 01informacionpersonal, 01adjuntoscolaboradores
		where 
		01informacionpersonal.idRelacion = 01empresa.id and
		01adjuntoscolaboradores.idRelacion = 01empresa.id
		and 01empresa.id='.$id.' order by 01empresa.id desc ';
		$query = mysqli_query($conn,$var);
		return $row = mysqli_fetch_array($query, MYSQLI_ASSOC);
	}	

	public function estatusadjuntos($identificador,$conn){

		$var = 'select ESTATUS_CRM_ACTIVOBAJA from 01adjuntoscolaboradores where idRelacion ="'.$identificador.'" ; ';
		
		$query = mysqli_query($conn,$var);
		$row = mysqli_fetch_array($query);
		return $row['ESTATUS_CRM_ACTIVOBAJA'];
	}

	public function listado(){
		$conn = $this->db();

		$var = 'select *,01empresa.id as id1qwa from 
		01empresa, 01informacionpersonal 
		where 
		01informacionpersonal.idRelacion = 01empresa.id
		order by NOMBRE_1 asc';
		
		

		
		
		$query = mysqli_query($conn,$var);
$var_html1 = "<div class='mb-3'>
    <label for='buscadorNombreColaborador' class='form-label small fw-bold text-muted mb-1'>
        <ion-icon name='search-outline' class='align-middle'></ion-icon> BUSCAR POR NOMBRE
    </label>
    <div class='input-group input-group-sm shadow-sm' style='max-width: 320px;'>
        <span class='input-group-text bg-white border-end-0'>
            <ion-icon name='person-outline'></ion-icon>
        </span>
        <input type='search' 
               class='form-control buscador-nombre-colaborador border-start-0 ps-0' 
               id='buscadorNombreColaborador' 
               placeholder='Nombre o apellido...' 
               autocomplete='off' 
               aria-controls='listadoColaboradoresTable'
               style='font-size: 0.85rem;'>
    </div>
    <div class='form-text' aria-live='polite' id='resultadoBusquedaColaborador' style='font-size: 0.75rem;'></div>


		<table class='table mb-0 table-striped' id='listadoColaboradoresTable'>
		<tr>
		
		<td>NOMBRE</td>
		<td>APELLIDOS</td>
		<td>USUARIO CRM</td>
		<td>ESTATUS</td>
		<!--<td>ACTUALIZA DATOS</td>-->
		<td>CORREO</td>";
		
		if($this->variablespermisos('','listado','modificar')=='si'){ 
			$var_html1 .=  "<td>MODIFICAR</td>";
		}
		
		if($this->variablespermisos('','listado','borrar')=='si'){ 
			$var_html1 .=  "<td>BORRAR</td>";
		}
		if($this->variablespermisos('','vereventos','ver')=='si'){ 
			$var_html1 .=  "<td>VER TODOS<br> LOS EVENTOS</td>";
		}
		
		echo $var_html1 .=  "</tr>";
		
		
		
	while($row = mysqli_fetch_array($query)){
		//USUARIO_CRM
		$var_html .= '<tr>
		<td><a href="colaboradores.php?id='.$row['id1qwa'].'">'.$row['NOMBRE_1'].' '.$row['NOMBRE_2'].'</a></td>
		<td>'.$row['APELLIDO_PATERNO'].' '.$row['APELLIDO_MATERNO'].'</td>
		<td>'.$row['USUARIO_CRM'].'</td>
		<td>'.$this->estatusadjuntos($row['id1qwa'],$conn).'</td>
		<!--<td><a href="'.$_SERVER['PHP_SELF'].'?id='.$row['id1qwa'].'">'.$row['NOMBRE_1'].' '.$row['NOMBRE_2'].'</a></td>	-->
		<td>'.$row['id1qwa'].' '.$row['CORREO_4'].' '.$row['STATUS_CARGA_INFORMACION'].'</td>';
		if($this->variablespermisos('','listado','modificar')=='si'){ 
			$var_html .= '<td>		
			<input type="button" name="view" value="MODIFICAR" id="'.$row['id1qwa'].'" class="btn btn-info btn-xs view_datacolaboradoresmodifica">
			</td>';
		}
		if($this->variablespermisos('','listado','borrar')=='si'){ 
			$var_html .= '<td>		
			<input type="button" name="view" value="BORRAR" id="'.$row['id1qwa'].'" class="btn btn-info btn-xs view_datacolaborBORRAR">
			</td>';
		}
		if($this->variablespermisos('','vereventos','ver')=='si'){ 
			$checked = '';
			if($row["CHECKBOX"]=='si'){$checked = 'checked';}
			$var_html .= '<td>
			<center>
			<span id="pasarapersona67'.$row['id1qwa'].'"></span>&nbsp;&nbsp;
			<input type="checkbox" style="width:40PX;" class="form-check-input" id="pasarapersona45'.$row['id1qwa'].'" name="pasarapersona45'.$row['id1qwa'].'" value="'.$row['id1qwa'].'"  onclick="pasara1_persona45('.$row['id1qwa'].')" '.$checked.'/>		  
			</center>
			</td>';
		}
		$var_html .= '</tr>';
	}
	$var_html .= "</table>
	<script>
	(function () {
		var buscador = document.getElementById('buscadorNombreColaborador');
		var tabla = document.getElementById('tablaListadoColaboradores');

		if (!buscador || !tabla) {
			return;
		}

		buscador.addEventListener('input', function () {
			var busqueda = buscador.value.toLocaleLowerCase().trim();
			var filas = tabla.querySelectorAll('tr:not(:first-child)');

			filas.forEach(function (fila) {
				var celdas = fila.querySelectorAll('td');
				var nombreCompleto = ((celdas[0] ? celdas[0].textContent : '') + ' ' +
					(celdas[1] ? celdas[1].textContent : '')).toLocaleLowerCase();
				fila.style.display = nombreCompleto.indexOf(busqueda) !== -1 ? '' : 'none';
			});
		});
	}());
	</script>";
	echo 	$var_html ;	
	}	

	public function Listado_colaborador1($id_empresa){
	$conn = $this->db();
		$query5 = 'select *,01empresa.id as id1qwa from 
		01empresa, 01informacionpersonal 
		where 
		01informacionpersonal.idRelacion = 01empresa.id and
		01empresa.id = "'.$id_empresa.'"
		'; 
	$results5 = mysqli_query($conn,$query5) or die( mysqli_error($conn));
		return	$results5;

	}




	public function revisar_02TODOS2($usuario,$nommbrerazon,$rfc,$P_NOMBRE_FISCAL_RS_EMPRESA){
		$conn = $this->db();
		$var1 = 'select *,02usuarios.id AS IDDD from  02usuarios, 02direccionproveedor1 WHERE 
		02usuarios.id = 02direccionproveedor1.idRelacion and 
		02usuarios.usuario= "'.$usuario.'" and
		02usuarios.nommbrerazon="'.$nommbrerazon.'" and 
		02direccionproveedor1.P_RFC_MTDP= "'.$rfc.'" and 
		02direccionproveedor1.P_NOMBRE_FISCAL_RS_EMPRESA= "'.$P_NOMBRE_FISCAL_RS_EMPRESA.'"
		';
		$query = mysqli_query($conn,$var1) or die('P44'.mysqli_error($conn));
		$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
		return $row['IDDD'];
	}
	
	public function revisar_02USUARIO2($usuario){
		$conn = $this->db();
		$var1 = 'select id from 02usuarios where usuario =  "'.$usuario.'" ';
		$query = mysqli_query($conn,$var1) or die('P44'.mysqli_error($conn));
		$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
		return $row['id'];
	}

	public function revisar_02direccionproveedor12($idp){
		$conn = $this->db();
		$var1 = 'select id from 02direccionproveedor1 where idRelacion =  "'.$idp.'" ';
		$query = mysqli_query($conn,$var1) or die('P44'.mysqli_error($conn));
		$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
		return $row['id'];
	}

	public function PROVEEDOREMPRESA2($IDRELACIONP,$IDRELACIONC){
		if($IDRELACIONC != ''){
		$conn = $this->db();				
		$variablequery1 = "delete from 02empresarelacion where idRelacionP = '".$IDRELACIONP."' ";
		mysqli_query($conn,$variablequery1);
		

		$variablequery2 = "insert into 02empresarelacion(idRelacionP,idRelacionC)
		values('".$IDRELACIONP."','".$IDRELACIONC."')";
		mysqli_query($conn,$variablequery2); 

		}else{
		}
	}

	public function ingresar_colaborador_a_proveedor($conn,$usuario,$nommbrerazon,$rfc,$P_NOMBRE_FISCAL_RS_EMPRESA,$contrasenia,$email,$id_empresa, $idwebproveedor ){
		
		if($this->revisar_02TODOS2($usuario,$nommbrerazon,$rfc,$P_NOMBRE_FISCAL_RS_EMPRESA)>=1){
			
		}
		else{

		}



		$existe1 = $this->revisar_02USUARIO2($usuario);
		$idwebc ='';
		/*alter TABLE `02usuarios` add column IdColaborador int(50) comment 'campo para reconocer al usuario dentro del proveedor'*/
			if($existe1>=1){

		}else{
		mysqli_query($conn,"insert into 02usuarios (
		prefijo,
		usuario, nommbrerazon,
		contrasenia, email, PERMISOS, IdColaborador) values (
		'AdminPR',
		'".$usuario."' , 
		'".$nommbrerazon."' , 
		'".$contrasenia."' , 
		'".$email."',
		'PROVEEDORES',
		'".$idwebproveedor."'
		); ") or die('P160'.mysqli_error($conn));
		$idwebc = mysqli_insert_id($conn);
		$this->PROVEEDOREMPRESA2($idwebc,$id_empresa);		
		}
         

	
		$existe2 = $this->revisar_02direccionproveedor12($idwebc);		
		if($existe2>=1){
		
		}else{

		mysqli_query($conn,"insert into 02direccionproveedor1 
		( P_NOMBRE_COMERCIAL_EMPRESA, P_NOMBRE_FISCAL_RS_EMPRESA, idRelacion, P_RFC_MTDP) values 
		( '".$nommbrerazon."' ,'".$P_NOMBRE_FISCAL_RS_EMPRESA."' ,  '".$idwebc."','".$rfc."'  ); ") or die('P1401'.mysqli_error($conn));

		}

	}

	/*FIN PARA AGREGAR COLABORADOR A PROVEEDOR*/



	public function guardar_listado($USUARIO_CRM , $CONTRASENIA_CRM , $NIVEL_ACCESO_CRM , $PUESTO , $DEPARTAMENTO , $CORREO_1 ,$CORREO_4, $NOMBRE_1 ,$NOMBRE_2, $APELLIDO_PATERNO , $APELLIDO_MATERNO , $listado , $STATUS_CARGA_INFORMACION, $PERMISOS,$id_empresa, $idrow,$mandacorreo ){
	$conn = $this->db();
	if($this->ambiente()=='PROD'){
	$link_generado = 'https://epcinn.com/crm/sistemaPROD/?salir=1';	
	}elseif($this->ambiente()=='PROD2'){
	$link_generado = 'https://epcinn.com/crm/sistemaPROD2/?salir=1';	
	}
	elseif($this->ambiente()=='PROD3'){
	$link_generado = 'https://epcinn.com/crm/SISTEMA_PRUEBAS/?salir=1';		
	}else{
	$link_generado = 'https://www.epcinn.com/pruebas/crm2/main-files/syn-ui/sistemaPRUEBAS/?salir=1';
	}
	$EMAILnombre = array($CORREO_1=>$NOMBRE_1 .' '.$APELLIDO_PATERNO);
	
	
	$empresa="";
		$query5 = 'SELECT NFE_INFORMACION FROM 
		`03datosdelaempresa` WHERE 
		03datosdelaempresa.id = "'.$id_empresa.'" ';
		$results5 = mysqli_query($conn,$query5) or die( mysqli_error($conn));
		$row5 = mysqli_fetch_array($results5);/**/
		$empresa=$row5['NFE_INFORMACION'];
		
		
	$Subject = 'Favor de Completar el Formulario';
	$conexion = new herramientas();
	
	$usuario_existente = $this->revisar_usuario_existente($conn,$USUARIO_CRM);
	if($idrow=='' && $usuario_existente!=''){
		echo '<span style="color:red; font-size:25px;font-weight:bold;">EL USUARIO CRM YA EXISTE, FAVOR DE USAR OTRO.</span>';
		return;
	}
	if($idrow!='' && $usuario_existente!='' && $usuario_existente!=$idrow){
		echo '<span style="color:red; font-size:25px;font-weight:bold;">EL USUARIO CRM YA ESTÁ ASIGNADO A OTRO COLABORADOR.</span>';
		return;
	}

	$existe = $this->revisar_usuario($conn,$USUARIO_CRM, $idrow);
	$idwebc = '';
	if($existe==''){
	 $query = "insert into 01empresa (
	USUARIO_CRM, CONTRASENIA_CRM,  
	NIVEL_ACCESO_CRM, PUESTO, DEPARTAMENTO, CORREO_1,CORREO_4, PERMISOS) values ( 
	'".$USUARIO_CRM."','".$CONTRASENIA_CRM."',
	'".$NIVEL_ACCESO_CRM."','".$PUESTO."', '".$DEPARTAMENTO."', '".$CORREO_1."', '".$CORREO_4."', '".$PERMISOS."'
	);";

	mysqli_query($conn,$query) or die('P744'.mysqli_error($conn));
	$idwebc = mysqli_insert_id($conn);
	

	
		
	$rfc = '';
	$this->ingresar_colaborador_a_proveedor($conn,$USUARIO_CRM,$NOMBRE_1,$rfc,$NOMBRE_1,$CONTRASENIA_CRM,$CORREO_1,$id_empresa, $idwebc);

	}else{
	 $query1 = "update 01empresa set 
	USUARIO_CRM = '".$USUARIO_CRM."', 
	CONTRASENIA_CRM = '".$CONTRASENIA_CRM."', 
	NIVEL_ACCESO_CRM = '".$NIVEL_ACCESO_CRM."',
	PUESTO = '".$PUESTO."',
	DEPARTAMENTO = '".$DEPARTAMENTO."',
	CORREO_1 = '".$CORREO_1."',
	CORREO_4 = '".$CORREO_4."',
	PERMISOS = '".$PERMISOS."'
	where id = '".$existe."' ;  ";
	
	mysqli_query($conn,$query1) or die ('P45'.mysqli_error($conn));
	$html = $this->html($link_generado,'Usuario: '.$USUARIO_CRM.' Password: '.$CONTRASENIA_CRM, $empresa);
		//funcion agregar DE COLABORADOR A PROVEEDOR 
	$rfc = '';
	$this->ingresar_colaborador_a_proveedor($conn,$USUARIO_CRM,$NOMBRE_1,$rfc,$NOMBRE_1,$CONTRASENIA_CRM,$CORREO_1,$id_empresa, $existe);
	}	

	if($existe==''){$idreviwcoordina =$idwebc;}else{$idreviwcoordina =$existe;}
	
	$existe2 = $this->revisar_01informacionpersonal01($conn,$idreviwcoordina);	
	if($existe2==''){
	$query = "insert into 01informacionpersonal (
	NOMBRE_1, NOMBRE_2 ,APELLIDO_PATERNO, APELLIDO_MATERNO,CORREO_1, idRelacion 
	) values ( 
	'".$NOMBRE_1."', '".$NOMBRE_2."', '".$APELLIDO_PATERNO."', '".$APELLIDO_MATERNO."', '".$CORREO_1."', '".$idreviwcoordina."' );";
	mysqli_query($conn,$query) or die('P745'.mysqli_error($conn));
	}else{
	$query1 = "update 01informacionpersonal set 
	NOMBRE_1 = '".$NOMBRE_1."', 
	NOMBRE_2 = '".$NOMBRE_2."', 
	APELLIDO_PATERNO = '".$APELLIDO_PATERNO."', 
	CORREO_1 = '".$CORREO_1."', 
	APELLIDO_MATERNO = '".$APELLIDO_MATERNO."'
	where idRelacion = '".$idreviwcoordina."' ;  ";
	mysqli_query($conn,$query1) or die ('P45'.mysqli_error($conn));
	}
	$existe3 = $this->revisar_01adjuntoscolaboradores01($conn,$idreviwcoordina);
	if($existe3==''){
	$query = "insert into 01adjuntoscolaboradores (
	STATUS_CARGA_INFORMACION, ESTATUS_CRM_ACTIVOBAJA, idRelacion 
	) values ( 
	'".$STATUS_CARGA_INFORMACION."','ACTIVO', '".$idreviwcoordina."' );";
	mysqli_query($conn,$query) or die('P745'.mysqli_error($conn));
	'<span style="color:green; font-size:25px;font-weight:bold;">INGRESADO</span>';
	}else{
	$query1 = "update 01adjuntoscolaboradores set 
	STATUS_CARGA_INFORMACION = '".$STATUS_CARGA_INFORMACION."'
	where idRelacion = '".$idreviwcoordina."' ;  ";
	mysqli_query($conn,$query1) or die ('P45'.mysqli_error($conn));
	'<span style="color:green; font-size:25px;font-weight:bold;">ACTUALIZADO</span>';
	}
	
	$existe4 = $this->revisar_01empresapertenece($conn,$idreviwcoordina,$id_empresa);


	if($mandacorreo=='si'){

	$html = $this->html($link_generado,'Usuario: '.$USUARIO_CRM.' Password: '.$CONTRASENIA_CRM, $empresa);
	$smtp = $conexion->array_smtp_ID($conn,$id_empresa);
	$idlogo = $smtp['idRelacion'];
	$logo = $conexion->variables_informacionfiscal_logo2_ID($conn,$idlogo);
	}

	if($existe4==''){
	$query = "insert into 01empresapertenece (
	idRelacionC, idRelacionE, imagen 
	) values ( 
	'".$idreviwcoordina."','".$id_empresa."','".$logo."' );";
	mysqli_query($conn,$query) or die('P745'.mysqli_error($conn));
	 '<span style="color:green; font-size:25px;font-weight:bold;">INGRESADO</span>';
	}else{
	$query1 = "update 01empresapertenece set 
	idRelacionE = '".$id_empresa."',
	imagen  = '".$logo."'
	where idRelacionC = '".$idreviwcoordina."' ;  ";
	mysqli_query($conn,$query1) or die ('P45'.mysqli_error($conn));
	 '<span style="color:green; font-size:25px;font-weight:bold;">ACTUALIZADO</span>';
	}

	if($mandacorreo=='si'){
	$embebida = array('../includes/archivos/'.$logo => 'ver');
	echo '<span style="color:green; font-size:25px;font-weight:bold;text-transform: uppercase;">ACTUALIZADO Y '.$conexion->email($EMAILnombre, $html, $adjuntos, $embebida, $Subject,$smtp).'</span>';
	}else{
	echo '<span style="color:green; font-size:25px;font-weight:bold;">ACTUALIZADO </span>';		
	}


}


}


	class colaboradores extends listadocolaboradores{

/**//**//**//**//*FUNCION PARA EXCEL*//**//**//**//**/

	public function descargararchivo($archivoadjunto){

		if($archivoadjunto === '' || $archivoadjunto === null || $archivoadjunto == '2'){
			return "";
		}

		$extension = strtolower(pathinfo($archivoadjunto, PATHINFO_EXTENSION));

		if(in_array($extension, array('xlsx','csv','txt','xls'), true)){
			$urldescarga = 'descargar.php?archivo='.$archivoadjunto;
		}else{
			$urldescarga = $archivoadjunto;
		}

		// Correos de Outlook (.msg): el navegador no los puede mostrar, se leen en el servidor
		if($extension === 'msg'){
			return "<a target='_blank' rel='noopener noreferrer' href='ver_msg.php?archivo=".rawurlencode($archivoadjunto)."'>Ver correo</a><br/>"
				."<a href='includes/archivos/".rawurlencode($archivoadjunto)."'>Descargar</a><br/>";
		}

		return "<a target='_blank'  href='includes/archivos/".$urldescarga."'>Visualizar!</a><br/>";
	}





/**//**//**//**//*DATOS COLABORADORES (DATOS)*//**//**//**//**/

	public function variableCOORDINADOR2(){
		$conn = $this->db();
		$variablequery = "select * from 01adjuntoscolaboradores where idRelacion = '".$_SESSION['id']."' ";
		$arrayquery = mysqli_query($conn,$variablequery);
		return $row = mysqli_fetch_array($arrayquery, MYSQLI_ASSOC);		
	}

	public function revisar_COORDINADOR2(){
		$conn = $this->db();
		$var1 = 'select id from 01adjuntoscolaboradores where idRelacion =  "'.$_SESSION['id'].'" ';
		$query = mysqli_query($conn,$var1) or die('P44'.mysqli_error($conn));
		$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
		return $row['id'];
	}

	public function guardar_COORDINADOR2(  $url , $ESTATUS_CRM_ACTIVOBAJA , $STATUS_CARGA_INFORMACION , $DATOS_COLABORADORES){
		$conn = $this->db();
		$existe = $this->revisar_empresa();
		$session = isset($_SESSION['id'])?$_SESSION['id']:'';
		if($session != ''){
			
		$var1 = "update 01adjuntoscolaboradores set url = '".$url."' , ESTATUS_CRM_ACTIVOBAJA = '".$ESTATUS_CRM_ACTIVOBAJA."' , STATUS_CARGA_INFORMACION = '".$STATUS_CARGA_INFORMACION."' where idRelacion = '".$session."' ; ";
		
		$var2 = "insert into 01adjuntoscolaboradores ( url, ESTATUS_CRM_ACTIVOBAJA, STATUS_CARGA_INFORMACION, DATOS_COLABORADORES, idRelacion) values ( '".$url."' , '".$ESTATUS_CRM_ACTIVOBAJA."' , '".$STATUS_CARGA_INFORMACION."' , '".$DATOS_COLABORADORES."' , '".$session."' );  ";			
			
		if($existe>=1){		

		mysqli_query($conn,$var1) or die('P156'.mysqli_error($conn));
		return "ACTUALIZADO";
		}else{
		mysqli_query($conn,$var2) or die('P160'.mysqli_error($conn));
		return "INGRESADO";
		}
		}		
	}


/**//**//**//**//*DATOS COLABORADORES (ADJUNTOS)*//**//**//**//**/

	public function contadorporcentaje($ROW,$ROW2){
		
		foreach($ROW2 as $etiqueta => $valor){
		unset($ROW[$etiqueta]);
		}
		unset($ROW['idRelacion']);
		unset($ROW['id']);
		$total = isset($ROW)?count($ROW):0;
		$vacios=0;
		if($total==0){return '1';}
		foreach($ROW as $etiqueta => $valor){
			//$valor = trim($valor);
			if($valor!=''){$vacios++;}
			 //$vacios += isset($valor)?1:0;
		}
		
		$subtotal = $vacios * 100;
		$gtotal = $subtotal / $total;
		return  round($gtotal,0);
		
	}

	public function variablesadjuntos(){
		$conn = $this->db();
		$variablequery = "select * from 01adjuntoscolaboradores where idRelacion = '".$_SESSION['id']."' ";
		$arrayquery = mysqli_query($conn,$variablequery);
		return $row = mysqli_fetch_array($arrayquery,MYSQLI_ASSOC);		
	}

	public function revisar_adjuntoscolaboradores($nombretabla){
		$conn = $this->db();
		$var1 = 'select id from '.$nombretabla.' where idRelacion =  "'.$_SESSION['id'].'" ';
		$query = mysqli_query($conn,$var1) or die('P44'.mysqli_error($conn));
		$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
		return $row['id'];
	}

	public function sologuardar($campo,$nuevonombre,$nombretabla){
		$conn = $this->db();
		$existe = $this->revisar_adjuntoscolaboradores($nombretabla);
		$session = isset($_SESSION['id'])?$_SESSION['id']:'';
		if($session != ''){
		if($existe>=1){
		$variablequery1 = "update ".$nombretabla." set ".$campo." = '".$nuevonombre."' where idRelacion = '".$_SESSION['id']."' ";
		mysqli_query($conn,$variablequery1);
		}else{
		$variablequery2 = "insert into ".$nombretabla." 
		(idRelacion) values ('".$_SESSION['id']."') ";
		$variablequery3 = "update ".$nombretabla." set ".$campo." = '".$nuevonombre."' where idRelacion = '".$_SESSION['id']."' ";
		mysqli_query($conn,$variablequery2);
		mysqli_query($conn,$variablequery3);
		}
		}
	}



	public function revisar_adjuntoscolaboradores5($nombretabla,$where){
		$conn = $this->db();
		$var1 = 'select id from '.$nombretabla.'  '.$where;
		$query = mysqli_query($conn,$var1) or die('P44'.mysqli_error($conn));
		$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
		return $row['id'];
	}
//$archivo,$nuevonombre,$nombretabla,$where,$idpost
	public function sologuardar5($campo,$nuevonombre,$nombretabla,$where,$idpost){
		$conn = $this->db();
		$existe = $this->revisar_adjuntoscolaboradores5($nombretabla,$where);
		$session = isset($idpost)?$idpost:'';
		if($session != ''){
		if($existe>=1){
		 $variablequery1 = "update ".$nombretabla." set ".$campo." = '".$nuevonombre."'  ".$where." ";
		mysqli_query($conn,$variablequery1);
		}else{
		 $variablequery2 = "insert into ".$nombretabla." 
		( idRelacion,".$campo.") values ('".$idpost."','".$nuevonombre."') ";
	
		mysqli_query($conn,$variablequery2);

		}
		}
	}






	public function revisar_adjuntoscolaboradores2($nombretabla){
		$conn = $this->db();
		$var1 = 'select id from '.$nombretabla.' where id =  "'.$_SESSION['id'].'" ';
		$query = mysqli_query($conn,$var1) or die('P44'.mysqli_error($conn));
		$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
		return $row['id'];
	}

	public function sologuardar2($campo,$nuevonombre,$nombretabla){
		$conn = $this->db();
		$existe = $this->revisar_adjuntoscolaboradores2($nombretabla);
		$session = isset($_SESSION['id'])?$_SESSION['id']:'';
		if($session != ''){
		if($existe>=1){
		$variablequery1 = "update ".$nombretabla." set ".$campo." = '".$nuevonombre."' where id = '".$_SESSION['id']."' ";
		mysqli_query($conn,$variablequery1);
		}else{
		$variablequery2 = "insert into ".$nombretabla." 
		(id,idRelacion) values ('".$_SESSION['id']."','".$_SESSION['id']."') ";
		
		$variablequery3 = "update ".$nombretabla." set ".$campo." = '".$nuevonombre."' where id = '".$_SESSION['id']."' ";
		mysqli_query($conn,$variablequery2);
		mysqli_query($conn,$variablequery3);
		}
		}
	}

	public function sologuardar3($campo,$nuevonombre,$nombretabla,$idpost){
		$conn = $this->db();
		
		$variablequery1 = "update ".$nombretabla." set ".$campo." = '".$nuevonombre."' where id = '".$idpost."' ";
		mysqli_query($conn,$variablequery1);
		
		}

	
	public function sologuardar4($campo,$nuevonombre,$nombretabla,$idpost){
		$conn = $this->db();
		$variablequery2 = "insert into ".$nombretabla." 
		(idRelacion,".$campo.") values (".$idpost.",'".$nuevonombre."') ";
		mysqli_query($conn,$variablequery2);
		}

	public function sologuardar6($campo,$nuevonombre,$nombretabla,$idpost,$idTemporal=false){
		$conn = $this->db();
		if($idTemporal==false){$idTemporal2 ='si';}else{$idTemporal2 =$idTemporal;}
		$variablequery2 = "insert into ".$nombretabla." 
		(idRelacion,".$campo.",idTemporal ) values (".$idpost.",'".$nuevonombre."','".$idTemporal2."') ";
		mysqli_query($conn,$variablequery2);		
		return $nuevonombre;
		}

	public function sologuardar6_usuario($campo,$nuevonombre,$nombretabla,$idpost,$idTemporal,$idusuario){
		$conn = $this->db();
		$ruta = __ROOT2__.'/includes/archivos/'; // ✅ fix: $ruta estaba indefinida

		$var2 = "SELECT * FROM ".$nombretabla." WHERE 
		`idRelacionU` = '".$idusuario."' and TIPOARCHIVO = 'xml' and idTemporal = 'si' ";
		$QUERYVAR2 = mysqli_query($conn,$var2) or die('P44'.mysqli_error($conn));
		while($row = mysqli_fetch_array($QUERYVAR2, MYSQLI_ASSOC)){
			if( file_exists($ruta.$row['ADJUNTAR_FACTURA_XML']) ){
				unlink($ruta.$row['ADJUNTAR_FACTURA_XML']);
			}
		}
		$var3 = "DELETE FROM ".$nombretabla." WHERE 
		`idRelacionU` = '".$idusuario."' and 
		TIPOARCHIVO = 'xml' and idTemporal = 'si' ";
		mysqli_query($conn,$var3) or die('P44'.mysqli_error($conn));		

		if($idTemporal==false){$idTemporal2 ='si';}else{$idTemporal2 =$idTemporal;}
		$variablequery2 = "insert into ".$nombretabla." 
		(idRelacion,".$campo.",idTemporal, idRelacionU, TIPOARCHIVO  ) values (".$idpost.",'".$nuevonombre."','".$idTemporal2."','".$idusuario."', 'xml') ";
		mysqli_query($conn,$variablequery2);		
		return $nuevonombre;
		}
		
	public function sologuardar7($campo,$nuevonombre,$nombretabla,$idpost){
		$conn = $this->db();
		$variablequery2 = "insert into ".$nombretabla." 
		(idRelacion,".$campo.",idTemporal ) values (".$idpost.",'".$nuevonombre."','si') ";
		mysqli_query($conn,$variablequery2);
		}
		
	public function sologuardar8($campo,$nuevonombre,$nombretabla,$idpost,$idTemporal=false,$idTemporalU=false){
		$conn = $this->db();
		if($idTemporal==false){$idTemporal2 ='si';}else{$idTemporal2 =$idTemporal;}
		$variablequery2 = "insert into ".$nombretabla." 
		(idRelacion,".$campo.",idTemporal, idRelacionU, TIPOARCHIVO ) values (".$idpost.",'".$nuevonombre."','".$idTemporal2."','".$idTemporalU."','OTRO') ";
		mysqli_query($conn,$variablequery2);		
		return $nuevonombre;
		}
		
private function sanitizarNombreArchivo($nombrebase) {

    // Decodificar %3F, %20, etc.
    $nombrebase = urldecode($nombrebase);

    // Corregir problemas de codificación UTF-8 / latin1
    if (function_exists('mb_check_encoding') && !mb_check_encoding($nombrebase, 'UTF-8')) {
        $nombrebase = mb_convert_encoding($nombrebase, 'UTF-8', 'ISO-8859-1');
    }

    // Limpiar caracteres inválidos UTF-8
    if (function_exists('iconv')) {
        $nombrebase = iconv('UTF-8', 'UTF-8//IGNORE', $nombrebase);
    }

    // Quitar caracteres conflictivos, permitir acentos y ñ
    $nombrebase = preg_replace(
        '/[^a-zA-Z0-9_\-áéíóúÁÉÍÓÚñÑüÜ]/u',
        '_',
        $nombrebase
    );

    // Colapsar guiones bajos múltiples
    $nombrebase = preg_replace('/_+/', '_', $nombrebase);

    // Quitar _ al inicio y final
    $nombrebase = trim($nombrebase, '_');

    // Limitar longitud
    if (function_exists('mb_strlen')) {
        if (mb_strlen($nombrebase, 'UTF-8') > 60) {
            $nombrebase = mb_substr($nombrebase, 0, 60, 'UTF-8');
        }
    } else {
        if (strlen($nombrebase) > 60) {
            $nombrebase = substr($nombrebase, 0, 60);
        }
    }

    // Fallback si quedó vacío
    if ($nombrebase === '') {
        $nombrebase = 'archivo';
    }

    return $nombrebase;
}

	public function solocargar($archivo)/*new file*/
	{
		$nombre_carpeta = __ROOT2__.'/includes/archivos';
		$filehandle     = opendir($nombre_carpeta);
		$nombretemp     = $_FILES[$archivo]["tmp_name"];
		$nombrearchivo  = basename($_FILES[$archivo]["name"]);
		$extension      = explode('.', $nombrearchivo);
		$cuenta         = count($extension) - 1;
		$ext            = strtolower($extension[$cuenta]);

		// ✅ nombre único para evitar sobreescribir archivos de otros registros
	$nombrebase  = pathinfo($nombrearchivo, PATHINFO_FILENAME);
    $nombrebase  = $this->sanitizarNombreArchivo($nombrebase);
    $nuevonombre = $nombrebase . '_' . uniqid() . '.' . $ext;

		if( 
			$ext == 'pdf'  || $ext == 'gif'  || $ext == 'jpeg' ||
			$ext == 'jpg'  || $ext == 'png'  || $ext == 'mp4'  ||
			$ext == 'docx' || $ext == 'doc'  || $ext == 'xml'  ||
			$ext == 'xlsx' || $ext == 'xls'  || $ext == 'ppt'  ||
			$ext == 'pptx' || $ext == 'txt'  || $ext == 'htm'  ||
			$ext == 'webp' || $ext == 'msg' || $ext == 'xlsm'
		){
			if(move_uploaded_file($nombretemp, $nombre_carpeta.'/'.$nuevonombre)){
				chmod($nombre_carpeta.'/'.$nuevonombre, 0755);
				return trim($nuevonombre);
			} else {
				return "1";
			}
		} else {
			return "2";
		}
	}

	public function cargar($archivo,$nombretabla,$IDENTIFICADOR='1',$idpost='no',$where=false,$idTemporal=false,$idTemporalU=false)/*new file*/
	{
		$nombre_carpeta = __ROOT2__.'/includes/archivos';
		$filehandle     = opendir($nombre_carpeta);
		$nombretemp     = $_FILES[$archivo]["tmp_name"];
		$nombrearchivo  = basename($_FILES[$archivo]["name"]);
		$extension      = explode('.', $nombrearchivo);
		$cuenta         = count($extension) - 1;
		$ext            = strtolower($extension[$cuenta]);

		// ✅ nombre único para evitar sobreescribir archivos de otros registros
$nombrebase  = pathinfo($nombrearchivo, PATHINFO_FILENAME);
$nombrebase  = $this->sanitizarNombreArchivo($nombrebase);
$nuevonombre = $nombrebase . '_' . uniqid() . '.' . $ext;

		if(
			$ext == 'pdf'  || $ext == 'gif'  || $ext == 'jpeg' ||
			$ext == 'jpg'  || $ext == 'png'  || $ext == 'mp4'  ||
			$ext == 'docx' || $ext == 'doc'  || $ext == 'xml'  ||
			$ext == 'txt'  || $ext == 'xlsx' || $ext == 'xls'  ||
			$ext == 'ppt'  || $ext == 'pptx' || $ext == 'htm'  ||
			$ext == 'webp' || $ext == 'msg' ||$ext == 'xlsm'  
		){
			if(move_uploaded_file($nombretemp, $nombre_carpeta.'/'.$nuevonombre)){
				chmod($nombre_carpeta.'/'.$nuevonombre, 0755);

				if($IDENTIFICADOR=='1'){
					$this->sologuardar($archivo,$nuevonombre,$nombretabla);
				}elseif($IDENTIFICADOR=='2'){
					$this->sologuardar2($archivo,$nuevonombre,$nombretabla);
				}elseif($IDENTIFICADOR=='3'){
					$this->sologuardar3($archivo,$nuevonombre,$nombretabla,$idpost);
				}elseif($IDENTIFICADOR=='4'){
					$this->sologuardar4($archivo,$nuevonombre,$nombretabla,$idpost);
				}elseif($IDENTIFICADOR=='5'){
					$this->sologuardar5($archivo,$nuevonombre,$nombretabla,$where,$idpost);
				}elseif($IDENTIFICADOR=='6'){
					$this->sologuardar6($archivo,$nuevonombre,$nombretabla,$idpost,$idTemporal);
				}elseif($IDENTIFICADOR=='8'){
					$this->sologuardar8($archivo,$nuevonombre,$nombretabla,$idpost,$idTemporal,$idTemporalU);
				}

				return trim($nuevonombre);
			} else {
				return "1";
			}
		} else {
			return "2";
		}
	}

	public function cargar6($archivo,$nombretabla,$IDENTIFICADOR='1',$idpost='no',$where=false,$idTemporal=false,$tipodearchivo=false)/*new file*/
	{
		$nombre_carpeta = __ROOT2__.'/includes/archivos';
		$filehandle     = opendir($nombre_carpeta);
		$nombretemp     = $_FILES[$archivo]["tmp_name"];
		$nombrearchivo  = basename($_FILES[$archivo]["name"]);
		$extension      = explode('.', $nombrearchivo);
		$cuenta         = count($extension) - 1;
		$ext            = strtolower($extension[$cuenta]);

		// ✅ nombre único para evitar sobreescribir archivos de otros registros
$nombrebase  = pathinfo($nombrearchivo, PATHINFO_FILENAME);
$nombrebase  = $this->sanitizarNombreArchivo($nombrebase);
$nuevonombre = $nombrebase . '_' . uniqid() . '.' . $ext;

		if( 
			$ext == 'pdf'  || $ext == 'gif'  || $ext == 'jpeg' ||
			$ext == 'jpg'  || $ext == 'png'  || $ext == 'mp4'  ||
			$ext == 'docx' || $ext == 'doc'  || $ext == 'xml'  ||
			$ext == 'xlsx' || $ext == 'xls'  || $ext == 'ppt'  ||
			$ext == 'pptx' || $ext == 'msg' ||$ext == 'htm'  || $ext == 'webp'  || $ext == 'xlsm'
		){
			if(move_uploaded_file($nombretemp, $nombre_carpeta.'/'.$nuevonombre)){
				chmod($nombre_carpeta.'/'.$nuevonombre, 0755);

				if($IDENTIFICADOR=='1'){
					$this->sologuardar($archivo,$nuevonombre,$nombretabla);
				}elseif($IDENTIFICADOR=='2'){
					$this->sologuardar2($archivo,$nuevonombre,$nombretabla);
				}elseif($IDENTIFICADOR=='3'){
					$this->sologuardar3($archivo,$nuevonombre,$nombretabla,$idpost);
				}elseif($IDENTIFICADOR=='4'){
					$this->sologuardar4($archivo,$nuevonombre,$nombretabla,$idpost);
				}elseif($IDENTIFICADOR=='5'){
					$this->sologuardar5($archivo,$nuevonombre,$nombretabla,$where,$idpost);
				}elseif($IDENTIFICADOR=='6'){
					$this->sologuardar6($archivo,$nuevonombre,$nombretabla,$idpost);
				}

				return trim($nuevonombre);
			} else {
				return "1";
			}
		} else {
			return "2";
		}
	}


	public function variablesempresa(){
		$conn = $this->db();
		$variablequery = "select * from 01empresa where id = '".$_SESSION['id']."' ";
		$arrayquery = mysqli_query($conn,$variablequery);
		return $row = mysqli_fetch_array($arrayquery, MYSQLI_ASSOC);		
	}

	public function revisar_empresa(){
		$conn = $this->db();
		$var1 = 'select id from 01empresa where id =  "'.$_SESSION['id'].'" ';
		$query = mysqli_query($conn,$var1) or die('P44'.mysqli_error($conn));
		$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
		return $row['id'];
	}

	public function guardar_empresa( $FECHA_INGRESO , $NUMERO_COLABORADOR , $NUMERO_REGISTRO_BIOMETRICO , $CONTRASENIA_REGISTRO_BIOMETRICO , $USUARIO_CRM , $CONTRASENIA_CRM , $NIVEL_ACCESO_CRM , $PUESTO , $DEPARTAMENTO , $NUMERO_TARJETA_CREDITO , $CORREO_1 , $CORREO_2 , $CORREO_3 ,$CORREO_4, $FECHA_SALIDA_EMPRESA , $MOTIVO_SALIDA_EMPRESA, $FECHA_INGRESO_IMSS, $JEFE_DIRECTO_1, $JEFE_DIRECTO_2, $JEFE_DIRECTO_3, $PERMISOS ){
		

		$conn = $this->db();
		$existe = $this->revisar_empresa();
		$session = isset($_SESSION['id'])?$_SESSION['id']:'';
		if($session != ''){

		 $var1 = "update 01empresa set FECHA_INGRESO = '".$FECHA_INGRESO."' , NUMERO_COLABORADOR = '".$NUMERO_COLABORADOR."' , NUMERO_REGISTRO_BIOMETRICO = '".$NUMERO_REGISTRO_BIOMETRICO."' , CONTRASENIA_REGISTRO_BIOMETRICO = '".$CONTRASENIA_REGISTRO_BIOMETRICO."' , USUARIO_CRM = '".$USUARIO_CRM."' , CONTRASENIA_CRM = '".$CONTRASENIA_CRM."' , NIVEL_ACCESO_CRM = '".$NIVEL_ACCESO_CRM."' , PUESTO = '".$PUESTO."' , DEPARTAMENTO = '".$DEPARTAMENTO."' , NUMERO_TARJETA_CREDITO = '".$NUMERO_TARJETA_CREDITO."' , CORREO_1 = '".$CORREO_1."' , CORREO_2 = '".$CORREO_2."' , CORREO_3 = '".$CORREO_3."' , CORREO_4 = '".$CORREO_4."' , FECHA_SALIDA_EMPRESA = '".$FECHA_SALIDA_EMPRESA."' , MOTIVO_SALIDA_EMPRESA = '".$MOTIVO_SALIDA_EMPRESA."', 
JEFE_DIRECTO_1 = '".$JEFE_DIRECTO_1."', 
JEFE_DIRECTO_2 = '".$JEFE_DIRECTO_2."', 
JEFE_DIRECTO_3 = '".$JEFE_DIRECTO_3."', 
FECHA_INGRESO_IMSS = '".$FECHA_INGRESO_IMSS."',
PERMISOS = '".$PERMISOS."'
		where id = '".$_SESSION['id']."' ; ";
		
		 $var2 = "insert into 01empresa ( FECHA_INGRESO, NUMERO_COLABORADOR, NUMERO_REGISTRO_BIOMETRICO, CONTRASENIA_REGISTRO_BIOMETRICO, USUARIO_CRM, CONTRASENIA_CRM, NIVEL_ACCESO_CRM, PUESTO, DEPARTAMENTO, NUMERO_TARJETA_CREDITO, CORREO_1, CORREO_2, CORREO_3,CORREO_4, FECHA_SALIDA_EMPRESA, MOTIVO_SALIDA_EMPRESA, 
FECHA_INGRESO_IMSS, JEFE_DIRECTO_1, JEFE_DIRECTO_2, JEFE_DIRECTO_3,PERMISOS, idRelacion
		) values ( '".$FECHA_INGRESO."' , '".$NUMERO_COLABORADOR."' , '".$NUMERO_REGISTRO_BIOMETRICO."' , '".$CONTRASENIA_REGISTRO_BIOMETRICO."' , '".$USUARIO_CRM."' , '".$CONTRASENIA_CRM."' , '".$NIVEL_ACCESO_CRM."' , '".$PUESTO."' , '".$DEPARTAMENTO."' , '".$NUMERO_TARJETA_CREDITO."' , '".$CORREO_1."' , '".$CORREO_2."' , '".$CORREO_3."' , '".$CORREO_4."' , '".$FECHA_SALIDA_EMPRESA."' , '".$MOTIVO_SALIDA_EMPRESA."' ,

'".$FECHA_INGRESO_IMSS."' , '".$JEFE_DIRECTO_1."' , '".$JEFE_DIRECTO_2."' , 
'".$JEFE_DIRECTO_3."' , '".$PERMISOS."'  , '".$_SESSION['id']."' ); ";			
			
		if($existe>=1){		

		mysqli_query($conn,$var1) or die('P156'.mysqli_error($conn));
		return "ACTUALIZADO";
		}else{
		mysqli_query($conn,$var2) or die('P160'.mysqli_error($conn));
		return "INGRESADO";
		}
		}		
	}

/**//**//**//**//*INFORMACION PERSONAL*//**//**//**//**/

	public function variablesIPERSONAL(){
		$conn = $this->db();
		$variablequery = "select * from 01informacionpersonal where idRelacion = '".$_SESSION['id']."' ";
		$arrayquery = mysqli_query($conn,$variablequery);
		return $row = mysqli_fetch_array($arrayquery, MYSQLI_ASSOC);		
	}

	public function revisar_IPERSONAL(){
		$conn = $this->db();
		$var1 = 'select id from 01informacionpersonal where idRelacion =  "'.$_SESSION['id'].'" ';
		$query = mysqli_query($conn,$var1) or die('P44'.mysqli_error($conn));
		$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
		return $row['id'];
	}

	public function guardar_IPERSONAL( $NOMBRE_1 , $NOMBRE_2 , $NOMBRE_3 , $APELLIDO_MATERNO , $APELLIDO_PATERNO ,  $CORREO_1, $IPCORREO2, $FECHA_DE_NACIMIENTO , $ANIOS , $LUGAR_DE_NACIMIENTO_ESTADO_PROVINCIA , $PAIS_DE_NACIMIENTO , $ESTADO_CIVIL , $NUMERO_DE_FAMILIARES_PADRES_HERMANOS , $NUMERO_DE_FAMILIARES_ESPOSA_HIJOS , $CELULAR_1 , $CELULAR_2 , $TELEFONO_DE_CASA_1 , $TELEFONO_DE_CASA_2 , $PORCENTAJE_DE_INGLES_HABLADO , $PORCENTAJE_DE_INGLES_ESCRITO , $DOMINIO_DE_OTRO_IDIOMA_Y_PORCENTAJE , $BENEFICIARIO_Y_PORCENTAJE_1_PARA_SEGURO , $BENEFICIARIO_Y_PORCENTAJE_2_PARA_SEGURO , $BENEFICIARIO_YPORCENTAJE_3_PARA_SEGURO , $TELEGRAM , $TIPO_DE_SANGRE , $AUTO , $MARCA_DEL_AUTO , $SUB_MARCA , $MODELO , $ipersonal1 , $COLOR , $PLACAS ){
		$conn = $this->db();
		$existe = $this->revisar_IPERSONAL();
		$session = isset($_SESSION['id'])?$_SESSION['id']:'';
		if($session != ''){
		$var1 = "update 01informacionpersonal set NOMBRE_1 = '".$NOMBRE_1."' , NOMBRE_2 = '".$NOMBRE_2."' , NOMBRE_3 = '".$NOMBRE_3."' , APELLIDO_MATERNO = '".$APELLIDO_MATERNO."' , APELLIDO_PATERNO = '".$APELLIDO_PATERNO."', CORREO_1 = '".$CORREO_1."' , IPCORREO2 = '".$IPCORREO2."', FECHA_DE_NACIMIENTO = '".$FECHA_DE_NACIMIENTO."' , ANIOS = '".$ANIOS."' , LUGAR_DE_NACIMIENTO_ESTADO_PROVINCIA = '".$LUGAR_DE_NACIMIENTO_ESTADO_PROVINCIA."' , PAIS_DE_NACIMIENTO = '".$PAIS_DE_NACIMIENTO."' , ESTADO_CIVIL = '".$ESTADO_CIVIL."' , NUMERO_DE_FAMILIARES_PADRES_HERMANOS = '".$NUMERO_DE_FAMILIARES_PADRES_HERMANOS."' , NUMERO_DE_FAMILIARES_ESPOSA_HIJOS = '".$NUMERO_DE_FAMILIARES_ESPOSA_HIJOS."' , CELULAR_1 = '".$CELULAR_1."' , CELULAR_2 = '".$CELULAR_2."' , TELEFONO_DE_CASA_1 = '".$TELEFONO_DE_CASA_1."' , TELEFONO_DE_CASA_2 = '".$TELEFONO_DE_CASA_2."' , PORCENTAJE_DE_INGLES_HABLADO = '".$PORCENTAJE_DE_INGLES_HABLADO."' , PORCENTAJE_DE_INGLES_ESCRITO = '".$PORCENTAJE_DE_INGLES_ESCRITO."' , DOMINIO_DE_OTRO_IDIOMA_Y_PORCENTAJE = '".$DOMINIO_DE_OTRO_IDIOMA_Y_PORCENTAJE."' , BENEFICIARIO_Y_PORCENTAJE_1_PARA_SEGURO = '".$BENEFICIARIO_Y_PORCENTAJE_1_PARA_SEGURO."' , BENEFICIARIO_Y_PORCENTAJE_2_PARA_SEGURO = '".$BENEFICIARIO_Y_PORCENTAJE_2_PARA_SEGURO."' , BENEFICIARIO_YPORCENTAJE_3_PARA_SEGURO = '".$BENEFICIARIO_YPORCENTAJE_3_PARA_SEGURO."' , TELEGRAM = '".$TELEGRAM."' , TIPO_DE_SANGRE = '".$TIPO_DE_SANGRE."' , AUTO = '".$AUTO."' , MARCA_DEL_AUTO = '".$MARCA_DEL_AUTO."' , SUB_MARCA = '".$SUB_MARCA."' , MODELO = '".$MODELO."' , ipersonal1 = '".$ipersonal1."' , COLOR = '".$COLOR."' , PLACAS = '".$PLACAS."' where idRelacion = '".$session."' ; ";
		
		$var2 = "insert into 01informacionpersonal ( NOMBRE_1, NOMBRE_2, NOMBRE_3, APELLIDO_MATERNO, APELLIDO_PATERNO, CORREO_1, IPCORREO2, FECHA_DE_NACIMIENTO, ANIOS, LUGAR_DE_NACIMIENTO_ESTADO_PROVINCIA, PAIS_DE_NACIMIENTO, ESTADO_CIVIL, NUMERO_DE_FAMILIARES_PADRES_HERMANOS, NUMERO_DE_FAMILIARES_ESPOSA_HIJOS, CELULAR_1, CELULAR_2, TELEFONO_DE_CASA_1, TELEFONO_DE_CASA_2, PORCENTAJE_DE_INGLES_HABLADO, PORCENTAJE_DE_INGLES_ESCRITO, DOMINIO_DE_OTRO_IDIOMA_Y_PORCENTAJE, BENEFICIARIO_Y_PORCENTAJE_1_PARA_SEGURO, BENEFICIARIO_Y_PORCENTAJE_2_PARA_SEGURO, BENEFICIARIO_YPORCENTAJE_3_PARA_SEGURO, TELEGRAM, TIPO_DE_SANGRE, AUTO, MARCA_DEL_AUTO, SUB_MARCA, MODELO, ipersonal1, COLOR, PLACAS, idRelacion) values ( '".$NOMBRE_1."' , '".$NOMBRE_2."' , '".$NOMBRE_3."' , '".$APELLIDO_MATERNO."' , '".$APELLIDO_PATERNO."', '".$CORREO_1."' , '".$IPCORREO2."', '".$FECHA_DE_NACIMIENTO."' , '".$ANIOS."' , '".$LUGAR_DE_NACIMIENTO_ESTADO_PROVINCIA."' , '".$PAIS_DE_NACIMIENTO."' , '".$ESTADO_CIVIL."' , '".$NUMERO_DE_FAMILIARES_PADRES_HERMANOS."' , '".$NUMERO_DE_FAMILIARES_ESPOSA_HIJOS."' , '".$CELULAR_1."' , '".$CELULAR_2."' , '".$TELEFONO_DE_CASA_1."' , '".$TELEFONO_DE_CASA_2."' , '".$PORCENTAJE_DE_INGLES_HABLADO."' , '".$PORCENTAJE_DE_INGLES_ESCRITO."' , '".$DOMINIO_DE_OTRO_IDIOMA_Y_PORCENTAJE."' , '".$BENEFICIARIO_Y_PORCENTAJE_1_PARA_SEGURO."' , '".$BENEFICIARIO_Y_PORCENTAJE_2_PARA_SEGURO."' , '".$BENEFICIARIO_YPORCENTAJE_3_PARA_SEGURO."' , '".$TELEGRAM."' , '".$TIPO_DE_SANGRE."' , '".$AUTO."' , '".$MARCA_DEL_AUTO."' , '".$SUB_MARCA."' , '".$MODELO."' , '".$ipersonal1."' , '".$COLOR."' , '".$PLACAS."' , '".$session."' ); ";			
			
		if($existe>=1){
		mysqli_query($conn,$var1) or die('P194'.mysqli_error($conn));
		return "ACTUALIZADO";
		}else{
		mysqli_query($conn,$var2) or die('P199'.mysqli_error($conn));
		return "INGRESADO";
		}
		}else{
		echo "NO HAY UN USUARIO SELECCIONADO";	
		}	
	}



/**//**//**//**//*INFORMACION PERSONAL coordinadores*//**//**//**//**/

	public function guardar_IPERSONALcoordina( $ipersonalcoordina , $NOMBRE_1 , $NOMBRE_2 , $NOMBRE_3 , $APELLIDO_PATERNO , $APELLIDO_MATERNO , $CORREO_1 , $IPCORREO2 , $FECHA_DE_NACIMIENTO , $ANIOS , $CELULAR_1 , $CELULAR_2 , $TELEFONO_DE_CASA_1 , $TELEFONO_DE_CASA_2 , $PORCENTAJE_DE_INGLES_HABLADO , $PORCENTAJE_DE_INGLES_ESCRITO , $DOMINIO_DE_OTRO_IDIOMA_Y_PORCENTAJE ){
		$conn = $this->db();
		$existe = $this->revisar_IPERSONAL();
		$session = isset($_SESSION['id'])?$_SESSION['id']:'';
		if($session != ''){
		$var1 = "update 01informacionpersonal set  NOMBRE_1 = '".$NOMBRE_1."' , NOMBRE_2 = '".$NOMBRE_2."' , NOMBRE_3 = '".$NOMBRE_3."' , APELLIDO_PATERNO = '".$APELLIDO_PATERNO."' , APELLIDO_MATERNO = '".$APELLIDO_MATERNO."' , CORREO_1 = '".$CORREO_1."' , IPCORREO2 = '".$IPCORREO2."' , FECHA_DE_NACIMIENTO = '".$FECHA_DE_NACIMIENTO."' , ANIOS = '".$ANIOS."' , CELULAR_1 = '".$CELULAR_1."' , CELULAR_2 = '".$CELULAR_2."' , TELEFONO_DE_CASA_1 = '".$TELEFONO_DE_CASA_1."' , TELEFONO_DE_CASA_2 = '".$TELEFONO_DE_CASA_2."' , PORCENTAJE_DE_INGLES_HABLADO = '".$PORCENTAJE_DE_INGLES_HABLADO."' , PORCENTAJE_DE_INGLES_ESCRITO = '".$PORCENTAJE_DE_INGLES_ESCRITO."' , DOMINIO_DE_OTRO_IDIOMA_Y_PORCENTAJE = '".$DOMINIO_DE_OTRO_IDIOMA_Y_PORCENTAJE."' where idRelacion = '".$session."'; ";
		
		$var2 = "insert into 01informacionpersonal ( NOMBRE_1, NOMBRE_2, NOMBRE_3, APELLIDO_PATERNO, APELLIDO_MATERNO, CORREO_1, IPCORREO2, FECHA_DE_NACIMIENTO, ANIOS, CELULAR_1, CELULAR_2, TELEFONO_DE_CASA_1, TELEFONO_DE_CASA_2, PORCENTAJE_DE_INGLES_HABLADO, PORCENTAJE_DE_INGLES_ESCRITO, DOMINIO_DE_OTRO_IDIOMA_Y_PORCENTAJE, idRelacion) values ( '".$NOMBRE_1."' , '".$NOMBRE_2."' , '".$NOMBRE_3."' , '".$APELLIDO_PATERNO."' , '".$APELLIDO_MATERNO."' , '".$CORREO_1."' , '".$IPCORREO2."' , '".$FECHA_DE_NACIMIENTO."' , '".$ANIOS."' , '".$CELULAR_1."' , '".$CELULAR_2."' , '".$TELEFONO_DE_CASA_1."' , '".$TELEFONO_DE_CASA_2."' , '".$PORCENTAJE_DE_INGLES_HABLADO."' , '".$PORCENTAJE_DE_INGLES_ESCRITO."' , '".$DOMINIO_DE_OTRO_IDIOMA_Y_PORCENTAJE."' , '".$session."' );";			
			
		if($existe>=1){
		mysqli_query($conn,$var1) or die('P194'.mysqli_error($conn));
		return "ACTUALIZADO";
		}else{
		mysqli_query($conn,$var2) or die('P199'.mysqli_error($conn));
		return "INGRESADO";
		}
		}else{
		echo "NO HAY UN USUARIO SELECCIONADO";	
		}	
	}

     	public function borraCOLABORADOR($id){ 
		$conn = $this->db();  
		//papa
		$var1 = "DELETE FROM 01empresa where id = '".$id."' "; 
		mysqli_query($conn,$var1) or die('P44'.mysqli_error($conn));

		$var2 = "DELETE FROM 01informacionpersonal WHERE idRelacion = '".$id."' ";
		mysqli_query($conn,$var2) or die('P44'.mysqli_error($conn));
		
		$var3 = "DELETE FROM `01adjuntoscolaboradores` WHERE `idRelacion` = '".$id."' ";
		mysqli_query($conn,$var3) or die('P44'.mysqli_error($conn));
		
		$var4 = "DELETE FROM `01DATOSFISCALESC` WHERE `idRelacion` = '".$id."' ";
		mysqli_query($conn,$var4) or die('P44'.mysqli_error($conn));
		
	    $var5 = "DELETE FROM `01DATOSBANCARIOS` WHERE `idRelacion` = '".$id."' ";
		mysqli_query($conn,$var5) or die('P44'.mysqli_error($conn));
		
		$var7 = "DELETE FROM `01DATOScolaborador` WHERE `idRelacion` = '".$id."' ";
		mysqli_query($conn,$var7) or die('P44'.mysqli_error($conn));
		
	    $var8 = "DELETE FROM `01dircasa1` WHERE `idRelacion` = '".$id."' ";
		mysqli_query($conn,$var8) or die('P44'.mysqli_error($conn));
		
	    $var9 = "DELETE FROM `01dircasa2` WHERE `idRelacion` = '".$id."' ";
		mysqli_query($conn,$var9) or die('P44'.mysqli_error($conn));
		
	    $var10 = "DELETE FROM `001familiar1mascercano` WHERE `idRelacion` = '".$id."' ";
		mysqli_query($conn,$var10) or die('P44'.mysqli_error($conn));
		
	    $var11 = "DELETE FROM `01familiar2mascercano` WHERE `idRelacion` = '".$id."' ";
		mysqli_query($conn,$var11) or die('P44'.mysqli_error($conn));
		
	    $var12 = "DELETE FROM `01familiar3mascercano` WHERE `idRelacion` = '".$id."' ";
		mysqli_query($conn,$var12) or die('P44'.mysqli_error($conn));
		
	    $var13 = "DELETE FROM `01familiar4mascercano` WHERE `idRelacion` = '".$id."' ";
		mysqli_query($conn,$var13) or die('P44'.mysqli_error($conn));
		
	    $var14 = "DELETE FROM `01habilidades` WHERE `idRelacion` = '".$id."' ";
		mysqli_query($conn,$var14) or die('P44'.mysqli_error($conn));
		
	    $var15 = "DELETE FROM `01Tempresarial` WHERE `idRelacion` = '".$id."' ";
		mysqli_query($conn,$var15) or die('P44'.mysqli_error($conn));
		
		$var16 = "DELETE FROM `01contrasenias` WHERE `idRelacion` = '".$id."' ";
		mysqli_query($conn,$var16) or die('P44'.mysqli_error($conn));
		
		$var17 = "DELETE FROM `01convenioprestamo` WHERE `idRelacion` = '".$id."' ";
		mysqli_query($conn,$var17) or die('P44'.mysqli_error($conn));  
		
		$var18 = "DELETE FROM `01ComPendientes` WHERE `idRelacion` = '".$id."' ";
		mysqli_query($conn,$var18) or die('P44'.mysqli_error($conn));
		
		$var19 = "DELETE FROM `01conveniopago` WHERE `idRelacion` = '".$id."' ";
		mysqli_query($conn,$var19) or die('P44'.mysqli_error($conn));
		
		$var20 = "DELETE FROM `01materialequipoa` WHERE `idRelacion` = '".$id."' ";
		mysqli_query($conn,$var20) or die('P44'.mysqli_error($conn));
		
		$var21 = "DELETE FROM `01uniformes` WHERE `idRelacion` = '".$id."' ";
		mysqli_query($conn,$var21) or die('P44'.mysqli_error($conn));
		
		$var22 = "DELETE FROM `01polizasydocumentos` WHERE `idRelacion` = '".$id."' ";
		mysqli_query($conn,$var22) or die('P44'.mysqli_error($conn));
		
		RETURN 
		"<strong><P style='color:green; font-size:25px;'>ELEMENTO BORRADO</P></strong>";

	}


/**//**//**//**//*DIRECCION CASA 1*//**//**//**//**/


	public function variablesdircasa1(){
		$conn = $this->db();
		$variablequery = "select * from 01dircasa1 where idRelacion = '".$_SESSION['id']."' ";
		$arrayquery = mysqli_query($conn,$variablequery);
		return $row = mysqli_fetch_array($arrayquery, MYSQLI_ASSOC);		
	}

	public function revisar_dircasa1(){
		$conn = $this->db();
		$var1 = 'select id from 01dircasa1 where idRelacion =  "'.$_SESSION['id'].'" ';
		$query = mysqli_query($conn,$var1) or die('P44'.mysqli_error($conn));
		$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
		return $row['id'];
	}

public function guardar_dircasa1($AUTORIZA_1, $EDIFICIO , $calledir1, $NUMERO_EXTERIOR , $NUMERO_INTERIOR , $NUMERO_INTERIOR_2 , $COLONIA , $ALCALDIA , $C_P , $CIUDAD , $ESTADO , $PAIS , $dircasa11 , $DIRECCION_DE_CASA_1_UBICACION_MAPA, $registro_id = 0){

		$conn = $this->db();
		$session = isset($_SESSION['id'])?$_SESSION['id']:'';
		$registro_id = (int)$registro_id;
		if($session != ''){
			
		$var1 = "update 01dircasa1 set EDIFICIO = '".$EDIFICIO."' , calledir1 = '".$calledir1."' , NUMERO_EXTERIOR = '".$NUMERO_EXTERIOR."' , NUMERO_INTERIOR = '".$NUMERO_INTERIOR."' ,
		 AUTORIZA_1 = '".$AUTORIZA_1."' ,


			NUMERO_INTERIOR_2 = '".$NUMERO_INTERIOR_2."' , COLONIA = '".$COLONIA."' , ALCALDIA = '".$ALCALDIA."' , C_P = '".$C_P."' , CIUDAD = '".$CIUDAD."' , ESTADO = '".$ESTADO."' , PAIS = '".$PAIS."' , dircasa11 = '".$dircasa11."' , DIRECCION_DE_CASA_1_UBICACION_MAPA = '".$DIRECCION_DE_CASA_1_UBICACION_MAPA."' where id = '".$registro_id."' and idRelacion = '".$session."' ; ";

		
		$var2 = "insert into 01dircasa1 ( EDIFICIO, calledir1, NUMERO_EXTERIOR, NUMERO_INTERIOR, NUMERO_INTERIOR_2, COLONIA, ALCALDIA, C_P, CIUDAD, ESTADO, PAIS, dircasa11, DIRECCION_DE_CASA_1_UBICACION_MAPA, idRelacion,AUTORIZA_1) values ( '".$EDIFICIO."' , '".$calledir1."' , '".$NUMERO_EXTERIOR."' , '".$NUMERO_INTERIOR."' , '".$NUMERO_INTERIOR_2."' , '".$COLONIA."' , '".$ALCALDIA."' , '".$C_P."' , '".$CIUDAD."' , '".$ESTADO."' , '".$PAIS."' , '".$dircasa11."' , '".$DIRECCION_DE_CASA_1_UBICACION_MAPA."' , '".$session."' , '".$AUTORIZA_1."'); ";			
			
		if($registro_id > 0){	

		mysqli_query($conn,$var1) or die('P233'.mysqli_error($conn));
		return "ACTUALIZADO";
		}else{
		mysqli_query($conn,$var2) or die('P237'.mysqli_error($conn));
		return "INGRESADO";
		}
		}else{
		echo "NO HAY UN USUARIO SELECCIONADO";	
		}		
	}


	public function listado_dircasa1(){

		$conn = $this->db();

		return mysqli_query($conn,"select * from 01dircasa1 where idRelacion='".$_SESSION['id']."' order by id desc");

	}



	public function borrar_dircasa1($id){

		$conn = $this->db();

		$id = (int)$id;

		mysqli_query($conn,"delete from 01dircasa1 where id='".$id."' and idRelacion='".$_SESSION['id']."'");

		return "ELEMENTO BORRADO";

	}


/**//**//**//**//*DIRECCION CASA 2*//**//**//**//**/

	public function variablesdircasa2(){
		$conn = $this->db();
		$variablequery = "select * from 01dircasa2 where idRelacion = '".$_SESSION['id']."' ";
		$arrayquery = mysqli_query($conn,$variablequery);
		return $row = mysqli_fetch_array($arrayquery, MYSQLI_ASSOC);		
	}

	public function revisar_dircasa2(){
		$conn = $this->db();
		$var1 = 'select id from 01dircasa2 where idRelacion =  "'.$_SESSION['id'].'" ';
		$query = mysqli_query($conn,$var1) or die('P44'.mysqli_error($conn));
		$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
		return $row['id'];
	}

	public function guardar_dircasa2($AUTORIZA_2, $DIRECCION_CASA_2_EDIFICIO , $calledir2,  $DIRECCION_CASA_2_NUMERO_EXTERIOR , $DIRECCION_CASA_2_INTERIOR , $DIRECCION_CASA_INTERIOR_2 , $DIRECCION_CASA_2_COLONIA , $DIRECCION_CASA_2_ALCALDIA , $DIRECCION_CASA_2_C_P , $DIRECCION_CASA_2_CIUDAD , $DIRECCION_CASA_2_ESTADO , $DIRECCION_CASA_2_PAIS , $dircasa22 , $DIRECCION_DE_CASA_2__UBICACION_EN_EL_MAPA ){
		$conn = $this->db();
		$existe = $this->revisar_dircasa2();
		$session = isset($_SESSION['id'])?$_SESSION['id']:'';
		if($session != ''){
			
		$var1 = "update 01dircasa2 set DIRECCION_CASA_2_EDIFICIO = '".$DIRECCION_CASA_2_EDIFICIO."' , calledir2 = '".$calledir2."' , DIRECCION_CASA_2_NUMERO_EXTERIOR = '".$DIRECCION_CASA_2_NUMERO_EXTERIOR."' , 
		AUTORIZA_2 = '".$AUTORIZA_2."' , 
		DIRECCION_CASA_2_INTERIOR = '".$DIRECCION_CASA_2_INTERIOR."' , DIRECCION_CASA_INTERIOR_2 = '".$DIRECCION_CASA_INTERIOR_2."' , DIRECCION_CASA_2_COLONIA = '".$DIRECCION_CASA_2_COLONIA."' , DIRECCION_CASA_2_ALCALDIA = '".$DIRECCION_CASA_2_ALCALDIA."' , DIRECCION_CASA_2_C_P = '".$DIRECCION_CASA_2_C_P."' , DIRECCION_CASA_2_CIUDAD = '".$DIRECCION_CASA_2_CIUDAD."' , DIRECCION_CASA_2_ESTADO = '".$DIRECCION_CASA_2_ESTADO."' , DIRECCION_CASA_2_PAIS = '".$DIRECCION_CASA_2_PAIS."' , dircasa22 = '".$dircasa22."' , DIRECCION_DE_CASA_2__UBICACION_EN_EL_MAPA = '".$DIRECCION_DE_CASA_2__UBICACION_EN_EL_MAPA."' where idRelacion = '".$session."' ; ";
		
		$var2 = "insert into 01dircasa2 ( DIRECCION_CASA_2_EDIFICIO,  calledir2, DIRECCION_CASA_2_NUMERO_EXTERIOR, DIRECCION_CASA_2_INTERIOR, DIRECCION_CASA_INTERIOR_2, DIRECCION_CASA_2_COLONIA, DIRECCION_CASA_2_ALCALDIA, DIRECCION_CASA_2_C_P, DIRECCION_CASA_2_CIUDAD, DIRECCION_CASA_2_ESTADO, DIRECCION_CASA_2_PAIS, dircasa22, DIRECCION_DE_CASA_2__UBICACION_EN_EL_MAPA, idRelacion, AUTORIZA_2) values ( '".$DIRECCION_CASA_2_EDIFICIO."' ,  '".$calledir2."' , '".$DIRECCION_CASA_2_NUMERO_EXTERIOR."' , '".$DIRECCION_CASA_2_INTERIOR."' , '".$DIRECCION_CASA_INTERIOR_2."' , '".$DIRECCION_CASA_2_COLONIA."' , '".$DIRECCION_CASA_2_ALCALDIA."' , '".$DIRECCION_CASA_2_C_P."' , '".$DIRECCION_CASA_2_CIUDAD."' , '".$DIRECCION_CASA_2_ESTADO."' , '".$DIRECCION_CASA_2_PAIS."' , '".$dircasa22."' , '".$DIRECCION_DE_CASA_2__UBICACION_EN_EL_MAPA."' , '".$session."', '".$AUTORIZA_2."' ); ";			
			
		if($existe>=1){	

		mysqli_query($conn,$var1) or die('P276'.mysqli_error($conn));
		return "ACTUALIZADO";
		}else{
		mysqli_query($conn,$var2) or die('P279'.mysqli_error($conn));
		return "INGRESADO";
		}
		}else{
		echo "NO HAY UN USUARIO SELECCIONADO";	
		}		
	}


/**//**//**//**//*001familiar1mascercano*//**//**//**//**/

	public function variablesf1cercano(){
		$conn = $this->db();
		$variablequery = "select * from 001familiar1mascercano where idRelacion = '".$_SESSION['id']."' ";
		$arrayquery = mysqli_query($conn,$variablequery);
		return $row = mysqli_fetch_array($arrayquery, MYSQLI_ASSOC);		
	}

	public function revisar_f1cercano(){
		$conn = $this->db();
		$var1 = 'select id from 001familiar1mascercano where idRelacion =  "'.$_SESSION['id'].'" ';
		$query = mysqli_query($conn,$var1) or die('P44'.mysqli_error($conn));
		$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
		return $row['id'];
	}

public function guardar_f1cercano(  $FAMILIAR_1_PARENTESCO , $FAMILIAR_1_NOMBRE_1 , $FAMILIAR_1_NOMBRE_2 , $FAMILIAR_1_APELLIDO_MATERNO , $FAMILIAR_1_APELLIDO_PATERNO , $FAMILIAR_1_CELULAR_1 , $FAMILIAR_1_CELULAR_2 , $FAMILIAR_1_TELEFONO_DE_CASA_I , $FAMILIAR_1_CORREO_ELECTRONICO , $FAMILIAR_1_EDIFICIO , $FAMILIAR_1_NUMERO_CALLE , $FAMILIAR_1_NUMERO_EXTERIOR , $FAMILIAR_1_NUMERO_INTERIOR , $FAMILIAR_1_NUMER__INTERIOR_2 , $FAMILIAR_1_COLONIA , $FAMILIAR_1_ALCALDIA , $FAMILIAR_1_C_P , $FAMILIAR_1_CIUDAD , $FAMILIAR_1_ESTADO , $FAMILIAR_1_PAIS , $F1CERCANO1 , $FAMILIAR_1_UBICACION__EN_EL_MAPA, $registro_id = 0){

		$conn = $this->db();
		
		$session = isset($_SESSION['id'])?$_SESSION['id']:'';
		$registro_id = (int)$registro_id;

		if($session != ''){
			
	$var1 = "update 001familiar1mascercano set FAMILIAR_1_PARENTESCO = '".$FAMILIAR_1_PARENTESCO."' , FAMILIAR_1_NOMBRE_1 = '".$FAMILIAR_1_NOMBRE_1."' , FAMILIAR_1_NOMBRE_2 = '".$FAMILIAR_1_NOMBRE_2."' , FAMILIAR_1_APELLIDO_MATERNO = '".$FAMILIAR_1_APELLIDO_MATERNO."' , FAMILIAR_1_APELLIDO_PATERNO = '".$FAMILIAR_1_APELLIDO_PATERNO."' , FAMILIAR_1_CELULAR_1 = '".$FAMILIAR_1_CELULAR_1."' , FAMILIAR_1_CELULAR_2 = '".$FAMILIAR_1_CELULAR_2."' , FAMILIAR_1_TELEFONO_DE_CASA_I = '".$FAMILIAR_1_TELEFONO_DE_CASA_I."' , FAMILIAR_1_CORREO_ELECTRONICO = '".$FAMILIAR_1_CORREO_ELECTRONICO."' , FAMILIAR_1_EDIFICIO = '".$FAMILIAR_1_EDIFICIO."' , FAMILIAR_1_NUMERO_CALLE = '".$FAMILIAR_1_NUMERO_CALLE."' , FAMILIAR_1_NUMERO_EXTERIOR = '".$FAMILIAR_1_NUMERO_EXTERIOR."' , FAMILIAR_1_NUMERO_INTERIOR = '".$FAMILIAR_1_NUMERO_INTERIOR."' , FAMILIAR_1_NUMER__INTERIOR_2 = '".$FAMILIAR_1_NUMER__INTERIOR_2."' , FAMILIAR_1_COLONIA = '".$FAMILIAR_1_COLONIA."' , FAMILIAR_1_ALCALDIA = '".$FAMILIAR_1_ALCALDIA."' , FAMILIAR_1_C_P = '".$FAMILIAR_1_C_P."' , FAMILIAR_1_CIUDAD = '".$FAMILIAR_1_CIUDAD."' , FAMILIAR_1_ESTADO = '".$FAMILIAR_1_ESTADO."' , FAMILIAR_1_PAIS = '".$FAMILIAR_1_PAIS."' , F1CERCANO1 = '".$F1CERCANO1."' , FAMILIAR_1_UBICACION__EN_EL_MAPA = '".$FAMILIAR_1_UBICACION__EN_EL_MAPA."' where id = '".$registro_id."' and idRelacion = '".$session."' ; ";

		
		$var2 = "insert into 001familiar1mascercano ( FAMILIAR_1_PARENTESCO, FAMILIAR_1_NOMBRE_1, FAMILIAR_1_NOMBRE_2, FAMILIAR_1_APELLIDO_MATERNO, FAMILIAR_1_APELLIDO_PATERNO, FAMILIAR_1_CELULAR_1, FAMILIAR_1_CELULAR_2, FAMILIAR_1_TELEFONO_DE_CASA_I, FAMILIAR_1_CORREO_ELECTRONICO, FAMILIAR_1_EDIFICIO, FAMILIAR_1_NUMERO_CALLE, FAMILIAR_1_NUMERO_EXTERIOR, FAMILIAR_1_NUMERO_INTERIOR, FAMILIAR_1_NUMER__INTERIOR_2, FAMILIAR_1_COLONIA, FAMILIAR_1_ALCALDIA, FAMILIAR_1_C_P, FAMILIAR_1_CIUDAD, FAMILIAR_1_ESTADO, FAMILIAR_1_PAIS, F1CERCANO1, FAMILIAR_1_UBICACION__EN_EL_MAPA, idRelacion) values ( '".$FAMILIAR_1_PARENTESCO."' , '".$FAMILIAR_1_NOMBRE_1."' , '".$FAMILIAR_1_NOMBRE_2."' , '".$FAMILIAR_1_APELLIDO_MATERNO."' , '".$FAMILIAR_1_APELLIDO_PATERNO."' , '".$FAMILIAR_1_CELULAR_1."' , '".$FAMILIAR_1_CELULAR_2."' , '".$FAMILIAR_1_TELEFONO_DE_CASA_I."' , '".$FAMILIAR_1_CORREO_ELECTRONICO."' , '".$FAMILIAR_1_EDIFICIO."' , '".$FAMILIAR_1_NUMERO_CALLE."' , '".$FAMILIAR_1_NUMERO_EXTERIOR."' , '".$FAMILIAR_1_NUMERO_INTERIOR."' , '".$FAMILIAR_1_NUMER__INTERIOR_2."' , '".$FAMILIAR_1_COLONIA."' , '".$FAMILIAR_1_ALCALDIA."' , '".$FAMILIAR_1_C_P."' , '".$FAMILIAR_1_CIUDAD."' , '".$FAMILIAR_1_ESTADO."' , '".$FAMILIAR_1_PAIS."' , '".$F1CERCANO1."' , '".$FAMILIAR_1_UBICACION__EN_EL_MAPA."' , '".$session."' ); ";			
			
		if($registro_id > 0){	

		mysqli_query($conn,$var1) or die('P276'.mysqli_error($conn));
		return "ACTUALIZADO";
		}else{
		mysqli_query($conn,$var2) or die('P279'.mysqli_error($conn));
		return "INGRESADO";
		}
		}else{
		echo "NO HAY UN USUARIO SELECCIONADO";	
		}		
	}
	public function listado_f1cercano(){

		$conn = $this->db();

		return mysqli_query($conn,"select * from 001familiar1mascercano where idRelacion='".$_SESSION['id']."' order by id desc");

	}



	public function borrar_f1cercano($id){

		$conn = $this->db();

		$id = (int)$id;

		mysqli_query($conn,"delete from 001familiar1mascercano where id='".$id."' and idRelacion='".$_SESSION['id']."'");

		return "ELEMENTO BORRADO";

	}




/**//**//**//**//*02familiar2mascercano *//**//**//**//**/


	public function variablesf2cercano(){
		$conn = $this->db();
		$variablequery = "select * from 01familiar2mascercano where idRelacion = '".$_SESSION['id']."' ";
		$arrayquery = mysqli_query($conn,$variablequery);
		return $row = mysqli_fetch_array($arrayquery, MYSQLI_ASSOC);		
	}

	public function revisar_f2cercano(){
		$conn = $this->db();
		$var1 = 'select id from 01familiar2mascercano where idRelacion =  "'.$_SESSION['id'].'" ';
		$query = mysqli_query($conn,$var1) or die('P44'.mysqli_error($conn));
		$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
		return $row['id'];
	}

	public function guardar_f2cercano( $FAMILIAR2_PARENTESCO , $FAMILIAR2_NOMBRE_1 , $FAMILIAR2_NOMBRE_2 , $FAMILIAR2_APELLIDO_MATERNO , $FAMILIAR2_APELLIDO_PATERNO , $FAMILIAR2_CELULAR_1 , $FAMILIAR2_CELULAR_2 , $FAMILIAR2_TELEFONO_DE_CASA_I , $FAMILIAR2_CORREO_ELECTRONICO , $FAMILIAR2_EDIFICIO , $FAMILIAR2_CALLE , $FAMILIAR2_NUMERO_EXTERIOR , $FAMILIAR2_NUMERO_INTERIOR , $FAMILIAR2_NUMER_INTERIOR_2 , $FAMILIAR2_COLONIA , $FAMILIAR2_ALCALDIA , $FAMILIAR2_C_P , $FAMILIAR2_CIUDAD , $FAMILIAR2_ESTADO , $FAMILIAR2_PAIS , $F2CERCANO2 , $FAMILIAR2_UBICACION_EN_EL_MAPA ){
		$conn = $this->db();
		$existe = $this->revisar_f2cercano();
		$session = isset($_SESSION['id'])?$_SESSION['id']:'';
		if($session != ''){
			
		$var1 = "update 01familiar2mascercano set FAMILIAR2_PARENTESCO = '".$FAMILIAR2_PARENTESCO."' , FAMILIAR2_NOMBRE_1 = '".$FAMILIAR2_NOMBRE_1."' , FAMILIAR2_NOMBRE_2 = '".$FAMILIAR2_NOMBRE_2."' , FAMILIAR2_APELLIDO_MATERNO = '".$FAMILIAR2_APELLIDO_MATERNO."' , FAMILIAR2_APELLIDO_PATERNO = '".$FAMILIAR2_APELLIDO_PATERNO."' , FAMILIAR2_CELULAR_1 = '".$FAMILIAR2_CELULAR_1."' , FAMILIAR2_CELULAR_2 = '".$FAMILIAR2_CELULAR_2."' , FAMILIAR2_TELEFONO_DE_CASA_I = '".$FAMILIAR2_TELEFONO_DE_CASA_I."' , FAMILIAR2_CORREO_ELECTRONICO = '".$FAMILIAR2_CORREO_ELECTRONICO."' , FAMILIAR2_EDIFICIO = '".$FAMILIAR2_EDIFICIO."' , FAMILIAR2_CALLE = '".$FAMILIAR2_CALLE."' , FAMILIAR2_NUMERO_EXTERIOR = '".$FAMILIAR2_NUMERO_EXTERIOR."' , FAMILIAR2_NUMERO_INTERIOR = '".$FAMILIAR2_NUMERO_INTERIOR."' , FAMILIAR2_NUMER_INTERIOR_2 = '".$FAMILIAR2_NUMER_INTERIOR_2."' , FAMILIAR2_COLONIA = '".$FAMILIAR2_COLONIA."' , FAMILIAR2_ALCALDIA = '".$FAMILIAR2_ALCALDIA."' , FAMILIAR2_C_P = '".$FAMILIAR2_C_P."' , FAMILIAR2_CIUDAD = '".$FAMILIAR2_CIUDAD."' , FAMILIAR2_ESTADO = '".$FAMILIAR2_ESTADO."' , FAMILIAR2_PAIS = '".$FAMILIAR2_PAIS."' , F2CERCANO2 = '".$F2CERCANO2."' , FAMILIAR2_UBICACION_EN_EL_MAPA = '".$FAMILIAR2_UBICACION_EN_EL_MAPA."' where idRelacion = '".$session."' ; ";
		
		$var2 = "insert into 01familiar2mascercano ( FAMILIAR2_PARENTESCO, FAMILIAR2_NOMBRE_1, FAMILIAR2_NOMBRE_2, FAMILIAR2_APELLIDO_MATERNO, FAMILIAR2_APELLIDO_PATERNO, FAMILIAR2_CELULAR_1, FAMILIAR2_CELULAR_2, FAMILIAR2_TELEFONO_DE_CASA_I, FAMILIAR2_CORREO_ELECTRONICO, FAMILIAR2_EDIFICIO, FAMILIAR2_CALLE, FAMILIAR2_NUMERO_EXTERIOR, FAMILIAR2_NUMERO_INTERIOR, FAMILIAR2_NUMER_INTERIOR_2, FAMILIAR2_COLONIA, FAMILIAR2_ALCALDIA, FAMILIAR2_C_P, FAMILIAR2_CIUDAD, FAMILIAR2_ESTADO, FAMILIAR2_PAIS, F2CERCANO2, FAMILIAR2_UBICACION_EN_EL_MAPA, idRelacion) values ( '".$FAMILIAR2_PARENTESCO."' , '".$FAMILIAR2_NOMBRE_1."' , '".$FAMILIAR2_NOMBRE_2."' , '".$FAMILIAR2_APELLIDO_MATERNO."' , '".$FAMILIAR2_APELLIDO_PATERNO."' , '".$FAMILIAR2_CELULAR_1."' , '".$FAMILIAR2_CELULAR_2."' , '".$FAMILIAR2_TELEFONO_DE_CASA_I."' , '".$FAMILIAR2_CORREO_ELECTRONICO."' , '".$FAMILIAR2_EDIFICIO."' , '".$FAMILIAR2_CALLE."' , '".$FAMILIAR2_NUMERO_EXTERIOR."' , '".$FAMILIAR2_NUMERO_INTERIOR."' , '".$FAMILIAR2_NUMER_INTERIOR_2."' , '".$FAMILIAR2_COLONIA."' , '".$FAMILIAR2_ALCALDIA."' , '".$FAMILIAR2_C_P."' , '".$FAMILIAR2_CIUDAD."' , '".$FAMILIAR2_ESTADO."' , '".$FAMILIAR2_PAIS."' , '".$F2CERCANO2."' , '".$FAMILIAR2_UBICACION_EN_EL_MAPA."' , '".$session."' ); ";			
			
		if($existe>=1){	

		mysqli_query($conn,$var1) or die('P233'.mysqli_error($conn));
		return "ACTUALIZADO";
		}else{
		mysqli_query($conn,$var2) or die('P237'.mysqli_error($conn));
		return "INGRESADO";
		}
		}else{
		echo "NO HAY UN USUARIO SELECCIONADO";	
		}		
	}

	/* ═════════════════════════════════════════════════════════════
	   CONTACTOS COLABORADOR
	   ═════════════════════════════════════════════════════════════ */


	public function enviarNOMBRECONTACTO($NOMBRE_CONTACTO_COLAB,$CEL_CONTACTO_COLAB,$TELEFONO_CONTACCOLAB,$NUMERO_EXTENSION_COLAB,$EMAIL_CONTACTO_COLAB,$OBSERVACIONES_COLAB,$FECHA_CONTACTOS_COLAB,$TARJETA_COLAB,$validaNOMBRECONTACTO,$IPcontactosCOLAB,$enviarimailCONT,$ENVIACONTACTOCOLAB){
		$conn    = $this->db();
		$session = isset($_SESSION['id'])?$_SESSION['id']:'';    
		if($session != ''){
			$var1 = "update 01CONTACTOSCOLAB set NOMBRE_CONTACTO_COLAB='".$NOMBRE_CONTACTO_COLAB."',CEL_CONTACTO_COLAB='".$CEL_CONTACTO_COLAB."',TELEFONO_CONTACCOLAB='".$TELEFONO_CONTACCOLAB."',EMAIL_CONTACTO_COLAB='".$EMAIL_CONTACTO_COLAB."',NUMERO_EXTENSION_COLAB='".$NUMERO_EXTENSION_COLAB."',OBSERVACIONES_COLAB='".$OBSERVACIONES_COLAB."',FECHA_CONTACTOS_COLAB='".$FECHA_CONTACTOS_COLAB."',TARJETA_COLAB='".$TARJETA_COLAB."',validaNOMBRECONTACTO='".$validaNOMBRECONTACTO."' where id='".$IPcontactosCOLAB."' ;";
			$var2 = "insert into 01CONTACTOSCOLAB (NOMBRE_CONTACTO_COLAB,CEL_CONTACTO_COLAB,TELEFONO_CONTACCOLAB,EMAIL_CONTACTO_COLAB,NUMERO_EXTENSION_COLAB,OBSERVACIONES_COLAB,FECHA_CONTACTOS_COLAB,TARJETA_COLAB,validaNOMBRECONTACTO,idRelacion) values('".$NOMBRE_CONTACTO_COLAB."','".$CEL_CONTACTO_COLAB."','".$TELEFONO_CONTACCOLAB."','".$EMAIL_CONTACTO_COLAB."','".$NUMERO_EXTENSION_COLAB."','".$OBSERVACIONES_COLAB."','".$FECHA_CONTACTOS_COLAB."','".$TARJETA_COLAB."','".$validaNOMBRECONTACTO."','".$_SESSION['id']."');";

	if($ENVIACONTACTOCOLAB=='ENVIACONTACTOCOLAB'){
		mysqli_query($conn,$var1) or die('P156'.mysqli_error($conn));
		return "Actualizado";
	}else{
		mysqli_query($conn,$var2) or die('P160'.mysqli_error($conn));
		return "Ingresado";
			}
		}else{ echo '<p class="fs-4">NO HAY UN PROVEEDOR SELECCIONADO</p>'; }
    }

	public function listadocontactocola(){ $conn=$this->db(); return mysqli_query($conn,"select * from 01CONTACTOSCOLAB where idRelacion='".$_SESSION['id']."' order by id desc "); }
	public function listadocontactocola2($id){ $conn=$this->db(); return mysqli_query($conn,"select * from 01CONTACTOSCOLAB where id='".$id."' "); }

	public function borracontactoCOLAB($id){
		$conn = $this->db();
		mysqli_query($conn,"delete from 01CONTACTOSCOLAB where id='".$id."' ");
		return "<P style='color:green; font-size:25px;'>ELEMENTO BORRADO</P>";
	}

/**//**//**//**//*03familiar2mascercano *//**//**//**//**/


	public function variablesf3cercano(){
		$conn = $this->db();
		$variablequery = "select * from 01familiar3mascercano where idRelacion = '".$_SESSION['id']."' ";
		$arrayquery = mysqli_query($conn,$variablequery);
		return $row = mysqli_fetch_array($arrayquery, MYSQLI_ASSOC);		
	}

	public function revisar_f3cercano(){
		$conn = $this->db();
		$var1 = 'select id from 01familiar3mascercano where idRelacion =  "'.$_SESSION['id'].'" ';
		$query = mysqli_query($conn,$var1) or die('P44'.mysqli_error($conn));
		$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
		return $row['id'];
	}

	public function guardar_f3cercano($FAMILIAR3_PARENTESCO , $FAMILIAR3_NOMBRE_1 , $FAMILIAR3_NOMBRE_2 , $FAMILIAR3_APELLIDO_MATERNO , $FAMILIAR3_APELLIDO_PATERNO , $FAMILIAR3_CELULAR_1 , $FAMILIAR3_CELULAR_2 , $FAMILIAR3_TELEFONO_DE_CASA_I , $FAMILIAR3_CORREO_ELECTRONICO , $FAMILIAR3_EDIFICIO , $FAMILIAR3_calle , $FAMILIAR3_NUMERO_EXTERIOR , $FAMILIAR3_NUMERO_INTERIOR , $FAMILIAR3_NUMER_INTERIOR_2 , $FAMILIAR3_COLONIA , $FAMILIAR3_ALCALDIA , $FAMILIAR3_C_P , $FAMILIAR3_CIUDAD , $FAMILIAR3_ESTADO , $FAMILIAR3_PAIS , $F3CERCANO3 , $FAMILIAR3_UBICACION_EN_EL_MAPA ){
		$conn = $this->db();
		$existe = $this->revisar_f3cercano();
		$session = isset($_SESSION['id'])?$_SESSION['id']:'';
		if($session != ''){
			
		$var1 = "update 01familiar3mascercano set FAMILIAR3_PARENTESCO = '".$FAMILIAR3_PARENTESCO."' , FAMILIAR3_NOMBRE_1 = '".$FAMILIAR3_NOMBRE_1."' , FAMILIAR3_NOMBRE_2 = '".$FAMILIAR3_NOMBRE_2."' , FAMILIAR3_APELLIDO_MATERNO = '".$FAMILIAR3_APELLIDO_MATERNO."' , FAMILIAR3_APELLIDO_PATERNO = '".$FAMILIAR3_APELLIDO_PATERNO."' , FAMILIAR3_CELULAR_1 = '".$FAMILIAR3_CELULAR_1."' , FAMILIAR3_CELULAR_2 = '".$FAMILIAR3_CELULAR_2."' , FAMILIAR3_TELEFONO_DE_CASA_I = '".$FAMILIAR3_TELEFONO_DE_CASA_I."' , FAMILIAR3_CORREO_ELECTRONICO = '".$FAMILIAR3_CORREO_ELECTRONICO."' , FAMILIAR3_EDIFICIO = '".$FAMILIAR3_EDIFICIO."' , FAMILIAR3_calle = '".$FAMILIAR3_calle."' , FAMILIAR3_NUMERO_EXTERIOR = '".$FAMILIAR3_NUMERO_EXTERIOR."' , FAMILIAR3_NUMERO_INTERIOR = '".$FAMILIAR3_NUMERO_INTERIOR."' , FAMILIAR3_NUMER_INTERIOR_2 = '".$FAMILIAR3_NUMER_INTERIOR_2."' , FAMILIAR3_COLONIA = '".$FAMILIAR3_COLONIA."' , FAMILIAR3_ALCALDIA = '".$FAMILIAR3_ALCALDIA."' , FAMILIAR3_C_P = '".$FAMILIAR3_C_P."' , FAMILIAR3_CIUDAD = '".$FAMILIAR3_CIUDAD."' , FAMILIAR3_ESTADO = '".$FAMILIAR3_ESTADO."' , FAMILIAR3_PAIS = '".$FAMILIAR3_PAIS."' , F3CERCANO3 = '".$F3CERCANO3."' , FAMILIAR3_UBICACION_EN_EL_MAPA = '".$FAMILIAR3_UBICACION_EN_EL_MAPA."' where idRelacion = '".$session."' ; ";
		
		$var2 = "insert into 01familiar3mascercano ( FAMILIAR3_PARENTESCO, FAMILIAR3_NOMBRE_1, FAMILIAR3_NOMBRE_2, FAMILIAR3_APELLIDO_MATERNO, FAMILIAR3_APELLIDO_PATERNO, FAMILIAR3_CELULAR_1, FAMILIAR3_CELULAR_2, FAMILIAR3_TELEFONO_DE_CASA_I, FAMILIAR3_CORREO_ELECTRONICO, FAMILIAR3_EDIFICIO, FAMILIAR3_calle, FAMILIAR3_NUMERO_EXTERIOR, FAMILIAR3_NUMERO_INTERIOR, FAMILIAR3_NUMER_INTERIOR_2, FAMILIAR3_COLONIA, FAMILIAR3_ALCALDIA, FAMILIAR3_C_P, FAMILIAR3_CIUDAD, FAMILIAR3_ESTADO, FAMILIAR3_PAIS, F3CERCANO3, FAMILIAR3_UBICACION_EN_EL_MAPA, idRelacion) values ( '".$FAMILIAR3_PARENTESCO."' , '".$FAMILIAR3_NOMBRE_1."' , '".$FAMILIAR3_NOMBRE_2."' , '".$FAMILIAR3_APELLIDO_MATERNO."' , '".$FAMILIAR3_APELLIDO_PATERNO."' , '".$FAMILIAR3_CELULAR_1."' , '".$FAMILIAR3_CELULAR_2."' , '".$FAMILIAR3_TELEFONO_DE_CASA_I."' , '".$FAMILIAR3_CORREO_ELECTRONICO."' , '".$FAMILIAR3_EDIFICIO."' , '".$FAMILIAR3_calle."' , '".$FAMILIAR3_NUMERO_EXTERIOR."' , '".$FAMILIAR3_NUMERO_INTERIOR."' , '".$FAMILIAR3_NUMER_INTERIOR_2."' , '".$FAMILIAR3_COLONIA."' , '".$FAMILIAR3_ALCALDIA."' , '".$FAMILIAR3_C_P."' , '".$FAMILIAR3_CIUDAD."' , '".$FAMILIAR3_ESTADO."' , '".$FAMILIAR3_PAIS."' , '".$F3CERCANO3."' , '".$FAMILIAR3_UBICACION_EN_EL_MAPA."' , '".$session."' ); ";			
			
		if($existe>=1){	

		mysqli_query($conn,$var1) or die('P233'.mysqli_error($conn));
		return "ACTUALIZADO";
		}else{
		mysqli_query($conn,$var2) or die('P237'.mysqli_error($conn));
		return "INGRESADO";
		}
		}else{
		echo "NO HAY UN USUARIO SELECCIONADO";	
		}		
	}


/**//**//**//**//*04familiar2mascercano *//**//**//**//**/


	public function variablesf4cercano(){
		$conn = $this->db();
		$variablequery = "select * from 01familiar4mascercano where idRelacion = '".$_SESSION['id']."' ";
		$arrayquery = mysqli_query($conn,$variablequery);
		return $row = mysqli_fetch_array($arrayquery, MYSQLI_ASSOC);		
	}

	public function revisar_f4cercano(){
		$conn = $this->db();
		$var1 = 'select id from 01familiar4mascercano where idRelacion =  "'.$_SESSION['id'].'" ';
		$query = mysqli_query($conn,$var1) or die('P44'.mysqli_error($conn));
		$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
		return $row['id'];
	}

	public function guardar_f4cercano($FAMILIAR4_PARENTESCO , $FAMILIAR4_NOMBRE_1 , $FAMILIAR4_NOMBRE_2 , $FAMILIAR4_APELLIDO_MATERNO , $FAMILIAR4_APELLIDO_PATERNO , $FAMILIAR4_CELULAR_1 , $FAMILIAR4_CELULAR_2 , $FAMILIAR4_TELEFONO_DE_CASA_I , $FAMILIAR4_CORREO_ELECTRONICO , $FAMILIAR4_EDIFICIO , $FAMILIAR4_CALLE , $FAMILIAR4_NUMERO_EXTERIOR , $FAMILIAR4_NUMERO_INTERIOR , $FAMILIAR4_NUMER__INTERIOR_2 , $FAMILIAR4_COLONIA , $FAMILIAR4_ALCALDIA , $FAMILIAR4_C_P , $FAMILIAR4_CIUDAD , $FAMILIAR4_ESTADO , $FAMILIAR4_PAIS , $F4CERCANO4 , $FAMILIAR4_UBICACION_EN_EL_MAPA ){
		$conn = $this->db();
		$existe = $this->revisar_f4cercano();
		$session = isset($_SESSION['id'])?$_SESSION['id']:'';
		if($session != ''){
			
		$var1 = "update 01familiar4mascercano set FAMILIAR4_PARENTESCO = '".$FAMILIAR4_PARENTESCO."' , FAMILIAR4_NOMBRE_1 = '".$FAMILIAR4_NOMBRE_1."' , FAMILIAR4_NOMBRE_2 = '".$FAMILIAR4_NOMBRE_2."' , FAMILIAR4_APELLIDO_MATERNO = '".$FAMILIAR4_APELLIDO_MATERNO."' , FAMILIAR4_APELLIDO_PATERNO = '".$FAMILIAR4_APELLIDO_PATERNO."' , FAMILIAR4_CELULAR_1 = '".$FAMILIAR4_CELULAR_1."' , FAMILIAR4_CELULAR_2 = '".$FAMILIAR4_CELULAR_2."' , FAMILIAR4_TELEFONO_DE_CASA_I = '".$FAMILIAR4_TELEFONO_DE_CASA_I."' , FAMILIAR4_CORREO_ELECTRONICO = '".$FAMILIAR4_CORREO_ELECTRONICO."' , FAMILIAR4_EDIFICIO = '".$FAMILIAR4_EDIFICIO."' , FAMILIAR4_CALLE = '".$FAMILIAR4_CALLE."' , FAMILIAR4_NUMERO_EXTERIOR = '".$FAMILIAR4_NUMERO_EXTERIOR."' , FAMILIAR4_NUMERO_INTERIOR = '".$FAMILIAR4_NUMERO_INTERIOR."' , FAMILIAR4_NUMER__INTERIOR_2 = '".$FAMILIAR4_NUMER__INTERIOR_2."' , FAMILIAR4_COLONIA = '".$FAMILIAR4_COLONIA."' , FAMILIAR4_ALCALDIA = '".$FAMILIAR4_ALCALDIA."' , FAMILIAR4_C_P = '".$FAMILIAR4_C_P."' , FAMILIAR4_CIUDAD = '".$FAMILIAR4_CIUDAD."' , FAMILIAR4_ESTADO = '".$FAMILIAR4_ESTADO."' , FAMILIAR4_PAIS = '".$FAMILIAR4_PAIS."' , F4CERCANO4 = '".$F4CERCANO4."' , FAMILIAR4_UBICACION_EN_EL_MAPA = '".$FAMILIAR4_UBICACION_EN_EL_MAPA."' where idRelacion = '".$session."' ; ";
		
		$var2 = "insert into 01familiar4mascercano ( FAMILIAR4_PARENTESCO, FAMILIAR4_NOMBRE_1, FAMILIAR4_NOMBRE_2, FAMILIAR4_APELLIDO_MATERNO, FAMILIAR4_APELLIDO_PATERNO, FAMILIAR4_CELULAR_1, FAMILIAR4_CELULAR_2, FAMILIAR4_TELEFONO_DE_CASA_I, FAMILIAR4_CORREO_ELECTRONICO, FAMILIAR4_EDIFICIO, FAMILIAR4_CALLE, FAMILIAR4_NUMERO_EXTERIOR, FAMILIAR4_NUMERO_INTERIOR, FAMILIAR4_NUMER__INTERIOR_2, FAMILIAR4_COLONIA, FAMILIAR4_ALCALDIA, FAMILIAR4_C_P, FAMILIAR4_CIUDAD, FAMILIAR4_ESTADO, FAMILIAR4_PAIS, F4CERCANO4, FAMILIAR4_UBICACION_EN_EL_MAPA, idRelacion) values ( '".$FAMILIAR4_PARENTESCO."' , '".$FAMILIAR4_NOMBRE_1."' , '".$FAMILIAR4_NOMBRE_2."' , '".$FAMILIAR4_APELLIDO_MATERNO."' , '".$FAMILIAR4_APELLIDO_PATERNO."' , '".$FAMILIAR4_CELULAR_1."' , '".$FAMILIAR4_CELULAR_2."' , '".$FAMILIAR4_TELEFONO_DE_CASA_I."' , '".$FAMILIAR4_CORREO_ELECTRONICO."' , '".$FAMILIAR4_EDIFICIO."' , '".$FAMILIAR4_CALLE."' , '".$FAMILIAR4_NUMERO_EXTERIOR."' , '".$FAMILIAR4_NUMERO_INTERIOR."' , '".$FAMILIAR4_NUMER__INTERIOR_2."' , '".$FAMILIAR4_COLONIA."' , '".$FAMILIAR4_ALCALDIA."' , '".$FAMILIAR4_C_P."' , '".$FAMILIAR4_CIUDAD."' , '".$FAMILIAR4_ESTADO."' , '".$FAMILIAR4_PAIS."' , '".$F4CERCANO4."' , '".$FAMILIAR4_UBICACION_EN_EL_MAPA."' , '".$session."' ); ";			
			
		if($existe>=1){

		mysqli_query($conn,$var1) or die('P233'.mysqli_error($conn));
		return "ACTUALIZADO";
		}else{
		mysqli_query($conn,$var2) or die('P237'.mysqli_error($conn));
		return "INGRESADO";
		}
		}else{
		echo "NO HAY UN USUARIO SELECCIONADO";	
		}		
	}

/**//**//**//**//*habilidades *//**//**//**//**/

	public function variableshabilidades(){
		$conn = $this->db();
		$variablequery = "select * from 01habilidades where idRelacion = '".$_SESSION['id']."' ";
		$arrayquery = mysqli_query($conn,$variablequery);
		return $row = mysqli_fetch_array($arrayquery, MYSQLI_ASSOC);		
	}

	public function revisar_habilidades(){
		$conn = $this->db();
		$var1 = 'select id from 01habilidades where idRelacion =  "'.$_SESSION['id'].'" ';
		$query = mysqli_query($conn,$var1) or die('P44'.mysqli_error($conn));
		$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
		return $row['id'];
	}

	public function guardar_habilidades(  $LUNES , $LHORARIOS , $LOBSERBACIONES , $MARTES , $MAHORARIOS , $MAOBSERBACIONES , $MIERCOLES , $MIHORARIOS , $MIOBSERBACIONES , $JUEVES , $JHORARIOS , $JOBSERBACIONES , $VIERNES , $VHORARIOS , $VOBSERBACIONES , $SABADO , $SHORARIOS , $SOBSERBACIONES , $DOMINGO , $DHORARIOS , $DOBSERBACIONES , $FIESTA_MEXICANA , $EFIESTA_MEXICANA , $OFIESTA_MEXICANA , $JEOPARDY , $EJEOPARDY , $OJEOPARDY , $RALLYS , $ERALLYS , $ORALLYS , $a100_MEXICANOS_DIJIERON , $E100_MEXICANOS_DIJIERON , $O100_MEXICANOS_DIJIERON , $NOCHE_DE_LAS_ESTRELLAS , $ENOCHE_DE_LAS_ESTRELLAS , $ONOCHE_DE_LAS_ESTRELLAS , $HANDS_UP , $EHANDS_UP , $OHANDS_UP , $CALAMAR , $ECALAMAR , $OCALAMAR , $CRUCERO , $ECRUCERO , $OCRUCERO , $MAESTRO_DE_CEREMONIAS , $EMAESTRO_DE_CEREMONIAS , $OMAESTRO_DE_CEREMONIAS , $CASINO , $ECASINO , $OCASINO , $CUBILETE , $ECUBILETE , $OCUBILETE , $CRABS , $ECRABS , $OCRABS , $RULETA , $ERULETA , $ORULETA , $BLACK_JACK , $EBLACK_JACK , $OBLACK_JACK , $INFLABLES , $EINFLABLES , $OINFLABLES , $AAA , $AAAA , $EAAAA , $OAAAA , $BBB , $BBBB , $EBBB , $OBBB , $CCC , $CCCC , $ECCC , $OCCC , $DDD, $DDDD , $EDDD , $ODDD , $EEE , $EEEEE , $EEEE , $OEEE , $FFF , $FFFF , $EFFF , $OFFF , $GGG , $GGGG , $EGGG , $OGGG , $OBSERVACIONES , $habilidades1 ){
		$conn = $this->db();
		$existe = $this->revisar_habilidades();
		$session = isset($_SESSION['id'])?$_SESSION['id']:'';
		if($session != ''){
			
		$var1 = "update 01habilidades set LUNES = '".$LUNES."', DDDD = '".$DDDD."' , LHORARIOS = '".$LHORARIOS."' , LOBSERBACIONES = '".$LOBSERBACIONES."' , MARTES = '".$MARTES."' , MAHORARIOS = '".$MAHORARIOS."' , MAOBSERBACIONES = '".$MAOBSERBACIONES."' , MIERCOLES = '".$MIERCOLES."' , MIHORARIOS = '".$MIHORARIOS."' , MIOBSERBACIONES = '".$MIOBSERBACIONES."' , JUEVES = '".$JUEVES."' , JHORARIOS = '".$JHORARIOS."' , JOBSERBACIONES = '".$JOBSERBACIONES."' , VIERNES = '".$VIERNES."' , VHORARIOS = '".$VHORARIOS."' , VOBSERBACIONES = '".$VOBSERBACIONES."' , SABADO = '".$SABADO."' , SHORARIOS = '".$SHORARIOS."' , SOBSERBACIONES = '".$SOBSERBACIONES."' , DOMINGO = '".$DOMINGO."' , DHORARIOS = '".$DHORARIOS."' , DOBSERBACIONES = '".$DOBSERBACIONES."' , FIESTA_MEXICANA = '".$FIESTA_MEXICANA."' , EFIESTA_MEXICANA = '".$EFIESTA_MEXICANA."' , OFIESTA_MEXICANA = '".$OFIESTA_MEXICANA."' , JEOPARDY = '".$JEOPARDY."' , EJEOPARDY = '".$EJEOPARDY."' , OJEOPARDY = '".$OJEOPARDY."' , RALLYS = '".$RALLYS."' , ERALLYS = '".$ERALLYS."' , ORALLYS = '".$ORALLYS."' , a100_MEXICANOS_DIJIERON = '".$a100_MEXICANOS_DIJIERON."' , E100_MEXICANOS_DIJIERON = '".$E100_MEXICANOS_DIJIERON."' , O100_MEXICANOS_DIJIERON = '".$O100_MEXICANOS_DIJIERON."' , NOCHE_DE_LAS_ESTRELLAS = '".$NOCHE_DE_LAS_ESTRELLAS."' , ENOCHE_DE_LAS_ESTRELLAS = '".$ENOCHE_DE_LAS_ESTRELLAS."' , ONOCHE_DE_LAS_ESTRELLAS = '".$ONOCHE_DE_LAS_ESTRELLAS."' , HANDS_UP = '".$HANDS_UP."' , EHANDS_UP = '".$EHANDS_UP."' , OHANDS_UP = '".$OHANDS_UP."' , CALAMAR = '".$CALAMAR."' , ECALAMAR = '".$ECALAMAR."' , OCALAMAR = '".$OCALAMAR."' , CRUCERO = '".$CRUCERO."' , ECRUCERO = '".$ECRUCERO."' , OCRUCERO = '".$OCRUCERO."' , MAESTRO_DE_CEREMONIAS = '".$MAESTRO_DE_CEREMONIAS."' , EMAESTRO_DE_CEREMONIAS = '".$EMAESTRO_DE_CEREMONIAS."' , OMAESTRO_DE_CEREMONIAS = '".$OMAESTRO_DE_CEREMONIAS."' , CASINO = '".$CASINO."' , ECASINO = '".$ECASINO."' , OCASINO = '".$OCASINO."' , CUBILETE = '".$CUBILETE."' , ECUBILETE = '".$ECUBILETE."' , OCUBILETE = '".$OCUBILETE."' , CRABS = '".$CRABS."' , ECRABS = '".$ECRABS."' , OCRABS = '".$OCRABS."' , RULETA = '".$RULETA."' , ERULETA = '".$ERULETA."' , ORULETA = '".$ORULETA."' , BLACK_JACK = '".$BLACK_JACK."' , EBLACK_JACK = '".$EBLACK_JACK."' , OBLACK_JACK = '".$OBLACK_JACK."' , INFLABLES = '".$INFLABLES."' , EINFLABLES = '".$EINFLABLES."' , OINFLABLES = '".$OINFLABLES."' , AAA = '".$AAA."' , AAAA = '".$AAAA."' , EAAAA = '".$EAAAA."' , OAAAA = '".$OAAAA."' , BBB = '".$BBB."' , BBBB = '".$BBBB."' , EBBB = '".$EBBB."' , OBBB = '".$OBBB."' , CCC = '".$CCC."' , CCCC = '".$CCCC."' , ECCC = '".$ECCC."' , OCCC = '".$OCCC."' , DDD = '".$DDD."' , EDDD = '".$EDDD."' , ODDD = '".$ODDD."' , EEE = '".$EEE."' , EEEEE = '".$EEEEE."' , EEEE = '".$EEEE."' , OEEE = '".$OEEE."' , FFF = '".$FFF."' , FFFF = '".$FFFF."' , EFFF = '".$EFFF."' , OFFF = '".$OFFF."' , GGG = '".$GGG."' , GGGG = '".$GGGG."' , EGGG = '".$EGGG."' , OGGG = '".$OGGG."' , OBSERVACIONES = '".$OBSERVACIONES."' , habilidades1 = '".$habilidades1."' where idRelacion = '".$session."' ;  ";
		
		$var2 = "insert into 01habilidades ( LUNES, LHORARIOS, LOBSERBACIONES, MARTES, MAHORARIOS, MAOBSERBACIONES, MIERCOLES, MIHORARIOS, MIOBSERBACIONES, JUEVES, JHORARIOS, JOBSERBACIONES, VIERNES, VHORARIOS, VOBSERBACIONES, SABADO, SHORARIOS, SOBSERBACIONES, DOMINGO, DHORARIOS, DOBSERBACIONES, FIESTA_MEXICANA, EFIESTA_MEXICANA, OFIESTA_MEXICANA, JEOPARDY, EJEOPARDY, OJEOPARDY, RALLYS, ERALLYS, ORALLYS, a100_MEXICANOS_DIJIERON, E100_MEXICANOS_DIJIERON, O100_MEXICANOS_DIJIERON, NOCHE_DE_LAS_ESTRELLAS, ENOCHE_DE_LAS_ESTRELLAS, ONOCHE_DE_LAS_ESTRELLAS, HANDS_UP, EHANDS_UP, OHANDS_UP, CALAMAR, ECALAMAR, OCALAMAR, CRUCERO, ECRUCERO, OCRUCERO, MAESTRO_DE_CEREMONIAS, EMAESTRO_DE_CEREMONIAS, OMAESTRO_DE_CEREMONIAS, CASINO, ECASINO, OCASINO, CUBILETE, ECUBILETE, OCUBILETE, CRABS, ECRABS, OCRABS, RULETA, ERULETA, ORULETA, BLACK_JACK, EBLACK_JACK, OBLACK_JACK, INFLABLES, EINFLABLES, OINFLABLES, AAA, AAAA, EAAAA, OAAAA, BBB, BBBB, EBBB, OBBB, CCC, CCCC, ECCC, OCCC, DDD, DDDD, EDDD, ODDD, EEE, EEEEE, EEEE, OEEE, FFF, FFFF, EFFF, OFFF, GGG, GGGG, EGGG, OGGG, OBSERVACIONES, habilidades1, idRelacion) values ( '".$LUNES."' , '".$LHORARIOS."' , '".$LOBSERBACIONES."' , '".$MARTES."' , '".$MAHORARIOS."' , '".$MAOBSERBACIONES."' , '".$MIERCOLES."' , '".$MIHORARIOS."' , '".$MIOBSERBACIONES."' , '".$JUEVES."' , '".$JHORARIOS."' , '".$JOBSERBACIONES."' , '".$VIERNES."' , '".$VHORARIOS."' , '".$VOBSERBACIONES."' , '".$SABADO."' , '".$SHORARIOS."' , '".$SOBSERBACIONES."' , '".$DOMINGO."' , '".$DHORARIOS."' , '".$DOBSERBACIONES."' , '".$FIESTA_MEXICANA."' , '".$EFIESTA_MEXICANA."' , '".$OFIESTA_MEXICANA."' , '".$JEOPARDY."' , '".$EJEOPARDY."' , '".$OJEOPARDY."' , '".$RALLYS."' , '".$ERALLYS."' , '".$ORALLYS."' , '".$a100_MEXICANOS_DIJIERON."' , '".$E100_MEXICANOS_DIJIERON."' , '".$O100_MEXICANOS_DIJIERON."' , '".$NOCHE_DE_LAS_ESTRELLAS."' , '".$ENOCHE_DE_LAS_ESTRELLAS."' , '".$ONOCHE_DE_LAS_ESTRELLAS."' , '".$HANDS_UP."' , '".$EHANDS_UP."' , '".$OHANDS_UP."' , '".$CALAMAR."' , '".$ECALAMAR."' , '".$OCALAMAR."' , '".$CRUCERO."' , '".$ECRUCERO."' , '".$OCRUCERO."' , '".$MAESTRO_DE_CEREMONIAS."' , '".$EMAESTRO_DE_CEREMONIAS."' , '".$OMAESTRO_DE_CEREMONIAS."' , '".$CASINO."' , '".$ECASINO."' , '".$OCASINO."' , '".$CUBILETE."' , '".$ECUBILETE."' , '".$OCUBILETE."' , '".$CRABS."' , '".$ECRABS."' , '".$OCRABS."' , '".$RULETA."' , '".$ERULETA."' , '".$ORULETA."' , '".$BLACK_JACK."' , '".$EBLACK_JACK."' , '".$OBLACK_JACK."' , '".$INFLABLES."' , '".$EINFLABLES."' , '".$OINFLABLES."' , '".$AAA."' , '".$AAAA."' , '".$EAAAA."' , '".$OAAAA."' , '".$BBB."' , '".$BBBB."' , '".$EBBB."' , '".$OBBB."' , '".$CCC."' , '".$CCCC."' , '".$ECCC."' , '".$OCCC."' , '".$DDD."', '".$DDDD."' , '".$EDDD."' , '".$ODDD."' , '".$EEE."' , '".$EEEEE."' , '".$EEEE."' , '".$OEEE."' , '".$FFF."' , '".$FFFF."' , '".$EFFF."' , '".$OFFF."' , '".$GGG."' , '".$GGGG."' , '".$EGGG."' , '".$OGGG."' , '".$OBSERVACIONES."' , '".$habilidades1."' , '".$session."' ); ";			
			
		if($existe>=1){

		mysqli_query($conn,$var1) or die('P233'.mysqli_error($conn));
		return "ACTUALIZADO";
		}else{
		mysqli_query($conn,$var2) or die('P237'.mysqli_error($conn));
		return "INGRESADO";
		}
		}else{
		echo "NO HAY UN USUARIO SELECCIONADO";	
		}		
	}




/**//**//**//**//*material y equipo asignado 1*//**//**//**//**/

	public function variablesMEASIGNADO1(){
		$conn = $this->db();
		$variablequery = "select * from 01materialequipo1 where idRelacion = '".$_SESSION['id']."' ";
		$arrayquery = mysqli_query($conn,$variablequery);
		return $row = mysqli_fetch_array($arrayquery, MYSQLI_ASSOC);		
	}

	public function revisar_MEASIGNADO1(){
		$conn = $this->db();
		$var1 = 'select id from 01materialequipo1 where idRelacion =  "'.$_SESSION['id'].'" ';
		$query = mysqli_query($conn,$var1) or die('P44'.mysqli_error($conn));
		$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
		return $row['id'];
	}

	public function guardar_MEASIGNADO1(  $COMPUTADORA_MARCA , $COMPUTADORA_NO_DE_CEL , $COMPUTADORA_MODELO , $COMPUTADORA_MODELO_2 , $COMPUTADORA_NUMERO_DE_SERIE , $PANTALLA_MARCA , $PANTALLA_NO_DE_CEL , $PANTALLA_MODELO , $PANTALLA_MODELO_2 , $PANTALLA_NUMERO_DE_SERIE , $MOUSE_MARCA , $MOUSE_NO_DE_CEL , $MOUSE_MODELO , $MOUSE_MODELO_2 , $MOUSE_NUMERO_DE_SERIE , $DISCO_EXTERNO_MARCA , $DISCO_EXTERNO_NO_DE_CEL , $DISCO_EXTERNO_MODELO , $DISCO_EXTERNO_MODELO_2 , $DISCO_EXTERNO_NUMERO_DE_SERIE , $MATERIAL_EQUIPO_ASIGNADO_OTRO_MARCA , $MATERIAL_EQUIPO_ASIGNADO_OTRO_NO_DE_CEL , $MATERIAL_EQUIPO_ASIGNADO_OTRO_MODELO , $MATERIAL_EQUIPO_ASIGNADO_OTRO_MODELO_2 , $MATERIAL_EQUIPO_ASIGNADO_OTRO_NUMERO_DE_SERIE , $MATERIAL_EQUIPO_ASIGNADO_OTRO_2_MARCA , $MATERIAL_EQUIPO_ASIGNADO_OTRO_2_NO_DE_CEL , $MATERIAL_EQUIPO_ASIGNADO_OTRO_2_MODELO , $MATERIAL_EQUIPO_ASIGNADO_OTRO_2_MODELO_2 , $MATERIAL_EQUIPO_ASIGNADO_OTRO_2_NUMERO_DE_SERIE , $TELEFONO_CELULAR_MARCA , $TELEFONO_CELULAR_NO_DE_CEL , $TELEFONO_CELULAR_MODELO , $TELEFONO_CELULAR_MODELO_2 , $TELEFONO_CELULAR_NUMERO_DE_SERIE , $DIADEMA_MARCA , $DIADEMA_NO_DE_CEL , $DIADEMA_MODELO , $DIADEMA_MODELO_2 , $DIADEMA_NUMERO_DE_SERIE , $VRIM_MARCA , $VRIM_NO_DE_CEL , $VRIM_MODELO , $VRIM_MODELO2, $VRIM_NUMERO_DE_SERIE , $SEGURO_CONTRA_ACCIDENTES_MARCA , $SEGURO_CONTRA_ACCIDENTES_NO_DE_CEL , $SEGURO_CONTRA_ACCIDENTES_MODELO , $SEGURO_CONTRA_ACCIDENTES_MODELO_2 , $SEGURO_CONTRA_ACCIDENTES_NUMERO_DE_SERIE , $SEGURO_DE_VIDA_MARCA , $SEGURO_DE_VIDA_NO_DE_CEL , $SEGURO_DE_VIDA_MODELO , $SEGURO_DE_VIDA_MODELO_2 , $SEGURO_DE_VIDA_NUMERO_DE_SERIE , $SEGURO_DE_GASTOS_MEDICOS_MAYORES_MARCA , $SEGURO_DE_GASTOS_MEDICOS_NO_DE_CEL , $SEGURO_DE_GASTOS_MEDICOS_MODELO , $SEGURO_DE_GASTOS_MEDICOS_MODELO_2 , $SEGURO_DE_GASTOS_MEDICOS_NUMERO_DE_SERIE , $TARJETA_DE_VALES_DE_DESPENSA_MARCA , $TARJETA_DE_VALES_DE_DESPENSA_NO_DE_CEL , $TARJETA_DE_VALES_DE_DESPENSA_MODELO , $TARJETA_DE_VALES_DE_DESPENSA_MODELO_2 , $TARJETA_DE_VALES_DE_DESPENSA_NUMERO_DE_SERIE , $TARJETA_VALES_GASOLINA_MARCA , $TARJETA_VALES_GASOLINA_NO_DE_CEL , $TARJETA_VALES_GASOLINA_MODELO , $TARJETA_VALES_GASOLINA_MODELO_2 , $TARJETA_VALES_GASOLINA_NUMERO_DE_SERIE , $COMPUTADORA_FECHA_DE_ENTREGA , $COMPUTADORA_FECHA_DE_DEVOLUCION , $COMPUTADORA_OBSERVACIONES , $PANTALLA_FECHA_DE_ENTREGA , $PANTALLA_FECHA_DE_DEVOLUCION , $PANTALLA_OBSERVACIONES , $MOUSE_FECHA_DE_ENTREGA , $MOUSE_FECHA_DE_DEVOLUCION , $MOUSE_OBSERVACIONES , $DISCO_EXTERNO_FECHA_DE_ENTREGA , $DISCO_EXTERNO_FECHA_DE_DEVOLUCION , $DISCO_EXTERNO_OBSERVACIONES , $MATERIAL_EQUIPO_ASIGNADO_OTRO_FECHA_DE_ENTREGA , $MATERIAL_EQUIPO_ASIGNADO_OTRO_FECHA_DE_DEVOLUCION , $MATERIAL_EQUIPO_ASIGNADO_OTRO_OBSERVACIONES , $MATERIAL_EQUIPO_ASIGNADO_OTRO_FECHA_DE_ENTREGA_2 , $MATERIAL_EQUIPO_ASIGNADO_OTRO_FECHA_DE_DEVOLUCION_2 , $MATERIAL_EQUIPO_ASIGNADO_OTRO_OBSERVACIONES_2 , $TELEFONO_CELULAR_FECHA_DE_ENTREGA , $TELEFONO_CELULAR_FECHA_DE_DEVOLUCION , $TELEFONO_CELULAR_OBSERVACIONES , $DIADEMA_FECHA_DE_ENTREGA , $DIADEMA_FECHA_DE_DEVOLUCION , $DIADEMA_OBSERVACIONES , $VRIM_FECHA_DE_ENTREGA , $VRIM_FECHA_DE_DEVOLUCION , $VRIM_OBSERVACIONES , $SEGURO_CONTRA_ACCIDENTES_FECHA_DE_ENTREGA , $SEGURO_CONTRA_ACCIDENTES_FECHA_DE_DEVOLUCION , $SEGURO_CONTRA_ACCIDENTES_OBSERVACIONES , $SEGURO_DE_VIDA_FECHA_DE_ENTREGA , $SEGURO_DE_VIDA_FECHA_DE_DEVOLUCION , $SEGURO_DE_VIDA_OBSERVACIONES , $SEGURO_DE_GASTOS_MEDICOS_FECHA_DE_ENTREGA , $SEGURO_DE_GASTOS_MEDICOS_FECHA_DE_DEVOLUCION , $SEGURO_DE_GASTOS_MEDICOS_OBSERVACIONES , $TARJETA_DE_VALES_DE_DESPENSA_FECHA_DE_ENTREGA , $TARJETA_DE_VALES_DE_DESPENSA_FECHA_DE_DEVOLUCION , $TARJETA_DE_VALES_DE_DESPENSA_OBSERVACIONES , $TARJETA_DE_VALES_GASOLINA_FECHA_DE_ENTREGA , $TARJETA_DE_VALES_GASOLINA_FECHA_DE_DEVOLUCION , $TARJETA_DE_VALES_GASOLINA_OBSERVACIONES , $CMEASIGNADO1){
		$conn = $this->db();
		$existe = $this->revisar_MEASIGNADO1();
		$session = isset($_SESSION['id'])?$_SESSION['id']:'';
		if($session != ''){
			
		$var1 = "update 01materialequipo1 set COMPUTADORA_MARCA = '".$COMPUTADORA_MARCA."' , COMPUTADORA_NO_DE_CEL = '".$COMPUTADORA_NO_DE_CEL."' , COMPUTADORA_MODELO = '".$COMPUTADORA_MODELO."' , COMPUTADORA_MODELO_2 = '".$COMPUTADORA_MODELO_2."' , COMPUTADORA_NUMERO_DE_SERIE = '".$COMPUTADORA_NUMERO_DE_SERIE."' , PANTALLA_MARCA = '".$PANTALLA_MARCA."' , PANTALLA_NO_DE_CEL = '".$PANTALLA_NO_DE_CEL."' , PANTALLA_MODELO = '".$PANTALLA_MODELO."' , PANTALLA_MODELO_2 = '".$PANTALLA_MODELO_2."' , PANTALLA_NUMERO_DE_SERIE = '".$PANTALLA_NUMERO_DE_SERIE."' , MOUSE_MARCA = '".$MOUSE_MARCA."' , MOUSE_NO_DE_CEL = '".$MOUSE_NO_DE_CEL."' , MOUSE_MODELO = '".$MOUSE_MODELO."' , MOUSE_MODELO_2 = '".$MOUSE_MODELO_2."' , MOUSE_NUMERO_DE_SERIE = '".$MOUSE_NUMERO_DE_SERIE."' , DISCO_EXTERNO_MARCA = '".$DISCO_EXTERNO_MARCA."' , DISCO_EXTERNO_NO_DE_CEL = '".$DISCO_EXTERNO_NO_DE_CEL."' , DISCO_EXTERNO_MODELO = '".$DISCO_EXTERNO_MODELO."' , DISCO_EXTERNO_MODELO_2 = '".$DISCO_EXTERNO_MODELO_2."' , DISCO_EXTERNO_NUMERO_DE_SERIE = '".$DISCO_EXTERNO_NUMERO_DE_SERIE."' , MATERIAL_EQUIPO_ASIGNADO_OTRO_MARCA = '".$MATERIAL_EQUIPO_ASIGNADO_OTRO_MARCA."' , MATERIAL_EQUIPO_ASIGNADO_OTRO_NO_DE_CEL = '".$MATERIAL_EQUIPO_ASIGNADO_OTRO_NO_DE_CEL."' , MATERIAL_EQUIPO_ASIGNADO_OTRO_MODELO = '".$MATERIAL_EQUIPO_ASIGNADO_OTRO_MODELO."' , MATERIAL_EQUIPO_ASIGNADO_OTRO_MODELO_2 = '".$MATERIAL_EQUIPO_ASIGNADO_OTRO_MODELO_2."' , MATERIAL_EQUIPO_ASIGNADO_OTRO_NUMERO_DE_SERIE = '".$MATERIAL_EQUIPO_ASIGNADO_OTRO_NUMERO_DE_SERIE."' , MATERIAL_EQUIPO_ASIGNADO_OTRO_2_MARCA = '".$MATERIAL_EQUIPO_ASIGNADO_OTRO_2_MARCA."' , MATERIAL_EQUIPO_ASIGNADO_OTRO_2_NO_DE_CEL = '".$MATERIAL_EQUIPO_ASIGNADO_OTRO_2_NO_DE_CEL."' , MATERIAL_EQUIPO_ASIGNADO_OTRO_2_MODELO = '".$MATERIAL_EQUIPO_ASIGNADO_OTRO_2_MODELO."' , MATERIAL_EQUIPO_ASIGNADO_OTRO_2_MODELO_2 = '".$MATERIAL_EQUIPO_ASIGNADO_OTRO_2_MODELO_2."' , MATERIAL_EQUIPO_ASIGNADO_OTRO_2_NUMERO_DE_SERIE = '".$MATERIAL_EQUIPO_ASIGNADO_OTRO_2_NUMERO_DE_SERIE."' , TELEFONO_CELULAR_MARCA = '".$TELEFONO_CELULAR_MARCA."' , TELEFONO_CELULAR_NO_DE_CEL = '".$TELEFONO_CELULAR_NO_DE_CEL."' , TELEFONO_CELULAR_MODELO = '".$TELEFONO_CELULAR_MODELO."' , TELEFONO_CELULAR_MODELO_2 = '".$TELEFONO_CELULAR_MODELO_2."' , TELEFONO_CELULAR_NUMERO_DE_SERIE = '".$TELEFONO_CELULAR_NUMERO_DE_SERIE."' , DIADEMA_MARCA = '".$DIADEMA_MARCA."' , DIADEMA_NO_DE_CEL = '".$DIADEMA_NO_DE_CEL."' , DIADEMA_MODELO = '".$DIADEMA_MODELO."' , DIADEMA_MODELO_2 = '".$DIADEMA_MODELO_2."' , DIADEMA_NUMERO_DE_SERIE = '".$DIADEMA_NUMERO_DE_SERIE."' , VRIM_MARCA = '".$VRIM_MARCA."' , VRIM_NO_DE_CEL = '".$VRIM_NO_DE_CEL."' , VRIM_MODELO = '".$VRIM_MODELO."' , VRIM_MODELO2 = '".$VRIM_MODELO2."' , VRIM_NUMERO_DE_SERIE = '".$VRIM_NUMERO_DE_SERIE."' , SEGURO_CONTRA_ACCIDENTES_MARCA = '".$SEGURO_CONTRA_ACCIDENTES_MARCA."' , SEGURO_CONTRA_ACCIDENTES_NO_DE_CEL = '".$SEGURO_CONTRA_ACCIDENTES_NO_DE_CEL."' , SEGURO_CONTRA_ACCIDENTES_MODELO = '".$SEGURO_CONTRA_ACCIDENTES_MODELO."' , SEGURO_CONTRA_ACCIDENTES_MODELO_2 = '".$SEGURO_CONTRA_ACCIDENTES_MODELO_2."' , SEGURO_CONTRA_ACCIDENTES_NUMERO_DE_SERIE = '".$SEGURO_CONTRA_ACCIDENTES_NUMERO_DE_SERIE."' , SEGURO_DE_VIDA_MARCA = '".$SEGURO_DE_VIDA_MARCA."' , SEGURO_DE_VIDA_NO_DE_CEL = '".$SEGURO_DE_VIDA_NO_DE_CEL."' , SEGURO_DE_VIDA_MODELO = '".$SEGURO_DE_VIDA_MODELO."' , SEGURO_DE_VIDA_MODELO_2 = '".$SEGURO_DE_VIDA_MODELO_2."' , SEGURO_DE_VIDA_NUMERO_DE_SERIE = '".$SEGURO_DE_VIDA_NUMERO_DE_SERIE."' , SEGURO_DE_GASTOS_MEDICOS_MAYORES_MARCA = '".$SEGURO_DE_GASTOS_MEDICOS_MAYORES_MARCA."' , SEGURO_DE_GASTOS_MEDICOS_NO_DE_CEL = '".$SEGURO_DE_GASTOS_MEDICOS_NO_DE_CEL."' , SEGURO_DE_GASTOS_MEDICOS_MODELO = '".$SEGURO_DE_GASTOS_MEDICOS_MODELO."' , SEGURO_DE_GASTOS_MEDICOS_MODELO_2 = '".$SEGURO_DE_GASTOS_MEDICOS_MODELO_2."' , SEGURO_DE_GASTOS_MEDICOS_NUMERO_DE_SERIE = '".$SEGURO_DE_GASTOS_MEDICOS_NUMERO_DE_SERIE."' , TARJETA_DE_VALES_DE_DESPENSA_MARCA = '".$TARJETA_DE_VALES_DE_DESPENSA_MARCA."' , TARJETA_DE_VALES_DE_DESPENSA_NO_DE_CEL = '".$TARJETA_DE_VALES_DE_DESPENSA_NO_DE_CEL."' , TARJETA_DE_VALES_DE_DESPENSA_MODELO = '".$TARJETA_DE_VALES_DE_DESPENSA_MODELO."' , TARJETA_DE_VALES_DE_DESPENSA_MODELO_2 = '".$TARJETA_DE_VALES_DE_DESPENSA_MODELO_2."' , TARJETA_DE_VALES_DE_DESPENSA_NUMERO_DE_SERIE = '".$TARJETA_DE_VALES_DE_DESPENSA_NUMERO_DE_SERIE."' , TARJETA_VALES_GASOLINA_MARCA = '".$TARJETA_VALES_GASOLINA_MARCA."' , TARJETA_VALES_GASOLINA_NO_DE_CEL = '".$TARJETA_VALES_GASOLINA_NO_DE_CEL."' , TARJETA_VALES_GASOLINA_MODELO = '".$TARJETA_VALES_GASOLINA_MODELO."' , TARJETA_VALES_GASOLINA_MODELO_2 = '".$TARJETA_VALES_GASOLINA_MODELO_2."' , TARJETA_VALES_GASOLINA_NUMERO_DE_SERIE = '".$TARJETA_VALES_GASOLINA_NUMERO_DE_SERIE."' , COMPUTADORA_FECHA_DE_ENTREGA = '".$COMPUTADORA_FECHA_DE_ENTREGA."' , COMPUTADORA_FECHA_DE_DEVOLUCION = '".$COMPUTADORA_FECHA_DE_DEVOLUCION."' , COMPUTADORA_OBSERVACIONES = '".$COMPUTADORA_OBSERVACIONES."' , PANTALLA_FECHA_DE_ENTREGA = '".$PANTALLA_FECHA_DE_ENTREGA."' , PANTALLA_FECHA_DE_DEVOLUCION = '".$PANTALLA_FECHA_DE_DEVOLUCION."' , PANTALLA_OBSERVACIONES = '".$PANTALLA_OBSERVACIONES."' , MOUSE_FECHA_DE_ENTREGA = '".$MOUSE_FECHA_DE_ENTREGA."' , MOUSE_FECHA_DE_DEVOLUCION = '".$MOUSE_FECHA_DE_DEVOLUCION."' , MOUSE_OBSERVACIONES = '".$MOUSE_OBSERVACIONES."' , DISCO_EXTERNO_FECHA_DE_ENTREGA = '".$DISCO_EXTERNO_FECHA_DE_ENTREGA."' , DISCO_EXTERNO_FECHA_DE_DEVOLUCION = '".$DISCO_EXTERNO_FECHA_DE_DEVOLUCION."' , DISCO_EXTERNO_OBSERVACIONES = '".$DISCO_EXTERNO_OBSERVACIONES."' , MATERIAL_EQUIPO_ASIGNADO_OTRO_FECHA_DE_ENTREGA = '".$MATERIAL_EQUIPO_ASIGNADO_OTRO_FECHA_DE_ENTREGA."' , MATERIAL_EQUIPO_ASIGNADO_OTRO_FECHA_DE_DEVOLUCION = '".$MATERIAL_EQUIPO_ASIGNADO_OTRO_FECHA_DE_DEVOLUCION."' , MATERIAL_EQUIPO_ASIGNADO_OTRO_OBSERVACIONES = '".$MATERIAL_EQUIPO_ASIGNADO_OTRO_OBSERVACIONES."' , MATERIAL_EQUIPO_ASIGNADO_OTRO_FECHA_DE_ENTREGA_2 = '".$MATERIAL_EQUIPO_ASIGNADO_OTRO_FECHA_DE_ENTREGA_2."' , MATERIAL_EQUIPO_ASIGNADO_OTRO_FECHA_DE_DEVOLUCION_2 = '".$MATERIAL_EQUIPO_ASIGNADO_OTRO_FECHA_DE_DEVOLUCION_2."' , MATERIAL_EQUIPO_ASIGNADO_OTRO_OBSERVACIONES_2 = '".$MATERIAL_EQUIPO_ASIGNADO_OTRO_OBSERVACIONES_2."' , TELEFONO_CELULAR_FECHA_DE_ENTREGA = '".$TELEFONO_CELULAR_FECHA_DE_ENTREGA."' , TELEFONO_CELULAR_FECHA_DE_DEVOLUCION = '".$TELEFONO_CELULAR_FECHA_DE_DEVOLUCION."' , TELEFONO_CELULAR_OBSERVACIONES = '".$TELEFONO_CELULAR_OBSERVACIONES."' , DIADEMA_FECHA_DE_ENTREGA = '".$DIADEMA_FECHA_DE_ENTREGA."' , DIADEMA_FECHA_DE_DEVOLUCION = '".$DIADEMA_FECHA_DE_DEVOLUCION."' , DIADEMA_OBSERVACIONES = '".$DIADEMA_OBSERVACIONES."' , VRIM_FECHA_DE_ENTREGA = '".$VRIM_FECHA_DE_ENTREGA."' , VRIM_FECHA_DE_DEVOLUCION = '".$VRIM_FECHA_DE_DEVOLUCION."' , VRIM_OBSERVACIONES = '".$VRIM_OBSERVACIONES."' , SEGURO_CONTRA_ACCIDENTES_FECHA_DE_ENTREGA = '".$SEGURO_CONTRA_ACCIDENTES_FECHA_DE_ENTREGA."' , SEGURO_CONTRA_ACCIDENTES_FECHA_DE_DEVOLUCION = '".$SEGURO_CONTRA_ACCIDENTES_FECHA_DE_DEVOLUCION."' , SEGURO_CONTRA_ACCIDENTES_OBSERVACIONES = '".$SEGURO_CONTRA_ACCIDENTES_OBSERVACIONES."' , SEGURO_DE_VIDA_FECHA_DE_ENTREGA = '".$SEGURO_DE_VIDA_FECHA_DE_ENTREGA."' , SEGURO_DE_VIDA_FECHA_DE_DEVOLUCION = '".$SEGURO_DE_VIDA_FECHA_DE_DEVOLUCION."' , SEGURO_DE_VIDA_OBSERVACIONES = '".$SEGURO_DE_VIDA_OBSERVACIONES."' , SEGURO_DE_GASTOS_MEDICOS_FECHA_DE_ENTREGA = '".$SEGURO_DE_GASTOS_MEDICOS_FECHA_DE_ENTREGA."' , SEGURO_DE_GASTOS_MEDICOS_FECHA_DE_DEVOLUCION = '".$SEGURO_DE_GASTOS_MEDICOS_FECHA_DE_DEVOLUCION."' , SEGURO_DE_GASTOS_MEDICOS_OBSERVACIONES = '".$SEGURO_DE_GASTOS_MEDICOS_OBSERVACIONES."' , TARJETA_DE_VALES_DE_DESPENSA_FECHA_DE_ENTREGA = '".$TARJETA_DE_VALES_DE_DESPENSA_FECHA_DE_ENTREGA."' , TARJETA_DE_VALES_DE_DESPENSA_FECHA_DE_DEVOLUCION = '".$TARJETA_DE_VALES_DE_DESPENSA_FECHA_DE_DEVOLUCION."' , TARJETA_DE_VALES_DE_DESPENSA_OBSERVACIONES = '".$TARJETA_DE_VALES_DE_DESPENSA_OBSERVACIONES."' , TARJETA_DE_VALES_GASOLINA_FECHA_DE_ENTREGA = '".$TARJETA_DE_VALES_GASOLINA_FECHA_DE_ENTREGA."' , TARJETA_DE_VALES_GASOLINA_FECHA_DE_DEVOLUCION = '".$TARJETA_DE_VALES_GASOLINA_FECHA_DE_DEVOLUCION."' , TARJETA_DE_VALES_GASOLINA_OBSERVACIONES = '".$TARJETA_DE_VALES_GASOLINA_OBSERVACIONES."' , CMEASIGNADO1 = '".$CMEASIGNADO1."' where idRelacion = '".$session."' ; ";
		
		$var2 = "insert into 01materialequipo1 ( COMPUTADORA_MARCA, COMPUTADORA_NO_DE_CEL, COMPUTADORA_MODELO, COMPUTADORA_MODELO_2, COMPUTADORA_NUMERO_DE_SERIE, PANTALLA_MARCA, PANTALLA_NO_DE_CEL, PANTALLA_MODELO, PANTALLA_MODELO_2, PANTALLA_NUMERO_DE_SERIE, MOUSE_MARCA, MOUSE_NO_DE_CEL, MOUSE_MODELO, MOUSE_MODELO_2, MOUSE_NUMERO_DE_SERIE, DISCO_EXTERNO_MARCA, DISCO_EXTERNO_NO_DE_CEL, DISCO_EXTERNO_MODELO, DISCO_EXTERNO_MODELO_2, DISCO_EXTERNO_NUMERO_DE_SERIE, MATERIAL_EQUIPO_ASIGNADO_OTRO_MARCA, MATERIAL_EQUIPO_ASIGNADO_OTRO_NO_DE_CEL, MATERIAL_EQUIPO_ASIGNADO_OTRO_MODELO, MATERIAL_EQUIPO_ASIGNADO_OTRO_MODELO_2, MATERIAL_EQUIPO_ASIGNADO_OTRO_NUMERO_DE_SERIE, MATERIAL_EQUIPO_ASIGNADO_OTRO_2_MARCA, MATERIAL_EQUIPO_ASIGNADO_OTRO_2_NO_DE_CEL, MATERIAL_EQUIPO_ASIGNADO_OTRO_2_MODELO, MATERIAL_EQUIPO_ASIGNADO_OTRO_2_MODELO_2, MATERIAL_EQUIPO_ASIGNADO_OTRO_2_NUMERO_DE_SERIE, TELEFONO_CELULAR_MARCA, TELEFONO_CELULAR_NO_DE_CEL, TELEFONO_CELULAR_MODELO, TELEFONO_CELULAR_MODELO_2, TELEFONO_CELULAR_NUMERO_DE_SERIE, DIADEMA_MARCA, DIADEMA_NO_DE_CEL, DIADEMA_MODELO, DIADEMA_MODELO_2, DIADEMA_NUMERO_DE_SERIE, VRIM_MARCA, VRIM_NO_DE_CEL, VRIM_MODELO, VRIM_MODELO2, VRIM_NUMERO_DE_SERIE, SEGURO_CONTRA_ACCIDENTES_MARCA, SEGURO_CONTRA_ACCIDENTES_NO_DE_CEL, SEGURO_CONTRA_ACCIDENTES_MODELO, SEGURO_CONTRA_ACCIDENTES_MODELO_2, SEGURO_CONTRA_ACCIDENTES_NUMERO_DE_SERIE, SEGURO_DE_VIDA_MARCA, SEGURO_DE_VIDA_NO_DE_CEL, SEGURO_DE_VIDA_MODELO, SEGURO_DE_VIDA_MODELO_2, SEGURO_DE_VIDA_NUMERO_DE_SERIE, SEGURO_DE_GASTOS_MEDICOS_MAYORES_MARCA, SEGURO_DE_GASTOS_MEDICOS_NO_DE_CEL, SEGURO_DE_GASTOS_MEDICOS_MODELO, SEGURO_DE_GASTOS_MEDICOS_MODELO_2, SEGURO_DE_GASTOS_MEDICOS_NUMERO_DE_SERIE, TARJETA_DE_VALES_DE_DESPENSA_MARCA, TARJETA_DE_VALES_DE_DESPENSA_NO_DE_CEL, TARJETA_DE_VALES_DE_DESPENSA_MODELO, TARJETA_DE_VALES_DE_DESPENSA_MODELO_2, TARJETA_DE_VALES_DE_DESPENSA_NUMERO_DE_SERIE, TARJETA_VALES_GASOLINA_MARCA, TARJETA_VALES_GASOLINA_NO_DE_CEL, TARJETA_VALES_GASOLINA_MODELO, TARJETA_VALES_GASOLINA_MODELO_2, TARJETA_VALES_GASOLINA_NUMERO_DE_SERIE, COMPUTADORA_FECHA_DE_ENTREGA, COMPUTADORA_FECHA_DE_DEVOLUCION, COMPUTADORA_OBSERVACIONES, PANTALLA_FECHA_DE_ENTREGA, PANTALLA_FECHA_DE_DEVOLUCION, PANTALLA_OBSERVACIONES, MOUSE_FECHA_DE_ENTREGA, MOUSE_FECHA_DE_DEVOLUCION, MOUSE_OBSERVACIONES, DISCO_EXTERNO_FECHA_DE_ENTREGA, DISCO_EXTERNO_FECHA_DE_DEVOLUCION, DISCO_EXTERNO_OBSERVACIONES, MATERIAL_EQUIPO_ASIGNADO_OTRO_FECHA_DE_ENTREGA, MATERIAL_EQUIPO_ASIGNADO_OTRO_FECHA_DE_DEVOLUCION, MATERIAL_EQUIPO_ASIGNADO_OTRO_OBSERVACIONES, MATERIAL_EQUIPO_ASIGNADO_OTRO_FECHA_DE_ENTREGA_2, MATERIAL_EQUIPO_ASIGNADO_OTRO_FECHA_DE_DEVOLUCION_2, MATERIAL_EQUIPO_ASIGNADO_OTRO_OBSERVACIONES_2, TELEFONO_CELULAR_FECHA_DE_ENTREGA, TELEFONO_CELULAR_FECHA_DE_DEVOLUCION, TELEFONO_CELULAR_OBSERVACIONES, DIADEMA_FECHA_DE_ENTREGA, DIADEMA_FECHA_DE_DEVOLUCION, DIADEMA_OBSERVACIONES, VRIM_FECHA_DE_ENTREGA, VRIM_FECHA_DE_DEVOLUCION, VRIM_OBSERVACIONES, SEGURO_CONTRA_ACCIDENTES_FECHA_DE_ENTREGA, SEGURO_CONTRA_ACCIDENTES_FECHA_DE_DEVOLUCION, SEGURO_CONTRA_ACCIDENTES_OBSERVACIONES, SEGURO_DE_VIDA_FECHA_DE_ENTREGA, SEGURO_DE_VIDA_FECHA_DE_DEVOLUCION, SEGURO_DE_VIDA_OBSERVACIONES, SEGURO_DE_GASTOS_MEDICOS_FECHA_DE_ENTREGA, SEGURO_DE_GASTOS_MEDICOS_FECHA_DE_DEVOLUCION, SEGURO_DE_GASTOS_MEDICOS_OBSERVACIONES, TARJETA_DE_VALES_DE_DESPENSA_FECHA_DE_ENTREGA, TARJETA_DE_VALES_DE_DESPENSA_FECHA_DE_DEVOLUCION, TARJETA_DE_VALES_DE_DESPENSA_OBSERVACIONES, TARJETA_DE_VALES_GASOLINA_FECHA_DE_ENTREGA, TARJETA_DE_VALES_GASOLINA_FECHA_DE_DEVOLUCION, TARJETA_DE_VALES_GASOLINA_OBSERVACIONES, CMEASIGNADO1, idRelacion) values ( '".$COMPUTADORA_MARCA."' , '".$COMPUTADORA_NO_DE_CEL."' , '".$COMPUTADORA_MODELO."' , '".$COMPUTADORA_MODELO_2."' , '".$COMPUTADORA_NUMERO_DE_SERIE."' , '".$PANTALLA_MARCA."' , '".$PANTALLA_NO_DE_CEL."' , '".$PANTALLA_MODELO."' , '".$PANTALLA_MODELO_2."' , '".$PANTALLA_NUMERO_DE_SERIE."' , '".$MOUSE_MARCA."' , '".$MOUSE_NO_DE_CEL."' , '".$MOUSE_MODELO."' , '".$MOUSE_MODELO_2."' , '".$MOUSE_NUMERO_DE_SERIE."' , '".$DISCO_EXTERNO_MARCA."' , '".$DISCO_EXTERNO_NO_DE_CEL."' , '".$DISCO_EXTERNO_MODELO."' , '".$DISCO_EXTERNO_MODELO_2."' , '".$DISCO_EXTERNO_NUMERO_DE_SERIE."' , '".$MATERIAL_EQUIPO_ASIGNADO_OTRO_MARCA."' , '".$MATERIAL_EQUIPO_ASIGNADO_OTRO_NO_DE_CEL."' , '".$MATERIAL_EQUIPO_ASIGNADO_OTRO_MODELO."' , '".$MATERIAL_EQUIPO_ASIGNADO_OTRO_MODELO_2."' , '".$MATERIAL_EQUIPO_ASIGNADO_OTRO_NUMERO_DE_SERIE."' , '".$MATERIAL_EQUIPO_ASIGNADO_OTRO_2_MARCA."' , '".$MATERIAL_EQUIPO_ASIGNADO_OTRO_2_NO_DE_CEL."' , '".$MATERIAL_EQUIPO_ASIGNADO_OTRO_2_MODELO."' , '".$MATERIAL_EQUIPO_ASIGNADO_OTRO_2_MODELO_2."' , '".$MATERIAL_EQUIPO_ASIGNADO_OTRO_2_NUMERO_DE_SERIE."' , '".$TELEFONO_CELULAR_MARCA."' , '".$TELEFONO_CELULAR_NO_DE_CEL."' , '".$TELEFONO_CELULAR_MODELO."' , '".$TELEFONO_CELULAR_MODELO_2."' , '".$TELEFONO_CELULAR_NUMERO_DE_SERIE."' , '".$DIADEMA_MARCA."' , '".$DIADEMA_NO_DE_CEL."' , '".$DIADEMA_MODELO."' , '".$DIADEMA_MODELO_2."' , '".$DIADEMA_NUMERO_DE_SERIE."' , '".$VRIM_MARCA."' , '".$VRIM_NO_DE_CEL."' , '".$VRIM_MODELO."', '".$VRIM_MODELO2."' , '".$VRIM_NUMERO_DE_SERIE."' , '".$SEGURO_CONTRA_ACCIDENTES_MARCA."' , '".$SEGURO_CONTRA_ACCIDENTES_NO_DE_CEL."' , '".$SEGURO_CONTRA_ACCIDENTES_MODELO."' , '".$SEGURO_CONTRA_ACCIDENTES_MODELO_2."' , '".$SEGURO_CONTRA_ACCIDENTES_NUMERO_DE_SERIE."' , '".$SEGURO_DE_VIDA_MARCA."' , '".$SEGURO_DE_VIDA_NO_DE_CEL."' , '".$SEGURO_DE_VIDA_MODELO."' , '".$SEGURO_DE_VIDA_MODELO_2."' , '".$SEGURO_DE_VIDA_NUMERO_DE_SERIE."' , '".$SEGURO_DE_GASTOS_MEDICOS_MAYORES_MARCA."' , '".$SEGURO_DE_GASTOS_MEDICOS_NO_DE_CEL."' , '".$SEGURO_DE_GASTOS_MEDICOS_MODELO."' , '".$SEGURO_DE_GASTOS_MEDICOS_MODELO_2."' , '".$SEGURO_DE_GASTOS_MEDICOS_NUMERO_DE_SERIE."' , '".$TARJETA_DE_VALES_DE_DESPENSA_MARCA."' , '".$TARJETA_DE_VALES_DE_DESPENSA_NO_DE_CEL."' , '".$TARJETA_DE_VALES_DE_DESPENSA_MODELO."' , '".$TARJETA_DE_VALES_DE_DESPENSA_MODELO_2."' , '".$TARJETA_DE_VALES_DE_DESPENSA_NUMERO_DE_SERIE."' , '".$TARJETA_VALES_GASOLINA_MARCA."' , '".$TARJETA_VALES_GASOLINA_NO_DE_CEL."' , '".$TARJETA_VALES_GASOLINA_MODELO."' , '".$TARJETA_VALES_GASOLINA_MODELO_2."' , '".$TARJETA_VALES_GASOLINA_NUMERO_DE_SERIE."' , '".$COMPUTADORA_FECHA_DE_ENTREGA."' , '".$COMPUTADORA_FECHA_DE_DEVOLUCION."' , '".$COMPUTADORA_OBSERVACIONES."' , '".$PANTALLA_FECHA_DE_ENTREGA."' , '".$PANTALLA_FECHA_DE_DEVOLUCION."' , '".$PANTALLA_OBSERVACIONES."' , '".$MOUSE_FECHA_DE_ENTREGA."' , '".$MOUSE_FECHA_DE_DEVOLUCION."' , '".$MOUSE_OBSERVACIONES."' , '".$DISCO_EXTERNO_FECHA_DE_ENTREGA."' , '".$DISCO_EXTERNO_FECHA_DE_DEVOLUCION."' , '".$DISCO_EXTERNO_OBSERVACIONES."' , '".$MATERIAL_EQUIPO_ASIGNADO_OTRO_FECHA_DE_ENTREGA."' , '".$MATERIAL_EQUIPO_ASIGNADO_OTRO_FECHA_DE_DEVOLUCION."' , '".$MATERIAL_EQUIPO_ASIGNADO_OTRO_OBSERVACIONES."' , '".$MATERIAL_EQUIPO_ASIGNADO_OTRO_FECHA_DE_ENTREGA_2."' , '".$MATERIAL_EQUIPO_ASIGNADO_OTRO_FECHA_DE_DEVOLUCION_2."' , '".$MATERIAL_EQUIPO_ASIGNADO_OTRO_OBSERVACIONES_2."' , '".$TELEFONO_CELULAR_FECHA_DE_ENTREGA."' , '".$TELEFONO_CELULAR_FECHA_DE_DEVOLUCION."' , '".$TELEFONO_CELULAR_OBSERVACIONES."' , '".$DIADEMA_FECHA_DE_ENTREGA."' , '".$DIADEMA_FECHA_DE_DEVOLUCION."' , '".$DIADEMA_OBSERVACIONES."' , '".$VRIM_FECHA_DE_ENTREGA."' , '".$VRIM_FECHA_DE_DEVOLUCION."' , '".$VRIM_OBSERVACIONES."' , '".$SEGURO_CONTRA_ACCIDENTES_FECHA_DE_ENTREGA."' , '".$SEGURO_CONTRA_ACCIDENTES_FECHA_DE_DEVOLUCION."' , '".$SEGURO_CONTRA_ACCIDENTES_OBSERVACIONES."' , '".$SEGURO_DE_VIDA_FECHA_DE_ENTREGA."' , '".$SEGURO_DE_VIDA_FECHA_DE_DEVOLUCION."' , '".$SEGURO_DE_VIDA_OBSERVACIONES."' , '".$SEGURO_DE_GASTOS_MEDICOS_FECHA_DE_ENTREGA."' , '".$SEGURO_DE_GASTOS_MEDICOS_FECHA_DE_DEVOLUCION."' , '".$SEGURO_DE_GASTOS_MEDICOS_OBSERVACIONES."' , '".$TARJETA_DE_VALES_DE_DESPENSA_FECHA_DE_ENTREGA."' , '".$TARJETA_DE_VALES_DE_DESPENSA_FECHA_DE_DEVOLUCION."' , '".$TARJETA_DE_VALES_DE_DESPENSA_OBSERVACIONES."' , '".$TARJETA_DE_VALES_GASOLINA_FECHA_DE_ENTREGA."' , '".$TARJETA_DE_VALES_GASOLINA_FECHA_DE_DEVOLUCION."' , '".$TARJETA_DE_VALES_GASOLINA_OBSERVACIONES."' , '".$CMEASIGNADO1."' , '".$session."' ); ";			
			
		if($existe>=1){

		mysqli_query($conn,$var1) or die('P233'.mysqli_error($conn));
		return "ACTUALIZADO";
		}else{
		mysqli_query($conn,$var2) or die('P237'.mysqli_error($conn));
		return "INGRESADO";
		}
		}else{
		echo "NO HAY UN USUARIO SELECCIONADO";	
		}		
	}








/**//**//**//**//*material y equipo asignado 2 variablesMEASIGNADO2 *//**//**//**//**/

	public function variablesMEASIGNADO2(){
		$conn = $this->db();
		$variablequery = "select * from 01materialequipo2 where idRelacion = '".$_SESSION['id']."' ";
		$arrayquery = mysqli_query($conn,$variablequery);
		return $row = mysqli_fetch_array($arrayquery, MYSQLI_ASSOC);		
	}

	public function revisar_MEASIGNADO2(){
		$conn = $this->db();
		$var1 = 'select id from 01materialequipo2 where idRelacion =  "'.$_SESSION['id'].'" ';
		$query = mysqli_query($conn,$var1) or die('P44'.mysqli_error($conn));
		$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
		return $row['id'];
	}

	public function guardar_MEASIGNADO2($TALLA_CAMISA_MARCA , $TALLA_CAMISA_NO_DE_CEL , $TALLA_CAMISA_MODELO , $TALLA_CAMISA_MODELO_2 , $TALLA_CAMISA_NUMERO_DE_SERIE , $TALLA_PLAYERA_POLO_MARCA , $TALLA_PLAYERA_NO_DE_CEL , $TALLA_PLAYERA_MODELO , $TALLA_PLAYERA_MODELO_2 , $TALLA_PLAYERA_NUMERO_DE_SERIE , $TALLA_PLAYERA_MARCA , $TALLA_PANTALON_MARCA , $TALLA_PANTALON_NO_DE_CEL , $TALLA_PANTALON_MODELO , $TALLA_PANTALON_MODELO_2 , $TALLA_PANTALON_NUMERO_DE_SERIE , $TALLA_CHAMARRA_MARCA , $TALLA_CHAMARRA_NO_DE_CEL , $TALLA_CHAMARRA_MODELO , $TALLA_CHAMARRA_MODELO_2 , $TALLA_CHAMARRA_NUMERO_DE_SERIE , $TALLA_CHALECO_MARCA , $TALLA_CHALECO_NO_DE_CEL , $TALLA_CHALECO_MODELO , $TALLA_CHALECO_MODELO_2 , $TALLA_CHALECO_NUMERO_DE_SERIE , $TALLA_TENIS_MARCA , $TALLA_TENIS_NO_DE_CEL , $TALLA_TENIS_MODELO , $TALLA_TENIS_MODELO_2 , $TALLA_TENIS_NUMERO_DE_SERIE , $GORRA_MARCA , $GORRA_NO_DE_CEL , $GORRA_MODELO , $GORRA_MODELO_2 , $GORRA_NUMERO_DE_SERIE , $MALETA_GRANDE_MARCA , $MALETA_GRANDE_NO_DE_CEL , $MALETA_GRANDE_MODELO , $MALETA_GRANDE_MODELO_2 , $MALETA_GRANDE_NUMERO_DE_SERIE , $MALETA_MEDIANA_MARCA , $MALETA_MEDIANA_NO_DE_CEL , $MALETA_MEDIANA_MODELO , $MALETA_MEDIANA_MODELO_2 , $MALETA_MEDIANA_NUMERO_DE_SERIE , $MALETA_CHICA_MARCA , $MALETA_CHICA_NO_DE_CEL , $MALETA_CHICA_MODELO , $MALETA_CHICA_MODELO_2 , $MALETA_CHICA_NUMERO_DE_SERIE , $BACK_PACK_MARCA , $BACK_PACK_NO_DE_CEL , $BACK_PACK_MODELO , $BACK_PACK_MODELO_2 , $BACK_PACK_NUMERO_DE_SERIE , $OTROS_MARCA , $OTROS_NO_DE_CEL , $OTROS_MODELO , $OTROS_MODELO_2 , $OTROS_NUMERO_DE_SERIE , $OTROS_2_MARCA , $OTROS_2_NO_DE_CEL , $OTROS_2_MODELO , $OTROS_2_MODELO_2 , $OTROS_2_NUMERO_DE_SERIE , $OTROS_3_MARCA , $OTROS_3_NO_DE_CEL , $OTROS_3_MODELO , $OTROS_3_MODELO_2 , $OTROS_3_NUMERO_DE_SERIE , $OTROS_4_MARCA , $OTROS_4_NO_DE_CEL , $OTROS_4_MODELO , $OTROS_4_MODELO_2 , $OTROS_4_NUMERO_DE_SERIE , $TALLA_CAMISA_FECHA_DE_ENTREGA , $TALLA_CAMISA_FECHA_DE_DEVOLUCION , $TALLA_CAMISA_OBSERVACIONES , $TALLA_PLAYERA_FECHA_DE_ENTREGA , $TALLA_PLAYERA_FECHA_DE_DEVOLUCION , $TALLA_PLAYERA_OBSERVACIONES , $PLAYERA_FECHA_polo_ENTREGA, $PLAYERA_FECHA_polo_DEVOLUCION, $PLAYERA_polo_OBSERVACIONES, $TALLA_PANTALON_FECHA_DE_ENTREGA , $TALLA_PANTALON_FECHA_DE_DEVOLUCION , $TALLA_PANTALON_OBSERVACIONES , $TALLA_CHAMARRA_FECHA_DE_ENTREGA , $TALLA_CHAMARRA_FECHA_DE_DEVOLUCION , $TALLA_CHAMARRA_OBSERVACIONES , $TALLA_CHALECO_FECHA_DE_ENTREGA , $TALLA_CHALECO_FECHA_DE_DEVOLUCION , $TALLA_CHALECO_OBSERVACIONES , $TALLA_TENIS_FECHA_DE_ENTREGA , $TALLA_TENIS_FECHA_DE_DEVOLUCION , $TALLA_TENIS_OBSERVACIONES , $GORRA_FECHA_DE_ENTREGA , $GORRA_FECHA_DE_DEVOLUCION , $GORRA_OBSERVACIONES , $MALETA_GRANDE_FECHA_DE_ENTREGA , $MALETA_GRANDE_FECHA_DE_DEVOLUCION , $MALETA_GRANDE_OBSERVACIONES , $MALETA_MEDIANA_FECHA_DE_ENTREGA , $MALETA_MEDIANA_FECHA_DE_DEVOLUCION , $MALETA_MEDIANA_OBSERVACIONES , $MALETA_CHICA_FECHA_DE_ENTREGA , $MALETA_CHICA_FECHA_DE_DEVOLUCION , $MALETA_CHICA_OBSERVACIONES , $BACK_PACK_FECHA_DE_ENTREGA , $BACK_PACK_FECHA_DE_DEVOLUCION , $BACK_PACK_OBSERVACIONES , $OTROS_FECHA_DE_ENTREGA , $OTROS_FECHA_DE_DEVOLUCION , $OTROS_OBSERVACIONES , $OTROS_2_FECHA_DE_ENTREGA , $OTROS_2_FECHA_DE_DEVOLUCION , $OTROS_2_OBSERVACIONES , $OTROS_3_FECHA_DE_ENTREGA , $OTROS_3_FECHA_DE_DEVOLUCION , $OTROS_3_OBSERVACIONES , $OTROS_4_FECHA_DE_ENTREGA , $OTROS_4_FECHA_DE_DEVOLUCION , $OTROS_4_OBSERVACIONES , $CMEASIGNADO2){
		$conn = $this->db();
		$existe = $this->revisar_MEASIGNADO2();
		$session = isset($_SESSION['id'])?$_SESSION['id']:'';
		if($session != ''){
			//$PLAYERA_FECHA_polo_ENTREGA, $PLAYERA_FECHA_polo_DEVOLUCION, $PLAYERA_polo_OBSERVACIONES
		$var1 = "update 01materialequipo2 set 
		
		PLAYERA_FECHA_polo_ENTREGA = '".$PLAYERA_FECHA_polo_ENTREGA."' , 
		PLAYERA_FECHA_polo_DEVOLUCION = '".$PLAYERA_FECHA_polo_DEVOLUCION."' , 
		PLAYERA_polo_OBSERVACIONES = '".$PLAYERA_polo_OBSERVACIONES."' , 
		TALLA_CAMISA_MARCA = '".$TALLA_CAMISA_MARCA."' , TALLA_CAMISA_NO_DE_CEL = '".$TALLA_CAMISA_NO_DE_CEL."' , TALLA_CAMISA_MODELO = '".$TALLA_CAMISA_MODELO."' , TALLA_CAMISA_MODELO_2 = '".$TALLA_CAMISA_MODELO_2."' , TALLA_CAMISA_NUMERO_DE_SERIE = '".$TALLA_CAMISA_NUMERO_DE_SERIE."' , TALLA_PLAYERA_POLO_MARCA = '".$TALLA_PLAYERA_POLO_MARCA."' , TALLA_PLAYERA_NO_DE_CEL = '".$TALLA_PLAYERA_NO_DE_CEL."' , TALLA_PLAYERA_MODELO = '".$TALLA_PLAYERA_MODELO."' , TALLA_PLAYERA_MODELO_2 = '".$TALLA_PLAYERA_MODELO_2."' , TALLA_PLAYERA_NUMERO_DE_SERIE = '".$TALLA_PLAYERA_NUMERO_DE_SERIE."' , TALLA_PLAYERA_MARCA = '".$TALLA_PLAYERA_MARCA."' , TALLA_PANTALON_MARCA = '".$TALLA_PANTALON_MARCA."' , TALLA_PANTALON_NO_DE_CEL = '".$TALLA_PANTALON_NO_DE_CEL."' , TALLA_PANTALON_MODELO = '".$TALLA_PANTALON_MODELO."' , TALLA_PANTALON_MODELO_2 = '".$TALLA_PANTALON_MODELO_2."' , TALLA_PANTALON_NUMERO_DE_SERIE = '".$TALLA_PANTALON_NUMERO_DE_SERIE."' , TALLA_CHAMARRA_MARCA = '".$TALLA_CHAMARRA_MARCA."' , TALLA_CHAMARRA_NO_DE_CEL = '".$TALLA_CHAMARRA_NO_DE_CEL."' , TALLA_CHAMARRA_MODELO = '".$TALLA_CHAMARRA_MODELO."' , TALLA_CHAMARRA_MODELO_2 = '".$TALLA_CHAMARRA_MODELO_2."' , TALLA_CHAMARRA_NUMERO_DE_SERIE = '".$TALLA_CHAMARRA_NUMERO_DE_SERIE."' , TALLA_CHALECO_MARCA = '".$TALLA_CHALECO_MARCA."' , TALLA_CHALECO_NO_DE_CEL = '".$TALLA_CHALECO_NO_DE_CEL."' , TALLA_CHALECO_MODELO = '".$TALLA_CHALECO_MODELO."' , TALLA_CHALECO_MODELO_2 = '".$TALLA_CHALECO_MODELO_2."' , TALLA_CHALECO_NUMERO_DE_SERIE = '".$TALLA_CHALECO_NUMERO_DE_SERIE."' , TALLA_TENIS_MARCA = '".$TALLA_TENIS_MARCA."' , TALLA_TENIS_NO_DE_CEL = '".$TALLA_TENIS_NO_DE_CEL."' , TALLA_TENIS_MODELO = '".$TALLA_TENIS_MODELO."' , TALLA_TENIS_MODELO_2 = '".$TALLA_TENIS_MODELO_2."' , TALLA_TENIS_NUMERO_DE_SERIE = '".$TALLA_TENIS_NUMERO_DE_SERIE."' , GORRA_MARCA = '".$GORRA_MARCA."' , GORRA_NO_DE_CEL = '".$GORRA_NO_DE_CEL."' , GORRA_MODELO = '".$GORRA_MODELO."' , GORRA_MODELO_2 = '".$GORRA_MODELO_2."' , GORRA_NUMERO_DE_SERIE = '".$GORRA_NUMERO_DE_SERIE."' , MALETA_GRANDE_MARCA = '".$MALETA_GRANDE_MARCA."' , MALETA_GRANDE_NO_DE_CEL = '".$MALETA_GRANDE_NO_DE_CEL."' , MALETA_GRANDE_MODELO = '".$MALETA_GRANDE_MODELO."' , MALETA_GRANDE_MODELO_2 = '".$MALETA_GRANDE_MODELO_2."' , MALETA_GRANDE_NUMERO_DE_SERIE = '".$MALETA_GRANDE_NUMERO_DE_SERIE."' , MALETA_MEDIANA_MARCA = '".$MALETA_MEDIANA_MARCA."' , MALETA_MEDIANA_NO_DE_CEL = '".$MALETA_MEDIANA_NO_DE_CEL."' , MALETA_MEDIANA_MODELO = '".$MALETA_MEDIANA_MODELO."' , MALETA_MEDIANA_MODELO_2 = '".$MALETA_MEDIANA_MODELO_2."' , MALETA_MEDIANA_NUMERO_DE_SERIE = '".$MALETA_MEDIANA_NUMERO_DE_SERIE."' , MALETA_CHICA_MARCA = '".$MALETA_CHICA_MARCA."' , MALETA_CHICA_NO_DE_CEL = '".$MALETA_CHICA_NO_DE_CEL."' , MALETA_CHICA_MODELO = '".$MALETA_CHICA_MODELO."' , MALETA_CHICA_MODELO_2 = '".$MALETA_CHICA_MODELO_2."' , MALETA_CHICA_NUMERO_DE_SERIE = '".$MALETA_CHICA_NUMERO_DE_SERIE."' , BACK_PACK_MARCA = '".$BACK_PACK_MARCA."' , BACK_PACK_NO_DE_CEL = '".$BACK_PACK_NO_DE_CEL."' , BACK_PACK_MODELO = '".$BACK_PACK_MODELO."' , BACK_PACK_MODELO_2 = '".$BACK_PACK_MODELO_2."' , BACK_PACK_NUMERO_DE_SERIE = '".$BACK_PACK_NUMERO_DE_SERIE."' , OTROS_MARCA = '".$OTROS_MARCA."' , OTROS_NO_DE_CEL = '".$OTROS_NO_DE_CEL."' , OTROS_MODELO = '".$OTROS_MODELO."' , OTROS_MODELO_2 = '".$OTROS_MODELO_2."' , OTROS_NUMERO_DE_SERIE = '".$OTROS_NUMERO_DE_SERIE."' , OTROS_2_MARCA = '".$OTROS_2_MARCA."' , OTROS_2_NO_DE_CEL = '".$OTROS_2_NO_DE_CEL."' , OTROS_2_MODELO = '".$OTROS_2_MODELO."' , OTROS_2_MODELO_2 = '".$OTROS_2_MODELO_2."' , OTROS_2_NUMERO_DE_SERIE = '".$OTROS_2_NUMERO_DE_SERIE."' , OTROS_3_MARCA = '".$OTROS_3_MARCA."' , OTROS_3_NO_DE_CEL = '".$OTROS_3_NO_DE_CEL."' , OTROS_3_MODELO = '".$OTROS_3_MODELO."' , OTROS_3_MODELO_2 = '".$OTROS_3_MODELO_2."' , OTROS_3_NUMERO_DE_SERIE = '".$OTROS_3_NUMERO_DE_SERIE."' , OTROS_4_MARCA = '".$OTROS_4_MARCA."' , OTROS_4_NO_DE_CEL = '".$OTROS_4_NO_DE_CEL."' , OTROS_4_MODELO = '".$OTROS_4_MODELO."' , OTROS_4_MODELO_2 = '".$OTROS_4_MODELO_2."' , OTROS_4_NUMERO_DE_SERIE = '".$OTROS_4_NUMERO_DE_SERIE."' , TALLA_CAMISA_FECHA_DE_ENTREGA = '".$TALLA_CAMISA_FECHA_DE_ENTREGA."' , TALLA_CAMISA_FECHA_DE_DEVOLUCION = '".$TALLA_CAMISA_FECHA_DE_DEVOLUCION."' , TALLA_CAMISA_OBSERVACIONES = '".$TALLA_CAMISA_OBSERVACIONES."' , TALLA_PLAYERA_FECHA_DE_ENTREGA = '".$TALLA_PLAYERA_FECHA_DE_ENTREGA."' , TALLA_PLAYERA_FECHA_DE_DEVOLUCION = '".$TALLA_PLAYERA_FECHA_DE_DEVOLUCION."' , TALLA_PLAYERA_OBSERVACIONES = '".$TALLA_PLAYERA_OBSERVACIONES."' , TALLA_PANTALON_FECHA_DE_ENTREGA = '".$TALLA_PANTALON_FECHA_DE_ENTREGA."' , TALLA_PANTALON_FECHA_DE_DEVOLUCION = '".$TALLA_PANTALON_FECHA_DE_DEVOLUCION."' , TALLA_PANTALON_OBSERVACIONES = '".$TALLA_PANTALON_OBSERVACIONES."' , TALLA_CHAMARRA_FECHA_DE_ENTREGA = '".$TALLA_CHAMARRA_FECHA_DE_ENTREGA."' , TALLA_CHAMARRA_FECHA_DE_DEVOLUCION = '".$TALLA_CHAMARRA_FECHA_DE_DEVOLUCION."' , TALLA_CHAMARRA_OBSERVACIONES = '".$TALLA_CHAMARRA_OBSERVACIONES."' , TALLA_CHALECO_FECHA_DE_ENTREGA = '".$TALLA_CHALECO_FECHA_DE_ENTREGA."' , TALLA_CHALECO_FECHA_DE_DEVOLUCION = '".$TALLA_CHALECO_FECHA_DE_DEVOLUCION."' , TALLA_CHALECO_OBSERVACIONES = '".$TALLA_CHALECO_OBSERVACIONES."' , TALLA_TENIS_FECHA_DE_ENTREGA = '".$TALLA_TENIS_FECHA_DE_ENTREGA."' , TALLA_TENIS_FECHA_DE_DEVOLUCION = '".$TALLA_TENIS_FECHA_DE_DEVOLUCION."' , TALLA_TENIS_OBSERVACIONES = '".$TALLA_TENIS_OBSERVACIONES."' , GORRA_FECHA_DE_ENTREGA = '".$GORRA_FECHA_DE_ENTREGA."' , GORRA_FECHA_DE_DEVOLUCION = '".$GORRA_FECHA_DE_DEVOLUCION."' , GORRA_OBSERVACIONES = '".$GORRA_OBSERVACIONES."' , MALETA_GRANDE_FECHA_DE_ENTREGA = '".$MALETA_GRANDE_FECHA_DE_ENTREGA."' , MALETA_GRANDE_FECHA_DE_DEVOLUCION = '".$MALETA_GRANDE_FECHA_DE_DEVOLUCION."' , MALETA_GRANDE_OBSERVACIONES = '".$MALETA_GRANDE_OBSERVACIONES."' , MALETA_MEDIANA_FECHA_DE_ENTREGA = '".$MALETA_MEDIANA_FECHA_DE_ENTREGA."' , MALETA_MEDIANA_FECHA_DE_DEVOLUCION = '".$MALETA_MEDIANA_FECHA_DE_DEVOLUCION."' , MALETA_MEDIANA_OBSERVACIONES = '".$MALETA_MEDIANA_OBSERVACIONES."' , MALETA_CHICA_FECHA_DE_ENTREGA = '".$MALETA_CHICA_FECHA_DE_ENTREGA."' , MALETA_CHICA_FECHA_DE_DEVOLUCION = '".$MALETA_CHICA_FECHA_DE_DEVOLUCION."' , MALETA_CHICA_OBSERVACIONES = '".$MALETA_CHICA_OBSERVACIONES."' , BACK_PACK_FECHA_DE_ENTREGA = '".$BACK_PACK_FECHA_DE_ENTREGA."' , BACK_PACK_FECHA_DE_DEVOLUCION = '".$BACK_PACK_FECHA_DE_DEVOLUCION."' , BACK_PACK_OBSERVACIONES = '".$BACK_PACK_OBSERVACIONES."' , OTROS_FECHA_DE_ENTREGA = '".$OTROS_FECHA_DE_ENTREGA."' , OTROS_FECHA_DE_DEVOLUCION = '".$OTROS_FECHA_DE_DEVOLUCION."' , OTROS_OBSERVACIONES = '".$OTROS_OBSERVACIONES."' , OTROS_2_FECHA_DE_ENTREGA = '".$OTROS_2_FECHA_DE_ENTREGA."' , OTROS_2_FECHA_DE_DEVOLUCION = '".$OTROS_2_FECHA_DE_DEVOLUCION."' , OTROS_2_OBSERVACIONES = '".$OTROS_2_OBSERVACIONES."' , OTROS_3_FECHA_DE_ENTREGA = '".$OTROS_3_FECHA_DE_ENTREGA."' , OTROS_3_FECHA_DE_DEVOLUCION = '".$OTROS_3_FECHA_DE_DEVOLUCION."' , OTROS_3_OBSERVACIONES = '".$OTROS_3_OBSERVACIONES."' , OTROS_4_FECHA_DE_ENTREGA = '".$OTROS_4_FECHA_DE_ENTREGA."' , OTROS_4_FECHA_DE_DEVOLUCION = '".$OTROS_4_FECHA_DE_DEVOLUCION."' , OTROS_4_OBSERVACIONES = '".$OTROS_4_OBSERVACIONES."' , CMEASIGNADO2 = '".$CMEASIGNADO2."' where idRelacion = '".$session."' ; ";
			
		$var2 = "insert into 01materialequipo2 ( 
		PLAYERA_FECHA_polo_ENTREGA, PLAYERA_FECHA_polo_DEVOLUCION, PLAYERA_polo_OBSERVACIONES,
		TALLA_CAMISA_MARCA, TALLA_CAMISA_NO_DE_CEL, TALLA_CAMISA_MODELO, TALLA_CAMISA_MODELO_2, TALLA_CAMISA_NUMERO_DE_SERIE, TALLA_PLAYERA_POLO_MARCA, TALLA_PLAYERA_NO_DE_CEL, TALLA_PLAYERA_MODELO, TALLA_PLAYERA_MODELO_2, TALLA_PLAYERA_NUMERO_DE_SERIE, TALLA_PLAYERA_MARCA, TALLA_PANTALON_MARCA, TALLA_PANTALON_NO_DE_CEL, TALLA_PANTALON_MODELO, TALLA_PANTALON_MODELO_2, TALLA_PANTALON_NUMERO_DE_SERIE, TALLA_CHAMARRA_MARCA, TALLA_CHAMARRA_NO_DE_CEL, TALLA_CHAMARRA_MODELO, TALLA_CHAMARRA_MODELO_2, TALLA_CHAMARRA_NUMERO_DE_SERIE, TALLA_CHALECO_MARCA, TALLA_CHALECO_NO_DE_CEL, TALLA_CHALECO_MODELO, TALLA_CHALECO_MODELO_2, TALLA_CHALECO_NUMERO_DE_SERIE, TALLA_TENIS_MARCA, TALLA_TENIS_NO_DE_CEL, TALLA_TENIS_MODELO, TALLA_TENIS_MODELO_2, TALLA_TENIS_NUMERO_DE_SERIE, GORRA_MARCA, GORRA_NO_DE_CEL, GORRA_MODELO, GORRA_MODELO_2, GORRA_NUMERO_DE_SERIE, MALETA_GRANDE_MARCA, MALETA_GRANDE_NO_DE_CEL, MALETA_GRANDE_MODELO, MALETA_GRANDE_MODELO_2, MALETA_GRANDE_NUMERO_DE_SERIE, MALETA_MEDIANA_MARCA, MALETA_MEDIANA_NO_DE_CEL, MALETA_MEDIANA_MODELO, MALETA_MEDIANA_MODELO_2, MALETA_MEDIANA_NUMERO_DE_SERIE, MALETA_CHICA_MARCA, MALETA_CHICA_NO_DE_CEL, MALETA_CHICA_MODELO, MALETA_CHICA_MODELO_2, MALETA_CHICA_NUMERO_DE_SERIE, BACK_PACK_MARCA, BACK_PACK_NO_DE_CEL, BACK_PACK_MODELO, BACK_PACK_MODELO_2, BACK_PACK_NUMERO_DE_SERIE, OTROS_MARCA, OTROS_NO_DE_CEL, OTROS_MODELO, OTROS_MODELO_2, OTROS_NUMERO_DE_SERIE, OTROS_2_MARCA, OTROS_2_NO_DE_CEL, OTROS_2_MODELO, OTROS_2_MODELO_2, OTROS_2_NUMERO_DE_SERIE, OTROS_3_MARCA, OTROS_3_NO_DE_CEL, OTROS_3_MODELO, OTROS_3_MODELO_2, OTROS_3_NUMERO_DE_SERIE, OTROS_4_MARCA, OTROS_4_NO_DE_CEL, OTROS_4_MODELO, OTROS_4_MODELO_2, OTROS_4_NUMERO_DE_SERIE, TALLA_CAMISA_FECHA_DE_ENTREGA, TALLA_CAMISA_FECHA_DE_DEVOLUCION, TALLA_CAMISA_OBSERVACIONES, TALLA_PLAYERA_FECHA_DE_ENTREGA, TALLA_PLAYERA_FECHA_DE_DEVOLUCION, TALLA_PLAYERA_OBSERVACIONES, TALLA_PANTALON_FECHA_DE_ENTREGA, TALLA_PANTALON_FECHA_DE_DEVOLUCION, TALLA_PANTALON_OBSERVACIONES, TALLA_CHAMARRA_FECHA_DE_ENTREGA, TALLA_CHAMARRA_FECHA_DE_DEVOLUCION, TALLA_CHAMARRA_OBSERVACIONES, TALLA_CHALECO_FECHA_DE_ENTREGA, TALLA_CHALECO_FECHA_DE_DEVOLUCION, TALLA_CHALECO_OBSERVACIONES, TALLA_TENIS_FECHA_DE_ENTREGA, TALLA_TENIS_FECHA_DE_DEVOLUCION, TALLA_TENIS_OBSERVACIONES, GORRA_FECHA_DE_ENTREGA, GORRA_FECHA_DE_DEVOLUCION, GORRA_OBSERVACIONES, MALETA_GRANDE_FECHA_DE_ENTREGA, MALETA_GRANDE_FECHA_DE_DEVOLUCION, MALETA_GRANDE_OBSERVACIONES, MALETA_MEDIANA_FECHA_DE_ENTREGA, MALETA_MEDIANA_FECHA_DE_DEVOLUCION, MALETA_MEDIANA_OBSERVACIONES, MALETA_CHICA_FECHA_DE_ENTREGA, MALETA_CHICA_FECHA_DE_DEVOLUCION, MALETA_CHICA_OBSERVACIONES, BACK_PACK_FECHA_DE_ENTREGA, BACK_PACK_FECHA_DE_DEVOLUCION, BACK_PACK_OBSERVACIONES, OTROS_FECHA_DE_ENTREGA, OTROS_FECHA_DE_DEVOLUCION, OTROS_OBSERVACIONES, OTROS_2_FECHA_DE_ENTREGA, OTROS_2_FECHA_DE_DEVOLUCION, OTROS_2_OBSERVACIONES, OTROS_3_FECHA_DE_ENTREGA, OTROS_3_FECHA_DE_DEVOLUCION, OTROS_3_OBSERVACIONES, OTROS_4_FECHA_DE_ENTREGA, OTROS_4_FECHA_DE_DEVOLUCION, OTROS_4_OBSERVACIONES, CMEASIGNADO2, idRelacion) values ( '".$PLAYERA_FECHA_polo_ENTREGA."' , '".$PLAYERA_FECHA_polo_DEVOLUCION."' , '".$PLAYERA_polo_OBSERVACIONES."' ,'".$TALLA_CAMISA_MARCA."' , '".$TALLA_CAMISA_NO_DE_CEL."' , '".$TALLA_CAMISA_MODELO."' , '".$TALLA_CAMISA_MODELO_2."' , '".$TALLA_CAMISA_NUMERO_DE_SERIE."' , '".$TALLA_PLAYERA_POLO_MARCA."' , '".$TALLA_PLAYERA_NO_DE_CEL."' , '".$TALLA_PLAYERA_MODELO."' , '".$TALLA_PLAYERA_MODELO_2."' , '".$TALLA_PLAYERA_NUMERO_DE_SERIE."' , '".$TALLA_PLAYERA_MARCA."' , '".$TALLA_PANTALON_MARCA."' , '".$TALLA_PANTALON_NO_DE_CEL."' , '".$TALLA_PANTALON_MODELO."' , '".$TALLA_PANTALON_MODELO_2."' , '".$TALLA_PANTALON_NUMERO_DE_SERIE."' , '".$TALLA_CHAMARRA_MARCA."' , '".$TALLA_CHAMARRA_NO_DE_CEL."' , '".$TALLA_CHAMARRA_MODELO."' , '".$TALLA_CHAMARRA_MODELO_2."' , '".$TALLA_CHAMARRA_NUMERO_DE_SERIE."' , '".$TALLA_CHALECO_MARCA."' , '".$TALLA_CHALECO_NO_DE_CEL."' , '".$TALLA_CHALECO_MODELO."' , '".$TALLA_CHALECO_MODELO_2."' , '".$TALLA_CHALECO_NUMERO_DE_SERIE."' , '".$TALLA_TENIS_MARCA."' , '".$TALLA_TENIS_NO_DE_CEL."' , '".$TALLA_TENIS_MODELO."' , '".$TALLA_TENIS_MODELO_2."' , '".$TALLA_TENIS_NUMERO_DE_SERIE."' , '".$GORRA_MARCA."' , '".$GORRA_NO_DE_CEL."' , '".$GORRA_MODELO."' , '".$GORRA_MODELO_2."' , '".$GORRA_NUMERO_DE_SERIE."' , '".$MALETA_GRANDE_MARCA."' , '".$MALETA_GRANDE_NO_DE_CEL."' , '".$MALETA_GRANDE_MODELO."' , '".$MALETA_GRANDE_MODELO_2."' , '".$MALETA_GRANDE_NUMERO_DE_SERIE."' , '".$MALETA_MEDIANA_MARCA."' , '".$MALETA_MEDIANA_NO_DE_CEL."' , '".$MALETA_MEDIANA_MODELO."' , '".$MALETA_MEDIANA_MODELO_2."' , '".$MALETA_MEDIANA_NUMERO_DE_SERIE."' , '".$MALETA_CHICA_MARCA."' , '".$MALETA_CHICA_NO_DE_CEL."' , '".$MALETA_CHICA_MODELO."' , '".$MALETA_CHICA_MODELO_2."' , '".$MALETA_CHICA_NUMERO_DE_SERIE."' , '".$BACK_PACK_MARCA."' , '".$BACK_PACK_NO_DE_CEL."' , '".$BACK_PACK_MODELO."' , '".$BACK_PACK_MODELO_2."' , '".$BACK_PACK_NUMERO_DE_SERIE."' , '".$OTROS_MARCA."' , '".$OTROS_NO_DE_CEL."' , '".$OTROS_MODELO."' , '".$OTROS_MODELO_2."' , '".$OTROS_NUMERO_DE_SERIE."' , '".$OTROS_2_MARCA."' , '".$OTROS_2_NO_DE_CEL."' , '".$OTROS_2_MODELO."' , '".$OTROS_2_MODELO_2."' , '".$OTROS_2_NUMERO_DE_SERIE."' , '".$OTROS_3_MARCA."' , '".$OTROS_3_NO_DE_CEL."' , '".$OTROS_3_MODELO."' , '".$OTROS_3_MODELO_2."' , '".$OTROS_3_NUMERO_DE_SERIE."' , '".$OTROS_4_MARCA."' , '".$OTROS_4_NO_DE_CEL."' , '".$OTROS_4_MODELO."' , '".$OTROS_4_MODELO_2."' , '".$OTROS_4_NUMERO_DE_SERIE."' , '".$TALLA_CAMISA_FECHA_DE_ENTREGA."' , '".$TALLA_CAMISA_FECHA_DE_DEVOLUCION."' , '".$TALLA_CAMISA_OBSERVACIONES."' , '".$TALLA_PLAYERA_FECHA_DE_ENTREGA."' , '".$TALLA_PLAYERA_FECHA_DE_DEVOLUCION."' , '".$TALLA_PLAYERA_OBSERVACIONES."' , '".$TALLA_PANTALON_FECHA_DE_ENTREGA."' , '".$TALLA_PANTALON_FECHA_DE_DEVOLUCION."' , '".$TALLA_PANTALON_OBSERVACIONES."' , '".$TALLA_CHAMARRA_FECHA_DE_ENTREGA."' , '".$TALLA_CHAMARRA_FECHA_DE_DEVOLUCION."' , '".$TALLA_CHAMARRA_OBSERVACIONES."' , '".$TALLA_CHALECO_FECHA_DE_ENTREGA."' , '".$TALLA_CHALECO_FECHA_DE_DEVOLUCION."' , '".$TALLA_CHALECO_OBSERVACIONES."' , '".$TALLA_TENIS_FECHA_DE_ENTREGA."' , '".$TALLA_TENIS_FECHA_DE_DEVOLUCION."' , '".$TALLA_TENIS_OBSERVACIONES."' , '".$GORRA_FECHA_DE_ENTREGA."' , '".$GORRA_FECHA_DE_DEVOLUCION."' , '".$GORRA_OBSERVACIONES."' , '".$MALETA_GRANDE_FECHA_DE_ENTREGA."' , '".$MALETA_GRANDE_FECHA_DE_DEVOLUCION."' , '".$MALETA_GRANDE_OBSERVACIONES."' , '".$MALETA_MEDIANA_FECHA_DE_ENTREGA."' , '".$MALETA_MEDIANA_FECHA_DE_DEVOLUCION."' , '".$MALETA_MEDIANA_OBSERVACIONES."' , '".$MALETA_CHICA_FECHA_DE_ENTREGA."' , '".$MALETA_CHICA_FECHA_DE_DEVOLUCION."' , '".$MALETA_CHICA_OBSERVACIONES."' , '".$BACK_PACK_FECHA_DE_ENTREGA."' , '".$BACK_PACK_FECHA_DE_DEVOLUCION."' , '".$BACK_PACK_OBSERVACIONES."' , '".$OTROS_FECHA_DE_ENTREGA."' , '".$OTROS_FECHA_DE_DEVOLUCION."' , '".$OTROS_OBSERVACIONES."' , '".$OTROS_2_FECHA_DE_ENTREGA."' , '".$OTROS_2_FECHA_DE_DEVOLUCION."' , '".$OTROS_2_OBSERVACIONES."' , '".$OTROS_3_FECHA_DE_ENTREGA."' , '".$OTROS_3_FECHA_DE_DEVOLUCION."' , '".$OTROS_3_OBSERVACIONES."' , '".$OTROS_4_FECHA_DE_ENTREGA."' , '".$OTROS_4_FECHA_DE_DEVOLUCION."' , '".$OTROS_4_OBSERVACIONES."' , '".$CMEASIGNADO2."' , '".$session."' ); ";			
			
		if($existe>=1){	

		mysqli_query($conn,$var1) or die('P276'.mysqli_error($conn));
		return "ACTUALIZADO";
		}else{
		mysqli_query($conn,$var2) or die('P279'.mysqli_error($conn));
		return "INGRESADO";
		}
		}else{
		echo "NO HAY UN USUARIO SELECCIONADO";	
		}		
	}







/**//**//**//**//*material y equipo asignado 3*//**//**//**//**/

	public function variablesMEASIGNADO3(){
		$conn = $this->db();
		$variablequery = "select * from 01materialequipo3 where idRelacion = '".$_SESSION['id']."' ";
		$arrayquery = mysqli_query($conn,$variablequery);
		return $row = mysqli_fetch_array($arrayquery, MYSQLI_ASSOC);		
	}

	public function revisar_MEASIGNADO3(){
		$conn = $this->db();
		$var1 = 'select id from 01materialequipo3 where idRelacion =  "'.$_SESSION['id'].'" ';
		$query = mysqli_query($conn,$var1) or die('P44'.mysqli_error($conn));
		$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
		return $row['id'];
	}

	public function guardar_MEASIGNADO3($A_TALLA , $A_FECHA_DE_ENTREGA , $A_FECHA_DE_DEVOLUCION , $A_OBSERVACIONES , $B_TALLA , $B_FECHA_DE_ENTREGA , $B_FECHA_DE_DEVOLUCION , $DANIEL_OBSERVACIONES , $DANIEL_TALLA , $C_FECHA_DE_ENTREGA , $C_FECHA_DE_DEVOLUCION , $C_OBSERVACIONES , $D_TALLA , $D_FECHA_DE_ENTREGA , $D_FECHA_DE_DEVOLUCION , $D_OBSERVACIONES , $E_TALLA , $E_FECHA_DE_ENTREGA , $E_FECHA_DE_DEVOLUCION , $E_OBSERVACIONES , $F_TALLA , $F_FECHA_DE_ENTREGA , $F_FECHA_DE_DEVOLUCION , $F_OBSERVACIONES , $G_TALLA , $G_FECHA_DE_ENTREGA , $G_FECHA_DE_DEVOLUCION , $G_OBSERVACIONES , $CMEASIGNADO3 ){
		$conn = $this->db();
		$existe = $this->revisar_MEASIGNADO3();
		$session = isset($_SESSION['id'])?$_SESSION['id']:'';
		if($session != ''){
			
		$var1 = "update 01materialequipo3 set A_TALLA = '".$A_TALLA."' , A_FECHA_DE_ENTREGA = '".$A_FECHA_DE_ENTREGA."' , A_FECHA_DE_DEVOLUCION = '".$A_FECHA_DE_DEVOLUCION."' , A_OBSERVACIONES = '".$A_OBSERVACIONES."' , B_TALLA = '".$B_TALLA."' , B_FECHA_DE_ENTREGA = '".$B_FECHA_DE_ENTREGA."' , B_FECHA_DE_DEVOLUCION = '".$B_FECHA_DE_DEVOLUCION."' , DANIEL_OBSERVACIONES = '".$DANIEL_OBSERVACIONES."' , DANIEL_TALLA = '".$DANIEL_TALLA."' , C_FECHA_DE_ENTREGA = '".$C_FECHA_DE_ENTREGA."' , C_FECHA_DE_DEVOLUCION = '".$C_FECHA_DE_DEVOLUCION."' , C_OBSERVACIONES = '".$C_OBSERVACIONES."' , D_TALLA = '".$D_TALLA."' , D_FECHA_DE_ENTREGA = '".$D_FECHA_DE_ENTREGA."' , D_FECHA_DE_DEVOLUCION = '".$D_FECHA_DE_DEVOLUCION."' , D_OBSERVACIONES = '".$D_OBSERVACIONES."' , E_TALLA = '".$E_TALLA."' , E_FECHA_DE_ENTREGA = '".$E_FECHA_DE_ENTREGA."' , E_FECHA_DE_DEVOLUCION = '".$E_FECHA_DE_DEVOLUCION."' , E_OBSERVACIONES = '".$E_OBSERVACIONES."' , F_TALLA = '".$F_TALLA."' , F_FECHA_DE_ENTREGA = '".$F_FECHA_DE_ENTREGA."' , F_FECHA_DE_DEVOLUCION = '".$F_FECHA_DE_DEVOLUCION."' , F_OBSERVACIONES = '".$F_OBSERVACIONES."' , G_TALLA = '".$G_TALLA."' , G_FECHA_DE_ENTREGA = '".$G_FECHA_DE_ENTREGA."' , G_FECHA_DE_DEVOLUCION = '".$G_FECHA_DE_DEVOLUCION."' , G_OBSERVACIONES = '".$G_OBSERVACIONES."' , CMEASIGNADO3 = '".$CMEASIGNADO3."' where idRelacion = '".$session."' ; ";
		
		$var2 = "insert into 01materialequipo3 ( A_TALLA, A_FECHA_DE_ENTREGA, A_FECHA_DE_DEVOLUCION, A_OBSERVACIONES, B_TALLA, B_FECHA_DE_ENTREGA, B_FECHA_DE_DEVOLUCION, DANIEL_OBSERVACIONES, DANIEL_TALLA, C_FECHA_DE_ENTREGA, C_FECHA_DE_DEVOLUCION, C_OBSERVACIONES, D_TALLA, D_FECHA_DE_ENTREGA, D_FECHA_DE_DEVOLUCION, D_OBSERVACIONES, E_TALLA, E_FECHA_DE_ENTREGA, E_FECHA_DE_DEVOLUCION, E_OBSERVACIONES, F_TALLA, F_FECHA_DE_ENTREGA, F_FECHA_DE_DEVOLUCION, F_OBSERVACIONES, G_TALLA, G_FECHA_DE_ENTREGA, G_FECHA_DE_DEVOLUCION, G_OBSERVACIONES, CMEASIGNADO3, idRelacion) values ( '".$A_TALLA."' , '".$A_FECHA_DE_ENTREGA."' , '".$A_FECHA_DE_DEVOLUCION."' , '".$A_OBSERVACIONES."' , '".$B_TALLA."' , '".$B_FECHA_DE_ENTREGA."' , '".$B_FECHA_DE_DEVOLUCION."' , '".$DANIEL_OBSERVACIONES."' , '".$DANIEL_TALLA."' , '".$C_FECHA_DE_ENTREGA."' , '".$C_FECHA_DE_DEVOLUCION."' , '".$C_OBSERVACIONES."' , '".$D_TALLA."' , '".$D_FECHA_DE_ENTREGA."' , '".$D_FECHA_DE_DEVOLUCION."' , '".$D_OBSERVACIONES."' , '".$E_TALLA."' , '".$E_FECHA_DE_ENTREGA."' , '".$E_FECHA_DE_DEVOLUCION."' , '".$E_OBSERVACIONES."' , '".$F_TALLA."' , '".$F_FECHA_DE_ENTREGA."' , '".$F_FECHA_DE_DEVOLUCION."' , '".$F_OBSERVACIONES."' , '".$G_TALLA."' , '".$G_FECHA_DE_ENTREGA."' , '".$G_FECHA_DE_DEVOLUCION."' , '".$G_OBSERVACIONES."' , '".$CMEASIGNADO3."' , '".$session."' ); ";			
			
		if($existe>=1){	

		mysqli_query($conn,$var1) or die('P276'.mysqli_error($conn));
		return "ACTUALIZADO";
		}else{
		mysqli_query($conn,$var2) or die('P279'.mysqli_error($conn));
		return "INGRESADO";
		}
		}else{
		echo "NO HAY UN USUARIO SELECCIONADO";	
		}		
	}


/**//**//**//**//*TARJETA DE CREDITO EMPRESARIAL*//**//**//**//**/




	public function revisar_Tempresarial(){
		$conn = $this->db();
		$var1 = 'select id from 01Tempresarial  where idRelacion =  "'.$_SESSION['id'].'" ';
		$query = mysqli_query($conn,$var1) or die('P44'.mysqli_error($conn));
		$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
		return $row['id'];
	}


	public function guardarTempresarial(  $FECHA_ENTREGA_TARJETA , $FECHA_DEVOLUCION_TARJETA , $TTARJETA , $TBANCO , $T_TIPO_TARJETA , $T_NUMERO_TARJETA , $T_FECHA_VENCIMIENTO , $T_CODIGO_SEGURIDAD , $T_LIMITE_CREDITO , $T_FECHA_CORTE , $T_FECHA_LIMITE , $T_NIP , $T_USUARIO , $T_CONTRASENA , $T_TELEFONO_EXTRAVIO ,$T_TELEFONO_EXTRAVIO1,$T_TELEFONO_EXTRAVIO2,$T_TELEFONO_EXTRAVIO3, $T_DIRECCION_COMPLETA , $T_TELEFONO_TARJETA , $T_CORREO_ALTA , $T_FECHA_BAJA , $T_NUMERO_REPORTE , $T_NOMBRE_OPERADOR , $T_OBSERVACIONES_1 , $T_OBSERVACIONES_2, $ITEMPRESARIAL2,$IpTEMPRESARIAL ){
		$T_LIMITE_CREDITO = str_replace(['$', ','], '', $T_LIMITE_CREDITO);
		$conn = $this->db();
		$existe = $this->revisar_Tempresarial();
		$session = isset($_SESSION['id'])?$_SESSION['id']:'';
		if($session != ''){
			
		$var1 = "update 01Tempresarial set FECHA_ENTREGA_TARJETA = '".$FECHA_ENTREGA_TARJETA."' , FECHA_DEVOLUCION_TARJETA = '".$FECHA_DEVOLUCION_TARJETA."' , TTARJETA = '".$TTARJETA."' , TBANCO = '".$TBANCO."' , T_TIPO_TARJETA = '".$T_TIPO_TARJETA."' , T_NUMERO_TARJETA = '".$T_NUMERO_TARJETA."' , T_FECHA_VENCIMIENTO = '".$T_FECHA_VENCIMIENTO."' , T_CODIGO_SEGURIDAD = '".$T_CODIGO_SEGURIDAD."' , T_LIMITE_CREDITO = '".$T_LIMITE_CREDITO."' , T_FECHA_CORTE = '".$T_FECHA_CORTE."' , T_FECHA_LIMITE = '".$T_FECHA_LIMITE."' , T_NIP = '".$T_NIP."' , T_USUARIO = '".$T_USUARIO."' , T_CONTRASENA = '".$T_CONTRASENA."' , T_TELEFONO_EXTRAVIO = '".$T_TELEFONO_EXTRAVIO."' , T_TELEFONO_EXTRAVIO1 = '".$T_TELEFONO_EXTRAVIO1."' , T_TELEFONO_EXTRAVIO2 = '".$T_TELEFONO_EXTRAVIO2."' , T_TELEFONO_EXTRAVIO3 = '".$T_TELEFONO_EXTRAVIO3."' , T_DIRECCION_COMPLETA = '".$T_DIRECCION_COMPLETA."' , T_TELEFONO_TARJETA = '".$T_TELEFONO_TARJETA."' , T_CORREO_ALTA = '".$T_CORREO_ALTA."' , T_FECHA_BAJA = '".$T_FECHA_BAJA."' , T_NUMERO_REPORTE = '".$T_NUMERO_REPORTE."' , T_NOMBRE_OPERADOR = '".$T_NOMBRE_OPERADOR."' , T_OBSERVACIONES_1 = '".$T_OBSERVACIONES_1."' , T_OBSERVACIONES_2 = '".$T_OBSERVACIONES_2."' where id = '".$IpTEMPRESARIAL."' ; ";
		
		$var2 = "insert into 01Tempresarial ( FECHA_ENTREGA_TARJETA, FECHA_DEVOLUCION_TARJETA, TTARJETA, TBANCO, T_TIPO_TARJETA, T_NUMERO_TARJETA, T_FECHA_VENCIMIENTO, T_CODIGO_SEGURIDAD, T_LIMITE_CREDITO, T_FECHA_CORTE, T_FECHA_LIMITE, T_NIP, T_USUARIO, T_CONTRASENA, T_TELEFONO_EXTRAVIO,T_TELEFONO_EXTRAVIO1,T_TELEFONO_EXTRAVIO2,T_TELEFONO_EXTRAVIO3, T_DIRECCION_COMPLETA, T_TELEFONO_TARJETA, T_CORREO_ALTA, T_FECHA_BAJA, T_NUMERO_REPORTE, T_NOMBRE_OPERADOR, T_OBSERVACIONES_1, T_OBSERVACIONES_2,  idRelacion) values ( '".$FECHA_ENTREGA_TARJETA."' , '".$FECHA_DEVOLUCION_TARJETA."' , '".$TTARJETA."' , '".$TBANCO."' , '".$T_TIPO_TARJETA."' , '".$T_NUMERO_TARJETA."' , '".$T_FECHA_VENCIMIENTO."' , '".$T_CODIGO_SEGURIDAD."' , '".$T_LIMITE_CREDITO."' , '".$T_FECHA_CORTE."' , '".$T_FECHA_LIMITE."' , '".$T_NIP."' , '".$T_USUARIO."' , '".$T_CONTRASENA."' , '".$T_TELEFONO_EXTRAVIO."' , '".$T_TELEFONO_EXTRAVIO1."' , '".$T_TELEFONO_EXTRAVIO2."' , '".$T_TELEFONO_EXTRAVIO3."' , '".$T_DIRECCION_COMPLETA."' , '".$T_TELEFONO_TARJETA."' , '".$T_CORREO_ALTA."' , '".$T_FECHA_BAJA."' , '".$T_NUMERO_REPORTE."' , '".$T_NOMBRE_OPERADOR."' , '".$T_OBSERVACIONES_1."' , '".$T_OBSERVACIONES_2."' , '".$session."' );  ";			
			
		if($ITEMPRESARIAL2 == 'ITEMPRESARIAL2'){

		mysqli_query($conn,$var1) or die('P276'.mysqli_error($conn));
		return "<P style='color:green; font-size:27px;'>ACTUALIZADO</P>";
		}else{
		mysqli_query($conn,$var2) or die('P279'.mysqli_error($conn));
		return "<P style='color:green; font-size:27px;'>INGRESADO</P>";
		}
		}else{
		echo "NO HAY UN USUARIO SELECCIONADO";	
		}		
	}


	public function listadoTARJETAEMPRESARIAL(){
		$conn = $this->db();
		$variablequery = "select * from 01Tempresarial  where idRelacion = '".$_SESSION['id']."' ";
		return $arrayquery = mysqli_query($conn,$variablequery);
	}


	public function listadoTARJETAEMPRESARIAL2($id){
		$conn = $this->db();
		$variablequery = "select * from 01Tempresarial  where id = '".$id."' ";
		return $arrayquery = mysqli_query($conn,$variablequery);
	}



	public function borraTARJETAEMPRESARIAL($id){
		$conn = $this->db();
		$variablequery = "delete from 01Tempresarial where id = '".$id."' ";
		$arrayquery = mysqli_query($conn,$variablequery);
		RETURN 
		
		"<P style='color:green; font-size:26px;'>ELEMENTO BORRADO</P>";
	}


/**//**//**//**//*UNIFORES*//**//**//**//**/

	public function variablesUNIFORMES(){
		$conn = $this->db();
		$variablequery = "select * from 01uniformes  where idRelacion = '".$_SESSION['id']."' ";
		$arrayquery = mysqli_query($conn,$variablequery);
		return $row = mysqli_fetch_array($arrayquery, MYSQLI_ASSOC);		
	}

	public function revisar_UNIFORMES(){
		$conn = $this->db();
		$var1 = 'select id from 01uniformes where idRelacion =  "'.$_SESSION['id'].'" ';
		$query = mysqli_query($conn,$var1) or die('P44'.mysqli_error($conn));
		$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
		return $row['id'];
	}

	public function guardarUNIFORMES( $U_ARTICULO , $U_CANTIDAD , $U_TALLA , $U_MARCA , $U_FECHA_ENTREGA , $iunifores , $U_FECHA_DEVOLUCION , $U_OBSERVACIONES , $U_ENVIAR_IMAIL, $IUNIFROME2, $IpUNIFORME ){
		$conn = $this->db();
		$existe = $this->revisar_UNIFORMES();
		$session = isset($_SESSION['id'])?$_SESSION['id']:'';
		if($session != ''){
			
		$var1 = "update 01uniformes set U_ARTICULO = '".$U_ARTICULO."' , U_CANTIDAD = '".$U_CANTIDAD."' , U_TALLA = '".$U_TALLA."' , U_MARCA = '".$U_MARCA."' , U_FECHA_ENTREGA = '".$U_FECHA_ENTREGA."' , iunifores = '".$iunifores."' , U_FECHA_DEVOLUCION = '".$U_FECHA_DEVOLUCION."' , U_OBSERVACIONES = '".$U_OBSERVACIONES."' , U_ENVIAR_IMAIL = '".$U_ENVIAR_IMAIL."' where id = '".$IpUNIFORME."' ; ";
		
		$var2 = "insert into 01uniformes ( U_ARTICULO, U_CANTIDAD, U_TALLA, U_MARCA, U_FECHA_ENTREGA, iunifores, U_FECHA_DEVOLUCION, U_OBSERVACIONES, U_ENVIAR_IMAIL, idRelacion) values ( '".$U_ARTICULO."' , '".$U_CANTIDAD."' , '".$U_TALLA."' , '".$U_MARCA."' , '".$U_FECHA_ENTREGA."' , '".$iunifores."' , '".$U_FECHA_DEVOLUCION."' , '".$U_OBSERVACIONES."' , '".$U_ENVIAR_IMAIL."' , '".$session."' ); ";			

		if($IUNIFROME2 == 'IUNIFROME2'){

		mysqli_query($conn,$var1) or die('P276'.mysqli_error($conn));
		return "<P style='color:green; font-size:27px;'>ACTUALIZADO</P>";
		}else{
		mysqli_query($conn,$var2) or die('P279'.mysqli_error($conn));
		return "<P style='color:green; font-size:27px;'>INGRESADO</P>";
		}
		}else{
		echo "NO HAY UN USUARIO SELECCIONADO";	
		}		
	}



	public function listadouniformes(){
		$conn = $this->db();
		$variablequery = "select * from 01uniformes  where idRelacion = '".$_SESSION['id']."' ";
		return $arrayquery = mysqli_query($conn,$variablequery);
	}


	public function listadouniformes2($id){
		$conn = $this->db();
		$variablequery = "select * from 01uniformes  where id = '".$id."' ";
		return $arrayquery = mysqli_query($conn,$variablequery);
	}



	public function borrauniformes2($id){
		$conn = $this->db();
		$variablequery = "delete from 01uniformes where id = '".$id."' ";
		$arrayquery = mysqli_query($conn,$variablequery);
		RETURN 
		
		"<P style='color:green; font-size:26px;'>ELEMENTO BORRADO</P>";
	}




/**//**//**//**//*UNIFORES*//**//**//**//**/

	public function variablesPOLIZAS(){
		$conn = $this->db();
		$variablequery = "select * from 01polizasydocumentos  where idRelacion = '".$_SESSION['id']."' ";
		$arrayquery = mysqli_query($conn,$variablequery);
		return $row = mysqli_fetch_array($arrayquery, MYSQLI_ASSOC);		
	}

	public function revisar_POLIZAS(){
		$conn = $this->db();
		$var1 = 'select id from 01polizasydocumentos where idRelacion =  "'.$_SESSION['id'].'" ';
		$query = mysqli_query($conn,$var1) or die('P44'.mysqli_error($conn));
		$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
		return $row['id'];
	}

	public function guardarPOLIZAS( $PD_TIPO_DOCUMENTO , $PD_FECHA_ENTREGA , $PD_FECHA_INICIO , $PD_FECHA_FINAL , $PD_EMPRESA , $PD_TELEFONO_EMERGENCIA , $PD_OBSERVACIONES , $ipolias , $PD_ENVIAR_IMAIL, $IPOLIZAS22, $IpPOLIZAS ){
		$conn = $this->db();
		$existe = $this->revisar_POLIZAS();
		$session = isset($_SESSION['id'])?$_SESSION['id']:'';
		if($session != ''){
			
		$var1 = "update 01polizasydocumentos set PD_TIPO_DOCUMENTO = '".$PD_TIPO_DOCUMENTO."' , PD_FECHA_ENTREGA = '".$PD_FECHA_ENTREGA."' , PD_FECHA_INICIO = '".$PD_FECHA_INICIO."' , PD_FECHA_FINAL = '".$PD_FECHA_FINAL."' , PD_EMPRESA = '".$PD_EMPRESA."' , PD_TELEFONO_EMERGENCIA = '".$PD_TELEFONO_EMERGENCIA."' , PD_OBSERVACIONES = '".$PD_OBSERVACIONES."' , ipolias = '".$ipolias."' , PD_ENVIAR_IMAIL = '".$PD_ENVIAR_IMAIL."' where id = '".$IpPOLIZAS."' ; ";
		
		$var2 = "insert into 01polizasydocumentos ( PD_TIPO_DOCUMENTO, PD_FECHA_ENTREGA, PD_FECHA_INICIO, PD_FECHA_FINAL, PD_EMPRESA, PD_TELEFONO_EMERGENCIA, PD_OBSERVACIONES, ipolias, PD_ENVIAR_IMAIL, idRelacion) values ( '".$PD_TIPO_DOCUMENTO."' , '".$PD_FECHA_ENTREGA."' , '".$PD_FECHA_INICIO."' , '".$PD_FECHA_FINAL."' , '".$PD_EMPRESA."' , '".$PD_TELEFONO_EMERGENCIA."' , '".$PD_OBSERVACIONES."' , '".$ipolias."' , '".$PD_ENVIAR_IMAIL."' , '".$session."' ); ";			
			
		if($IPOLIZAS22=='IPOLIZAS22'){

		mysqli_query($conn,$var1) or die('P276'.mysqli_error($conn));
		return "<P style='color:green; font-size:27px;'>ACTUALIZADO</P>";
		}else{
		mysqli_query($conn,$var2) or die('P279'.mysqli_error($conn));
		return "<P style='color:green; font-size:27px;'>INGRESADO</P>";
		}
		
		
		
		
		}else{
		echo "NO HAY UN USUARIO SELECCIONADO";	
		}		
	}







	public function listadoPolizas(){
		$conn = $this->db();
		$variablequery = "select * from 01polizasydocumentos  where idRelacion = '".$_SESSION['id']."' ";
		return $arrayquery = mysqli_query($conn,$variablequery);
	}


	public function listadoPolizas2($id){
		$conn = $this->db();
		$variablequery = "select * from 01polizasydocumentos  where id = '".$id."' ";
		return $arrayquery = mysqli_query($conn,$variablequery);
	}



	public function borraPolizas2($id){
		$conn = $this->db();
		$variablequery = "delete from 01polizasydocumentos where id = '".$id."' ";
		$arrayquery = mysqli_query($conn,$variablequery);
		RETURN 
		
		"<P style='color:green; font-size:26px;'>ELEMENTO BORRADO</P>";
	}









/**//**//**//**//*contrasenias*//**//**//**//**/

	public function variablescontrasenias(){
		$conn = $this->db();
		$variablequery = "select * from 01contrasenias  where idRelacion = '".$_SESSION['id']."' ";
		$arrayquery = mysqli_query($conn,$variablequery);
		return $row = mysqli_fetch_array($arrayquery, MYSQLI_ASSOC);		
	}

	public function revisar_contrasenias(){
		$conn = $this->db();
		$var1 = 'select id from 01contrasenias  where idRelacion =  "'.$_SESSION['id'].'" ';
		$query = mysqli_query($conn,$var1) or die('P44'.mysqli_error($conn));
		$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
		return $row['id'];
	}

	public function guardarcontrasenias( $CONTRASENA_DE1 , $C_USUARIO1 , $CONTRASENA1 , $C_OTRO1 , $C_OBSERVACIONES1 , $IpCONTRASENA,$ICONTRASENAS2   ){
		$conn = $this->db();
		$existe = $this->revisar_contrasenias();
		$session = isset($_SESSION['id'])?$_SESSION['id']:'';
		if($session != ''){
			
		$var1 = "update 01contrasenias set CONTRASENA_DE1 = '".$CONTRASENA_DE1."' , C_USUARIO1 = '".$C_USUARIO1."' , CONTRASENA1 = '".$CONTRASENA1."' , C_OTRO1 = '".$C_OTRO1."' , C_OBSERVACIONES1 = '".$C_OBSERVACIONES1."' , CONTRASENA_DE2 = '".$CONTRASENA_DE2."' , C_USUARIO2 = '".$C_USUARIO2."' , CONTRASENA2 = '".$CONTRASENA2."' , C_OTRO2 = '".$C_OTRO2."' , C_OBSERVACIONES2 = '".$C_OBSERVACIONES2."' , CONTRASENA_DE3 = '".$CONTRASENA_DE3."' , C_USUARIO3 = '".$C_USUARIO3."' , CONTRASENA3 = '".$CONTRASENA3."' , C_OTRO3 = '".$C_OTRO3."' , C_OBSERVACIONES3 = '".$C_OBSERVACIONES3."' , CONTRASENA_DE4 = '".$CONTRASENA_DE4."' , C_USUARIO4 = '".$C_USUARIO4."' , CONTRASENA4 = '".$CONTRASENA4."' , C_OTRO4 = '".$C_OTRO4."' , C_OBSERVACIONES4 = '".$C_OBSERVACIONES4."' , C_ENVIAR_IMAIL = '".$C_ENVIAR_IMAIL."' where id = '".$IpCONTRASENA."' ; ";
		
		$var2 = "insert into 01contrasenias ( CONTRASENA_DE1, C_USUARIO1, CONTRASENA1, C_OTRO1, C_OBSERVACIONES1, CONTRASENA_DE2, C_USUARIO2, CONTRASENA2, C_OTRO2, C_OBSERVACIONES2, CONTRASENA_DE3, C_USUARIO3, CONTRASENA3, C_OTRO3, C_OBSERVACIONES3, CONTRASENA_DE4, C_USUARIO4, CONTRASENA4, C_OTRO4, C_OBSERVACIONES4, C_ENVIAR_IMAIL, idRelacion) values ( '".$CONTRASENA_DE1."' , '".$C_USUARIO1."' , '".$CONTRASENA1."' , '".$C_OTRO1."' , '".$C_OBSERVACIONES1."' , '".$CONTRASENA_DE2."' , '".$C_USUARIO2."' , '".$CONTRASENA2."' , '".$C_OTRO2."' , '".$C_OBSERVACIONES2."' , '".$CONTRASENA_DE3."' , '".$C_USUARIO3."' , '".$CONTRASENA3."' , '".$C_OTRO3."' , '".$C_OBSERVACIONES3."' , '".$CONTRASENA_DE4."' , '".$C_USUARIO4."' , '".$CONTRASENA4."' , '".$C_OTRO4."' , '".$C_OBSERVACIONES4."' , '".$C_ENVIAR_IMAIL."' , '".$session."' );  ";			
			
		if($ICONTRASENAS2=='ICONTRASENAS2'){

		mysqli_query($conn,$var1) or die('P276'.mysqli_error($conn));
		return "<P style='color:green; font-size:27px;'>ACTUALIZADO</P>";
		}else{
		mysqli_query($conn,$var2) or die('P279'.mysqli_error($conn));
		return "<P style='color:green; font-size:27px;'>INGRESADO</P>";
		}
		
		
		}else{
		echo "NO HAY UN USUARIO SELECCIONADO";	
		}		
	}


	public function listadocontrasenias(){
		$conn = $this->db();
		$variablequery = "select * from 01contrasenias  where idRelacion = '".$_SESSION['id']."' ";
		return $arrayquery = mysqli_query($conn,$variablequery);
	}


	public function listadocontrasenias2($id){
		$conn = $this->db();
		$variablequery = "select * from 01contrasenias  where id = '".$id."' ";
		return $arrayquery = mysqli_query($conn,$variablequery);
	}



	public function borracontrasenias2($id){
		$conn = $this->db();
		$variablequery = "delete from 01contrasenias where id = '".$id."' ";
		$arrayquery = mysqli_query($conn,$variablequery);
		RETURN 
		
		"<P style='color:green; font-size:26px;'>ELEMENTO BORRADO</P>";
	}












/**//**//**//**//*material equipo asignado*//**//**//**//**/

	public function variablesMATERIALEQUIPO(){
		$conn = $this->db();
		$variablequery = "select * from 01materialequipoa  where idRelacion = '".$_SESSION['id']."' ";
		$arrayquery = mysqli_query($conn,$variablequery);
		return $row = mysqli_fetch_array($arrayquery, MYSQLI_ASSOC);		
	}

	public function revisar_MATERIALEQUIPO(){
		$conn = $this->db();
		$var1 = 'select id from 01materialequipoa  where idRelacion =  "'.$_SESSION['id'].'" ';
		$query = mysqli_query($conn,$var1) or die('P44'.mysqli_error($conn));
		$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
		return $row['id'];
	}

	public function guardarMaterialEquipo($MA_ARTICULO , $MA_CANTIDAD , $MA_MARCA , $MA_MODELO , $MA_NUMERO_SERIE , $MA_FECHA_ENTREGA , $MA_FECHA_DEVOLUCION , $MA_OBSERVACIONES , $MA_ENVIAR_IMAIL, $IMATERIAL2, $IpMATERIAL){
		$conn = $this->db();
		$existe = $this->revisar_MATERIALEQUIPO();
		$session = isset($_SESSION['id'])?$_SESSION['id']:'';
		if($session != ''){
			
		$var1 = "update 01materialequipoa set MA_ARTICULO = '".$MA_ARTICULO."' , MA_CANTIDAD = '".$MA_CANTIDAD."' , MA_MARCA = '".$MA_MARCA."' , MA_MODELO = '".$MA_MODELO."' , MA_NUMERO_SERIE = '".$MA_NUMERO_SERIE."' , MA_FECHA_ENTREGA = '".$MA_FECHA_ENTREGA."' , MA_FECHA_DEVOLUCION = '".$MA_FECHA_DEVOLUCION."' , MA_OBSERVACIONES = '".$MA_OBSERVACIONES."' , MA_ENVIAR_IMAIL = '".$MA_ENVIAR_IMAIL."' where id = '".$IpMATERIAL."' ; ";
		
		$var2 = "insert into 01materialequipoa ( MA_ARTICULO, MA_CANTIDAD, MA_MARCA, MA_MODELO, MA_NUMERO_SERIE, MA_FECHA_ENTREGA, MA_FECHA_DEVOLUCION, MA_OBSERVACIONES, MA_ENVIAR_IMAIL, idRelacion) values ( '".$MA_ARTICULO."' , '".$MA_CANTIDAD."' , '".$MA_MARCA."' , '".$MA_MODELO."' , '".$MA_NUMERO_SERIE."' , '".$MA_FECHA_ENTREGA."' , '".$MA_FECHA_DEVOLUCION."' , '".$MA_OBSERVACIONES."'  , '".$MA_ENVIAR_IMAIL."' , '".$session."' ); ";			
			
		if($IMATERIAL2 == 'IMATERIAL2'){

		mysqli_query($conn,$var1) or die('P276'.mysqli_error($conn));
		return "<P style='color:green; font-size:27px;'>ACTUALIZADO</P>";
		}else{
		mysqli_query($conn,$var2) or die('P279'.mysqli_error($conn));
		return "<P style='color:green; font-size:27px;'>INGRESADO</P>";
		}
		}else{
		echo "NO HAY UN USUARIO SELECCIONADO";	
		}		
	}






	public function listadoMaterialEquipo(){
		$conn = $this->db();
		$variablequery = "select * from 01materialequipoa  where idRelacion = '".$_SESSION['id']."' ";
		return $arrayquery = mysqli_query($conn,$variablequery);
	}


	public function listadoMaterialEquipo2($id){
		$conn = $this->db();
		$variablequery = "select * from 01materialequipoa  where id = '".$id."' ";
		return $arrayquery = mysqli_query($conn,$variablequery);
	}



	public function borraMaterialEquipo2($id){
		$conn = $this->db();
		$variablequery = "delete from 01materialequipoa where id = '".$id."' ";
		$arrayquery = mysqli_query($conn,$variablequery);
		RETURN 
		
		"<P style='color:green; font-size:26px;'>ELEMENTO BORRADO</P>";
	}





/**//**//**//**//*CONVENIOPRESTAMO*//**//**//**//**/

	public function variablesCONVENIOPRESTAMO(){
		$conn = $this->db();
		$variablequery = "select * from 01convenioprestamo  where idRelacion = '".$_SESSION['id']."' ";
		$arrayquery = mysqli_query($conn,$variablequery);
		return $row = mysqli_fetch_array($arrayquery, MYSQLI_ASSOC);		
	}

	public function revisar_CONVENIOPRESTAMO(){
		$conn = $this->db();
		$var1 = 'select id from 01convenioprestamo  where idRelacion =  "'.$_SESSION['id'].'" ';
		$query = mysqli_query($conn,$var1) or die('P44'.mysqli_error($conn));
		$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
		return $row['id'];
	}

	public function guardarCONVENIOPRESTAMO ($CP_FECHA_SOLICITUD , $CP_MONTO_SOLICITADO , $CP_AUTORIZADO_POR , $CP_CONDICIONES_PAGO , $CP_DESCUENTO_QUINCENA , $CP_FECHA_AUTORIZACION , $CP_FECHA_DEPOSITO , $CP_ENVIAR_IMAIL , $iCONVENIOPRESTAMO , $iCONVENIOPRESTAMO2, $IpCONVENIOPRESTAMO, 
	$CP_REPARTO_UTILIDADES, $CP_BONO, $CP_AGUINALDO ,$CP_NOMINA_MENSUAL, $CP_NOMINA_QUINCENAL){
		
		
		$conn = $this->db();
		$existe = $this->revisar_CONVENIOPRESTAMO();
		$session = isset($_SESSION['id'])?$_SESSION['id']:'';
		if($session != ''){
			
			
		$var1 = "update 01convenioprestamo set CP_FECHA_SOLICITUD = '".$CP_FECHA_SOLICITUD."' , CP_MONTO_SOLICITADO = '".$CP_MONTO_SOLICITADO."' , CP_AUTORIZADO_POR = '".$CP_AUTORIZADO_POR."' , CP_CONDICIONES_PAGO = '".$CP_CONDICIONES_PAGO."' , CP_DESCUENTO_QUINCENA = '".$CP_DESCUENTO_QUINCENA."' , CP_FECHA_AUTORIZACION = '".$CP_FECHA_AUTORIZACION."' , CP_FECHA_DEPOSITO = '".$CP_FECHA_DEPOSITO."' , CP_ENVIAR_IMAIL = '".$CP_ENVIAR_IMAIL."' , 
		iCONVENIOPRESTAMO = '".$iCONVENIOPRESTAMO."',
		CP_REPARTO_UTILIDADES = '".$CP_REPARTO_UTILIDADES."',
		CP_BONO = '".$CP_BONO."',
		CP_AGUINALDO = '".$CP_AGUINALDO."',
		CP_NOMINA_MENSUAL = '".$CP_NOMINA_MENSUAL."',
		CP_NOMINA_QUINCENAL = '".$CP_NOMINA_QUINCENAL."'
		where id = '".$IpCONVENIOPRESTAMO."' ; ";

		
		$var2 = "insert into 01convenioprestamo ( CP_FECHA_SOLICITUD, CP_MONTO_SOLICITADO, CP_AUTORIZADO_POR, CP_CONDICIONES_PAGO, CP_DESCUENTO_QUINCENA, CP_FECHA_AUTORIZACION, CP_FECHA_DEPOSITO, CP_ENVIAR_IMAIL, iCONVENIOPRESTAMO, idRelacion,
		CP_REPARTO_UTILIDADES, CP_BONO, CP_AGUINALDO, CP_NOMINA_MENSUAL, CP_NOMINA_QUINCENAL
		
		
		) values ( '".$CP_FECHA_SOLICITUD."' , '".$CP_MONTO_SOLICITADO."' , '".$CP_AUTORIZADO_POR."' , '".$CP_CONDICIONES_PAGO."' , '".$CP_DESCUENTO_QUINCENA."' , '".$CP_FECHA_AUTORIZACION."' , '".$CP_FECHA_DEPOSITO."' , '".$CP_ENVIAR_IMAIL."' , '".$iCONVENIOPRESTAMO."' , '".$session."'
		 , '".$CP_REPARTO_UTILIDADES."' , '".$CP_BONO."'
		   , '".$CP_AGUINALDO."'
		    , '".$CP_NOMINA_MENSUAL."'
			 , '".$CP_NOMINA_QUINCENAL."'



		); ";
		
		
			
		if($iCONVENIOPRESTAMO2 == 'iCONVENIOPRESTAMO2'){

		mysqli_query($conn,$var1) or die('P276'.mysqli_error($conn));
		return "<P style='color:green; font-size:27px;'>ACTUALIZADO</P>";
		}else{
		mysqli_query($conn,$var2) or die('P279'.mysqli_error($conn));
		return "<P style='color:green; font-size:27px;'>INGRESADO</P>";
		}
		}else{
		echo "NO HAY UN USUARIO SELECCIONADO";	
		}	
}


	public function listadoCONVENIOPRESTAMO(){
		$conn = $this->db();
		$variablequery = "select * from 01convenioprestamo  where idRelacion = '".$_SESSION['id']."' ";
		return $arrayquery = mysqli_query($conn,$variablequery);
	}


	public function listadoCONVENIOPRESTAMO2($id){
		$conn = $this->db();
		$variablequery = "select * from 01convenioprestamo  where id = '".$id."' ";
		return $arrayquery = mysqli_query($conn,$variablequery);
	}



	public function borraCONVENIOPRESTAMO2($id){
		$conn = $this->db();
		$variablequery = "delete from 01convenioprestamo where id = '".$id."' ";
		$arrayquery = mysqli_query($conn,$variablequery);
		RETURN 
		
		"<P style='color:green; font-size:26px;'>ELEMENTO BORRADO</P>";
	}








/**//**//**//**//*GUARDAR INVENTARIO*//**//**//**//**/



	public function variablesINVENTARIO(){
		$conn = $this->db();
		$variablequery = "select * from 01inventario  where idRelacion = '".$_SESSION['id']."' ";
		$arrayquery = mysqli_query($conn,$variablequery);
		return $row = mysqli_fetch_array($arrayquery, MYSQLI_ASSOC);		
	}

	public function revisar_INVENTARIO(){
		$conn = $this->db();
		$var1 = 'select id from 01inventario  where idRelacion =  "'.$_SESSION['id'].'" ';
		$query = mysqli_query($conn,$var1) or die('P44'.mysqli_error($conn));
		$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
		return $row['id'];
	}



	public function guardarINVENTARIO (  $I_NUMERO , $RE_CANTIDAD , $I_SUB_CATEGORIA , $I_ARTICULO , $I_METROS , $I_PIEZAS , $I_LITROS , $I_COLOR , $I_MARCA , $I_SUB_MARCA , $I_MODELO , $I_NUMERO_SERIE , $I_CARACTERISTICAS1 , $I_CARACTERISTICAS2 , $I_CARACTERISTICAS3 , $I_CARACTERISTICAS4 , $I_CARACTERISTICAS5 , $I_OBSERVACIONES , $I_STATUS , $I_SEVE , $I_NOSEVE , $I_BODEGA , $I_CANTIDAD_BODEGA , $I_CANTIDAD_REQUERIDA , $I_CANTIDAD_DESPUES_SALIDA , $I_SOLICITANTE , $I_FECHA_DE_SOLICITUD , $I_QUIEN_ENTREGA , $I_QUIEN_RECIBE2 , $I_FECHA_ENTREGA , $I_HORA_ENTREGA , $I_FECHA_DEVOLUCION , $I_HORA_DEVOLUCION , $I_QUIEN_DEVUELVE , $I_QUIEN_RECIBE , $I_NUMERO_EVENTO , $I_TOTAL_DIAS , $I_RAZON_SOCIAL_PROVEEDOR , $I_NOMBRE_PROVEEDOR , $I_TEL_PROVEEDOR , $I_CEL_PROVEEDOR , $I_DIRECCION_PROVEEDOR , $I_PRECIO1 ,$I_PRECIO2, $I_FECHA_COMPRA , $I_PAGADO_CON , $I_FACTURA_A_NOMBRE , $ihiddeninventario,$IpINVENTARIO , $IINVENTARIO2, $I_FOTOS11,$CODIGO_QR11, $I_FACTURA111){
		
		
		$conn = $this->db();
		$existe = $this->revisar_INVENTARIO();
		$session = isset($_SESSION['id'])?$_SESSION['id']:'';
		if($session != ''){
			
			
		$var1 = "update 01inventario set I_NUMERO = '".$I_NUMERO."' , RE_CANTIDAD = '".$RE_CANTIDAD."' , I_SUB_CATEGORIA = '".$I_SUB_CATEGORIA."' , I_ARTICULO = '".$I_ARTICULO."' , I_METROS = '".$I_METROS."' , I_PIEZAS = '".$I_PIEZAS."' , I_LITROS = '".$I_LITROS."' , I_COLOR = '".$I_COLOR."' , I_MARCA = '".$I_MARCA."' , I_SUB_MARCA = '".$I_SUB_MARCA."' , I_MODELO = '".$I_MODELO."' , I_NUMERO_SERIE = '".$I_NUMERO_SERIE."' , I_CARACTERISTICAS1 = '".$I_CARACTERISTICAS1."' , I_CARACTERISTICAS2 = '".$I_CARACTERISTICAS2."' , I_CARACTERISTICAS3 = '".$I_CARACTERISTICAS3."' , I_CARACTERISTICAS4 = '".$I_CARACTERISTICAS4."' , I_CARACTERISTICAS5 = '".$I_CARACTERISTICAS5."' , I_OBSERVACIONES = '".$I_OBSERVACIONES."' , I_STATUS = '".$I_STATUS."' , I_SEVE = '".$I_SEVE."' , I_NOSEVE = '".$I_NOSEVE."' , I_BODEGA = '".$I_BODEGA."' , I_CANTIDAD_BODEGA = '".$I_CANTIDAD_BODEGA."' , I_CANTIDAD_REQUERIDA = '".$I_CANTIDAD_REQUERIDA."' , I_CANTIDAD_DESPUES_SALIDA = '".$I_CANTIDAD_DESPUES_SALIDA."' , I_SOLICITANTE = '".$I_SOLICITANTE."' , I_FECHA_DE_SOLICITUD = '".$I_FECHA_DE_SOLICITUD."' , I_QUIEN_ENTREGA = '".$I_QUIEN_ENTREGA."' , I_QUIEN_RECIBE2 = '".$I_QUIEN_RECIBE2."' , I_FECHA_ENTREGA = '".$I_FECHA_ENTREGA."' , I_HORA_ENTREGA = '".$I_HORA_ENTREGA."' , I_FECHA_DEVOLUCION = '".$I_FECHA_DEVOLUCION."' , I_HORA_DEVOLUCION = '".$I_HORA_DEVOLUCION."' , I_QUIEN_DEVUELVE = '".$I_QUIEN_DEVUELVE."' , I_QUIEN_RECIBE = '".$I_QUIEN_RECIBE."' , I_NUMERO_EVENTO = '".$I_NUMERO_EVENTO."' , I_TOTAL_DIAS = '".$I_TOTAL_DIAS."' , I_RAZON_SOCIAL_PROVEEDOR = '".$I_RAZON_SOCIAL_PROVEEDOR."' , I_NOMBRE_PROVEEDOR = '".$I_NOMBRE_PROVEEDOR."' , I_TEL_PROVEEDOR = '".$I_TEL_PROVEEDOR."' , I_CEL_PROVEEDOR = '".$I_CEL_PROVEEDOR."' , I_DIRECCION_PROVEEDOR = '".$I_DIRECCION_PROVEEDOR."' , I_PRECIO1 = '".$I_PRECIO1."' , I_PRECIO2 = '".$I_PRECIO2."' , I_FECHA_COMPRA = '".$I_FECHA_COMPRA."' , I_PAGADO_CON = '".$I_PAGADO_CON."' , I_FACTURA_A_NOMBRE = '".$I_FACTURA_A_NOMBRE."' , ihiddeninventario = '".$ihiddeninventario."' where id = '".$IpINVENTARIO."' ; ";

		
		$var2 = "insert into 01inventario ( I_NUMERO, RE_CANTIDAD, I_SUB_CATEGORIA, I_ARTICULO, I_METROS, I_PIEZAS, I_LITROS, I_COLOR, I_MARCA, I_SUB_MARCA, I_MODELO, I_NUMERO_SERIE, I_CARACTERISTICAS1, I_CARACTERISTICAS2, I_CARACTERISTICAS3, I_CARACTERISTICAS4, I_CARACTERISTICAS5, I_OBSERVACIONES, I_STATUS, I_SEVE, I_NOSEVE, I_BODEGA, I_CANTIDAD_BODEGA, I_CANTIDAD_REQUERIDA, I_CANTIDAD_DESPUES_SALIDA, I_SOLICITANTE, I_FECHA_DE_SOLICITUD, I_QUIEN_ENTREGA, I_QUIEN_RECIBE2, I_FECHA_ENTREGA, I_HORA_ENTREGA, I_FECHA_DEVOLUCION, I_HORA_DEVOLUCION, I_QUIEN_DEVUELVE, I_QUIEN_RECIBE, I_NUMERO_EVENTO, I_TOTAL_DIAS, I_RAZON_SOCIAL_PROVEEDOR, I_NOMBRE_PROVEEDOR, I_TEL_PROVEEDOR, I_CEL_PROVEEDOR, I_DIRECCION_PROVEEDOR, I_PRECIO1,I_PRECIO2, I_FECHA_COMPRA, I_PAGADO_CON, I_FACTURA_A_NOMBRE, ihiddeninventario,
		I_FOTOS, CODIGO_QR, I_FACTURA1, idRelacion) values ( '".$I_NUMERO."' , '".$RE_CANTIDAD."' , '".$I_SUB_CATEGORIA."' , '".$I_ARTICULO."' , '".$I_METROS."' , '".$I_PIEZAS."' , '".$I_LITROS."' , '".$I_COLOR."' , '".$I_MARCA."' , '".$I_SUB_MARCA."' , '".$I_MODELO."' , '".$I_NUMERO_SERIE."' , '".$I_CARACTERISTICAS1."' , '".$I_CARACTERISTICAS2."' , '".$I_CARACTERISTICAS3."' , '".$I_CARACTERISTICAS4."' , '".$I_CARACTERISTICAS5."' , '".$I_OBSERVACIONES."' , '".$I_STATUS."' , '".$I_SEVE."' , '".$I_NOSEVE."' , '".$I_BODEGA."' , '".$I_CANTIDAD_BODEGA."' , '".$I_CANTIDAD_REQUERIDA."' , '".$I_CANTIDAD_DESPUES_SALIDA."' , '".$I_SOLICITANTE."' , '".$I_FECHA_DE_SOLICITUD."' , '".$I_QUIEN_ENTREGA."' , '".$I_QUIEN_RECIBE2."' , '".$I_FECHA_ENTREGA."' , '".$I_HORA_ENTREGA."' , '".$I_FECHA_DEVOLUCION."' , '".$I_HORA_DEVOLUCION."' , '".$I_QUIEN_DEVUELVE."' , '".$I_QUIEN_RECIBE."' , '".$I_NUMERO_EVENTO."' , '".$I_TOTAL_DIAS."' , '".$I_RAZON_SOCIAL_PROVEEDOR."' , '".$I_NOMBRE_PROVEEDOR."' , '".$I_TEL_PROVEEDOR."' , '".$I_CEL_PROVEEDOR."' , '".$I_DIRECCION_PROVEEDOR."' , '".$I_PRECIO1."' , '".$I_PRECIO2."' , '".$I_FECHA_COMPRA."' , '".$I_PAGADO_CON."' , '".$I_FACTURA_A_NOMBRE."' , '".$ihiddeninventario."', '".$I_FOTOS11."' ,  '".$CODIGO_QR11."', '".$I_FACTURA111."' , '".$session."' );  ";
		
		
			
		if($IINVENTARIO2 == 'IINVENTARIO2'){

		mysqli_query($conn,$var1) or die('P276'.mysqli_error($conn));
		return "ACTUALIZADO";
		}else{
		mysqli_query($conn,$var2) or die('P279'.mysqli_error($conn));
		return "INGRESADO";
		}
		}else{
		echo "NO HAY UN USUARIO SELECCIONADO";	
		}		
}


	public function listadoINVENTARIO(){
		$conn = $this->db();
		$variablequery = "select * from 01inventario  where idRelacion = '".$_SESSION['id']."' order by id desc ";
		return $arrayquery = mysqli_query($conn,$variablequery);
	}





	public function listadoINVENTARIOBUSQUEDA($BUSQUEDA){
		$conn = $this->db();
		$variablequery = "select * from 01inventario  where idRelacion = '".$_SESSION['id']."' 
AND (I_SUB_CATEGORIA LIKE '%".$BUSQUEDA."%' OR  I_ARTICULO LIKE '%".$BUSQUEDA."%'  OR  RE_CANTIDAD LIKE '%".$BUSQUEDA."%' OR  I_COLOR LIKE '%".$BUSQUEDA."%' ) order by id desc ";
		return $arrayquery = mysqli_query($conn,$variablequery);
	}




	public function listadoINVENTARIO2($id){
		$conn = $this->db();
		$variablequery = "select * from 01inventario  where id = '".$id."' ";
		return $arrayquery = mysqli_query($conn,$variablequery);
	}



	public function borraINVENTARIO2($id){
		$conn = $this->db();
		$variablequery = "delete from 01inventario where id = '".$id."' ";
		$arrayquery = mysqli_query($conn,$variablequery);
		RETURN 
		
		"<P style='color:green; font-size:26px;'>ELEMENTO BORRADO</P>";
	}





/*
categoria inventario

*/
	public function variablesINVENTARIOcategoria(){
		$conn = $this->db();
		$variablequery = "select * from 01inventariocategoria  where idRelacion = '".$_SESSION['id']."' ";
		$arrayquery = mysqli_query($conn,$variablequery);
		return $row = mysqli_fetch_array($arrayquery, MYSQLI_ASSOC);		
	}

	public function revisar_INVENTARIOcategoria(){
		$conn = $this->db();
		$var1 = 'select id from 01inventariocategoria  where idRelacion =  "'.$_SESSION['id'].'" ';
		$query = mysqli_query($conn,$var1) or die('P44'.mysqli_error($conn));
		$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
		return $row['id'];
	}



public function guardarcategoriainventario ( $I_CATEGORIAS , $ICATEGORIAS ){
		
		
		$conn = $this->db();
		$existe = $this->revisar_INVENTARIOcategoria();
		$session = isset($_SESSION['id'])?$_SESSION['id']:'';
		if($session != ''){
			
			
		$var1 = "update 01inventariocategoria set I_CATEGORIAS = '".$I_CATEGORIAS."' , ICATEGORIAS = '".$ICATEGORIAS."' where idRelacion = '".$session."' ; ";

		
		$var2 = "insert into 01inventariocategoria ( I_CATEGORIAS, ICATEGORIAS, idRelacion) values ( '".$I_CATEGORIAS."' , '".$ICATEGORIAS."' , '".$session."' ); ";
		
		
			
		if($IINVENTARIO2 == 'IINVENTARIO2'){

		mysqli_query($conn,$var1) or die('P276'.mysqli_error($conn));
		return "ACTUALIZADO";
		}else{
		mysqli_query($conn,$var2) or die('P279'.mysqli_error($conn));
		return "INGRESADO";
		}
		}else{
		echo "NO HAY UN USUARIO SELECCIONADO";	
		}		
}


	public function listadoINVENTARIOcategoria(){
		$conn = $this->db();
		$variablequery = "select * from 01inventariocategoria  where idRelacion = '".$_SESSION['id']."' order by id desc ";
		return $arrayquery = mysqli_query($conn,$variablequery);
	}





	public function listadoINVENTARIO2categoria($id){
		$conn = $this->db();
		$variablequery = "select * from 01inventariocategoria  where id = '".$id."' ";
		return $arrayquery = mysqli_query($conn,$variablequery);
	}





	public function BORRAcategoriainventario($id){
		$conn = $this->db();
		$variablequery = "delete from 01inventariocategoria where id = '".$id."' ";
		$arrayquery = mysqli_query($conn,$variablequery);
		RETURN 
		
		"<P style='color:green; font-size:26px;'>ELEMENTO BORRADO</P>";
	}










/**//**//**//**//*CONVENIO PAGO PDF*//**//**//**//**/


	public function variablesTOTALADEUDO(){
		
		$conn = $this->db();
		$variablequery = "SELECT sum(`CP_MONTO_SOLICITADO`) as totalito FROM `01convenioprestamo` WHERE `idRelacion` ='".$_SESSION['id']."'  ";

		$arrayquery = mysqli_query($conn,$variablequery);
		$row = mysqli_fetch_array($arrayquery, MYSQLI_ASSOC);
		return $row['totalito'];
		
	}
	
	public function variablesTOTALPAGADO(){
		
		$conn = $this->db();
		$variablequery = "SELECT sum(`CP_MONTO_PAGADO1`) as totalito FROM `01conveniopago` WHERE `idRelacion` ='".$_SESSION['id']."'  ";

		$arrayquery = mysqli_query($conn,$variablequery);
		$row = mysqli_fetch_array($arrayquery, MYSQLI_ASSOC);
		return $row['totalito'];
		
	}	
	
	public function variablesTODOSCONVENIOPAGO(){
		
		$conn = $this->db();
		return $variablequery = "SELECT * FROM `01conveniopago` WHERE `idRelacion` ='".$_SESSION['id']."'  ";

		 $arrayquery = mysqli_query($conn,$variablequery);
	}	
	
/**//**//**//**//*CONVENIO PAGO*//**//**//**//**/

	public function variablesCONVENIOPAGO(){
		$conn = $this->db();
		$variablequery = "select * from 01conveniopago  where idRelacion = '".$_SESSION['id']."' ";
		$arrayquery = mysqli_query($conn,$variablequery);
		return $row = mysqli_fetch_array($arrayquery, MYSQLI_ASSOC);		
	}

	public function revisar_CONVENIOPAGO(){
		$conn = $this->db();
		$var1 = 'select id from 01conveniopago  where idRelacion =  "'.$_SESSION['id'].'" ';
		$query = mysqli_query($conn,$var1) or die('P44'.mysqli_error($conn));
		$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
		return $row['id'];
	}

	public function gardarconveniopago ( $CP_FECHA_PAGO , $CP_MONTO_PAGADO1 , $CP_CONCEPTO , $CP_NUMERO_EVENTO , $CP_OBSERVACIONES , $CP_MONTO_A_PAGAR1 , $CP_TOTAL_PAGADO , $CP_MONTO_A_PAGAR , $iCONVENIOPAGO , $CP_ENVIAR_IMAIL ,$ICONVENIOPAGO22, $IpCONVENIOPAGO2){
		
		
		$conn = $this->db();
		$existe = $this->revisar_CONVENIOPAGO();
		$session = isset($_SESSION['id'])?$_SESSION['id']:'';
		if($session != ''){
			
			
		$var1 = "update 01conveniopago set CP_FECHA_PAGO = '".$CP_FECHA_PAGO."' , CP_MONTO_PAGADO1 = '".$CP_MONTO_PAGADO1."' , CP_CONCEPTO = '".$CP_CONCEPTO."' , CP_NUMERO_EVENTO = '".$CP_NUMERO_EVENTO."' , CP_OBSERVACIONES = '".$CP_OBSERVACIONES."' , CP_MONTO_A_PAGAR1 = '".$CP_MONTO_A_PAGAR1."' , CP_TOTAL_PAGADO = '".$CP_TOTAL_PAGADO."' , CP_MONTO_A_PAGAR = '".$CP_MONTO_A_PAGAR."' , iCONVENIOPAGO = '".$iCONVENIOPAGO."' , CP_ENVIAR_IMAIL = '".$CP_ENVIAR_IMAIL."' where id = '".$IpCONVENIOPAGO2."' ;  ";

		
		$var2 = "insert into 01conveniopago ( CP_FECHA_PAGO, CP_MONTO_PAGADO1, CP_CONCEPTO, CP_NUMERO_EVENTO, CP_OBSERVACIONES, CP_MONTO_A_PAGAR1, CP_TOTAL_PAGADO, CP_MONTO_A_PAGAR, iCONVENIOPAGO, CP_ENVIAR_IMAIL, idRelacion) values ( '".$CP_FECHA_PAGO."' , '".$CP_MONTO_PAGADO1."' , '".$CP_CONCEPTO."' , '".$CP_NUMERO_EVENTO."' , '".$CP_OBSERVACIONES."' , '".$CP_MONTO_A_PAGAR1."' , '".$CP_TOTAL_PAGADO."' , '".$CP_MONTO_A_PAGAR."' , '".$iCONVENIOPAGO."' , '".$CP_ENVIAR_IMAIL."' , '".$session."' );  ";
		
		
			
		if($ICONVENIOPAGO22 == 'ICONVENIOPAGO22'){

		mysqli_query($conn,$var1) or die('P276'.mysqli_error($conn));
		return "<P style='color:green; font-size:27px;'>ACTUALIZADO</P>";
		}else{
		mysqli_query($conn,$var2) or die('P279'.mysqli_error($conn));
		return "<P style='color:green; font-size:27px;'>INGRESADO</P>";
		}
		}else{
		echo "NO HAY UN USUARIO SELECCIONADO";	
		}	
}


	public function listadoCONVENIOPAGO(){
		$conn = $this->db();
		$variablequery = "select * from 01conveniopago  where idRelacion = '".$_SESSION['id']."' ";
		return $arrayquery = mysqli_query($conn,$variablequery);
	}


	public function listadoCONVENIOPAGO2($id){
		$conn = $this->db();
		$variablequery = "select * from 01conveniopago  where id = '".$id."' ";
		return $arrayquery = mysqli_query($conn,$variablequery);
	}



	public function borraCONVENIOPAGO2($id){
		$conn = $this->db();
		$variablequery = "delete from 01conveniopago where id = '".$id."' ";
		$arrayquery = mysqli_query($conn,$variablequery);
		RETURN 
		
		"<P style='color:green; font-size:26px;'>ELEMENTO BORRADO</P>";
	}




/**//**//**//**//*CARGA MASIVA MATERIALES*//**//**//**//**/
	public function variablesCARGAMASIVAM(){
		$conn = $this->db();
		$variablequery = "select * from 01cargamasivamateriales  where idRelacion = '".$_SESSION['id']."' ORDER BY id desc ";
		$arrayquery = mysqli_query($conn,$variablequery);
		return $row = mysqli_fetch_array($arrayquery, MYSQLI_ASSOC);		
	}

	public function revisar_CARGAMASIVAM(){
		$conn = $this->db();
		$var1 = 'select id from 01cargamasivamateriales  where idRelacion =  "'.$_SESSION['id'].'" ';
		$query = mysqli_query($conn,$var1) or die('P44'.mysqli_error($conn));
		$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
		return $row['id'];
	}

	public function guardarCARGAMASIVAM ($DEPARTAMENTO , $CM_NO , $CM_NOMBRE , $CM_APELLIDO , $CM_APELLIDOM , $CM_ARTICULO , $CM_CANTIDAD , $CM_MARCA , $CM_NODELO , $CM_NO_SERIE , $CM_FECHA_ENTREGA , $CM_FECHA_DEVOLUCION , $CM_OBSERVACIONES , $ICARGAMM2,$IpCARGAMM2){
		
		
		$conn = $this->db();
		$existe = $this->revisar_CARGAMASIVAM();
		$session = isset($_SESSION['id'])?$_SESSION['id']:'';
		if($session != ''){
			
			
		 $var1 = "update 01cargamasivamateriales set CM_NO = '".$CM_NO."', CM_APELLIDO = '".$CM_APELLIDO."' , CM_APELLIDOM = '".$CM_APELLIDOM."' , CM_ARTICULO = '".$CM_ARTICULO."' , CM_CANTIDAD = '".$CM_CANTIDAD."' , CM_MARCA = '".$CM_MARCA."' , CM_NODELO = '".$CM_NODELO."' , CM_NO_SERIE = '".$CM_NO_SERIE."' , CM_FECHA_ENTREGA = '".$CM_FECHA_ENTREGA."' , CM_FECHA_DEVOLUCION = '".$CM_FECHA_DEVOLUCION."' , CM_OBSERVACIONES = '".$CM_OBSERVACIONES."' , ICARGAMASIVAM = '".$ICARGAMASIVAM."' where id = '".$IpCARGAMM2."' ; ";

		
		$var2 = "insert into 01cargamasivamateriales ( DEPARTAMENTO, CM_NO, CM_NOMBRE, CM_APELLIDO, CM_APELLIDOM, CM_ARTICULO, CM_CANTIDAD, CM_MARCA, CM_NODELO, CM_NO_SERIE, CM_FECHA_ENTREGA, CM_FECHA_DEVOLUCION, CM_OBSERVACIONES, ICARGAMASIVAM, idRelacion) values ( '".$DEPARTAMENTO."' , '".$CM_NO."' , '".$CM_NOMBRE."' , '".$CM_APELLIDO."' , '".$CM_APELLIDOM."' , '".$CM_ARTICULO."' , '".$CM_CANTIDAD."' , '".$CM_MARCA."' , '".$CM_NODELO."' , '".$CM_NO_SERIE."' , '".$CM_FECHA_ENTREGA."' , '".$CM_FECHA_DEVOLUCION."' , '".$CM_OBSERVACIONES."' , '".$ICARGAMASIVAM."' , '".$session."' ); ";
		
		
			
if($ICARGAMM2 == 'ICARGAMM2'){

    mysqli_query($conn,$var1) or die('P276'.mysqli_error($conn));
    return "<span style='color:green; font-size:24px; font-weight:bold;'>ACTUALIZADO</span>";

}else{

    mysqli_query($conn,$var2) or die('P279'.mysqli_error($conn));
    return "<span style='color:green; font-size:24px; font-weight:bold;'>INGRESADO</span>";

}

}else{
    echo "NO HAY UN USUARIO SELECCIONADO";    
}
}




public function listadoCARGAMASIVAM(){
    $conn = $this->db();
    // Eliminar la restricción de idRelacion basada en $_SESSION['id']
    $variablequery = "SELECT * 
                      FROM 01cargamasivamateriales  
                      ORDER BY id DESC"; // Se eliminó WHERE idRelacion = '".$_SESSION['id']."' 
    return $arrayquery = mysqli_query($conn,$variablequery);
}




public function listadoCARGAMASIVAMnombre($id) {
    $conn = $this->db();
    $variablequery = "SELECT * FROM 01informacionpersonal WHERE idRelacion = '".$id."' ORDER BY NOMBRE_1 ASC"; // ASC para orden ascendente
    return $arrayquery = mysqli_query($conn, $variablequery);
}



	public function listadoCARGAMASIVAM2($id){
		$conn = $this->db();
		$variablequery = "select * from 01cargamasivamateriales  where id = '".$id."' ";
		return $arrayquery = mysqli_query($conn,$variablequery);
	}





	public function borraCARGAMM2($id){
		$conn = $this->db();
		$variablequery = "delete from 01cargamasivamateriales where id = '".$id."' ";
		$arrayquery = mysqli_query($conn,$variablequery);
		RETURN 
		
		"<P style='color:green; font-size:26px;'>ELEMENTO BORRADO</P>";
	}







/**//**//**//**//*carga masiva equipo u*//**//**//**//**/

	public function revisar_CARGAMASIVAU(){
		$conn = $this->db();
		$var1 = 'select id from 01CARGAMASIVAU  where idRelacion =  "'.$_SESSION['id'].'" ';
		$query = mysqli_query($conn,$var1) or die('P44'.mysqli_error($conn));
		$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
		return $row['id'];
	}

	public function guardarcargamasivau ($DEPARTAMENTO , $CMU_NO , $CMU_NOMBRE , $CMU_APELLIDO , $CMU_APELLIDOM , $CMU_ARTICULO , $CMU_CANTIDAD , $CMU_TALLA , $CMU_MARCA , $CMU_FECHA_ENTREGA , $CMU_FECHA_DEVOLUCION , $CMU_OBSERVACIONES , $icargamasivaU , $CMU_FOTO , $IpCARGAMU2, $ICARGAMU2){
		
		
		$conn = $this->db();
		$existe = $this->revisar_CARGAMASIVAU();
		$session = isset($_SESSION['id'])?$_SESSION['id']:'';
		if($session != ''){
			
			
		$var1 = "update 01CARGAMASIVAU set  CMU_NO = '".$CMU_NO."' , CMU_APELLIDO = '".$CMU_APELLIDO."' , CMU_APELLIDOM = '".$CMU_APELLIDOM."' , CMU_ARTICULO = '".$CMU_ARTICULO."' , CMU_CANTIDAD = '".$CMU_CANTIDAD."' , CMU_TALLA = '".$CMU_TALLA."' , CMU_MARCA = '".$CMU_MARCA."' , CMU_FECHA_ENTREGA = '".$CMU_FECHA_ENTREGA."' , CMU_FECHA_DEVOLUCION = '".$CMU_FECHA_DEVOLUCION."' , CMU_OBSERVACIONES = '".$CMU_OBSERVACIONES."' , icargamasivaU = '".$icargamasivaU."' where id = '".$IpCARGAMU2."' ; ";

		
		$var2 = "insert into 01CARGAMASIVAU ( DEPARTAMENTO, CMU_NO, CMU_NOMBRE, CMU_APELLIDO, CMU_APELLIDOM, CMU_ARTICULO, CMU_CANTIDAD, CMU_TALLA, CMU_MARCA, CMU_FECHA_ENTREGA, CMU_FECHA_DEVOLUCION, CMU_OBSERVACIONES, icargamasivaU, CMU_FOTO, idRelacion) values ( '".$DEPARTAMENTO."' , '".$CMU_NO."' , '".$CMU_NOMBRE."' , '".$CMU_APELLIDO."' , '".$CMU_APELLIDOM."' , '".$CMU_ARTICULO."' , '".$CMU_CANTIDAD."' , '".$CMU_TALLA."' , '".$CMU_MARCA."' , '".$CMU_FECHA_ENTREGA."' , '".$CMU_FECHA_DEVOLUCION."' , '".$CMU_OBSERVACIONES."' , '".$icargamasivaU."', '".$CMU_FOTO."' , '".$session."' ); ";
		
		
			
if($ICARGAMU2 == 'ICARGAMU2'){

    mysqli_query($conn,$var1) or die('P276'.mysqli_error($conn));
    return "<span style='color:green; font-size:24px; font-weight:bold;'>ACTUALIZADO</span>";

}else{

    mysqli_query($conn,$var2) or die('P279'.mysqli_error($conn));
    return "<span style='color:green; font-size:24px; font-weight:bold;'>INGRESADO</span>";

}

}else{
    echo "NO HAY UN USUARIO SELECCIONADO";    
}
	}




public function listadoCARGAMASIVAu(){
     $conn = $this->db();
    // Eliminar la restricción de idRelacion basada en $_SESSION['id']
    $variablequery = "SELECT * 
                      FROM 01CARGAMASIVAU  
                      ORDER BY id DESC"; // Se eliminó WHERE idRelacion = '".$_SESSION['id']."' 
    return $arrayquery = mysqli_query($conn,$variablequery);
}

	public function listadoCARGAMASIVAu2($id){
		$conn = $this->db();
		$variablequery = "select * from 01CARGAMASIVAU  where id = '".$id."' ";
		return $arrayquery = mysqli_query($conn,$variablequery);
	}






	public function listadoCARGAMASIVAUnombre($id){
		$conn = $this->db();
		$variablequery = "select * from 01CARGAMASIVAU  where idRelacion = '".$id."' ";
		return $arrayquery = mysqli_query($conn,$variablequery);
	}


	public function borraCARGAuM2($id){
		$conn = $this->db();
		$variablequery = "delete from 01CARGAMASIVAU where id = '".$id."' ";
		$arrayquery = mysqli_query($conn,$variablequery);
		RETURN 
		
		"<P style='color:green; font-size:26px;'>ELEMENTO BORRADO</P>";
	}
/**//**//**//**//*carga masiva polizas*//**//**//**//**/

	public function revisar_CARGAMASIVAP(){
		$conn = $this->db();
		$var1 = 'select id from 01cargamasivap  where idRelacion =  "'.$_SESSION['id'].'" ';
		$query = mysqli_query($conn,$var1) or die('P44'.mysqli_error($conn));
		$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
		return $row['id'];
	}

	public function guardarcargamasivap ($DEPARTAMENTOP , $CMD_NO , $CMD_NOMBRE , $CMD_APELLIDO , $CMD_APELLIDOM , $CMD_DOCUMENTO , $CMD_FECHA_ENTREGA , $CMD_FECHA_INICIO , $CMD_EMPRESA_CONTRATO , $CMD_TELEFONO_EMERGENCIA , $CMD_OBSERVACIONES , $ICARGAMASIVAP , $inlineRadioOptions, $ICARGAMP222,$IpCARGAMP2,$CMD_CARGA_POLIZA1){
		
		
		$conn = $this->db();
		$existe = $this->revisar_CARGAMASIVAP();
		$session = isset($_SESSION['id'])?$_SESSION['id']:'';
		if($session != ''){
			
			
		$var1 = "update 01cargamasivap set CMD_NO = '".$CMD_NO."' , CMD_DOCUMENTO = '".$CMD_DOCUMENTO."' , CMD_FECHA_ENTREGA = '".$CMD_FECHA_ENTREGA."' , CMD_FECHA_INICIO = '".$CMD_FECHA_INICIO."' , CMD_EMPRESA_CONTRATO = '".$CMD_EMPRESA_CONTRATO."' , CMD_TELEFONO_EMERGENCIA = '".$CMD_TELEFONO_EMERGENCIA."' , CMD_OBSERVACIONES = '".$CMD_OBSERVACIONES."' , ICARGAMASIVAP = '".$ICARGAMASIVAP."' , inlineRadioOptions = '".$inlineRadioOptions."' where idRelacion = '".$IpCARGAMP2."' ; ";

		
		$var2 = " insert into 01cargamasivap ( DEPARTAMENTO, CMD_NO, CMD_NOMBRE, CMD_APELLIDO, CMD_APELLIDOM, CMD_DOCUMENTO, CMD_FECHA_ENTREGA, CMD_FECHA_INICIO, CMD_EMPRESA_CONTRATO, CMD_TELEFONO_EMERGENCIA, CMD_OBSERVACIONES, ICARGAMASIVAP, inlineRadioOptions, CMD_CARGA_POLIZA, idRelacion) values ( '".$DEPARTAMENTOP."' , '".$CMD_NO."' , '".$CMD_NOMBRE."' , '".$CMD_APELLIDO."' , '".$CMD_APELLIDOM."' , '".$CMD_DOCUMENTO."' , '".$CMD_FECHA_ENTREGA."' , '".$CMD_FECHA_INICIO."' , '".$CMD_EMPRESA_CONTRATO."' , '".$CMD_TELEFONO_EMERGENCIA."' , '".$CMD_OBSERVACIONES."' , '".$ICARGAMASIVAP."' , '".$inlineRadioOptions."' , '".$CMD_CARGA_POLIZA1."', '".$session."' ); ";
		
		
			
		if($ICARGAMP222 == 'ICARGAMP222'){

    mysqli_query($conn,$var1) or die('P276'.mysqli_error($conn));
    return "<span style='color:green; font-size:24px; font-weight:bold;'>ACTUALIZADO</span>";

}else{

    mysqli_query($conn,$var2) or die('P279'.mysqli_error($conn));
    return "<span style='color:green; font-size:24px; font-weight:bold;'>INGRESADO</span>";

}

}else{
    echo "NO HAY UN USUARIO SELECCIONADO";    
}
	}




	public function listadoCARGAMASIVAP2($id){
		$conn = $this->db();
		$variablequery = "select * from 01cargamasivap where id = '".$id."' ";
		return $arrayquery = mysqli_query($conn,$variablequery);
	}





public function listadoCARGAMASIVAPnombre($id) {
    $conn = $this->db();
    $variablequery = "SELECT * 
                      FROM 01informacionpersonal  
                      WHERE idRelacion = '".$id."' 
                      ORDER BY NOMBRE_1 ASC"; // Orden ascendente por NOMBRE_1
    return $arrayquery = mysqli_query($conn, $variablequery);
}





	public function listadoCARGAMASIVAP(){
    $conn = $this->db();
    // Eliminar la restricción de idRelacion basada en $_SESSION['id']
    $variablequery = "SELECT * 
                      FROM 01cargamasivap  
                      ORDER BY id DESC"; // Se eliminó WHERE idRelacion = '".$_SESSION['id']."' 
    return $arrayquery = mysqli_query($conn,$variablequery);
}







	public function borraCARGAMpp2($id){
		$conn = $this->db();
		$variablequery = "delete from 01cargamasivap where id = '".$id."' ";
		$arrayquery = mysqli_query($conn,$variablequery);
		RETURN 
		
		"<P style='color:green; font-size:26px;'>ELEMENTO BORRADO</P>";
	}









/**//**//**//**//*material equipo asignado*//**//**//**//**/

	public function variablesComPendientes(){
		$conn = $this->db();
		$variablequery = "select * from 01ComPendientes  where idRelacion = '".$_SESSION['id']."' ";
		$arrayquery = mysqli_query($conn,$variablequery);
		return $row = mysqli_fetch_array($arrayquery, MYSQLI_ASSOC);		
	}


  private $cache_variablespermisos = [];
    private $cache_departamento_permiso = null;






    public function variablespermisos($MMMMMM, $idtablapersmiso, $CAMPO){

        // Administradores siempre tienen acceso total - no necesitan consulta
        if(isset($_SESSION['idempermiso']) && ($_SESSION['idempermiso'] == '20' || $_SESSION['idempermiso'] == '1')){
            return "si";
        }

        // Clave única para este permiso
        $cacheKey = $idtablapersmiso . '|' . $CAMPO;
        if(isset($this->cache_variablespermisos[$cacheKey])){
            return $this->cache_variablespermisos[$cacheKey];
        }

        $conn = $this->db();

        if($this->cache_departamento_permiso === null){
            $VARempresa = '';

            if(!empty($_SESSION['idempermiso'])){
                $VARempresa = 'select PERMISOS from 01empresa where id = "'.$_SESSION['idempermiso'].'" ';
            } elseif(!empty($_SESSION['idPROVpermiso'])){
                $VARempresa = 'select PERMISOS from 02usuarios where id = "'.$_SESSION['idPROVpermiso'].'" ';
            } elseif(!empty($_SESSION['idcpermiso'])){
                $VARempresa = 'select PERMISOS from 06usuarios where id = "'.$_SESSION['idcpermiso'].'" ';
            }

            if(empty($VARempresa)){
                $this->cache_variablespermisos[$cacheKey] = "no";
                return "no";
            }

            $queryempresa = mysqli_query($conn, $VARempresa) or die('2978P44'.mysqli_error($conn));
            $rowempresa = mysqli_fetch_array($queryempresa, MYSQLI_ASSOC);
            $this->cache_departamento_permiso = isset($rowempresa['PERMISOS']) ? $rowempresa['PERMISOS'] : '';
        }

        if($this->cache_departamento_permiso === ''){
            $this->cache_variablespermisos[$cacheKey] = "no";
            return "no";
        }

        $campoSeguro = preg_replace('/[^a-zA-Z0-9_]/', '', (string)$CAMPO);
        if($campoSeguro === ''){
            $this->cache_variablespermisos[$cacheKey] = "no";
            return "no";
        }

        $var1 = 'select '.$campoSeguro.' from 05permisosindex where 
        pagina = "'.$idtablapersmiso.'" AND departamento = "'.$this->cache_departamento_permiso.'" ';
        $query = mysqli_query($conn, $var1) or die('P44'.mysqli_error($conn));
        $row = mysqli_fetch_array($query, MYSQLI_ASSOC);

        $resultado = isset($row[$campoSeguro]) ? $row[$campoSeguro] : "no";

        // Guardar en cache
        $this->cache_variablespermisos[$cacheKey] = $resultado;

        return $resultado;
    }


    public function lista_plantillas2() {
    $conn = $this->db();
    $variablequery = "SELECT * FROM 05permisosplantilla ORDER BY nombreplantilla ASC";
    return mysqli_query($conn, $variablequery);		
    }

		public function lista_plantillaevencliente(){
		$conn = $this->db();
		$variablequery = "select * from 06usuarios";
		return $arrayquery = mysqli_query($conn,$variablequery);		
	}
	
	
	
		public function lista_empresacolaborador(){
		$conn = $this->db();
		$variablequery = "select * from 03datosdelaempresa";
		return $arrayquery = mysqli_query($conn,$variablequery);		
	}
	
		
		public function lista_plantillaventavehi(){
		$conn = $this->db();
		$variablequery = "select * from 09vehiculos where ver_vehiculo = 1 ";
		return $arrayquery = mysqli_query($conn,$variablequery);		
	}


		public function lista_plantillaventavehi_todos(){
		$conn = $this->db();
		$variablequery = "select * from 09vehiculos ";
		return $arrayquery = mysqli_query($conn,$variablequery);		
	}


		public function lista_plantillacostovehiculo(){
		$conn = $this->db();
		$variablequery = "select * from 09MENSAJERIA";
		return $arrayquery = mysqli_query($conn,$variablequery);		
	}
	
	
		public function lista_inventario1(){
		$conn = $this->db();
		$variablequery = "select * from 01inventario where RE_CANTIDAD = 'MATERIAL Y EQUIPO' ";
		return $arrayquery = mysqli_query($conn,$variablequery);		
	}
		public function lista_inventario2(){
		$conn = $this->db();
		$variablequery = "select * from 01inventario where RE_CANTIDAD = 'PAPELERÍA' ";
		return $arrayquery = mysqli_query($conn,$variablequery);		
	}

	    public function lista_inventario3(){
		$conn = $this->db();
		$variablequery = "select * from 01inventario where RE_CANTIDAD = 'EQUIPO DE OFICINA' ";
		return $arrayquery = mysqli_query($conn,$variablequery);		
	}

		public function lista_inventario4(){
		$conn = $this->db();
		$variablequery = "select * from 01inventario where RE_CANTIDAD = 'BOTIQUIN DE PRIMEROS AUXILIOS' ";
		return $arrayquery = mysqli_query($conn,$variablequery);		
	}




		public function lista_plantillausuariocontrase(){
		$conn = $this->db();
		$variablequery = "select * from 01empresa";
		return $arrayquery = mysqli_query($conn,$variablequery);		
	}

	
	 	public function lista_plantillasolicitadopor(){
		$conn = $this->db();
		$variablequery = "select * from 01empresa";
		return $arrayquery = mysqli_query($conn,$variablequery);		
	}
	
	 	public function lista_plantillaautorizadopor(){
		$conn = $this->db();
		$variablequery = "select * from 01empresa";
		return $arrayquery = mysqli_query($conn,$variablequery);		
	}
	 	public function lista_plantillacontratadopor(){
		$conn = $this->db();
		$variablequery = "select * from 01empresa";
		return $arrayquery = mysqli_query($conn,$variablequery);		
	}
	
	 	public function lista_plantillatarjeta(){
		$conn = $this->db();                                          
		$variablequery = "select * from 01empresa";
		return $arrayquery = mysqli_query($conn,$variablequery);		
	}
	
		 	public function lista_plantillausuariocontrasenas(){
		$conn = $this->db();                                           
		$variablequery = 'select *,01empresa.id as idem from 01empresa JOIN 01adjuntoscolaboradores ON 01empresa.id = 01adjuntoscolaboradores.idRelacion where ESTATUS_CRM_ACTIVOBAJA = "ACTIVO" ';
		return $arrayquery = mysqli_query($conn,$variablequery);		
	}
	
		public function lista_plantillaempresacontrasenas(){
		$conn = $this->db();
		$variablequery = "select * from 03datosdelaempresa";
		return $arrayquery = mysqli_query($conn,$variablequery);
	
    }

    		public function lista_proveedormensajeria(){
		$conn = $this->db();
		$variablequery = "select * from 02direccionproveedor1";
		return $arrayquery = mysqli_query($conn,$variablequery);
	
    }
	   		public function lista_clientesmensajeria(){
		$conn = $this->db();
		$variablequery = "select * from 06direccionclientes";
		return $arrayquery = mysqli_query($conn,$variablequery);
	
    }
	
	public function colaborador_generico_bueno(){
	$conn = $this->db();                                           
	$variablequery = "select *,01informacionpersonal.idRelacion as aliasid from 01informacionpersonal inner join 01adjuntoscolaboradores on 01informacionpersonal.idRelacion = 01adjuntoscolaboradores.idRelacion where ESTATUS_CRM_ACTIVOBAJA = 'ACTIVO' order by 01informacionpersonal.`NOMBRE_1` asc; ";
	
	return $arrayquery = mysqli_query($conn,$variablequery);	
		
	}



public function informacionpersonal_contrasenias($id) {
    $conn = $this->db();
    
    $query2 = 'SELECT NOMBRE_1,NOMBRE_2, APELLIDO_PATERNO, APELLIDO_MATERNO 
               FROM `01informacionpersonal` 
               WHERE idRelacion = "' . $id . '" 
               ORDER BY NOMBRE_1 ASC'; // Orden alfabético por este campo
    
    $results2 = mysqli_query($conn, $query2) or die(mysqli_error($conn));
    $row2 = mysqli_fetch_array($results2);
    
    return $row2['NOMBRE_1'] . ' ' 
    
           . $row2['APELLIDO_PATERNO'] . ' ' 
           . $row2['APELLIDO_MATERNO'];
}	
		



/**//**//**//**//*acepta privacidad*//**//**//**//**/


	public function aceptaprivacidad($id){
		$conn = $this->db();
		//$row1['idem']
	$query2 = 'SELECT * FROM `11privacidad` where idRelacion = "'.$id.'" ';
	$results2 = mysqli_query($conn,$query2) or die( mysqli_error($conn));
	$row2 = mysqli_fetch_array($results2);
	return $row2['aceptar'];
	}

	public function variablesprivacidad($id){
		$conn = $this->db();
		//$row1['idem']
	$query2 = 'SELECT * FROM `11privacidad` where idRelacion = "'.$id.'" ';
	$results2 = mysqli_query($conn,$query2) or die( mysqli_error($conn));
	$row2 = mysqli_fetch_array($results2);
	return $row2;
	}

	public function variablesprivacidad2($id){
		$conn = $this->db();
		//$row1['idem']
	$query2 = 'SELECT * FROM `11privacidad` where idRelacion = "'.$id.'" ';
	$results2 = mysqli_query($conn,$query2) or die( mysqli_error($conn));
	$row2 = mysqli_fetch_array($results2);
	return $row2;
	}

	public function guardaPrivacidad($idRelacion, $fechaAceptacion, $aceptar ){
		$conn = $this->db();
		$preggunta  = $this->variablesprivacidad2($idRelacion);
		
		//$row1['idem']
	if($preggunta['id']>'1'){
		
	$query3  = 'update `11privacidad` set 
	fechaAceptacion = "'.$fechaAceptacion.'", 
	aceptar= "'.$aceptar.'"
	where idRelacion ="'.$idRelacion.'"	';
	mysqli_query($conn,$query3) or die( mysqli_error($conn));		
					return "ACEPTADA";
	}else{
	$query2 = 'INSERT INTO `11privacidad` 
	(idRelacion, fechaAceptacion, aceptar) VALUES
	("'.$idRelacion.'","'.$fechaAceptacion.'","'.$aceptar.'")
	';
	mysqli_query($conn,$query2) or die( mysqli_error($conn));
		$id= mysqli_insert_id($conn);
		if($id>=1){
			return "ACEPTADA";
		}else{
			return "ACEPTA LOS TERMINOS DE PRIVACIDAD PARA CONTINUARS";
		}
	
		}
	}
/**//**//**//**//*acepta privacidad*//**//**//**//**/








	
/**//**//**//**//*menu lateral*//**//**//**//**/

	public function menulateral($bloque){
		$conn = $this->db();

		if($_SESSION['idempermiso']==true){
		$VARempresa = 'select PERMISOS from 01empresa where id = "'.$_SESSION['idempermiso'].'" ';
		}
		
		if($_SESSION['idPROVpermiso']==true){
		$VARempresa = 'select PERMISOS from 02usuarios where id = "'.$_SESSION['idPROVpermiso'].'" ';
		}
		
		if($_SESSION['idcpermiso']==true){
		$VARempresa = 'select PERMISOS from 06usuarios where id = "'.$_SESSION['idcpermiso'].'" ';
		}
		
		$queryempresa = mysqli_query($conn,$VARempresa) or die('P44'.mysqli_error($conn));
		$rowempresa = mysqli_fetch_array($queryempresa, MYSQLI_ASSOC);			
		$var = "SELECT count(*) as cuenta1 FROM `05permisosindex` WHERE  ver = 'si' and `departamento` = '".$rowempresa['PERMISOS']."' and bloque =  '".$bloque."' ";
		$query = mysqli_query($conn,$var) or die('P44'.mysqli_error($conn));
		$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
		
		
		if($_SESSION['idempermiso']=='20' or $_SESSION['idempermiso']=='1'){
		return 2;	
		}else{
		return $row['cuenta1'];
		}
	}

	public function menulateralicono($icono){
		$conn = $this->db();
		
		if($_SESSION['idempermiso']==true){
		$VARempresa = 'select PERMISOS from 01empresa where id = "'.$_SESSION['idempermiso'].'" ';
		}
		
		if($_SESSION['idPROVpermiso']==true){
		$VARempresa = 'select PERMISOS from 02usuarios where id = "'.$_SESSION['idPROVpermiso'].'" ';
		}		

		if($_SESSION['idcpermiso']==true){
		$VARempresa = 'select PERMISOS from 06usuarios where id = "'.$_SESSION['idcpermiso'].'" ';
		}

		$queryempresa = mysqli_query($conn,$VARempresa) or die('P44'.mysqli_error($conn));
		$rowempresa = mysqli_fetch_array($queryempresa, MYSQLI_ASSOC);			
		$var = "SELECT count(*) as cuenta1 FROM `05permisosindex` WHERE  ver = 'si' and `departamento` = '".$rowempresa['PERMISOS']."' and icono =  '".$icono."' ";
		$query = mysqli_query($conn,$var) or die('P44'.mysqli_error($conn));
		$row = mysqli_fetch_array($query, MYSQLI_ASSOC);

		if($_SESSION['idempermiso']=='20' or $_SESSION['idempermiso']=='1'){
		return 2;	
		}else{
		return $row['cuenta1'];
		}
	}
	
	
		/*SEPLEGABLES*/
	
	public function desplegables07($nombre_bloque,$nombre_formulario){
		$conn = $this->db();
		$variablequery = "select * from 07desplegables where nombre_bloque = '".$nombre_bloque."' and nombre_formulario = '".$nombre_formulario."' ";
		//nombre_bloque, nombre_formulario, nombre_campo
		return $arrayquery = mysqli_query($conn,$variablequery);
	}

	/*plantilla filtro*/
	public function plantilla_filtro($query,$nombrecampo,$otro,$DEPARTAMENTO){
		$conn = $this->db();
		 $DEPARTAMENTO;
		$query = str_replace('SUBIR COTIZACIÓN FIRMADA POR EL CLIENTE AUTORIZANDO EL EVENTO','',$query); 
		
			$nombrecampo = str_replace('SUBIR COTIZACIÓN FIRMADA POR EL CLIENTE AUTORIZANDO EL EVENTO','',$nombrecampo); 	
						$DEPARTAMENTO = str_replace('SUBIR COTIZACIÓN FIRMADA POR EL CLIENTE AUTORIZANDO EL EVENTO','',$DEPARTAMENTO);
		/*SELECT * FROM `08altaeventosfiltroDes`, 08altaeventosfiltroPLA WHERE 08altaeventosfiltroDes.id = 08altaeventosfiltroPLA.idRelacion; */
		
		$DEPARTAMENTO2 = isset($DEPARTAMENTO)?$DEPARTAMENTO:'';
		if($DEPARTAMENTO2=='DEFAULT' or $DEPARTAMENTO2==''){
		return "si";
		}else{
		$variablequery = $query." and '".$nombrecampo."' = nombrecampo and nombreformulario = '".$otro."' and NOMBRE_PLANTILLA = '".$DEPARTAMENTO2."'; ";
		$arrayquery = mysqli_query($conn,$variablequery);
		$row = mysqli_fetch_array($arrayquery, MYSQLI_ASSOC);
		return $row['permiso'];
		}
	}

	public function desplegablesfiltro($nombre_bloque,$nombre_formulario){
		$conn = $this->db();
		$variablequery = "select * from 08alteventfiltroNombre where idRelacion = '".$nombre_bloque."'  ";
		//nombre_bloque, nombre_formulario, nombre_campo
		return $arrayquery = mysqli_query($conn,$variablequery);
	}





  ///////////////////////////// nuevo modulo /////////////////////////

  public function DATOSCOLABORADOR($DOCUMENTO_DATOSCOLABORADOR ,$ADJUNTO_DATOSCOLABORADOR, $OBSERVACIONES_DATOSCOLABORADOR , $FECHA_DATOSCOLABORADOR , $hDATOSCOLABORADOR,$IpDATOSCOLABORADOR,$enviarDATOSCOLABORADOR){
		
    $conn = $this->db();
    $session = isset($_SESSION['id'])?$_SESSION['id']:'';  
    if($session != ''){                            
        
     $var1 = "update 01DATOScolaborador  set
     
     
     DOCUMENTO_DATOSCOLABORADOR= '".$DOCUMENTO_DATOSCOLABORADOR."' , 
     OBSERVACIONES_DATOSCOLABORADOR = '".$OBSERVACIONES_DATOSCOLABORADOR."' ,  
     hDATOSCOLABORADOR = '".$hDATOSCOLABORADOR."'
     where id = '".$IpDATOSCOLABORADOR."' ;  ";
     
     $var2 = "insert into 01DATOScolaborador ( DOCUMENTO_DATOSCOLABORADOR,ADJUNTO_DATOSCOLABORADOR, OBSERVACIONES_DATOSCOLABORADOR, FECHA_DATOSCOLABORADOR, hDATOSCOLABORADOR, idRelacion) values ( '".$DOCUMENTO_DATOSCOLABORADOR."' , '".$ADJUNTO_DATOSCOLABORADOR."' , '".$OBSERVACIONES_DATOSCOLABORADOR."' , '".$FECHA_DATOSCOLABORADOR."' , '".$hDATOSCOLABORADOR."' , '".$session."' ); ";		



		if($IpDATOSCOLABORADOR!=''){
		$query_datos_bancarios1 = "select * from 01DATOScolaborador where id = '".$IpDATOSCOLABORADOR."' ";
		$identificador1 = $IpDATOSCOLABORADOR;
		}else{
		$query_datos_bancarios1 = "select * from 01DATOScolaborador where idRelacion = '".$session."' ";
		$identificador1 = $session;
		}
		//proveedor conseguir en ID del proveedor
		 $QUERYPROVEEDOR25 = "select id from 02usuarios where IdColaborador = '".$identificador1."'";
		$respuestaquery = mysqli_query($conn,$QUERYPROVEEDOR25) or die('P156'.mysqli_error($conn));
		$rowproveedor = mysqli_fetch_array($respuestaquery, MYSQLI_ASSOC);
			//proveedor pegar
		$var2proveedordelete = "delete from 02DOCUMENTOSFISCALES where idRelacion = '".$rowproveedor['id']."' ";
		//		$QUERYPROVEEDOR25 = "select id from 02usuarios where IdColaborador = '".$session."'";


        if($enviarDATOSCOLABORADOR=='enviarDATOSCOLABORADOR'){
    mysqli_query($conn,$var1) or die('P156'.mysqli_error($conn));
	
	
	
		mysqli_query($conn,$var2proveedordelete) or die('P156'.mysqli_error($conn));
		$QUERY_RESULTADO_02DATOSBANCARIOS1 = mysqli_query($conn,$query_datos_bancarios1) or die('P156'.mysqli_error($conn));
		while($row_bancario=mysqli_fetch_array($QUERY_RESULTADO_02DATOSBANCARIOS1, MYSQLI_ASSOC)){
		$var2proveedor = "insert into 02DOCUMENTOSFISCALES (
		 DOCUMENTO_LEGAL, ADJUNTAR_DOCUMENTO_LEGAL, 
		 ADJUNTAR_DOCUMENTO_OBSERVACIONES, FECHA_ULTIMA_DOCUMEN, 
		validaDOCUMENTOSFISCAL, idRelacion) values (
		 '".$row_bancario['DOCUMENTO_DATOSCOLABORADOR']."' , '".$row_bancario['ADJUNTO_DATOSCOLABORADOR']."' , 
		  '".$row_bancario['OBSERVACIONES_DATOSCOLABORADOR']."', '".$row_bancario['FECHA_DATOSCOLABORADOR']."' ,
		 '".$row_bancario['hDATOSCOLABORADOR']."', '".$rowproveedor['id']."' );  ";							
		mysqli_query($conn,$var2proveedor) or die('P4223'.mysqli_error($conn));
		}
	
	
    return "ACTUALIZADO";
                
    }else{



		mysqli_query($conn,$var2proveedordelete) or die('P156'.mysqli_error($conn));
		$QUERY_RESULTADO_02DATOSBANCARIOS1 = mysqli_query($conn,$query_datos_bancarios1) or die('P156'.mysqli_error($conn));
		while($row_bancario=mysqli_fetch_array($QUERY_RESULTADO_02DATOSBANCARIOS1, MYSQLI_ASSOC)){
		$var2proveedor = "insert into 02DOCUMENTOSFISCALES (
		 DOCUMENTO_LEGAL, ADJUNTAR_DOCUMENTO_LEGAL, 
		 ADJUNTAR_DOCUMENTO_OBSERVACIONES, FECHA_ULTIMA_DOCUMEN, 
		validaDOCUMENTOSFISCAL, idRelacion) values (
		 '".$row_bancario['DOCUMENTO_DATOSCOLABORADOR']."' , '".$row_bancario['ADJUNTO_DATOSCOLABORADOR']."' , 
		  '".$row_bancario['OBSERVACIONES_DATOSCOLABORADOR']."', '".$row_bancario['FECHA_DATOSCOLABORADOR']."' ,
		 '".$row_bancario['hDATOSCOLABORADOR']."', '".$rowproveedor['id']."' );  ";							
		mysqli_query($conn,$var2proveedor) or die('P4223'.mysqli_error($conn));
		}		
/* 
02DOCUMENTOSFISCALES

1 DOCUMENTO_LEGAL
2 ADJUNTAR_DOCUMENTO_LEGAL 
3 ADJUNTAR_DOCUMENTO_OBSERVACIONES
4 FECHA_ULTIMA_DOCUMEN
validaDOCUMENTOSFISCAL
idRelacion
	
*/
		
/*
01DATOScolaborador

1 DOCUMENTO_DATOSCOLABORADOR,
2 ADJUNTO_DATOSCOLABORADOR,           documento
3 OBSERVACIONES_DATOSCOLABORADOR,
4 FECHA_DATOSCOLABORADOR
hDATOSCOLABORADOR
idRelacion
*/




    mysqli_query($conn,$var2) or die('P160'.mysqli_error($conn));
    return "INGRESADO";
    }
        
    }else{
    echo "TU SESIÓN HA TERMINADO";	
    }
    
}

    
public function Listado_DATOSCOLABORADOR(){
    $session = isset($_SESSION['id'])?$_SESSION['id']:'';
    
    $conn = $this->db();
    $variablequery = "select * from 01DATOScolaborador WHERE idRelacion = '".$session."' order by id desc ";
    return $arrayquery = mysqli_query($conn,$variablequery);
}	


    public function Listado_DATOSCOLABORADOR2($id){
    $conn = $this->db();
    $variablequery = "select * from 01DATOScolaborador  where id = '".$id."' ";
    return $arrayquery = mysqli_query($conn,$variablequery);
}






public function borra_DATOSCOLABORADOR($id){
    $conn = $this->db();
    $variablequery = "delete from 01DATOScolaborador where id = '".$id."' ";
    $arrayquery = mysqli_query($conn,$variablequery);
    RETURN 
    
    "<P style='color:green; font-size:26px;'>ELEMENTO BORRADO</P>";
}


///////////////////////////////////////////////////////////////////////////////////////////


public function NUEVODOCU($nuevo_documento , $hnuevodocu,$enviarNUEVO,$IPNUEVO){
		
		$conn = $this->db();
		//$existe = $this->revisar_guardar_cierrenuevo();
		$session = isset($_SESSION['id'])?$_SESSION['id']:'';  
		if($session != ''){
			
		 $var1 = "update 01NUEVODATO set 
		 nuevo_documento = '".$nuevo_documento."' , hnuevodocu = '".$hnuevodocu."'  where id = '".$IPNUEVO."' ; ";
	
		 $var2 = " insert into 01NUEVODATO (nuevo_documento, hnuevodocu, idRelacion) values ( '".$nuevo_documento."' , '".$hnuevodocu."' , '".$session."' ); ";		
			
	    if($enviarNUEVO=='enviarNUEVO'){
		mysqli_query($conn,$var1) or die('P156'.mysqli_error($conn));
		return "ACTUALIZADO";
					
		}else{
		mysqli_query($conn,$var2) or die('P160'.mysqli_error($conn));
		return "INGRESADO";
		}
			
        }else{
		echo "TU SESIÓN HA TERMINADO";	
		}
		
	}


	public function Listado_nuevo2($id){
		$conn = $this->db();
		$variablequery = "select * from 01NUEVODATO  where id = '".$id."' ";
		return $arrayquery = mysqli_query($conn,$variablequery);
	}

	public function Listado_nuevo(){
		$conn = $this->db();
		$variablequery = "select * from 01NUEVODATO ";
		return $arrayquery = mysqli_query($conn,$variablequery);
	}	

	public function revisar_guardar_nuevo($id){
		$conn = $this->db();
		$var1 = 'select id from 01NUEVODATO where id = "'.$id.'" ';
		
		$query = mysqli_query($conn,$var1) or die('P44'.mysqli_error($conn));
		$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
		return $row['id'];
	}
	public function BORRAREGISTRO_NUEVO($id){
		$conn = $this->db();
		$var1 = 'DELETE from 01NUEVODATO where id = "'.$id.'" ';
	
		$query = mysqli_query($conn,$var1) or die('P44'.mysqli_error($conn));
		mysqli_fetch_array($query, MYSQLI_ASSOC);
				RETURN 
		
		"<P style='color:green;font-size:25px;'>ELEMENTO BORRADO</P>";
	}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	public function revisar_DATOSBANCARIOS1(){
		$conn = $this->db();
		$var1 = 'select id from 01DATOSBANCARIOS where idRelacion =  "'.$_SESSION['id'].'" ';
		$query = mysqli_query($conn,$var1) or die('P44'.mysqli_error($conn));
		$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
		return $row['id'];
	}


	public function enviarDATOSBANCARIOS1 (
	$TIPO_DE_MONEDA_1 , $INSTITUCION_FINANCIERA_1 , $NUMERO_DE_CUENTA_DB_1 , $NUMERO_CLABE_1 , 
	$NUMERO_DE_SUCURSAL_1 , $NUMERO_IBAN_1 , $NUMERO_CUENTA_SWIFT_1,$FOTO_ESTADO_PROVEE,$ULTIMA_CARGA_DATOBANCA,$OBSERVACIONES_D, $ENVIARRdatosbancario1p,$IPdatosbancario1p ){
	
		$conn = $this->db();
		$existe = $this->revisar_DATOSBANCARIOS1();
		$session = isset($_SESSION['id'])?$_SESSION['id']:'';    
		if($session != ''){
			
		$var1 = "update 01DATOSBANCARIOS set TIPO_DE_MONEDA_1 = '".$TIPO_DE_MONEDA_1."' , INSTITUCION_FINANCIERA_1 = '".$INSTITUCION_FINANCIERA_1."' , NUMERO_DE_CUENTA_DB_1 = '".$NUMERO_DE_CUENTA_DB_1."' , NUMERO_CLABE_1 = '".$NUMERO_CLABE_1."' , NUMERO_DE_SUCURSAL_1 = '".$NUMERO_DE_SUCURSAL_1."' , NUMERO_IBAN_1 = '".$NUMERO_IBAN_1."' , NUMERO_CUENTA_SWIFT_1 = '".$NUMERO_CUENTA_SWIFT_1."' ,ULTIMA_CARGA_DATOBANCA = '".$ULTIMA_CARGA_DATOBANCA."' ,OBSERVACIONES_D = '".$OBSERVACIONES_D."'  where id = '".$IPdatosbancario1p."' ; ";
		
		
		$var2 = "insert into 01DATOSBANCARIOS (TIPO_DE_MONEDA_1, INSTITUCION_FINANCIERA_1, NUMERO_DE_CUENTA_DB_1, NUMERO_CLABE_1, NUMERO_DE_SUCURSAL_1, NUMERO_IBAN_1, NUMERO_CUENTA_SWIFT_1,FOTO_ESTADO_PROVEE, ULTIMA_CARGA_DATOBANCA,OBSERVACIONES_D, idRelacion) values ( '".$TIPO_DE_MONEDA_1."' , '".$INSTITUCION_FINANCIERA_1."' , '".$NUMERO_DE_CUENTA_DB_1."' , '".$NUMERO_CLABE_1."' , '".$NUMERO_DE_SUCURSAL_1."' , '".$NUMERO_IBAN_1."' , '".$NUMERO_CUENTA_SWIFT_1."' , '".$FOTO_ESTADO_PROVEE."' , '".$ULTIMA_CARGA_DATOBANCA."' , '".$OBSERVACIONES_D."' , '".$session."' );  ";			



		if($IPdatosbancario1p!=''){
		$query_datos_bancarios1 = "select * from 01DATOSBANCARIOS where id = '".$IPdatosbancario1p."' ";
		$identificador1 = $IPdatosbancario1p;
		}else{
		$query_datos_bancarios1 = "select * from 01DATOSBANCARIOS where idRelacion = '".$session."' ";
		$identificador1 = $session;
		}
		//proveedor conseguir en ID del proveedor
		 $QUERYPROVEEDOR25 = "select id from 02usuarios where IdColaborador = '".$identificador1."'";
		$respuestaquery = mysqli_query($conn,$QUERYPROVEEDOR25) or die('P156'.mysqli_error($conn));
		$rowproveedor = mysqli_fetch_array($respuestaquery, MYSQLI_ASSOC);
			//proveedor pegar
		$var2proveedordelete = "delete from 02DATOSBANCARIOS1 where idRelacion = '".$rowproveedor['id']."' ";
		//		$QUERYPROVEEDOR25 = "select id from 02usuarios where IdColaborador = '".$session."'";


		if($ENVIARRdatosbancario1p=='ENVIARRdatosbancario1p'){	
		mysqli_query($conn,$var1) or die('P156'.mysqli_error($conn));
		





		mysqli_query($conn,$var2proveedordelete) or die('P156'.mysqli_error($conn));
		$QUERY_RESULTADO_02DATOSBANCARIOS1 = mysqli_query($conn,$query_datos_bancarios1) or die('P156'.mysqli_error($conn));
		while($row_bancario=mysqli_fetch_array($QUERY_RESULTADO_02DATOSBANCARIOS1, MYSQLI_ASSOC)){
		$var2proveedor = "insert into 02DATOSBANCARIOS1 (
		 P_TIPO_DE_MONEDA_1, P_INSTITUCION_FINANCIERA_1, 
		 P_NUMERO_DE_CUENTA_DB_1, P_NUMERO_CLABE_1, 
		 P_NUMERO_DE_SUCURSAL_1, P_NUMERO_IBAN_1, 
		 P_NUMERO_CUENTA_SWIFT_1, FOTO_ESTADO_PROVEE, 
		 ULTIMA_CARGA_DATOBANCA, OBSERVACIONES_D, checkbox,
		 idRelacion) values ( 
		 '".$row_bancario['P_TIPO_DE_MONEDA_1']."' , '".$row_bancario['P_INSTITUCION_FINANCIERA_1']."' , 
		 '".$row_bancario['P_NUMERO_DE_CUENTA_DB_1']."' , '".$row_bancario['P_NUMERO_CLABE_1']."' , 
		 '".$row_bancario['P_NUMERO_DE_SUCURSAL_1']."' , '".$row_bancario['P_NUMERO_IBAN_1']."' , 
		 '".$row_bancario['P_NUMERO_CUENTA_SWIFT_1']."' , '".$row_bancario['FOTO_ESTADO_PROVEE']."' , 
		 '".$row_bancario['ULTIMA_CARGA_DATOBANCA']."' , '".$row_bancario['OBSERVACIONES_D']."' ,
		 '".$row_bancario['checkbox']."' , 
		 '".$rowproveedor['id']."' );  ";							
		mysqli_query($conn,$var2proveedor) or die('P4223'.mysqli_error($conn));
		}






	
		
		return '<strong><span style="color:green; font-size:25px;">ACTUALIZADO </span></strong>';
		}else{
		mysqli_query($conn,$var2) or die('P160'.mysqli_error($conn));


		
		//echo $var2;
		mysqli_query($conn,$var2proveedordelete) or die('P156'.mysqli_error($conn));
		$QUERY_RESULTADO_02DATOSBANCARIOS1 = mysqli_query($conn,$query_datos_bancarios1) or die('P156'.mysqli_error($conn));
		while($row_bancario=mysqli_fetch_array($QUERY_RESULTADO_02DATOSBANCARIOS1, MYSQLI_ASSOC)){
		$var2proveedor = "insert into 02DATOSBANCARIOS1 (
		 P_TIPO_DE_MONEDA_1, P_INSTITUCION_FINANCIERA_1, 
		 P_NUMERO_DE_CUENTA_DB_1, P_NUMERO_CLABE_1, 
		 P_NUMERO_DE_SUCURSAL_1, P_NUMERO_IBAN_1, 
		 P_NUMERO_CUENTA_SWIFT_1, FOTO_ESTADO_PROVEE, 
		 ULTIMA_CARGA_DATOBANCA, OBSERVACIONES_D, checkbox,
		 idRelacion) values (
	 
		 '".$row_bancario['TIPO_DE_MONEDA_1']."' , '".$row_bancario['INSTITUCION_FINANCIERA_1']."' , 
		 '".$row_bancario['NUMERO_DE_CUENTA_DB_1']."' , '".$row_bancario['NUMERO_CLABE_1']."' , 
		 '".$row_bancario['NUMERO_DE_SUCURSAL_1']."' , '".$row_bancario['NUMERO_IBAN_1']."' , 
		 '".$row_bancario['NUMERO_CUENTA_SWIFT_1']."' , '".$row_bancario['FOTO_ESTADO_PROVEE']."' , 
		 '".$row_bancario['ULTIMA_CARGA_DATOBANCA']."' , '".$row_bancario['OBSERVACIONES_D']."' ,
		 '".$row_bancario['checkbox']."' , 
		 '".$rowproveedor['id']."' );  ";							
		mysqli_query($conn,$var2proveedor) or die('P4223'.mysqli_error($conn));
		}			
			

		return '<strong><span style="color:green; font-size:25px;">INGRESADO </span></strong>';
		}
			
        }else{
		echo "NO HAY UN COLABORADOR SELECCIONADO";	
		}
    }



public function Listado_DATOSBAN(){
    $session = isset($_SESSION['id'])?$_SESSION['id']:'';  

    $conn = $this->db();
    $variablequery = "select * from 01DATOSBANCARIOS WHERE idRelacion = '".$session."' order by id desc ";
    return $arrayquery = mysqli_query($conn,$variablequery);
}

	public function Listado_datos_bancariosPRO2($id){
		$conn = $this->db();
		$variablequery = "select * from 01DATOSBANCARIOS  where id = '".$id."' ";
		return $arrayquery = mysqli_query($conn,$variablequery);
	}


         function borra_datos_bancario1($id){
		$conn = $this->db();
		$variablequery = "delete from 01DATOSBANCARIOS where id = '".$id."' ";
		$arrayquery = mysqli_query($conn,$variablequery);
		RETURN 
		
		"<P style='color:green; font-size:25px;'>ELEMENTO BORRADO</P>";
	}


	
	public function datos_bancario_default($pasarDID, $pasarD_text) {
    $conn = $this->db();
    $session = isset($_SESSION['id']) ? $_SESSION['id'] : '';
    
    if ($session != '') {
       
        $check_active = $conn->prepare("SELECT id FROM 01DATOSBANCARIOS 
                                       WHERE idRelacion = ? AND checkbox = 'si'");
        $check_active->bind_param("s", $session);
        $check_active->execute();
        $check_active->store_result();
        $active_count = $check_active->num_rows;
        $check_active->close();
        
  
        $update_current = $conn->prepare("UPDATE 01DATOSBANCARIOS SET checkbox = ? 
                                         WHERE id = ?");
        $update_current->bind_param("ss", $pasarD_text, $pasarDID);
        $update_current->execute();
        $update_current->close();
        
        if ($pasarD_text == 'si') {
          
            $deselect_others = $conn->prepare("UPDATE 01DATOSBANCARIOS SET checkbox = 'no' 
                                              WHERE idRelacion = ? AND id != ?");
            $deselect_others->bind_param("ss", $session, $pasarDID);
            $deselect_others->execute();
            $deselect_others->close();
        } 
        else if ($active_count <= 1) {
            
            $activate_last = $conn->prepare("UPDATE 01DATOSBANCARIOS SET checkbox = 'si' 
                                            WHERE idRelacion = ? 
                                            ORDER BY id DESC LIMIT 1");
            $activate_last->bind_param("s", $session);
            $activate_last->execute();
            $activate_last->close();
        }
        
        echo "Actualizado: " . $pasarD_text;
    } else {
        echo "TU SESIÓN HA TERMINADO";
    }
}



	




	public function variable_DIRECCIONP1(){
		$conn = $this->db();
		$variablequery = "select * from 01DATOSFISCALESC where idRelacion = '".$_SESSION['id']."' ";
		$arrayquery = mysqli_query($conn,$variablequery);
		return $row = mysqli_fetch_array($arrayquery, MYSQLI_ASSOC);		
	}

	public function revisar_DIRECCIONP1(){
		$conn = $this->db();
		$var1 = 'select id from 01DATOSFISCALESC where idRelacion =  "'.$_SESSION['id'].'" ';
		$query = mysqli_query($conn,$var1) or die('P44'.mysqli_error($conn));
		$row = mysqli_fetch_array($query, MYSQLI_ASSOC);
		return $row['id'];
	}

	public function DATOSFISCALES ($NOMBRE_COMERCIAL_EMPRESA , $NOMBRE_FISCAL_RS_EMPRESA ,$RFC_MTDP , $REGIMEN_FISCAL_MTDP , $METODO_DE_PAGO , $FORMADE_PAGO , $USO_CFDI ,$FISICA_MORAL,$DIRECCION_FISCAL_EMPRESA , $EDIFICIO_EMPRESA , $CALLE_EMPRESA , $NUMERO_EXTERIOR_EMPRESA , $NUMERO_INTERIOR_EMPRESA , $NUMERO_OFICINA_EMPRESA , $COLONIA_EMPRESA , $ALCALDIA_EMPRESA , $C_EMPRESA , $CIUDAD_EMPRESA , $ESTADO_EMPRESA , $PAIS_EMPRESA , $UBICACION_MAPA_1 , $TELEFONO_1_EMPRESA , $TELEFONO_2_EMPRESA , $WHATSAPEMPRESA_1 , $IMAIL_EMPRESA , $PAGINA_WEB_EMPRESA , $NOMBRE_APEMPRESA  ){
		$conn = $this->db();
		$existe = $this->revisar_DIRECCIONP1();
		$session = isset($_SESSION['id'])?$_SESSION['id']:'';
		if($session != ''){
			
		$var1 = "update 01DATOSFISCALESC set NOMBRE_COMERCIAL_EMPRESA = '".$NOMBRE_COMERCIAL_EMPRESA."' , NOMBRE_FISCAL_RS_EMPRESA = '".$NOMBRE_FISCAL_RS_EMPRESA."' ,RFC_MTDP = '".$RFC_MTDP."' , REGIMEN_FISCAL_MTDP = '".$REGIMEN_FISCAL_MTDP."' , METODO_DE_PAGO = '".$METODO_DE_PAGO."' , FORMADE_PAGO = '".$FORMADE_PAGO."' , USO_CFDI = '".$USO_CFDI."' , FISICA_MORAL = '".$FISICA_MORAL."' , DIRECCION_FISCAL_EMPRESA = '".$DIRECCION_FISCAL_EMPRESA."' , EDIFICIO_EMPRESA = '".$EDIFICIO_EMPRESA."' , CALLE_EMPRESA = '".$CALLE_EMPRESA."' , NUMERO_EXTERIOR_EMPRESA = '".$NUMERO_EXTERIOR_EMPRESA."' , NUMERO_INTERIOR_EMPRESA = '".$NUMERO_INTERIOR_EMPRESA."' , NUMERO_OFICINA_EMPRESA = '".$NUMERO_OFICINA_EMPRESA."' , COLONIA_EMPRESA = '".$COLONIA_EMPRESA."' , ALCALDIA_EMPRESA = '".$ALCALDIA_EMPRESA."' , C_EMPRESA = '".$C_EMPRESA."' , CIUDAD_EMPRESA = '".$CIUDAD_EMPRESA."' , ESTADO_EMPRESA = '".$ESTADO_EMPRESA."' , PAIS_EMPRESA = '".$PAIS_EMPRESA."' , UBICACION_MAPA_1 = '".$UBICACION_MAPA_1."' , TELEFONO_1_EMPRESA = '".$TELEFONO_1_EMPRESA."' , TELEFONO_2_EMPRESA = '".$TELEFONO_2_EMPRESA."' , WHATSAPEMPRESA_1 = '".$WHATSAPEMPRESA_1."' , IMAIL_EMPRESA = '".$IMAIL_EMPRESA."' , PAGINA_WEB_EMPRESA = '".$PAGINA_WEB_EMPRESA."' , NOMBRE_APEMPRESA = '".$NOMBRE_APEMPRESA."' where idRelacion = '".$session."' ; ";
		
		$var2 = "insert into 01DATOSFISCALESC  ( NOMBRE_COMERCIAL_EMPRESA, NOMBRE_FISCAL_RS_EMPRESA, RFC_MTDP, REGIMEN_FISCAL_MTDP, METODO_DE_PAGO, FORMADE_PAGO, USO_CFDI,FISICA_MORAL, DIRECCION_FISCAL_EMPRESA, EDIFICIO_EMPRESA, CALLE_EMPRESA, NUMERO_EXTERIOR_EMPRESA, NUMERO_INTERIOR_EMPRESA, NUMERO_OFICINA_EMPRESA, COLONIA_EMPRESA, ALCALDIA_EMPRESA, C_EMPRESA, CIUDAD_EMPRESA, ESTADO_EMPRESA, PAIS_EMPRESA,  UBICACION_MAPA_1, TELEFONO_1_EMPRESA, TELEFONO_2_EMPRESA, WHATSAPEMPRESA_1, IMAIL_EMPRESA, PAGINA_WEB_EMPRESA, NOMBRE_APEMPRESA,  idRelacion) values ( '".$NOMBRE_COMERCIAL_EMPRESA."' , '".$NOMBRE_FISCAL_RS_EMPRESA."' , '". $RFC_MTDP."' , '".$REGIMEN_FISCAL_MTDP."' , '".$METODO_DE_PAGO."' , '".$FORMADE_PAGO."' , '".$USO_CFDI."' , '".$FISICA_MORAL."' , '".$DIRECCION_FISCAL_EMPRESA."' , '".$EDIFICIO_EMPRESA."' , '".$CALLE_EMPRESA."' , '".$NUMERO_EXTERIOR_EMPRESA."' , '".$NUMERO_INTERIOR_EMPRESA."' , '".$NUMERO_OFICINA_EMPRESA."' , '".$COLONIA_EMPRESA."' , '".$ALCALDIA_EMPRESA."' , '".$C_EMPRESA."' , '".$CIUDAD_EMPRESA."' , '".$ESTADO_EMPRESA."' , '".$PAIS_EMPRESA."' , '".$UBICACION_MAPA_1."' , '".$TELEFONO_1_EMPRESA."' , '".$TELEFONO_2_EMPRESA."' , '".$WHATSAPEMPRESA_1."' , '".$IMAIL_EMPRESA."' , '".$PAGINA_WEB_EMPRESA."' , '".$NOMBRE_APEMPRESA."' , '".$session."' );  ";			

		//proveedor
		$QUERYPROVEEDOR25 = "select id from 02usuarios where IdColaborador = '".$session."'";
		$respuestaquery = mysqli_query($conn,$QUERYPROVEEDOR25) or die('P156'.mysqli_error($conn));
		$rowproveedor = mysqli_fetch_array($respuestaquery, MYSQLI_ASSOC);
		
		$var1proveedor = "update 02direccionproveedor1 set P_NOMBRE_COMERCIAL_EMPRESA = '".$NOMBRE_COMERCIAL_EMPRESA."' , P_NOMBRE_FISCAL_RS_EMPRESA = '".$NOMBRE_FISCAL_RS_EMPRESA."' ,P_RFC_MTDP = '".$RFC_MTDP."' , P_REGIMEN_FISCAL_MTDP = '".$REGIMEN_FISCAL_MTDP."' , _P_METODO_DE_PAGO = '".$_P_METODO_DE_PAGO."' , P_FORMADE_PAGO = '".$FORMADE_PAGO."' , P_USO_CFDI = '".$USO_CFDI."' , FISICA_MORAL = '".$FISICA_MORAL."' , P_DIRECCION_FISCAL_EMPRESA = '".$DIRECCION_FISCAL_EMPRESA."' , P_EDIFICIO_EMPRESA = '".$EDIFICIO_EMPRESA."' , P_CALLE_EMPRESA = '".$CALLE_EMPRESA."' , P_NUMERO_EXTERIOR_EMPRESA = '".$NUMERO_EXTERIOR_EMPRESA."' , P_NUMERO_INTERIOR_EMPRESA = '".$NUMERO_INTERIOR_EMPRESA."' , P_NUMERO_OFICINA_EMPRESA = '".$NUMERO_OFICINA_EMPRESA."' , P_COLONIA_EMPRESA = '".$COLONIA_EMPRESA."' , P_ALCALDIA_EMPRESA = '".$ALCALDIA_EMPRESA."' , P_C_P_EMPRESA = '".$C_P_EMPRESA."' , P_CIUDAD_EMPRESA = '".$CIUDAD_EMPRESA."' , P_ESTADO_EMPRESA = '".$ESTADO_EMPRESA."' , P_PAIS_EMPRESA = '".$PAIS_EMPRESA."' , dircasa11 = '".$dircasa11."' , P_UBICACION_MAPA_1 = '".$UBICACION_MAPA_1."' , P_TELEFONO_1_EMPRESA = '".$TELEFONO_1_EMPRESA."' , P_TELEFONO_2_EMPRESA = '".$TELEFONO_2_EMPRESA."' , P_WHATSAPP_EMPRESA_1 = '".$WHATSAPP_EMPRESA_1."' , P_IMAIL_EMPRESA = '".$IMAIL_EMPRESA."' , P_PAGINA_WEB_EMPRESA = '".$PAGINA_WEB_EMPRESA."' , P_NOMBRE_APP_EMPRESA = '".$NOMBRE_APP_EMPRESA."' where idRelacion = '".$rowproveedor['id']."' ; ";
		//FISICA_MORAL
		$var2proveedor = "insert into 02direccionproveedor1  ( 
		P_NOMBRE_COMERCIAL_EMPRESA, P_NOMBRE_FISCAL_RS_EMPRESA, P_RFC_MTDP,
		P_REGIMEN_FISCAL_MTDP, _P_METODO_DE_PAGO, P_FORMADE_PAGO, 
		P_USO_CFDI, FISICA_MORAL, P_DIRECCION_FISCAL_EMPRESA, 
		P_EDIFICIO_EMPRESA, P_CALLE_EMPRESA, P_NUMERO_EXTERIOR_EMPRESA, P_NUMERO_INTERIOR_EMPRESA, P_NUMERO_OFICINA_EMPRESA, P_COLONIA_EMPRESA, P_ALCALDIA_EMPRESA, P_C_P_EMPRESA, P_CIUDAD_EMPRESA, 
		P_ESTADO_EMPRESA, P_PAIS_EMPRESA, dircasa11, 
		P_UBICACION_MAPA_1, P_TELEFONO_1_EMPRESA, P_TELEFONO_2_EMPRESA,
		P_WHATSAPP_EMPRESA_1, P_IMAIL_EMPRESA, P_PAGINA_WEB_EMPRESA, P_NOMBRE_APP_EMPRESA, idRelacion) values ( 
		'".$NOMBRE_COMERCIAL_EMPRESA."' , '".$NOMBRE_FISCAL_RS_EMPRESA."' , '". $RFC_MTDP."' , 
		'".$REGIMEN_FISCAL_MTDP."' , '".$_P_METODO_DE_PAGO."' , '".$FORMADE_PAGO."' ,
		'".$USO_CFDI."' , '".$FISICA_MORAL."', '".$DIRECCION_FISCAL_EMPRESA."' , 
		'".$EDIFICIO_EMPRESA."' , '".$CALLE_EMPRESA."' , '".$NUMERO_EXTERIOR_EMPRESA."' , 
		'".$NUMERO_INTERIOR_EMPRESA."' , '".$NUMERO_OFICINA_EMPRESA."' , '".$COLONIA_EMPRESA."' , 
		'".$ALCALDIA_EMPRESA."' , '".$C_P_EMPRESA."' , '".$CIUDAD_EMPRESA."' , 
		'".$ESTADO_EMPRESA."' , '".$PAIS_EMPRESA."' , '".$dircasa11."' , 
		'".$UBICACION_MAPA_1."' , '".$TELEFONO_1_EMPRESA."' ,'".$TELEFONO_2_EMPRESA."' , 
		'".$WHATSAPP_EMPRESA_1."' , '".$IMAIL_EMPRESA."' ,'".$PAGINA_WEB_EMPRESA."' ,
		'".$NOMBRE_APP_EMPRESA."' , '".$rowproveedor['id']."' );  ";

		$var2proveedordelete = "delete from 02direccionproveedor1 where idRelacion = '".$rowproveedor['id']."' ";

		if($existe>=1){		

		mysqli_query($conn,$var1) or die('P156'.mysqli_error($conn));
		
		mysqli_query($conn,$var2proveedordelete) or die('P4222'.mysqli_error($conn));
		mysqli_query($conn,$var2proveedor) or die('P4223'.mysqli_error($conn));		
		
		return '<strong><span style="color:green; font-size:25px;">ACTUALIZADO </span></strong>';
		}else{
		mysqli_query($conn,$var2) or die('P160'.mysqli_error($conn));
		mysqli_query($conn,$var2proveedordelete) or die('P4229'.mysqli_error($conn));
		mysqli_query($conn,$var2proveedor) or die('P4230'.mysqli_error($conn));		
		return '<strong><span style="color:green; font-size:25px;">INGRESADO </span></strong>';
		}
		}else{
		echo "NO HAY UN COLABORADOR SELECCIONADO";	
		}
	}
	
	
	
	
	
	
	
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////






	
	
	public function variable_comborelacion1a(){
		$session = isset($_SESSION['id'])?$_SESSION['id']:'';		
		
		$conn = $this->db();				
		$variablequery = "select * from 02empresarelacion where idRelacionP = '".$session."' ";
		$arrayquery = mysqli_query($conn,$variablequery);
		$row = mysqli_fetch_array($arrayquery);
		if($row['idRelacionC']>=1){
		return $row['idRelacionC'];
		}else{
		return "no";			
		}
		
		}

		public function variables_informacionfiscal_logo(){
		$conn = $this->db();
		$variablequery = "select ADJUNTAR_LOGO_INFORMACION from 03docs_info_fiscal where idRelacion = '".$_SESSION['idlc']."' ";
		$arrayquery = mysqli_query($conn,$variablequery);
		$row = mysqli_fetch_array($arrayquery, MYSQLI_ASSOC);
		return $row['ADJUNTAR_LOGO_INFORMACION'];
		
	}


	public function paises(){
		$conn = $this->db();
		$query_paises = "select * from paises ";
		$preguntaQ = mysqli_query($conn,$query_paises) or die('p3676'.mysqli_error($conn));
		return $preguntaQ;
	}



		public function listado_inventario(){
		$conn = $this->db(); 
		$variablequery = "select * from 01inventario where  ver_inventario  = 1 order by id desc"; 
		return $arrayquery = mysqli_query($conn,$variablequery); 
		} 


		public function listado_inventarioTODOS(){
		$conn = $this->db(); 
		$variablequery = "select * from 01inventario order by id desc"; 
		return $arrayquery = mysqli_query($conn,$variablequery); 
		} 




	public function PASAR_VER_ACTUALIZAR ( $pasarVER_text , $pasarVER_id){
	
		$conn = $this->db();
		$session = isset($_SESSION['idem'])?$_SESSION['idem']:'';    
		if($session != ''){
			/*if($pasarpagado_text=='1'){
				$STATUS_DE_PAGO = 'PAGADO';
			}else{
				$STATUS_DE_PAGO = 'SOLICITADO';				
			}*/
		$var1 = "update 01inventario SET ver_inventario = '".$pasarVER_text."' WHERE id = '".$pasarVER_id."'  ";	
	
		//if($pasarpagado_text=='si'){
		mysqli_query($conn,$var1) or die('P156'.mysqli_error($conn));
		return "ACTUALIZADO";
		//}
			
        }else{
		echo "NO HAY UN PROVEEDOR SELECCIONADO";	
		}
    }
	
	
	public function guardar_checkbox($pasapersonal2_text,$pasara1_personal2_id){
		$conn = $this->db();
		$var_query = "update 01empresa set CHECKBOX = '".$pasapersonal2_text."' where id = '".$pasara1_personal2_id."' ";
		mysqli_query($conn,$var_query) or die('P156'.mysqli_error($conn));
		echo 'ACTUALIZADO: '.$pasapersonal2_text;
	}

	public function listado_links_generico($idrow,$tabla,$campo,$jqueryborrar){
		$conn = $this->db();
		$urlSUBIR_COTIZACION = '';
		$variablequery = "select id,".$campo." from ".$tabla." where idRelacion = '".$idrow."' order by id desc ";
		$arrayquery = mysqli_query($conn,$variablequery);
		while($rowDOCTOS = mysqli_fetch_array($arrayquery, MYSQLI_ASSOC))
		{
			if($rowDOCTOS[$campo]!=""){
			$urlSUBIR_COTIZACION .= "<a target='_blank'
            href='includes/archivos/".$rowDOCTOS[$campo]."'>Visualizar!</a>"." <span id='".$rowDOCTOS['id']."' class='".$jqueryborrar."' style='cursor:pointer;color:blue;'>Borrar!</span> ".'<br/>';
            }else{
            //$urlSUBIR_COTIZACION="";
            }
		}
		return $urlSUBIR_COTIZACION;
	}

	public function listado_links_generico_mysql($idrow,$tabla,$campo,$jqueryborrar){
		$conn = $this->db();
		$urlSUBIR_COTIZACION = '';
		$variablequery = "select id,".$campo." from ".$tabla." where idRelacion = '".$idrow."' order by id desc ";
		$arrayquery = mysqli_query($conn,$variablequery);
		/*while($rowDOCTOS = mysqli_fetch_array($arrayquery, MYSQLI_ASSOC))
		{
			if($rowDOCTOS[$campo]!=""){
			$urlSUBIR_COTIZACION .= "<a target='_blank'
            href='includes/archivos/".$rowDOCTOS[$campo]."'>Visualizar!</a>"." <span id='".$rowDOCTOS['id']."' class='".$jqueryborrar."' style='cursor:pointer;color:blue;'>Borrar!</span> ".'<br/>';
            }else{
            //$urlSUBIR_COTIZACION="";
            }
		}*/
		return $arrayquery;
	}

}



?>
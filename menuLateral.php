<link rel="shortcut icon" href="includes/captcha/images/epc.ico" />
<?php
require_once "class.epcinn.php";
$conexionmenulateral = new colaboradores();

$id = isset($_GET['id']) ? $_GET['id'] : '';

if ($id == '')
{
    $id = isset($_SESSION['id']) ? $_SESSION['id'] : '';
}

?>

	<div class="sidebar-header">
		<div id="actualizalogo">
			<img src="<?php echo $conexionmenulateral->revisar_01empresapertenece3($id); ?>"  alt="logo icon" width="150px;">
		</div>
		<div class="toggle-icon ms-auto"><ion-icon name="menu-sharp"></ion-icon></div>
	</div>
        <!--navigation-->
		
		
<ul class="metismenu" id="menu">
		
          <li>
            <a href="javascript:;" class="has-arrow">
              <div class="parent-icon"><ion-icon name="home-sharp"></ion-icon>
              </div>
              <div class="menu-title">MENU CRM <br>
    <span style="letter-spacing:7px; color:black;

">................</span>
			  </div>  
            </a>

          </li>

        


<!--INICIA INFORMACION-->
	<?php if ($conexionmenulateral->menulateral('INFORMACION') > 0)
{ ?>
	<li>
		<a href="javascript:;" class="has-arrow">
			<div class="parent-icon"><img src="iconos/infoin.png"></div>
			<div  class="menu-title">INFORMACIÓN IMPORTANTE</div>
		</a>
		<ul>
		
			<li>
				<a href="INFORMACION_IMPORTANTE.php"><ion-icon name="logo-ionic" ></ion-icon>INFORMACIÓN <br>IMPORTANTE</a>              
			</li>
			
			<?php if ($conexionmenulateral->variablespermisos('','DOCU_VARIOS','ver') == 'si')
    { ?>
						<li>
				<a href="DOCUMENTOS_VARIOS.php"><ion-icon name="logo-ionic" ></ion-icon>DOCUMENTOS VARIOS</a>              
			</li>
			<?php
    } ?>
			<?php if ($conexionmenulateral->variablespermisos('', 'NOTAS', 'ver') == 'si')
    { ?>
						<li>
				<a href="NOTAS_PRO.php"><ion-icon name="logo-ionic" ></ion-icon>INFORMACIÓN PARA<br> PROVEEDORES</a>              
			</li>
				<?php
    } ?>
				

		</ul>
	</li>
		<?php
} ?>
	

	<?php if ($conexionmenulateral->menulateral('INCIDENCIAS') > 0)
{ ?>
		<li>
		<a href="javascript:;" class="has-arrow">
			<div class="parent-icon"><img src="iconos/incidencias.png"></div>
			<div  class="menu-title">INCIDENCIAS</div>
		</a>
		<ul>
			<li>
				<a href="incidencias_rep.php"><ion-icon name="logo-ionic" ></ion-icon>INCIDENCIAS</a>              
			</li>
		</ul>
	</li>
	
	<?php
} ?>
	

	
	
		<?php if ($conexionmenulateral->variablespermisos('', 'DIARIO_COLABORADORES', 'ver') == 'si')
{ ?>
	<li>
		<a href="javascript:;" class="has-arrow">
			<div class="parent-icon"><img src="iconos/buzon.png"></div>
			<div  class="menu-title">DIARIO DE COLABORADORES</div>
		</a>
		<ul>
			<?php if ($conexionmenulateral->variablespermisos('', 'DIARIO_CREAEQUIPO', 'ver') == 'si')
    { ?>
					<li>
				<a href="DIARIO_SOLICITUD.php"><ion-icon name="logo-ionic" ></ion-icon>CREA UN EQUIPO</a>              
			</li>
			<?php
    } ?>

  
								<li>
				<a href="DIARIO_COLABORADOR.php"><ion-icon name="logo-ionic" ></ion-icon>BUZÓN COLABORADOR</a>              
			</li>
	
			
			
		</ul>
	</li>
	<?php
} ?>
<!--INICIA EVENTOS revisar-->
<!--INICIA EVENTOS revisar-->
	<?php if ($conexionmenulateral->variablespermisos('', 'MODULO_EVENTOS', 'ver') == 'si')
{ ?>
	<li>
		<a href="javascript:;" class="has-arrow">
			<div class="parent-icon">
				<img src="iconos/eventos.png">
			</div>
 

			<div class="menu-title">EVENTOS</div>
		</a>
		<ul>
	<?php if ($conexionmenulateral->variablespermisos('', 'ALTA_EVENTOSF', 'ver') == 'si')
    { ?>
			<li>
				<a href="listadeeventos.php"><ion-icon name="logo-ionic" ></ion-icon>ALTA DE EVENTOS </a>
			</li>
	<?php
    } ?>
		</ul>
	</li>
	<?php
} ?> 


<?php if ($conexionmenulateral->menulateralicono('CALENDARIO_ICONO') > 0)
{ ?>
	<li>
		<a href="javascript:;" class="has-arrow">
			<div class="parent-icon">
				<img src="iconos/calendario2.png"></div>
			<div class="menu-title">CALENDARIOS</div>
		</a>
		
		<ul>
	<?php if ($conexionmenulateral->variablespermisos('', 'MODULO_CALENDARIO', 'ver') == 'si')
    { ?>
			<li>
				<a href="calendarioDEeventos.php"><ion-icon name="logo-ionic" ></ion-icon>CALENDARIO DE<br> EVENTOS</a> 
			</li>
	<?php
    } ?>

		<?php if ($conexionmenulateral->variablespermisos('', 'calendario_orden', 'ver') == 'si')
    { ?>
			<li>
				<a href="calendario_materiales.php"><ion-icon name="logo-ionic" ></ion-icon>CALENDARIO DE <br>ORDEN DE PRODUCCIÓN</a> 
			</li>
       <?php
    } ?>
	   		<?php if ($conexionmenulateral->variablespermisos('', 'CALE_MENSAJERIA', 'ver') == 'si')
    { ?>
	   			<li>
				<a href="calendario_mensajeria.php"><ion-icon name="logo-ionic" ></ion-icon>CALENDARIO DE <br>MENSAJERÍA</a> 
			</li>
			   <?php
    } ?>
				   <?php if ($conexionmenulateral->variablespermisos('', 'COLACOR', 'ver') == 'si')
    { ?>
			<li>
				<a href="calendarioDcoordinacion.php"><ion-icon name="logo-ionic" ></ion-icon>CALENDARIO DE<br>COORDINACIÓN</a> 
			</li>
			<?php
    } ?>
			   <?php if ($conexionmenulateral->variablespermisos('', 'CALE_COLABORADOR', 'ver') == 'si')
    { ?>
			<li>
				<a href="calendario_personal.php"><ion-icon name="logo-ionic" ></ion-icon>CALENDARIO DE<br> COLABORADORES</a> 
			</li>
			<?php
    } ?>
<?php if ($conexionmenulateral->variablespermisos('', 'CALE_VEHICULOS', 'ver') == 'si') { ?>
    <li>
        <a href="CALENDARIO_VEHICULOS.php" target="_blank">
            <ion-icon name="logo-ionic"></ion-icon>CALENDARIO DE <br>VEHÍCULOS
        </a>
    </li>
<?php } ?>
			<?php if ($conexionmenulateral->variablespermisos('', 'CALE_VERIFI', 'ver') == 'si')
    { ?>
			<li>
			<a href="inicio.php#2"><ion-icon name="logo-ionic" ></ion-icon>CALENDARIO DE <br>VERIFICACIÓN DE <br>VEHÍCULOS</a> 
			</li>
			<?php
    } ?>
			<?php if ($conexionmenulateral->variablespermisos('', 'CALE_TARJETA', 'ver') == 'si')
    { ?>
			<li>
			<a href="inicio.php#3"><ion-icon name="logo-ionic" ></ion-icon>CALENDARIO DE<br> TARJETAS DE <br>CIRCULACIÓN</a> 
			</li>
			<?php
    } ?>
			<?php if ($conexionmenulateral->variablespermisos('', 'CALE_CUMPLEAÑOS', 'ver') == 'si')
    { ?>
			<li>
			<a href="inicio.php#4"><ion-icon name="logo-ionic" ></ion-icon>CALENDARIO DE<br> CUMPLEAÑOS</a> 
			</li>
				<?php
    } ?>
			</li>
			<?php if ($conexionmenulateral->variablespermisos('', 'CALE_CONVENIOS', 'ver') == 'si')
    { ?>
			<li>
			<a href="inicio.php#5"><ion-icon name="logo-ionic" ></ion-icon>CALENDARIO DE<br> CONVENIOS</a> 
			</li>
			<?php
    } ?>
			<?php if ($conexionmenulateral->variablespermisos('', 'CALE_MANTENIMIENTO', 'ver') == 'si')
    { ?>
			<li>
			
			<a href="inicio.php#6"><ion-icon name="logo-ionic" ></ion-icon>CALENDARIO DE<br> MANTENIMIENTO Y <br>EQUIPO DE COMPUTO</a> 
			</li>
<?php
    } ?>			
	   
		</ul>
	</li>
	<?php
} ?>
	   



	   





<!--INICIA CALENDARIOS-->
	<?php if ($conexionmenulateral->variablespermisos('', 'MODULO_COMGAS', 'ver') == 'si')
{ ?>
	<li>
		<a href="javascript:;" class="has-arrow">
			<div class="parent-icon">
				<img src="iconos/comp.png">   
			</div>
			<div class="menu-title">COMPROBACIONES <br>DE GASTOS</div>
		</a>
			<ul>
	<?php if ($conexionmenulateral->variablespermisos('', 'COMPROBACION_MENUA', 'ver') == 'si')
    { ?>
		<li>
	
				<a href="comprobaciones_P.php"><ion-icon name="logo-ionic" ></ion-icon>COMPROBACIONES <br>DE GASTOS (ADMIN) </a>              
			</li>
					<?php
    } ?>
			<li>
				<?php if ($conexionmenulateral->variablespermisos('', 'COMPROBACION_MENUVYO', 'ver') == 'si')
    { ?>
				<a href="comprobacionesVYO.php"><ion-icon name="logo-ionic" ></ion-icon>COMPROBACIONES <br>DE GASTOS VYO </a>              
			</li>
				<?php
    } ?>
		</ul>
	</li>
	<?php
} ?>

      
<!--INICIA PROVEEDORES-->
		<?php if ($conexionmenulateral->menulateralicono('icono_proveedores') > 0)
{ ?>
	<li>
		<a href="javascript:;" class="has-arrow">
			<div class="parent-icon">
				<img src="iconos/proveedores.png">
			</div>
			<div class="menu-title">PROVEEDORES</div>
		</a>
		<ul>
<?php if ($conexionmenulateral->variablespermisos('', 'PAGOPRO_MENUA', 'ver') == 'si')
    { ?>
			<li>
				<a href="pagoproveedores2.php"><ion-icon name="logo-ionic" color="primary"></ion-icon>PAGO A PROVEEDORES-A</a>  
			</li>
	<?php
    } ?>
		
		
		<?php if ($conexionmenulateral->variablespermisos('', 'PAGOPRO_MENUVYO', 'ver') == 'si')
    { ?>
			<li>
				<a href="ventas_operaciones.php"><ion-icon name="logo-ionic" color="primary"></ion-icon>PAGO A PROVEEDORES-VYO</a>
			</li>  
				<?php
    } ?>
	

		
		
			<?php if ($conexionmenulateral->variablespermisos('', 'VIATICOS_MENUVYO', 'ver') == 'si')
    { ?>
			<li>
				<a href="viaticos.php"><ion-icon name="logo-ionic" color="primary"></ion-icon>VIÁTICOS</a>
			</li>  		
		<?php
    } ?>
			<?php if ($conexionmenulateral->variablespermisos('', 'REEMBOLSOS_MENUVYO', 'ver') == 'si')
    { ?>
			<li>
				<a href="reembolsos.php"><ion-icon name="logo-ionic" color="primary"></ion-icon>REEMBOLSOS</a>
			</li>  		
		<?php
    } ?>
           <?php if ($conexionmenulateral->variablespermisos('', 'PAGOCONDOS_MENU', 'ver') == 'si')
    { ?>
			<li>
				<a href="PAGOPROVEEDOR4.php"><ion-icon name="logo-ionic" color="primary"></ion-icon>PAGO PROVEEDOR CON<br> DOS O MÁS FACTURAS</a>
			</li>  		
		<?php
    } ?>

	
				
		<?php if ($conexionmenulateral->variablespermisos('', 'FORMULARIO_PROVEEDORES', 'ver') == 'si')
    { ?>
			<li> 
				<a href="PROVEEDORES.php"><ion-icon name="logo-ionic" color="primary"></ion-icon>FORMULARIO <br>PROVEEDORES</a> 
			</li>
		<?php
    } ?>
		<?php if ($conexionmenulateral->menulateral('subirfactura') > 0)
    { ?>
			<li> 
				<a href="subirfactura.php"><ion-icon name="logo-ionic" color="primary"></ion-icon>SUBE TUS FACTURAS</a>
			</li>			
		<?php
    } ?>	
	<?php if ($conexionmenulateral->variablespermisos('', 'FORMULARIO_SOLOPROVEEDORES', 'ver') == 'si')
    { ?>
			<li> 
				<a href="SOLOPROVEEDORES.php"><ion-icon name="logo-ionic" color="primary" ></ion-icon>FORMULARIO SOLO<br> PROVEEDORES</a>
			</li>
		<?php
    } ?> 

		<?php if ($conexionmenulateral->menulateral('listaproveedores') > 0)
    { ?>
			<li>
				<a href="listaproveedores.php"><ion-icon name="logo-ionic" color="primary"></ion-icon>LISTA DE PROVEEDORES</a>
			</li>
		<?php
    } ?>
		</ul>			
	</li>
	<?php
} ?>



<!--INICIA MODULO_CLIENTES-->
	<?php if ($conexionmenulateral->variablespermisos('', 'MODULO_CLIENTES', 'ver') == 'si')
{ ?>
	<li>
		<a href="javascript:;" class="has-arrow">
			<div class="parent-icon">
				<img src="iconos/clientes.png">
			</div>
			<div class="menu-title">CLIENTES</div>
		</a>
		<ul>    
		
			<li> 
				<a href="listadoclientes.php"><ion-icon name="logo-ionic" ></ion-icon>LISTADO DE CLIENTES</a>
			</li>			
			
		</ul>
	</li>	
	<?php
} ?>


<?php if ($conexionmenulateral->variablespermisos('', 'MODULO_FACTURACION', 'ver') == 'si')
{ ?>
	<li>
		<a href="javascript:;" class="has-arrow">
			<div class="parent-icon"><img src="iconos/factura.png"></div>
			<div  class="menu-title">FACTURACIÓN</div>
		</a>
		<ul>

	
	<?php if ($conexionmenulateral->variablespermisos('', 'MODULO_FACTURACION', 'ver') == 'si')
    { ?>
			<li>
				<a href="facturacion1.php"><ion-icon name="logo-ionic" ></ion-icon>FACTURACIÓN</a>              
			</li>
			<?php
    }?>

	
	<?php   if($conexionmenulateral->variablespermisos('','NUEVOIMPUESTO_FACTURACION','ver')=='si'){?>
			<li>
				<a href="productososervicios.php"><ion-icon name="logo-ionic"></ion-icon>IMPUESTOS Y PRODUCTOS<br> O SERVICIOS</a>              
			</li>
			<?php } ?>	
	

	<?php if ($conexionmenulateral->variablespermisos('', 'MODULO_FACTURACION', 'ver') == 'si'){ ?>
			<li>
				<a href="complemento_pagos.php"><ion-icon name="logo-ionic" ></ion-icon>COMPLEMENTOS DE PAGO</a>              
			</li>
	<?php }?>	

	
		</ul>
	</li>
	<?php
} ?>


<!--INICIA MODULO_COLABORADORES-->
	<?php if ($conexionmenulateral->menulateralicono('icono_colaborador') > 0)
{ ?>
	<li>
		<a href="javascript:;" class="has-arrow">
			<div class="parent-icon">
				<img src="iconos/user.png">
			</div>
			<div class="menu-title">COLABORADORES, COORDINADORES Y ANIMADORES</div>
		</a>
			
		<ul>
<?php if ($conexionmenulateral->variablespermisos('', 'LISTA_COLABOR', 'ver') == 'si')
    { ?>
			<li> 
				<a href="LISTACOLAB_CEL.php"><ion-icon name="logo-ionic" ></ion-icon>LISTA DE COLABORADORES</a>
			</li>
		<?php
    } ?> 
		<?php if ($conexionmenulateral->variablespermisos('', 'LISTA_COORDINA', 'ver') == 'si')
    { ?>
	<li> 
				<a href="LISTACOORD_CEL.php"><ion-icon name="logo-ionic" ></ion-icon>LISTA DE COORDINADORES Y ANIMADORES</a>
			</li>
<?php
    } ?> 			
	<?php if ($conexionmenulateral->menulateral('listacolaboradores') > 0)
    { ?>
			<li> 
				<a href="listacolaboradores.php"><ion-icon name="logo-ionic" ></ion-icon>DATOS DE COLABORADORES</a>
			</li>
	<?php
    } ?>                
	<?php if ($conexionmenulateral->menulateral('COLABORADORES11') > 0)
    { ?>
              <li> 
				<a href="colaboradores.php"><ion-icon name="logo-ionic" ></ion-icon>FORMULARIO DE COLABORADORES</a>
              </li>
	<?php
    } ?>
	<?php if ($conexionmenulateral->menulateral('COLABORADORES22') > 0)
    { ?>
              <li> 
				<a href="solocolaboradores.php"><ion-icon name="logo-ionic" ></ion-icon> FORMULARIO SOLO COLABORADORES</a>
              </li>
	<?php
    } ?>
	<?php if ($conexionmenulateral->menulateral('COLABORADORES33') > 0)
    { ?>
              <li> 
				<a href="coordinadores.php"><ion-icon name="logo-ionic" ></ion-icon>CORDINADORES Y ANIMADORES</a>
              </li>
	<?php
    } ?>
	<?php if ($conexionmenulateral->menulateral('cargamasiva') > 0)
    { ?>
              <li> 
				<a href="cargamasiva.php"><ion-icon name="logo-ionic" ></ion-icon>CARGA MASIVA</a>	 
              </li>
	<?php
    } ?> 
		</ul>  
	</li>
	<?php
} ?> 
       	

<!--INICIA MODULO_TELEMARKETING-->
	<?php if ($conexionmenulateral->variablespermisos('', 'MODULO_TELEMARKETING', 'ver') == 'si')
{ ?>
  	<li>
		<a href="javascript:;" class="has-arrow">
			<div class="parent-icon">
				<img src="iconos/tele.png">
			</div>
			<div class="menu-title">TELEMARKETING</div>
		</a>
	</li>
	<?php
} ?>	
   	

<!--INICIA MODULO_VEHICULOS-->
    <?php if ($conexionmenulateral->menulateralicono('VEHICULOS_ICONO') > 0)
{ ?>
	<li>
		<a href="javascript:;" class="has-arrow">
			<div class="parent-icon">
				<img src="iconos/vehiculo.png">
			</div>
			<div class="menu-title">VEHÍCULOS</div>
</a>
		<ul>
			
		        <?php if ($conexionmenulateral->variablespermisos('', 'LISTA_VEHICULOS', 'ver') == 'si')
    { ?>
				<li> 
				<a href="listavehiculos.php"><ion-icon name="logo-ionic" ></ion-icon>LISTADO DE VEHÍCULOS</a>
			</li>
			<?php
    } ?>
			    <?php if ($conexionmenulateral->variablespermisos('', 'LISTA_VENTAVEHICULOS', 'ver') == 'si')
    { ?>
				<li> 
				<a href="listaventavehiculos.php"><ion-icon name="logo-ionic" ></ion-icon>LISTA DE VEHÍCULOS VENDIDOS</a>
			</li>
			<?php
    } ?>
		</ul>
	</li>
	<?php
} ?>	    



<!--INICIA MODULO_VEHICULOS-->
	<?php if ($conexionmenulateral->variablespermisos('', 'MODULO_MENSAJERIA', 'ver') == 'si')
{ ?>
	<li>
		<a href="javascript:;" class="has-arrow">
			<div class="parent-icon">
				<img src="iconos/mensajeria.png">
			</div>
			<div class="menu-title">MENSAJERIAS</div>  
		</a>
          		<ul>
				
			<li> 
				<a href="MENSAJERIA2.php"><ion-icon name="logo-ionic" ></ion-icon>MENSAJERÍAS</a>
			</li>
		</ul>     
	</li>
	<?php
} ?>	


<!--INICIA inventario-->
	<?php if ($conexionmenulateral->variablespermisos('', 'MODULO_SOLICITUD_EQUIPO', 'ver') == 'si')
{ ?>
	<li>
		<a href="javascript:;" class="has-arrow">
			<div class="parent-icon">
				<img src="iconos/inventariop.png">
			</div>
			<div class="menu-title">SOLICITUD DE MATERIALES Y EQUIPO</div>
		</a>
		<ul>
			<li> 
				<a href="inventario_general.php"><ion-icon name="logo-ionic" ></ion-icon>INVENTARIO GENERAL</a>
			</li>
		</ul>
	</li>
	<?php
} ?>			
		
         


<!--INICIA MODULO_VUELOS-->
	<?php if ($conexionmenulateral->variablespermisos('', 'MODULO_VUELOS', 'ver') == 'si')
{ ?>
	<li>
		<a href="javascript:;" class="has-arrow">
			<div class="parent-icon">
				<img src="iconos/vuelos.png">
			</div>
			<div class="menu-title">VUELOS</div>
		</a>
	</li>
	<?php
} ?>
		

<!--INICIA MODULO_REPORTES-->
	<?php if ($conexionmenulateral->variablespermisos('', 'MODULO_REPORTES', 'ver') == 'si')
{ ?>
	<li>
		<a href="javascript:;" class="has-arrow">
			<div class="parent-icon">
				<img src="iconos/reportes1.png">
			</div>
			<div class="menu-title">REPORTES</div>
		</a>
		
		<ul>
			<?php if ($conexionmenulateral->variablespermisos('', 'ALTAEVE_REPORTES', 'ver') == 'si')
    { ?>
					<li> 
				<a href="reporte_altaeventos.php"><ion-icon name="logo-ionic" ></ion-icon>ALTA DE EVENTOS</a>
			</li>
			<?php
    } ?>
			
			<?php if ($conexionmenulateral->variablespermisos('', 'CALENDARIO_REPORTES', 'ver') == 'si')
    { ?>
			<li> 
				<a href="reportes_calendario.php"><ion-icon name="logo-ionic" ></ion-icon>CALENDARIO EVENTOS</a>
			</li>
			<?php
    } ?>
	
				<?php if ($conexionmenulateral->variablespermisos('', 'BONOS_REPORTES', 'ver') == 'si')
    { ?>
			<li> 
				<a href="REPORTE_BONOS.php"><ion-icon name="logo-ionic" ></ion-icon>BONOS PERSONAL</a>
			</li>
			<?php
    } ?>
	
	
	
			<?php if ($conexionmenulateral->variablespermisos('', 'COMPROBACION_REPORTES', 'ver') == 'si')
    { ?>
			<li> 
				<a href="reportes_com.php"><ion-icon name="logo-ionic" ></ion-icon>COMPROBACIONES <br>DE GASTOS-A</a>
			</li>
           <?php
    } ?>
		 <?php if ($conexionmenulateral->variablespermisos('', 'COMPROBACIONVYO_REPORTES', 'ver') == 'si')
    { ?>
			<li> 
				<a href="reportes_comprobacionVYO.php"><ion-icon name="logo-ionic" ></ion-icon>COMPROBACIONES<br> DE GASTOS-VYO</a>
			</li>
           <?php
    } ?>
		   			<?php if ($conexionmenulateral->variablespermisos('', 'PAGOAP_REPORTES', 'ver') == 'si')
    { ?>
			<li> 
				<a href="reportes_pagoproveedores.php"><ion-icon name="logo-ionic" ></ion-icon>PAGO A PROVEEDORES-A</a>
			</li>
				<?php
    } ?>

		   
				<?php if ($conexionmenulateral->variablespermisos('', 'PAGOAPVYO_REPORTES', 'ver') == 'si')
    { ?>
			<li> 
				<a href="reporte_ventas_operaciones.php"><ion-icon name="logo-ionic" ></ion-icon>PAGO A PROVEEDORES-VYO</a>
			</li>
			
			
			<?php
    } ?>		   
		   
		   <?php if ($conexionmenulateral->variablespermisos('', 'FACTURACIONYCOBROS', 'ver') == 'si')
    { ?>
											<li> 
				<a href="calendarioDEeventos3.php"><ion-icon name="logo-ionic" ></ion-icon>FACTURACIÓN Y <br>COBROS DEL EVENTO</a>
			</li>
			 <?php
    } ?>
					   <?php if ($conexionmenulateral->variablespermisos('', 'MATCH_REPORTES', 'ver') == 'si')
    { ?>
			<li> <a class="has-arrow" href="javascript:;"><ion-icon name="logo-ionic" ></ion-icon>MATCH CON ESTADO<br> DE CUENTA</a>
			<ul>
            <li> 
				<a href="MATCHESTADO.php"><ion-icon name="logo-ionic" ></ion-icon>MATCH CON ESTADO<br> DE CUENTA</a>
			</li>	
              <?php if ($conexionmenulateral->variablespermisos('', 'MATCH_VERIFICADOS', 'ver') == 'si')
        { ?>
             <li> 
				<a href="MATCHESTADOTARJETAS.php"><ion-icon name="logo-ionic" ></ion-icon>MATCH ESTADO DE <br>CUENTAVERIFICADOS</a>
			</li>
             <?php
        } ?>			
			</ul>	
			 <?php
    } ?>	


			<?php if ($conexionmenulateral->variablespermisos('', 'TIPOCAMBIO_REPORTES', 'ver') == 'si')
    { ?>
			
	<li> 
				<a href="MONEDA.php"><ion-icon name="logo-ionic" ></ion-icon>TIPO DE CAMBIO</a>
			</li>
<?php
    } ?>
		
	</li>
	</ul>	
	<?php
} ?>
	
	
<!--INICIA MODULO_FACTURACION-->
<?php if ($conexionmenulateral->variablespermisos('', 'MODULO_TESORERIA', 'ver') == 'si')
{ ?>
	<li>
		<a href="javascript:;" class="has-arrow">
			<div class="parent-icon"><img src="iconos/tesoreria.png"></div>
			<div  class="menu-title">TESORERÍA</div>
		</a>
		<ul>
		<?php if ($conexionmenulateral->variablespermisos('', 'LISTA_TESORERIA', 'ver') == 'si')
    { ?>
					<li>
				<a href="listaempresatesoreria.php"><ion-icon name="logo-ionic" ></ion-icon>ESTADOS DE CUENTA</a>              
			</li>
			<?php
    } ?>
	


	
		</ul>
	</li>
	<?php
} ?>
	
	
	
	
	

<!--INICIA MODULO_LANDING_EVENTO-->
	<?php if ($conexionmenulateral->variablespermisos('', 'MODULO_LANDING_EVENTO', 'ver') == 'si')
{ ?>
	<li>
		<a href="javascript:;" class="has-arrow">
			<div class="parent-icon">
				<img src="iconos/landing.png">
			</div>
			<div class="menu-title">LANDING PAGE POR EVENTO</div>
		</a>
	</li>
	<?php
} ?>



<!--INICIA MODULO_LANDING_EVENTO-->
	<?php if ($conexionmenulateral->menulateral('datosEPC') > 0)
{ ?>
	<li>
		<a href="javascript:;" class="has-arrow">
			<div class="parent-icon">
				<img src="iconos/empresa.png">
			</div>
			<div class="menu-title">EMPRESAS DEL CORPORATIVO</div>
		</a>
		<ul>
			<li>
				<a href="listadeempresas.php"><ion-icon name="logo-ionic" ></ion-icon>LISTADO DE EMPRESAS </a>
			</li>
		</ul>
	</li>
	<?php
} ?>


<?php if ($conexionmenulateral->menulateral('SISTEMAS') > 0)
{ ?>
	<li>
		<a href="javascript:;" class="has-arrow">
			<div class="parent-icon">
				<img src="iconos/sistemas.png"></div>
			<div class="menu-title">SISTEMAS</div>
		</a>
		<ul>

			<li>
				<a href="CONTRASENASS.php"><ion-icon name="logo-ionic" ></ion-icon>CONTRASEÑAS </a> 
			</li>
	
		</ul>
	</li>

<?php
} ?>



<!--INICIA PERMISOS-->
<?php if ($conexionmenulateral->variablespermisos('', 'MODULO_CONFIGURACION', 'ver') == 'si')
{ ?>
	<li>
		<a href="javascript:;" class="has-arrow">
			<div class="parent-icon">
				<img src="iconos/configuracion.png">
			</div>
			<div class="menu-title">CONFIGURACIÓN</div>
		</a>
			<ul>
				<?php if ($conexionmenulateral->variablespermisos('', 'PERMISOS1', 'ver') == 'si')
    { ?>
				<li> 
					<a href="PERMISOS.php"><ion-icon name="logo-ionic" ></ion-icon>PERMISOS </a>
				</li>
					<?php
    } ?>
					
					<?php if ($conexionmenulateral->variablespermisos('', 'DESPLEGABLES', 'ver') == 'si')
    { ?>
				<li> 
					<a href="desplegables.php"><ion-icon name="logo-ionic" ></ion-icon>DESPLEGABLES </a>
				</li>
				<?php
    } ?>
				<?php if ($conexionmenulateral->variablespermisos('', 'FILTROS', 'ver') == 'si')
    { ?>              
				
                  <li> <a class="has-arrow" href="javascript:;"><ion-icon name="logo-ionic" ></ion-icon>PLANTILLAS</a>
                    <ul>
					<?php if ($conexionmenulateral->variablespermisos('', 'FILTROS_ALTAEVENTOS', 'ver') == 'si')
        { ?>
					 <li> <a href="filtro_eventos2.php"><ion-icon name="logo-ionic" color="primary"></ion-icon>ALTA DE EVENTOS</a>
                      </li>
					  <?php
        } ?>
					  <?php if ($conexionmenulateral->variablespermisos('', 'FILTROS_CALENDARIO', 'ver') == 'si')
        { ?>
					    <li> <a href="filtro_calendario.php"><ion-icon name="logo-ionic" color="primary"></ion-icon>CALENDARIO DE <br>EVENTOS</a>
                      </li>
					    <?php
        } ?>

					   <?php if ($conexionmenulateral->variablespermisos('', 'FILTROS_COMPROBACIONES', 'ver') == 'si')
        { ?>
                      <li> <a href="filtro_comprobaciones.php"><ion-icon name="logo-ionic" color="primary"></ion-icon>COMPROBACIONES DE<br> GASTOS</a>
                      </li>
					   <?php
        } ?>
					     <?php if ($conexionmenulateral->variablespermisos('', 'FILTROS_COMPROBACIONESVYO', 'ver') == 'si') 

        { ?>
					    <li > <a href="filtro_comprobacionesVYO.php"><ion-icon name="logo-ionic" color="primary"></ion-icon>COMPROBACIONES DE <br>GASTOS-VYO</a>
                      </li>
					  <?php
        } ?>
		   <?php if ($conexionmenulateral->variablespermisos('', 'FILTROS_PAGOA', 'ver') == 'si')
        { ?>
					   <li> <a href="filtro_pagoproveedores.php"><ion-icon name="logo-ionic" color="primary"></ion-icon>PAGO PROVEEDORES-A</a>
                      </li>
					  <?php
        } ?>
					    <?php if ($conexionmenulateral->variablespermisos('', 'FILTROS_PAGOPROVEEDORVYO', 'ver') == 'si')
        { ?>
					    <li> <a href="filtro_ventasoperaciones.php"><ion-icon name="logo-ionic" color="primary"></ion-icon>PAGO PROVEEDORES-VYO</a>
                      </li>
					  <?php
        } ?>
<?php if ($conexionmenulateral->variablespermisos('', 'FILTROS_LISTAPROVEE', 'ver') == 'si')
        { ?>
					  <li> <a href="filtro_listaPROVEEDORES.php"><ion-icon name="logo-ionic" color="primary"></ion-icon>LISTA DE PROVEEDORES</a> 
                      </li>
					  <?php
        } ?>
		
		<?php if ($conexionmenulateral->variablespermisos('', 'FILTROS_TARJETA', 'ver') == 'si')
        { ?>
					  <li> <a href="filtro_TARJETA.php"><ion-icon name="logo-ionic" color="primary"></ion-icon>TARJETA EMPRESARIAL <br>COLABORADORES</a> 
                      </li>
					  <?php
        } ?>
                      
                       <?php if ($conexionmenulateral->variablespermisos('', 'FILTROS_CONTRASENASSS', 'ver') == 'si')
        { ?>
                      <li> <a href="filtro_CONTRASENAS1.php"><ion-icon name="logo-ionic" color="primary"></ion-icon>CONTRASEÑAS</a>
                      </li>
					  <?php
        } ?>
					    <?php if ($conexionmenulateral->variablespermisos('', 'FILTROS_MENSAJERIASSS', 'ver') == 'si')
        { ?>
					      <li> <a href="filtro_MENSAJERIAf.php"><ion-icon name="logo-ionic" color="primary"></ion-icon>MENSAJERÍA</a>
                      </li>
					   <?php
        } ?>
                    </ul>
					<?php
    } ?>
                  </li>

				</li>				
			</ul>
	</li>
<?php
} ?>



	<!-- <li> 
		<a href="ayudas.php">
			<div class="parent-icon">
				<img src="iconos/videos.png">
			</div>
			<div class="menu-title">AYUDAS Y VIDEOS</div>
		</a>
	</li>-->


	<li>
		<a href="index.php?salir=1">
			<div class="parent-icon">
				<img src="iconos/salir.png">
			</div>
			<div class="menu-title">SALIR</div>
		</a>
	</li>
	<li>
		<a >.
			<div class="parent-icon">
		
			</div>
			<div class="menu-title">CRM- VERSIÓN 1.2</div>
		</a>
	</li>

</ul>
        <!--end navigation-->

<!DOCTYPE html>

<body>
     <?php use_stylesheet('social-card.css') ?>

<?php echo image_tag("logo-kimkelen-negro.png", array('width' => 240, 'height' => 70, 'absolute' => true)); ?>
    
<?php echo sfConfig::get('app_base')?>     
    <h1 style="text-align:center;font-size:400%" >Ficha Social Inicial</h1>
    <pre style=" margin-left: 100px;"> Sres Padres: para conocer las características y atender mejor a las necesidades de su hijo, necesitamos claridad         
    y rápidez en la devolución de la información.
                                                                                                <i>Departamento de Orientación Educativa</i>
     </pre>
 
  <pre>
                                                                                                                                                  Fecha: <?php echo date('Y-m-d');?>     

          <b>Nombres y apellido del alumno</b>: <?php echo $student ?>    <b>Sexo</b>: <?php echo BaseCustomOptionsHolder::getInstance('SexType')->getStringFor($student->getPerson()->getSex()); ?>   <b>Año que cursa</b>: <?php echo $student->getCurrentCourseYear() ?>
 
          <b>Tipo de Documento:</b>   <?php echo BaseCustomOptionsHolder::getInstance('IdentificationType')->getStringFor($student->getPerson()->getIdentificationType());  ?>      <b>Nro. de Documento:</b>   <?php echo $student->getPerson()->getIdentificationNumber(); ?>      <b>Nro. de CUIL:</b> <?php echo (is_null($student->getPerson()->getCuil()) | $student->getPerson()->getCuil() == '') ? '.....................' : $student->getPerson()->getCuil()?>

          <b>Fecha Nac.: </b> <?php echo (is_null($student->getPersonFormattedBirthDate()) | $student->getPersonFormattedBirthDate() == '') ? '.................' : $student->getPersonFormattedBirthDate();?>   <b>Lugar de Nac.: </b> <?php echo (is_null($student->getPerson()->getBirthCity()) | $student->getPerson()->getBirthCity() == '') ? '' : $student->getPerson()->getBirthCityRepresentation() .','; ?>
 <?php echo (is_null($student->getPerson()->getBirthState()) | $student->getPerson()->getBirthState() == '') ? '' : $student->getPerson()->getBirthStateRepresentation() . ', '; ?>
 <?php echo (is_null($student->getPerson()->getBirthCountry()) | $student->getPerson()->getBirthCountry() == '') ? '' : $student->getPerson()->getBirthCountryRepresentation(); ?>
 
          <b>Domicilio:</b> <?php echo (is_null($student->getPerson()->getAddress()) | $student->getPerson()->getAddress() == '') ? '.....................................................................' : $student->getPerson()->getAddress(); ?>     <b>Teléfono:</b>  <?php echo (is_null($student->getPersonPhone()) |  $student->getPersonPhone() == '') ? '..........................' : $student->getPersonPhone() ?>
         
          
          Establecimientos educativos de procendencia:.............................................................................................................

</pre>

	<p style="font-weight:bold; font-size:30px;"> A- DATOS FAMILIARES </p>

 <pre>
		PADRE

            Apellido y Nombre:........................................................................................................................................................

            Fecha de nacimiento:....................................................................................................................................................

            Nacionalidad:  <?php foreach($options_nationality as $n): ?> <input type="checkbox"><span style="font-size:25px"> <?php echo  $n;?></span><?php  endforeach?>
            

            Nivel educativo:                                                                                                                                             
							 <table style="margin-left:80px;" >
								<?php for($i = 0 ; $i < count($options_study) ; $i++){ ?> 
										<tr style="text-align: left;">
											<td style="font-size:25px"><?php echo ($options_study[$i] == "")? "":'<input type="checkbox"> '. $options_study[$i] ;$i++;?></td> 
											<td style="font-size:25px"><?php echo ($options_study[$i] == "")? "":'<input type="checkbox"> '. $options_study[$i] ;?></td> 
										</tr>              
								<?php }?>
                               </table> 
            Ocupación:                                                                                                                                                       
			<table style="margin-left:80px;" >
				<?php for($i = 0 ; $i < count($options_occupation);$i++){ ?>
					<tr style="text-align: left;">
						<td style="font-size:24px"> <?php echo ($options_occupation[$i] == "")? "":'<input type="checkbox"> '. $options_occupation[$i] ; $i++?></td> 
						<td style="font-size:24px"><?php echo ($options_occupation[$i] == "")? "":'<input type="checkbox"> '. $options_occupation[$i]; $i++?> </td> 
						<td style="font-size:24px">  <?php echo ($options_occupation[$i] == "")? "":'<input type="checkbox"> '. $options_occupation[$i] ?> </td>  
					</tr> 
				<?php }?>
			</table>
            
            Vive con el niño (1):  <input type="checkbox"> SI <input type="checkbox"> NO                                                                                          

            Horario de trabajo:.........................................................................................................................................................

            Domicilio:.......................................................................................................................................................................
            
            Provincia:.......................................................................................................................................................................
            
            Ciudad:..........................................................................................................................................................................
            
            Teléfonos:                                                                                                                                             
                                                                                                                                                                             
                      Fijo:......................................................................................................................................................................
                      
                      Celular:................................................................................................................................................................
            
            Email:.............................................................................................................................................................................
		
		
		MADRE	

            Apellido y Nombre:........................................................................................................................................................

            Fecha de nacimiento:....................................................................................................................................................

            Nacionalidad:  <?php foreach($options_nationality as $n): ?> <input type="checkbox"><span style="font-size:25px"> <?php echo  $n;?></span><?php  endforeach?>
            
            
            Nivel educativo:                                                                                                                                                       
            <br></br> 
            
                                                                                                                                   
							  <table style="margin-left:80px;" >
								<?php for($i = 0 ; $i < count($options_study) ; $i++){ ?> 
										<tr style="text-align: left;">
											<td style="font-size:25px"><?php echo ($options_study[$i] == "")? "":'<input type="checkbox"> '. $options_study[$i] ;$i++;?></td> 
											<td style="font-size:25px"><?php echo ($options_study[$i] == "")? "":'<input type="checkbox"> '. $options_study[$i] ;?></td> 
										</tr>              
								<?php }?>
                               </table>                                   

            Ocupación:                                                                                                                                                               
            <table style="margin-left:80px;" >
				<?php for($i = 0 ; $i < count($options_occupation);$i++){ ?>
					<tr style="text-align: left;">
						<td style="font-size:24px"><?php echo ($options_occupation[$i] == "")? "":'<input type="checkbox"> '. $options_occupation[$i]; $i++?></td> 
						<td style="font-size:24px"><?php echo ($options_occupation[$i] == "")? "":'<input type="checkbox"> '. $options_occupation[$i]; $i++?> </td> 
						<td style="font-size:24px"> <?php echo ($options_occupation[$i] == "")? "":'<input type="checkbox"> '. $options_occupation[$i] ?> </td>  
					</tr> 
				<?php }?>
			</table>
            
            Vive con el niño (1):  <input type="checkbox"> SI <input type="checkbox"> NO                                                                                                  

            Horario de trabajo:........................................................................................................................................................

            Domicilio:......................................................................................................................................................................
            
            Provincia:......................................................................................................................................................................
            
            Ciudad:.........................................................................................................................................................................
            
            Teléfonos:                                                                                                                                                                   
                                                                                                                                                                             
                      Fijo:.....................................................................................................................................................................
                      
                      Celular:...............................................................................................................................................................
            
            Email:............................................................................................................................................................................


            
     TUTOR

            Apellido y Nombre:.......................................................................................................................................................

            Fecha de nacimiento:...................................................................................................................................................

            Nacionalidad:  <?php foreach($options_nationality as $n): ?> <input type="checkbox"><span style="font-size:25px"> <?php echo  $n;?></span><?php  endforeach?>
            

            Nivel educativo:                                                                                                                                          
							<table style="margin-left:80px;" >
								<?php for($i = 0 ; $i < count($options_study) ; $i++){ ?> 
										<tr style="text-align: left;">
											<td style="font-size:25px"><?php echo ($options_study[$i] == "")? "":'<input type="checkbox"> '. $options_study[$i] ;$i++;?></td> 
											<td style="font-size:25px"><?php echo ($options_study[$i] == "")? "":'<input type="checkbox"> '. $options_study[$i] ;?></td> 
										</tr>              
								<?php }?>
                               </table>  
            Ocupación:                                                                                                                                                          
             <table style="margin-left:80px;" >
				<?php for($i = 0 ; $i < count($options_occupation);$i++){ ?>
					<tr style="text-align: left;">
						<td style="font-size:24px"><?php echo ($options_occupation[$i] == "")? "":'<input type="checkbox"> '. $options_occupation[$i] ;$i++;?></td> 
						<td style="font-size:24px"><?php echo ($options_occupation[$i] == "")? "":'<input type="checkbox"> '. $options_occupation[$i];$i++; ?></td> 
						<td style="font-size:24px"> <?php echo ($options_occupation[$i] == "")? "":'<input type="checkbox"> '. $options_occupation[$i] ?> </td>  
					</tr> 
				<?php }?>
			</table>
            
            Vive con el niño (1):  <input type="checkbox"> SI <input type="checkbox"> NO                                                                                                  

            Horario de trabajo:........................................................................................................................................................

            Domicilio:......................................................................................................................................................................
            
            Provincia:......................................................................................................................................................................
            
            Ciudad:.........................................................................................................................................................................
            
            Teléfonos:                                                                                                                                                                   
                                                                                                                                                                             
                      Fijo:.....................................................................................................................................................................
                      
 
 
 
                      Celular:...............................................................................................................................................................
            
            Email:............................................................................................................................................................................
		<p style="margin-left:80px;font-size:25px">
			(1) En caso de separación de los padres, especificarlo e indicar con quién vive el niño, régimen de visitas , etc. 
			Esta información consignela en OBSERVACIONES, al final de la hoja 4.
		</p>
		HERMANOS
		 <style type="text/css">
    .tg  {border-collapse:collapse;border-spacing:14;}
    .tg td{font-family:Arial, sans-serif;font-size:20px;padding:25px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;}
    .tg th{font-family:Arial, sans-serif;font-size:20px;font-weight:normal;padding:20px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;}
    </style>
    <table style="width:130%" class="tg" align="center">
    <colgroup>
		<col style="width: 100px">
        <col style="width: 500px">
        <col style="width: 100px">
        <col style="width: 600px">
        <col style="width: 600px">
        <col style="width: 800px">
        <col style="width: 800px">
    </colgroup>
      <tr>
		<th class="tg-031e"></th> 
        <th class="tg-031e">Apellido y Nombre </th>
        <th class="tg-031e">Fecha de Nac.</th>
        <th class="tg-031e">Nivel educativo (2)</th>
        <th class="tg-031e">Salud</th>
        <th class="tg-031e">Vive con el niño?</th>
        <th class="tg-031e">Otras ocupaciones (3)</th>
      </tr>
      <tr>
		<td class="tg-031e" rowspan="7">Hermanos</td>
        <td class="tg-031e"></td>
        <td class="tg-031e"></td>
        <td class="tg-031e"></td>
        <td class="tg-031e"></td>
        <td class="tg-031e"></td>
        <td class="tg-031e"></td>
      </tr>
      <tr>
		<td class="tg-031e"></td>
        <td class="tg-031e"></td>
        <td class="tg-031e"></td>
        <td class="tg-031e"></td>
        <td class="tg-031e"></td>
        <td class="tg-031e"></td>
      </tr>
      <tr>
		<td class="tg-031e"></td>
        <td class="tg-031e"></td>
        <td class="tg-031e"></td>
        <td class="tg-031e"></td>
        <td class="tg-031e"></td>
        <td class="tg-031e"></td>
      </tr>
       <tr>
		<td class="tg-031e"></td>
        <td class="tg-031e"></td>
        <td class="tg-031e"></td>
        <td class="tg-031e"></td>
        <td class="tg-031e"></td>
        <td class="tg-031e"></td>
      </tr>
       <tr>
		<td class="tg-031e"></td>
        <td class="tg-031e"></td>
        <td class="tg-031e"></td>
        <td class="tg-031e"></td>
        <td class="tg-031e"></td>
        <td class="tg-031e"></td>
      </tr>
    </table>                                                                                                                                          
	</pre>
	
	
    <!--.............TABLA 2............... -->
		
   

   <p style="font-weight:bold; font-size:250%"> Otras Personas que viven en la misma casa : </p>
    <!--.............TABLA 3...............-->

    <table style="width:110%;" class="tg" align="center">
    <colgroup>
        <col style="width: 400px">
        <col style="width: 100px">
        <col style="width: 600px">
        <col style="width: 800px">
    </colgroup>
      <tr style="text-align:center">
        <th>Relación o parentesco</th>
        <th class="tg-4mn7">Fecha de Nac.</th>
        <th class="tg-4mn7">Ocupación (3)</th>
        <th class="tg-4mn7">Salud (Indicar si padece enfermedad crónica que requiere cuidados)</th>
      </tr>
      <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
      </tr>
      <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
      </tr>
      <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
      </tr>
    </table>
    
    <style type="text/css">
		table{margin-left:100px;}
		tr{text-align: left;}
		td{font-size:23px;}   
    </style>
    <pre style="font-size:23px">
          (2) Seleccione el número correspondiente al nivel educativo elegido.
		<table style="width:130%;">
			<?php $j= 1;for($i = 0 ; $i < count($options_study) ; $i++){ ?> 
			<tr>
				<td><?php echo $j ."- ". $options_study[$i] ; $i++;$j++?>  </td> 
				<td><?php echo $j ."- ". $options_study[$i] ; $i++;$j++?>  </td>
				<td><?php echo $j ."- ". $options_study[$i] ; $i++;$j++?>  </td>
				<td><?php echo $j ."- ". $options_study[$i];$j++?> </td> 
			</tr>              
			<?php }?>
        </table> 
      (3) Seleccione el número correspondiente a la ocupacion elegida.
		<table style="width:100%;">
			<?php $j= 1;for($i = 0 ; $i < count($options_occupation) ; $i++){ ?> 
			<tr>
				<td><?php echo $j ."- ". $options_occupation[$i] ; $i++;$j++?>  </td> 
				<td><?php echo $j ."- ". $options_occupation[$i] ; $i++;$j++?>  </td>
				<td><?php echo ($options_occupation[$i] == "")? "" :$j ."- ". $options_occupation[$i];$j++?> </td> 
			</tr>              
			<?php }?>
        </table> 
    </pre>
  
    

    <br></br>

    <!--SEGUNDA HOJA -->
      
    <p style="font-weight:bold; font-size:30px" > B- DATOS PERSONALES DEL ALUMNO  </p>
    
    <p style="text-align:justify">
        <pre>
            Enfermedades que padece actualmente......................................................................................................................

            Medicado?.................Operaciones.............................Accidentes................................................................................

            Prolemas sensoriales: auditivos.........................................visuales.............................................................................

            Problemas en el sueño..........Cuáles?...................................................................... Cuántas horas duerme?..........

            Comparte la habitación?......................Con quién?.....................................................................................................

            Recibió o recibe asistencia: Foniátrica .....................................................Psicológica ..............................................
            otra ...............................................................................................................................................................................

            Manifiesta miedos? ........................................................A qué?..................................................................................

            Tiene tics nerviosos? ..................Cuáles?...................................................................................................................

            Características de la conducta: Subrayar todas las características que describe la conducta de su hijo:
            Alegre- Inquieto- Obediente- Dócil- Tranquilo- Triste- Agresivo- Cariñoso- Nervioso- Consentido- Otras Causas


			<br></br>
			<br></br>



            USO DEL TIEMPO LIBRE:
            

            Con quién prefiere jugar?............................................................................................................................................

            Quién dirige el juego?.................................................................................................................................................
   
            Comparte sus juguetes/juegos? Dónde juega?........................................................................................................

            Qué actividades extraescolares realiza su hijo, fuera de las propuestas por la escuela?........................................

            ......................................................................................................................................................................................

            Quién se encarga del cuidado del niño en ausencia de sus padres? ......................................................................

            Número de horas que comparte diariamente con su hijo? ........................................................................................

            Padre: ...........................................................................................................................................................................

            Madre: ..........................................................................................................................................................................

            Para realizar las tareas escolares, necesita orientación?  .........................................................................................
            Quién lo orienta?  ........................................................................................................................................................

            En qué momento?  ......................................................................................................................................................

            Qué opinión tiene del desempeño escolar de su hijo/a?  ..........................................................................................

            Madre:  ..........................................................................................................................................................................

            Padre:  ..........................................................................................................................................................................

            OBSERVACIONES: .....................................................................................................................................................
            .......................................................................................................................................................................................
            .......................................................................................................................................................................................
        
      </pre>
  </p>

<!--TERMINADA SEGUNDA HOJA -->

</body>

</html>

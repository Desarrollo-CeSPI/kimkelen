<!DOCTYPE html>
<html>
<body>

<header>
	<?php use_stylesheet('print-social-card.css') ?>

	<?php echo image_tag("logo-kimkelen-negro.png", array('width' => 240, 'height' => 70, 'absolute' => true)); ?>

	<?php echo sfConfig::get('app_base')?>

	<h1> Ficha Social Inicial </h1>

	<div id="nota"> 
		<p> Sres Padres: para conocer las características y atender mejor a las necesidades de su hijo, necesitamos claridad y rápidez en la devolución de la información.</p>
		<p id="firma"> Departamento de Orientación Educativa. </p>
	</div>
</header>

<div id="datos-personales">
		<div id="fecha">
	  	<label> Fecha: </label> <?php echo date('Y-m-d');?>
		</div>
		<div>
			<label> Nombres y apellido del alumno: </label> <?php echo $student ?>    
			<label> Sexo: </label> <?php echo BaseCustomOptionsHolder::getInstance('SexType')->getStringFor($student->getPerson()->getSex()); ?>
		</div>
		<div>
			<label> Tipo de Documento: </label>  <?php echo BaseCustomOptionsHolder::getInstance('IdentificationType')->getStringFor($student->getPerson()->getIdentificationType());  ?>
			<label> Nro. de Documento: </label>   <?php echo $student->getPerson()->getIdentificationNumber(); ?> 
			<label> Nro. de CUIL: </label> <?php echo (is_null($student->getPerson()->getCuil()) | $student->getPerson()->getCuil() == '') ? '......................................' : $student->getPerson()->getCuil()?>
		</div>
		<div>
			<label> Fecha Nac.: </label> <?php echo (is_null($student->getPersonFormattedBirthDate()) | $student->getPersonFormattedBirthDate() == '') ? '....................................' : $student->getPersonFormattedBirthDate();?>   
			<label> Lugar de Nac.: </label> <?php echo (is_null($student->getPerson()->getBirthCity()) | $student->getPerson()->getBirthCity() == '') ? '........................ ,' : $student->getPerson()->getBirthCityRepresentation() .','; ?>
		  <?php echo (is_null($student->getPerson()->getBirthState()) | $student->getPerson()->getBirthState() == '') ? '....................... ,' : $student->getPerson()->getBirthStateRepresentation() . ', '; ?>
		  <?php echo (is_null($student->getPerson()->getBirthCountry()) | $student->getPerson()->getBirthCountry() == '') ? '.......................' : $student->getPerson()->getBirthCountryRepresentation(); ?>
		</div>
		<div>
			<label> Domicilio: </label> <?php echo (is_null($student->getPerson()->getAddress()) | $student->getPerson()->getAddress() == '') ? '......................................................................' : $student->getPerson()->getAddress(); ?>     
			<label> Teléfono: </label>  <?php echo (is_null($student->getPersonPhone()) |  $student->getPersonPhone() == '') ? '......................................................' : $student->getPersonPhone() ?>
		</div>
		<div>
			<label> Año que cursa: </label> <?php echo $student->getCurrentCourseYear() ?>
		</div>
		<div>
		 	<label> Establecimientos educativos de procendencia: </label>  <span>................................................................................</span>
		</div>
</div> <!--  fin de datos personales -->

<div id="datos-familiares">	
	<h2> A- DATOS FAMILIARES </h2>

	<?php include_partial("student/family_info", array("titles" => array("PADRE", "MADRE", "TUTOR"), "options_nationality" => $options_nationality, "options_study" => $options_study, "options_occupation" => $options_occupation)) ?>

	<div class="form-info">
		<h3> HERMANOS </h3>
	  
	  <table class="tabla">
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
			  <th></th>
			  <th>Apellido y Nombre </th>
			  <th>Fecha de Nac.</th>
			  <th>Nivel educativo (2)</th>
			  <th>Salud</th>
			  <th>Vive con el niño?</th>
			  <th>Otras ocupaciones (3)</th>
		  </tr>
		  <tr>
			  <td rowspan="7">Hermanos</td>
			  <td></td>
		   	<td></td>
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
			  <td></td>
			  <td></td>
		  </tr>
		  <tr>
			  <td></td>
			  <td></td>
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
			  <td></td>
			  <td></td>
		  </tr>
		  <tr>
			  <td></td>
			  <td></td>
			  <td></td>
			  <td></td>
			  <td></td>
			  <td></td>
		  </tr>
	  </table>
	</div> <!--  fin Tabla HERMANOS -->

	<div>
		<h3> Otras Personas que viven en la misma casa </h3>
		<table class="tabla">
			<colgroup>
				<col style="width: 400px">
				<col style="width: 100px">
				<col style="width: 600px">
				<col style="width: 800px">
			</colgroup>
			<tr>
				<th> Relación o parentesco </th>
				<th> Fecha de Nac. </th>
				<th> Ocupación (3) </th>
				<th> Salud (Indicar si padece enfermedad crónica que requiere cuidados) </th>
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
	</div> <!--  fin Tabla Otras Personas -->

	<div>
		<h3> (2) Seleccione el número correspondiente al nivel educativo elegido. </h3>
		<table>
			<?php $j= 1;for($i = 0 ; $i < count($options_study) ; $i++){ ?>
			<tr>
				<td><?php echo $j ."- ". $options_study[$i] ; $i++;$j++?>  </td>
				<td><?php echo $j ."- ". $options_study[$i] ; $i++;$j++?>  </td>
				<td><?php echo $j ."- ". $options_study[$i] ; $i++;$j++?>  </td>
				<td><?php echo $j ."- ". $options_study[$i];$j++?> </td>
			</tr>
			<?php }?>
		</table>
	</div>
	
	<div>
		<h3> (3) Seleccione el número correspondiente a la ocupacion elegida. </h3>
		<table>
			<?php $j= 1;for($i = 0 ; $i < count($options_occupation) ; $i++){ ?>
			<tr>
				<td><?php echo $j ."- ". $options_occupation[$i] ; $i++;$j++?>  </td>
				<td><?php echo ($options_occupation[$i] == "")? "" :$j ."- ". $options_occupation[$i];$j++?> </td>
			</tr>
			<?php }?>
		</table>
	</div> <!--  fin de numeracion de opciones -->

</div> <!--  fin de datos familiares -->

<div style="clear:both;"></div>
<div style="page-break-before: always;"></div>

<div id="datos-salud"> 
	<h2> B- DATOS PERSONALES DEL ALUMNO  </h2>
	<div>
		<div>
			<label> Enfermedades que padece actualmente: </label> <span>...........................................................................................</span>
	  </div>
	  <div>
	  	<label> Medicado? </label> <span>...............................</span>
	  	<label> Operaciones? </label> <span>.............................</span>
	  	<label> Accidentes? </label> <span>................................</span>
		</div>
		<div>
			<section>
				<label> Problemas sensoriales: </label> 
			</section>	
			<label> Auditivos? </label> <span>..............................................................</span>
			<label> Visuales? </label> <span>..............................................................</span>
	  </div>
	  <div>
	  	<label> Problemas en el sueño? </label> <span>..........</span>
	  	<label> Cuáles? </label> <span>......................................</span>
	  	<label> Cuántas horas duerme? </label> <span>..........</span>
	  </div>
		<div>
			<label> Comparte la habitación? </label> <span>..........</span>
			<label> Con quién? </label> <span>......................................................................................</span>
		</div>
		<div>
			<section>
				<label> Recibió o recibe asistencia: </label> 
			</section>
				<label> Foniátrica? </label> <span>....................</span>
				<label> Psicológica? </label> <span>....................</span>
	    	<label> Otra: </label> <span>.................................................................</span>
		</div>
		<div>
			<label> Manifiesta miedos? </label>  <span>....................</span>
			<label> A qué? </label> <span>.............................................................................................</span>
		</div>
		<div>
			<label> Tiene tics nerviosos? </label> <span>....................</span>
			<label> Cuáles? </label> <span>........................................................................................</span>
		</div>
		<div>
			<div>
				<label> Características de la conducta: </label> <span id="aclaracion"> (Subrayar todas las características que describe la conducta de su hijo.) </span>
	    </div>
	    <div>
	    	<p> Alegre - Inquieto - Obediente - Dócil - Tranquilo - Triste - Agresivo - Cariñoso - Nervioso - Consentido - Otras Causas </p>
			</div>
		</div>
	</div>   <!--  fin de datos de salud generales -->
	
	<div>
		<h3> USO DEL TIEMPO LIBRE: </h3>
		<div>
			<label> Con quién prefiere jugar? </label> <span>.....................................................................................................................</span>
		</div>
		<div>
			<label> Quién dirige el juego? </label> <span>...........................................................................................................................</span>
		</div>
		<div>
			<label> Comparte sus juguetes/juegos? Dónde juega? </label> <span>.................................................................................</span>
		</div>
		<div>
			<section>
				<label> Qué actividades extraescolares realiza su hijo, fuera de las propuestas por la escuela? </label>
			</section>
			<span>....................................................................................................................................................................</span>
		</div>
		<div>
			<section>
				<label> Quién se encarga del cuidado del niño en ausencia de sus padres? </label>
			</section>
			<span>....................................................................................................................................................................</span>
		</div>
		<div>
			<section>
				<label>Número de horas que comparte diariamente con su hijo?</label>
			</section>
				<label>Padre:</label> <span>...................................................................</span>
				<label>Madre:</label> <span>...................................................................</span>
		</div>
		<div>
			<section>
				<label> Para realizar las tareas escolares, necesita orientación? </label> <span>..............................................................</span>
			</section>	
				<label> Quién lo orienta? </label> <span>...............................................</span>      
				<label> En qué momento? </label> <span>................................................</span>
		</div>
		<div>
			<section>
				<label>Qué opinión tiene del desempeño escolar de su hijo/a?</label> 
			</section>
			<section>
				<label>Madre:</label> <span>.....................................................................................................................................................</span>
				<span>....................................................................................................................................................................</span>
				<span>....................................................................................................................................................................</span>
			</section>
			<section>
				<label>Padre:</label> <span>......................................................................................................................................................</span>
				<span>....................................................................................................................................................................</span>
				<span>....................................................................................................................................................................</span>
			</section>
		</div>
	</div> <!-- fin de tiempo libre -->
</div> <!--  fin de Datos Salud -->

<div id="observacion">
	<section>
		<label>OBSERVACIONES:</label>
	</section>
	<span>....................................................................................................................................................................</span>
	<span>....................................................................................................................................................................</span>
	<span>....................................................................................................................................................................</span>
	<span>....................................................................................................................................................................</span>
	<span>....................................................................................................................................................................</span>
</div> <!-- fin de observaciones -->

</body>

</html>

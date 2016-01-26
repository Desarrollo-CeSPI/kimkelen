
<?php foreach ($titles as $key => $title) : ?>
	
<?php if ($key == 0): ?>
	<div>
<?php else : ?>
	<div class="form-info">
<?php endif ?>

		<h3> <?php echo $title; ?> </h3>
		<div>
			<label> Apellido y Nombre: </label> <span>.............................................................................................................................</span>
		</div>
		<div>
			<label> Fecha de nacimiento: </label> <span>.........................................................................................................................</span>
		</div>
		<div>
			<label> Nacionalidad: </label> <?php foreach($options_nationality as $n): ?> <input type="checkbox"> <span> <?php echo  $n;?> </span> <?php  endforeach?>
		</div>
		<div class="seleccion">
			<label> Nivel educativo: </label>
				<table>
				<?php for($i = 0 ; $i < count($options_study) ; $i++){ ?>
					<tr>
						<td> <?php echo (!isset($options_study[$i]))? "":'<input type="checkbox"> '. $options_study[$i] ;$i++;?> </td>
						<td> <?php echo (!isset($options_study[$i]))? "":'<input type="checkbox"> '. $options_study[$i] ;$i++;?> </td>
						<td> <?php echo (!isset($options_study[$i]))? "":'<input type="checkbox"> '. $options_study[$i] ;?> </td>
					</tr>
				<?php } ?>
				</table>
  	</div>
  	<div class="seleccion">
  		<label> Ocupación: </label>
				<table>
				<?php for($i = 0 ; $i < count($options_occupation);$i++){ ?>
					<tr>
						<td> <?php echo (!isset($options_occupation[$i]))? "":'<input type="checkbox"> '. $options_occupation[$i] ; $i++?> </td>
						<td> <?php echo (!isset($options_occupation[$i]))? "":'<input type="checkbox"> '. $options_occupation[$i]; ?> </td>
					</tr>
				<?php } ?>
				</table>
		</div>
		<div>
  		<label> Vive con el niño (1): </label> <input type="checkbox"> SI <input type="checkbox"> NO
  	</div>
  	<div>
  		<label> Horario de trabajo: </label> <span>.............................................................................................................................</span>
		</div>
		<div>
  		<label> Domicilio: </label> <span>.............................................................................................................................................</span>
		</div>
		<div>
  		<label> Ciudad: </label> <span>..............................................................</span>
 			<label> Provincia: </label> <span>..............................................................</span>
 		</div>
 		<div id="tel">
    	<label> Tel. Fijo: </label> <span>................................................................</span>
    	<label> Celular: </label> <span>..............................................................</span>
		</div>
		<div>
 	 		<label> Email: </label> <span>...................................................................................................................................................</span>
 	 	</div>
 	</div>

<?php if ($key == (count($titles)-1)): ?>
 	<div>
		<p> (1) En caso de separación de los padres, especificarlo e indicar con quién vive el niño, régimen de visitas , etc. Esta información consignela en OBSERVACIONES, al final de la hoja 5. </p>
	</div>
<?php endif ?>  

  <div style="clear:both;"></div>
  <div style="page-break-before: always;"></div>

<?php endforeach; ?>
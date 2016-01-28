
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
		<div class="seleccion-3columnas">
			<label> Nivel educativo: </label>
			<ul>
			<?php foreach ($options_study as $op):?>
				<li> <input type="checkbox"> <span> <?php echo  $op;?> </span> </li> 
			<?php endforeach; ?>
			</ul>
  	</div>
  	<div class="seleccion-2columnas">
  		<label> Ocupación: </label>
			<ul>
			<?php foreach ($options_occupation as $op):?>
				<li> <input type="checkbox"> <span> <?php echo  $op;?> </span> </li> 
			<?php endforeach; ?>
			</ul>
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
 	 		<label> Email: </label> <span>....................................................................................................................................................</span>
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
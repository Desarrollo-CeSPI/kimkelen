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

         <b> Nombres y apellido del alumno</b>: <?php echo $student ?>       <b>Año que cursa</b>: <?php echo $student->getCurrentCourseYear() ?>

          <b>Domicilio:</b> <?php echo $student->getPersonAddress() ?>    <b>Teléfono:</b>......................................................<?php echo $student->getPersonPhone() ?>..... <b>Fecha nacimiento: </b>  <?php echo $student->getPersonFormattedBirthDate() ?>

          
          Establecimientos educativos de procendencia:.........................................................................................................

          Dirección:.....................................................................................................................................................................

          Teléfono:......................................................................................................................................................................
</pre>

    <p style="font-weight:bold; font-size:30px; margin-left: 50px"> A- DATOS FAMILIARES </p>

 <p style="font-size:30px">

     <pre>
    PADRE

            Nombre: .....................................................................................................................................................................

            Apellido: ....................................................................................................................................................................

            Edad:.........................................................................................................................................................................                                                                                                        

            Nivel de estudios:..............................................................................................................................................................

            Nacionalidad: ...........................................................................................................................................................

            Salud:........................................................................................................................................................................                                                        

            Ocupación:................................................................................................................................................................                                

            Vive con el niño:........................................................................................................................................................                              

            Horario de trabajo:.....................................................................................................................................................                                 


    MADRE

            Nombre: ....................................................................................................................................................................

            Apellido: ....................................................................................................................................................................

            Edad:.........................................................................................................................................................................                                                                                                        

            Nivel de estudios:..............................................................................................................................................................

            Nacionalidad: ...........................................................................................................................................................

            Salud:........................................................................................................................................................................                                                        

            Ocupación:................................................................................................................................................................                                

            Vive con el niño:........................................................................................................................................................                              

            Horario de trabajo:....................................................................................................................................................                                 





      </pre>
</p>


    <!--.............TABLA 2............... -->
    <br></br>


    <style type="text/css">
    .tg  {border-collapse:collapse;border-spacing:14;}
    .tg td{font-family:Arial, sans-serif;font-size:20px;padding:25px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;}
    .tg th{font-family:Arial, sans-serif;font-size:20px;font-weight:normal;padding:20px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;}
    </style>


    <table class="tg" style="undefined;table-layout: fixed; width: 130%; font-size:25; margin-left: 100px" cellpadding="0" cellspacing="0" align="center">
        <colgroup>
        <col style="width: 150px;height:100px">
        <col style="width: 170px;height:100px">
        <col style="width: 80px;height:100px">
        <col style="width: 400px;height:100px">
        <col style="width: 100px;height:100px">
        <col style="width: 100px;height:100px">
        <col style="width: 219px;height:100px">
        </colgroup>
          <tr>
            <th class="tg-031e"></th>
            <th class="tg-031e">Nombre y apellido</th>
            <th class="tg-031e">Edad</th>
            <th class="tg-031e">Escolaridad, establecimientos Educativos a los que asiste o asistió</th>
            <th class="tg-031e">Salud</th>
            <th class="tg-031e">Vive c/ el niño</th>
            <th class="tg-031e">Otras ocupaciones</th>
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


  
   <p style="font-weight:bold; font-size:250%"> Otras Personas que viven en la misma casa : </p>
    <!--.............TABLA 3...............-->

    <table class="tg" align="center">
    <colgroup>
        <col style="width: 400px">
        <col style="width: 100px">
        <col style="width: 600px">
        <col style="width: 800px">
    </colgroup>
      <tr>
        <th>Relación o parentesco</th>
        <th class="tg-4mn7">Edad</th>
        <th class="tg-4mn7">Ocupación</th>
        <th class="tg-4mn7">Salud (Indicar si padece enfermedad crónica que requiere cuidados</th>
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

    <br></br>

    <!--SEGUNDA HOJA -->
      
    <p style="font-weight:bold; font-size:30px" > B- DATOS PERSONALES DEL ALUMNO  </p>
    
    <p style="text-align:justify">
        <pre>
            Enfermedades que padece actualmente.................................................................................................................

            Medicado?.................Operaciones.............................Accidentes...........................................................................

            Prolemas sensoriales: auditivos.........................................visuales........................................................................

            Problemas en el sueño..........Cuáles?...................................................................... Cuántas horas duerme?.....

            Comparte la habitación?......................Con quién?................................................................................................


            USO DEL TIEMPO LIBRE:

            Con quién prefiere jugar?.........................................................................................................................................

            Quién dirige el juego?.............................................................................................................................................
   
            Comparte sus juguetes/juegos? Dónde juega?.....................................................................................................

            Qué actividades extraescolares realiza su hijo, fuera de las propuestas por la escuela?....................................

            Características de la conducta: Subrayar todas las características que describe la conducta de su hijo
            Alegre- Inquieto- Obediente- Dócil- Tranquilo- Triste- Agresivo- Cariñoso- Nervioso- Consentido- Otras

            Miedos? ........................................................A qué?..............................................................................................

            Tiene tics nerviosos? ...........................................
            Cuales?...................................................................................................................................................................

            Recibió o recibe asistencia: Foniatrica .....................................................Psicologica .........................................
            otra ..........................................................................................................................................................................







            Causas

            Quién se encarga del cuidado del niño en ausencia de sus padres? .................................................................

            Número de horas que comparte diariamente con su hijo? ..................................................................................

            Padre: .....................................................................................................................................................................

            Madre: ....................................................................................................................................................................

            Para realizar las tareas escolares, necesita orientación?  ...................................................................................
            Quién lo orienta?  ..................................................................................................................................................

            En qué momento?  ................................................................................................................................................

            Qué opinión tiene del desempeño escolar de su hijo/a?  ....................................................................................

            Madre:  ....................................................................................................................................................................

            Padre:  ....................................................................................................................................................................

            OBSERVACIONES: ...............................................................................................................................................
            .................................................................................................................................................................................
            .................................................................................................................................................................................
        
      </pre>
  </p>

<!--TERMINADA SEGUNDA HOJA -->

</body>

</html>

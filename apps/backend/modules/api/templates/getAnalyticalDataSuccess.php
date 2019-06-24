<?php use_helper('Date') ?>
<?php $i = 0;?>
<?php $analytical_array= array();?>
<?php foreach ($analytical->get_years_in_career() as $year): ?>
    <?php $subjects = $analytical->get_subjects_in_year($year); ?>
    <?php if (count($subjects) > 0):?>
    <?php foreach ($subjects as $css):?>
        <?php $condition =  $css->getCondition();?>
        <?php if(!is_null($condition)):?>
        <?php $condition = ($condition == 'Regular') ? "P" : "E"?>
        <?php endif;?>
        <?php $array = array(
                "titulo_araucano"=> $araucano_title_code,
                "titulo_nombre"=>  $career_student->getCareer()->getCareerName(),
                "responsable_academica"=>  $school->getAraucanoCode(),
                "propuesta"=> $career_student->getCareer()->getId(),
                "propuesta_nombre" =>  $career_student->getCareer()->getCareerName(),
                "plan_alumno" => $career_student->getCareer()->getPlanName(),
                "titulo_esta_cumplido" => ($analytical->has_completed_career())? "SI":"NO",
                "nro_resolucion_ministerial" =>  $career_student->getCareer()->getResolutionNumber(),
                "nro_resolucion_coneau" =>  NULL,
                "nro_resolución_institucion"=>  null,
                "fecha_ingreso"=>  $career_student->getAdmissionDate(),
                "fecha_egreso"=>  ($analytical->has_completed_career()) ? $analytical->get_last_exam_date()->format('d/m/Y'): NULL,
                "tiene_sanciones"=> (StudentDisciplinarySanctionPeer::countTotalValueForStudent($student) == 0) ? "N":"S",
                "titulo_anterior_nivel" =>  "Primario",
                "titulo_anterior_origen" => '',
                "titulo_anterior_nacionalidad"=> "",
                "titulo_anterior_institucion"=> "No Corresponde",
                "titulo_anterior_denominacion"=> "No Corresponde",
                "titulo_anterior_revalidado"=> "",
                "titulo_anterior_nro_resolucion"=> "",
                "titulo_apto_ejercicio"=> "NO",
                "plan_vigente"=> "",
                "tipo" => "",
                "actividad_nombre"=> $css->getSubjectName(),
                "actividad_codigo"=> "",
                "creditos"=> "",
                "fecha"=> $css->getApprovedDate() ? $css->getApprovedDate()->format('d/m/Y') : NULL,
                "nota"=> $css->getMark(),
                "resultado"=> ($css->getMark())?"Aprobado" : "Desaprobado",
                "folio_fisico"=> "",
                "acta_resolucion"=> "",
                "promedio"=> ($analytical->has_completed_career()) ? round($analytical->get_total_average(),2) : NULL ,
                "promedio_sin_aplazos"=> "",
                "forma_aprobacion"=> $condition
          );?>
        <?php $analytical_array[$i] = $array; $i++?>
      <?php endforeach; ?>
    <?php endif;?>
  <?php endforeach; ?>

<?php echo json_encode( $analytical_array
        )?>

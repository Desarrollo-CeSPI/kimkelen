<?php 
/*
 * Kimkëlen - School Management Software
 * Copyright (C) 2013 CeSPI - UNLP <desarrollo@cespi.unlp.edu.ar>
 *
 * This file is part of Kimkëlen.
 *
 * Kimkëlen is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License v2.0 as published by
 * the Free Software Foundation.
 *
 * Kimkëlen is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Kimkëlen.  If not, see <http://www.gnu.org/licenses/gpl-2.0.html>.
 */ ?>
<?php require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');?>

<?php
//
//SCRIPT TO CREATE THE STUDENT'S FIXTURE
//
?>
<?php $handle = fopen("./csv/students.csv", "r")?>
<?php $handle_f = fopen("./fixtures/00-student.yml","w+")?>
<?php if(($handle===FALSE)||($handle_f===FALSE)):?>
  <?php die("Error al abrir el archivo\n")?>
<?php endif?>
<?php 
$i = 0;
fwrite($handle_f,"Student:\n");
while (($data = fgetcsv($handle,4096,",")) !== FALSE):
  $i=$i+1;
  $birthdate = explode("/",$data[3]);
  $name = explode(",",$data[0]);
  $lastname = sfInflector::humanize(sfInflector::underscore(trim($name[0])));
  $firstname = sfInflector::humanize(sfInflector::underscore(trim($name[1])));
  $dump = "  Student_".$i.":".
          "\n    firstname: ".$firstname.
          "\n    lastname: ". $lastname.
          "\n    sex: ".'<?php echo SexType::'.(($data[2]=='V') ? 'MALE' : 'FEMALE').'."\n"?>'.
          "\n    birthdate: ".$birthdate[2].$birthdate[1].$birthdate[0]."\n";
  fwrite($handle_f, $dump);
endwhile;
fclose($handle_f);
?>


<?php
//
//SCRIPT TO CREATE THE TEACHER'S FIXTURE
//
?>
<?php $handle = fopen("./csv/teachers.csv", "r")?>
<?php $handle_f = fopen("./fixtures/00-teacher.yml","w+")?>
<?php if(($handle===FALSE)||($handle_f===FALSE)):?>
  <?php die("Error al abrir el archivo\n")?>
<?php endif?>
<?php 
$i = 0;
fwrite($handle_f,"Teacher:\n");
while (($data = fgetcsv($handle,4096,",")) !== FALSE):
  $i=$i+1;
  $birthdate = explode("/",$data[3]); 
  $name = explode(",",$data[1]);
  $lastname = sfInflector::humanize(sfInflector::underscore(trim($name[0])));
  $firstname = sfInflector::humanize(sfInflector::underscore(trim($name[1])));
  $dump = "  Teacher_".$i.":".
          "\n    firstname: ".$firstname.
          "\n    lastname: ". $lastname.
          "\n    identification_type: ".'<?php echo IdentificationType::DNI."\n"?>'.
          "\n    identification_number: ".$data[0]."\n";
         // "\n    birthdate: ".$birthdate[2].$birthdate[1].$birthdate[0]."\n";
  fwrite($handle_f, $dump);
endwhile;
fclose($handle_f);
?>



<?php
//
//SCRIPT TO CREATE FIXTURE FOR FORMACION BASICA DE CANTO 
//
?>
<?php $handle = fopen("./csv/form-basica-canto.csv", "r")?>
<?php $handle_f = fopen("./fixtures/form-basica-canto.yml","w+")?>
<?php if(($handle===FALSE)||($handle_f===FALSE)):?>
  <?php die("Error al abrir el archivo\n")?>
<?php endif?>
<?php

fwrite($handle_f,"Career:\n"); 
$dump = "  Career_1:".
          "\n    career_name: Formación Basica Canto".
          "\n    plan_name: FOBA".
          "\n    quantity_years: 3".
          "\n    career_status_id: ".'<?php echo CareerStatus::S_NEW ."\n"?>'."\n";
fwrite($handle_f, $dump);

$handle = fopen("./csv/form-basica-canto.csv", "r");
fwrite($handle_f,"\nSubject:\n");
while (($data = fgetcsv($handle,4096,",")) !== FALSE):
  $dump = "  Subject_".$data[0].":".
          "\n    name: ".$data[1].
          "\n    fantasy_name: ".$data[0].
          "\n    credit_hours: ".$data[5].
          "\n    is_to_promote: ".(($data[7]=="promoción")?"1":"0").
          "\n    type: ".'<?php echo SubjectType::'.(($data[6]=='S')?"TYPE_SIX_MONTHLY":"TYPE_ANNUAL").'."\n"?>'."\n";
  fwrite($handle_f, $dump);
endwhile;
fclose($handle);

$handle = fopen("./csv/form-basica-canto.csv", "r");
fwrite($handle_f,"\nCareerSubject:\n");
while (($data = fgetcsv($handle,4096,",")) !== FALSE):
  $dump = "  CareerSubject_".$data[0].":".
          "\n    career_id: Career_1".
          "\n    subject_id: Subject_".$data[0].
          "\n    year: ".$data[3]."\n";
  fwrite($handle_f, $dump);
endwhile;

fclose($handle);
$handle = fopen("./csv/form-basica-canto.csv", "r");
fwrite($handle_f,"\nCorrelative:\n");
$i=0;
while (($data = fgetcsv($handle,4096,",")) !== FALSE):
  $correlatives = explode(",", $data[4]);
  foreach($correlatives as $correlative){
    $dump ="  Correlative_".$i++.":".
           "\n    career_subject_id: CareerSubject_".$data[0].
           "\n    correlative_career_subject_id: CareerSubject_".$correlative."\n";
    if($correlative!="")fwrite($handle_f, $dump);
  }
endwhile;





fclose($handle_f);
?>
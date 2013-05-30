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
<?php
/**
 * Description of InterfaceEvaluatorBehaviour
 *
 * @author gramirez
 */
abstract class InterfaceEvaluatorBehaviour
{
  /**
   * Returns if a student has approved or not the course subject.
   *
   * Devuelve si el alumno aprobó o no el curso.
   *
   * @param CourseSubjectStudent $course_subject_student
   * @param PropelPDO $con
   *
   * @return Object $object
   */
  public abstract function getCourseSubjectStudentResult(CourseSubjectStudent $course_subject_student, PropelPDO $con = null);

  /**
   * Returns the number of the examination for the given average.
   *
   * Devuelve el número de mesa para el promedio dado. Esto por lo general es
   * el número de instancia. Por ejemplo:
   *  * 1 => diciembre
   *  * 2 => febrero
   *
   * @param float
   * @return integer
   */
  public abstract function getExaminationNumberFor($average);

  /**
   * Returns the string for the examination instance.
   *
   * Devuelve el string que representa la instancia de la mesa de examen. Por ejemplo:
   *   * 1 => diciembre
   *   * 2 => febrero
   *
   * @param integer
   * @return string
   */
  public abstract function getStringFor($key);

  /**
   * Evaluates the career school year for the given student.
   *
   * Evalua el año lectivo actual para un alumno. Esto genera los resultados para cada curso del alumno.
   *
   * @param CareerSchoolYear $career_school_year
   * @param Student $student
   * @param PropelPDO $con
   */
  public abstract function evaluateCareerSchoolYearStudent(CareerSchoolYear $career_school_year, Student $student, PropelPDO $con = null);

  /**
   * Pasa de año al alumno si corresponde.
   * Esto tiene que ver con la cantidad de previas, mesas de examen y demás de acuerdo a cada colegio.
   *
   * @param Student $student
   * @param CareerSchoolYear career_school_year
   * @param PropelPDO $con
   */
  public abstract function stepToNextYear(Student $student, SchoolYear $school_year, PropelPDO $con = null);

  /**
   * Cierra una cursa de un alumno y crea si corresponde la marca de aprobación
   * de la materia (de acuerdo a la configuración del colegio).
   *
   * @param Object $result (StudentApprovedCourseSubject or SudentDissapprovedCourseSubject)
   * @param PropelPDO $con
   */
  public abstract function closeCourseSubjectStudent($result, PropelPDO $con = null);

  /**
   * Cierra una mesa de final. Puede crear otra inscripción a la siguiente instancia de mesa de examen
   * o crear la previa. O en caso de aprobar, puede crear la marca de aprobación de la materia.
   *
   * @param CourseSubjectStudentExamination $course_subject_student_examination
   * @param PropelPDO $con
   */
  public abstract function closeCourseSubjectStudentExamination(CourseSubjectStudentExamination $course_subject_student_examination, PropelPDO $con = null);

  /**
   * Devuelve el resultado de la mesa de examen pasada como argumento.
   * Este resultado es un arreglo con una clase (para CSS) y un string que indica el resultado.
   * (Aprobado o Desaprobado).
   *
   * @param CourseSubjectStudentExamination $css_examination
   * @return array
   */
  public abstract function getExaminationResult(CourseSubjectStudentExamination $css_examination);

  /**
   * Devuelve el resultado de la previa pasada como argumento.
   * Este resultado es un arreglo con una clase (para CSS) y un string que indica el resultado.
   * (Aprobado o Desaprobado).
   *
   * @param StudentExaminationRepprovedSubject $student_examination_repproved_subject
   * @return array
   */
  public abstract function getExaminationRepprovedResult(StudentExaminationRepprovedSubject $student_examination_repproved_subject);

  /**
   * Returns the minimum allowed mark for course grading.
   *
   * @return float
   */
  public abstract function getMinimumMark();

  /**
   * Returns the maximum allowed mark for course grading.
   *
   * @return float
   */
  public abstract function getMaximumMark();

  /**
   * If the student has approved the previous, then it creates a student_approved_career_subject for this student.
   *
   * Actualiza la información de aprobación de una materia para la previa dada.
   *
   * @param StudentExaminationRepprovedSubject $student_examination_repproved_subject
   * @param PropelPDO $con
   */
  public abstract function closeStudentExaminationRepprovedSubject(StudentExaminationRepprovedSubject $student_examination_repproved_subject, PropelPDO $con);

  /**
   * This method returns a string for the result.
   * @param StudentApprovedCourseSubject $student_approved_course_subject
   * @return String
   */
  abstract public function getStudentApprovedResultString(StudentApprovedCourseSubject $student_approved_course_subject);

  /**
   * This method returns the available marks for students. For Bba behavior like, depends of the closed notes.
   *
   * @param CourseSubjectStudent $course_subject_student
   * @param Criteria $criteria
   * @return <type>
   */
  abstract public function getAvailableCourseSubjectStudentMarks(CourseSubjectStudent $course_subject_student, Criteria $criteria = null);

  /**
   * Returns the note for examination check(Final en conservatorios case).
   */
  abstract public function getExaminationNote();
}
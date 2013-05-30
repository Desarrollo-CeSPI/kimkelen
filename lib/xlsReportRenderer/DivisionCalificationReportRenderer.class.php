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

class DivisionCalificationReportRenderer extends ReportRendererXls
{
  const TITLE_ROW_HEIGHT  = 40;
  const HEADER_ROW_HEIGHT = 18;
  const NORMAL_ROW_HEIGHT = 15;

  public function renderSubTitle($content)
  {
    $header_row = array(array(
        'content'    => $content,
        'size'       => BaseReportRenderer::FONT_SIZE_NORMAL,
        'style'      => BaseReportRenderer::STYLE_BOLD | BaseReportRenderer::STYLE_CENTERED | BaseReportRenderer::STYLE_VERTICAL_CENTER,
        'column_end' => 2,
    ));
    $this->renderRow($header_row, self::TITLE_ROW_HEIGHT);
  }

  public function renderTitle($content)
  {
    $header_row = array(array(
        'content'    => $content,
        'size'       => BaseReportRenderer::FONT_SIZE_LARGE,
        'style'      => BaseReportRenderer::STYLE_BOLD | BaseReportRenderer::STYLE_CENTERED | BaseReportRenderer::STYLE_VERTICAL_CENTER,
        'column_end' => 2,
    ));
    $this->renderRow($header_row, self::TITLE_ROW_HEIGHT);
  }

  public function renderColumnHeaders($course_subjects)
  {
    $headers = array();
    $i = 1;
    foreach ($course_subjects as $course_subject)
    {

      $headers[] = array(
        'content'    => sprintf($course_subject->__toString()),
        'size'       => BaseReportRenderer::FONT_SIZE_NORMAL,
        'style'      => BaseReportRenderer::STYLE_CENTERED | BaseReportRenderer::STYLE_BOLD,
        'column_start'  => $i,
        'column_end'    => $i = $i + $course_subject->getCareerSubjectSchoolYear()->getConfiguration()->getCourseMarks(),
      );

      $i++;
    }

    $this->renderRow($headers, self::HEADER_ROW_HEIGHT);

    // set Column sizes - ugly & only really working way
    $this->renderObject->getActiveSheet()->getColumnDimension('A')->setWidth(30);
  }

  public function renderStudentCalificationRow($student, $course_subjects)
  {
    $row = array($student->__toString());

    foreach ($course_subjects as $course_subject)
    {
      foreach ($student->getMarksForCourse($course_subject) as $mark)
      {
        $row[] = array(
          'size'       => BaseReportRenderer::FONT_SIZE_NORMAL,
          'style'      => BaseReportRenderer::STYLE_CENTERED,
          'content' => $mark->__toString());
      }

      $course_subject_student = CourseSubjectStudentPeer::retrieveByCourseSubjectAndStudent($course_subject->getId(), $student->getId());
      $row[] = array(
          'size'       => BaseReportRenderer::FONT_SIZE_NORMAL,
          'style'      => BaseReportRenderer::STYLE_CENTERED,
          'content'    =>    $course_subject_student->getMarksAverage());
    }

    $this->renderRow($row);
  }

  public function renderCourseSubjectHeader($configurations)
  {
    $row = array('');
    foreach ($configurations as $configuration)
    {
      for ($i = 1; $i <= $configuration->getCourseMarks(); $i++)
      {
        $row[] = array(
          'size'       => BaseReportRenderer::FONT_SIZE_NORMAL,
          'style'      => BaseReportRenderer::STYLE_CENTERED,
          'content' => SchoolBehaviourFactory::getInstance()->getMarkNameByNumberAndCourseType($i, $configuration->getCourseType()));
      }

      $row[] = 'Prom.';
    }

    $this->renderRow($row);
  }

  public function renderCourseSubjectsHeaders($course_subjects)
  {
    $column_headers = array('');

    foreach ($course_subjects as $course_subject)
    {
      $column_headers[] = $course_subject->__toString();
    }

    $this->renderColumnHeaders($column_headers);
  }

  public function renderRow($data, $row_height = self::NORMAL_ROW_HEIGHT)
  {
    $this->renderObject->getActiveSheet()->getRowDimension($this->rowIndex)->setRowHeight($row_height);
    parent::renderRow($data);
  }

}
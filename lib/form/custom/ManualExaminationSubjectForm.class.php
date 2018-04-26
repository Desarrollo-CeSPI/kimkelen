<?php

/**
 * ManualExaminationSubjectForm
 *
 * @author María Emilia Corrons <ecorrons@cespi.unlp.edu.ar>
 */
class ManualExaminationSubjectForm extends BaseExaminationSubjectForm
{
  public function configure()
  {
    parent::configure();
    unset($this['is_closed']);

    $this->setWidget("examination_id", new sfWidgetFormInputHidden());

    $c = new Criteria();
    $c->addJoin(CareerSubjectSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, CareerSchoolYearPeer::ID, Criteria::INNER_JOIN);
    $c->add(CareerSchoolYearPeer::SCHOOL_YEAR_ID, SchoolYearPeer::retrieveCurrent()->getId());

    $this->setWidget("career_subject_school_year_id", new sfWidgetFormPropelChoice(array(
      'model' => 'CareerSubjectSchoolYear',
        'method' => 'getFullToString',
      'add_empty' => false,
      'criteria' => $c
    )));
    
    $this->setWidget('date', new csWidgetFormDateInput());
    $this->setValidator('date', new mtValidatorDateString(array('required' => false)));

    $this->widgetSchema["examination_subject_teacher_list"]->setOption("multiple", true);
    $this->widgetSchema["examination_subject_teacher_list"]->setOption("peer_method", 'doSelectActive');
    $this->widgetSchema["examination_subject_teacher_list"]->setOption("renderer_class", "csWidgetFormSelectDoubleList");
  }

  protected function doSave($con = null)
  {
    ExaminationSubjectBehavior::disable();
    parent::doSave($con);
    ExaminationSubjectBehavior::enable();
  }
}

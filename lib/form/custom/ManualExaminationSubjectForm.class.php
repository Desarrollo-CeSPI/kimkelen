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
    $this->setWidget("career_subject_school_year_id", new sfWidgetFormPropelChoice(array('model' => 'CareerSubjectSchoolYear', 'add_empty' => false)));
    $this->widgetSchema["examination_subject_teacher_list"]->setOption("multiple", true);
    $this->widgetSchema["examination_subject_teacher_list"]->setOption("peer_method", 'doSelectActive');
    $this->widgetSchema["examination_subject_teacher_list"]->setOption("renderer_class", "csWidgetFormSelectDoubleList");

   
    }

}

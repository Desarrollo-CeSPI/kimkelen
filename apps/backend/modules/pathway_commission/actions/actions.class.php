<?php

require_once dirname(__FILE__) . '/../lib/pathway_commissionGeneratorConfiguration.class.php';
require_once dirname(__FILE__) . '/../lib/pathway_commissionGeneratorHelper.class.php';

/**
 * pathway_commission actions.
 *
 * @package    symfony
 * @subpackage pathway_commission
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class pathway_commissionActions extends autoPathway_commissionActions
{

    public static function getForms($course_subjects)
    {
        $forms = array();
        $i = 0;
        foreach ($course_subjects as $course_subject)
        {
            $forms[$course_subject->getId()] = new PathwayCourseSubjectStudentManyForm($course_subject);
            $forms[$course_subject->getId()]->getWidgetSchema()->setNameFormat("course_subject_${i}[%s]");
            $i++;
        }

        return $forms;
    }

    public function executeCourseSubjectStudent(sfWebRequest $request)
    {
        $this->getUser()->setAttribute("referer_module", "pathway_commission");
        $this->getUser()->setAttribute("referer_actions_class", __CLASS__);

        $this->forward("shared_course", "courseSubjectStudent");
    }

    public function executeAddSubject(sfWebRequest $request)
    {
        //TODO: Ver de extenderlo de commissionActions
        if ($request->isMethod('post'))
        {
            $params = $request->getPostParameters();
            $this->course = CoursePeer::retrieveByPk($params['course']['id']);
            $this->form = new SubjectForCommissionForm($this->course);
            $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));
            if ($this->form->isValid())
            {
                $this->form->save();

                $this->getUser()->setFlash("notice", "New subject added to commission successfully");

                $this->redirect("@pathway_commission");
            }
        }
        else
        {
            $this->course = $this->getRoute()->getObject();
            $this->course_subjects = $this->course->getCourseSubjects();
            $this->form = new SubjectForCommissionForm($this->course);
        }
    }

    public function executeDeleteSubject(sfWebRequest $request)
    {
        //TODO: Ver de extenderlo de commissionActions
        $cs = CourseSubjectPeer::retrieveByPK($request->getParameter('course_subject_id'));

        if ($cs and $course = $cs->getCourse() and $course->isPathway())
        {
            try
            {
                $cs->delete();
                $this->getUser()->setFlash("notice", "The item was deleted successfully.");
            }
            catch (PropelException $e)
            {
                $this->getUser()->setFlash('error', 'A problem occurs when deleting the selected items.');
            }
        }
        else
        {
            $this->getUser()->setFlash('error', 'The selected item is not a pathway commission.');
        }

        $this->redirect("@pathway_commission");
    }

}

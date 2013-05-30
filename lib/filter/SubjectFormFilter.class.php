<?php

/**
 * Subject filter form.
 *
 * @package    conservatorio
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormFilterTemplate.php 11675 2008-09-19 15:21:38Z fabien $
 */
class SubjectFormFilter extends BaseSubjectFormFilter
{
  public function removeFields()
  {
    unset(
      $this['fantasy_name'],
      $this['name'],
      $this['created_at']
    );
  }

  public function configure()
  {
    $this->removeFields();

    //widgets
    $this->setWidget('word', new sfWidgetFormInput());

    //validators
    $this->setValidator('word', new sfValidatorString(array('required' => false)));

    //widgets options
    $this->getWidgetSchema()->setLabel('word', 'Que contenga el nombre');
    $this->getWidgetSchema()->setHelp('word', 'Se filtrarán todas las materias que contengan lo ingresado en el nombre o nombre de fantasia');
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array('word' => 'Text'));
  }

  public function addWordColumnCriteria(Criteria $criteria, $field, $value)
  {
    $value = "%$value%";
    $criteria->setIgnoreCase(true);
    $criterion = $criteria->getNewCriterion(SubjectPeer::NAME, $value, Criteria::LIKE);
    $criterion->addOr($criteria->getNewCriterion(SubjectPeer::FANTASY_NAME, $value, Criteria::LIKE));
    $criteria->add($criterion);
  }

}

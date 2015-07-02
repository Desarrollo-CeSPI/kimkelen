<?php
require_once dirname(__FILE__).'/../symfony/autoload/sfCoreAutoload.class.php';
sfCoreAutoload::register();

class restoreBbaCourseSubjectStudentMarksTask extends sfBaseTask
{
	protected function configure()
	{
		// // add your own arguments here
		// $this->addArguments(array(
		//   new sfCommandArgument('my_arg', sfCommandArgument::REQUIRED, 'My argument'),
		// ));

		$this->addOptions(array(
			new sfCommandOption('application', null, sfCommandOption::PARAMETER_OPTIONAL, 'The application name'),
			new sfCommandOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'The environment', 'dev'),
			new sfCommandOption('connection', null, sfCommandOption::PARAMETER_OPTIONAL, 'The connection name', 'propel'),
			// add your own options here
		));

		$this->namespace        = 'kimkelen';
		$this->name             = 'restoreBbaCourseSubjectStudentMarks';
		$this->briefDescription = '';
		$this->detailedDescription = <<<EOF
The [restoreBbaCourseSubjectStudentMarks|INFO] task does things.
Call it with:

  [php symfony restoreBbaCourseSubjectStudentMarks|INFO]
EOF;
	}

	protected function execute($arguments = array(), $options = array())
	{
		$this->createContextInstance();

		// initialize the database connection
		$databaseManager = new sfDatabaseManager($this->configuration);
		/** @var $connection PropelPDO */
		$connection = $databaseManager->getDatabase($options['connection'] ? $options['connection'] : null)->getConnection();


		$username = 'maemilia_bongiorno';
		$date_from = '2013-11-23 00:00:00';
		$date_to = '2013-11-23 23:59:59';
		$class = 'CourseSubjectStudentMark';

		/** @var $user sfGuardSecurityUser */
		$user = sfContext::getInstance()->getUser();
		$sf_user = sfGuardUserPeer::retrieveByUsername($username);
		$user->signin($sf_user, false);

		$connection->beginTransaction();

		try
		{

			$c = new Criteria();
			$c->add(ncChangeLogEntryPeer::CLASS_NAME,$class);
			$c->add(ncChangeLogEntryPeer::USERNAME,$username);
			$cri = $c->getNewCriterion(ncChangeLogEntryPeer::CREATED_AT,$date_from,Criteria::GREATER_EQUAL);
			$cri->addAnd($c->getNewCriterion(ncChangeLogEntryPeer::CREATED_AT,$date_to,Criteria::LESS_EQUAL));
			$c->add($cri);

			$cambios = ncChangeLogEntryPeer::doSelect($c,$connection);
			/** @var $nc_change_log_entry ncChangeLogEntry */
			foreach($cambios as $nc_change_log_entry)
			{
				$obj = unserialize(base64_decode($nc_change_log_entry->getChangesDetail()));
				if (isset($obj['changes']) && isset($obj['changes']['mark']) )
				{
					$old = $obj['changes']['mark']['raw']['old'];
					$new = $obj['changes']['mark']['raw']['new'];
					$id = $obj['pk'];


					$mark = CourseSubjectStudentMarkPeer::retrieveByPK($id,$connection);
					$mark->setMark($old);
					$mark->save($connection);
				}
			}
			$connection->commit();
		}
		catch(Exception $e)
		{
			$connection->rollBack();
			$this->log($e->getMessage());
			$this->log($e->getTraceAsString());
		}



	}

	protected  function createContextInstance($application = 'backend', $enviroment = 'dev', $debug = true)
	{
		$configuration = ProjectConfiguration::getApplicationConfiguration($application, $enviroment, $debug);

		sfContext::createInstance($configuration);
		sfContext::switchTo($application);

		$this->context = sfContext::getInstance();
	}

}


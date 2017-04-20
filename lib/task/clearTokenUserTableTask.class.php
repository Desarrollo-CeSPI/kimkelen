<?php

class clearTokenUserTableTask extends sfBaseTask
{
	protected function configure()
	{
		$this->addOptions(array(
			new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
			new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
			new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'propel'),
			// add your own options here
		));

		$this->namespace = 'clear';
		$this->name = 'tokenUserTable';
		$this->briefDescription = '';
		$this->detailedDescription = <<<EOF
    The [clearTokenUserTableTask] deletes all token older than 1 day.
    Call it with:

    [php symfony clear:tokenUserTable|INFO]
EOF;
	}

	protected function createContextInstance($application = 'frontend', $enviroment = 'dev', $debug = true)
	{
		$configuration = ProjectConfiguration::getApplicationConfiguration($application, $enviroment, $debug);

		sfContext::createInstance($configuration);
		sfContext::switchTo($application);

		$this->context = sfContext::getInstance();
	}

	protected function execute($arguments = array(), $options = array())
	{
		// initialize the database connection
		$databaseManager = new sfDatabaseManager($this->configuration);
		$con = $databaseManager->getDatabase($options['connection'] ? $options['connection'] : null)->getConnection();
		$this->createContextInstance('backend');

		$criteria = new Criteria();
		$criteria->add(TokenUserPeer::CREATED_AT, false);
		$criteria->addJoin(CoursePeer::ID, CourseSubjectPeer::COURSE_ID, Criteria::INNER_JOIN);
		$criteria->addJoin(CourseSubjectPeer::ID, CourseSubjectStudentPeer::COURSE_SUBJECT_ID, Criteria::INNER_JOIN);

		$course_subject_students = CourseSubjectStudentPeer::doSelect($criteria);


	}
}

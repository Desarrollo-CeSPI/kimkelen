<?php

require_once dirname(__FILE__).'/../lib/sanctionGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/sanctionGeneratorHelper.class.php';

/**
 * sanction actions.
 *
 * @package    symfony
 * @subpackage sanction
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class sanctionActions extends autoSanctionActions
{

  public function executeEdit(sfWebRequest $request)
	{
		$this->forward404('Not implemented');
	}

	public function executeDelete(sfWebRequest $request)
	{
		$this->forward404('Not implemented');
	}

	public function executeNew(sfWebRequest $request)
	{
		$this->forward404('Not implemented');
	}

	public function executeCreate(sfWebRequest $request)
	{
		$this->forward404('Not implemented');
	}

	public function executeDownloadDocument(sfWebRequest $request)
	{
		$student_disciplinary_sanction = StudentDisciplinarySanctionPeer::retrieveByPK($request->getParameter('id'));

		if ($student_disciplinary_sanction && $student_disciplinary_sanction->getDocument())
		{
			$filePath = $student_disciplinary_sanction->getDocumentFullPath();
			$response = $this->getResponse();
			$response->setHttpHeader('Pragma', '');
			$response->setHttpHeader('Cache-Control', '');
			$data = file_get_contents($filePath);

			$file_exploded = explode('.', $student_disciplinary_sanction->getDocument());
			$file_extension = end($file_exploded);
			if ($file_extension == 'pdf')
			{
				$response->setHttpHeader('Content-Type', 'application/pdf');
			}
			else
			{
				if ($file_extension == 'jpg')
				{
					$content_type = 'jpeg';
				}
				else
				{
					$content_type = $file_extension;
				}
				$response->setHttpHeader('Content-Type', 'image/' . $content_type);
			}
			$response->setHttpHeader('Content-Disposition', "attachment; filename=\"" . $student_disciplinary_sanction->getDocument() . "\"");
			$response->setContent($data);
		}

		return sfView::NONE;
	}

}

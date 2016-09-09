<?php

/**
 * Created by PhpStorm.
 * User: ecorrons
 * Date: 06/09/16
 * Time: 13:13
 */
class jsonActions extends sfActions
{
	public function executeSearchSchools(sfWebRequest $request)
	{

		$this->getResponse()->setContentType('application/json');

		if (strlen(trim($q = $request->getParameter('q'))) > 2)
		{
			$c = new Criteria();
			$crit = $c->getNewCriterion(OriginSchoolPeer::NAME,"%$q%",Criteria::LIKE);
			$crit->addOr($c->getNewCriterion(OriginSchoolPeer::ADDRESS, "%$q%", Criteria::LIKE));
			$crit->addOr($c->getNewCriterion(OriginSchoolPeer::CUE, "%$q%", Criteria::LIKE));
			$c->add($crit);

			$c->setLimit(20);
			$results = array();
			foreach ($matches=OriginSchoolPeer::doSelect($c) as $match)
			{
				$results[] = array('id' => $match->getId(), 'text' => $match->__toString());
			}
			return $this->renderText(json_encode(array('results' => $results)));
		}
		else if ($request->getParameter('id') !== null)
		{
			$match = OriginSchoolPeer::retrieveByPK($request->getParameter('id'));
			$result = null;
			if ($match !== null)
			{
				$result = array('id' => $match->getId(), 'text' => strval($match));
			}

			return $this->renderText(json_encode($result));
		}

		return $this->renderText('{}');
	}
}
<?php
namespace Sfi\Encult\Controller;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Sfi.Encult".            *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use Sfi\Encult\Domain\Model\Vote;
use Sfi\Encult\Domain\Repository\VoteRepository;
use TYPO3\Flow\Http\Cookie;
use TYPO3\TYPO3CR\Domain\Service\ContextFactoryInterface;

/*
 * Trivial voting system, based on cookies.
 * Tracks returning voters and ip-address
 */
class VoteController extends \TYPO3\Flow\Mvc\Controller\ActionController {

	/**
	 * @var VoteRepository
	 * @Flow\Inject
	 */
	protected $voteRepository;

	/**
	 * @Flow\Inject
	 * @var \Doctrine\Common\Persistence\ObjectManager
	 */
	protected $entityManager;

	protected $liveContext;

	/**
	 * @param ContextFactoryInterface
	 */
	public function __construct(ContextFactoryInterface $contextFactory) {
		$this->liveContext = $contextFactory->create(array('workspaceName' => 'live'));
	}

	/**
	 * @param string $answerIdentifier
	 * @param string $language
	 *
	 * @return void
	 */
	public function castVoteAction($answerIdentifier, $language = 'rus') {
		$httpRequest = $this->controllerContext->getRequest()->getHttpRequest();
		$response = $this->controllerContext->getResponse();
		$ipAddress = $httpRequest->getClientIpAddress();
		$now = new \DateTime('NOW');

		$answerNode = $this->liveContext->getNodeByIdentifier($answerIdentifier);
		$questionIdentifier = $answerNode->getparent()->getIdentifier();
		$questionPath = $answerNode->getparent()->getProperty('uriPathSegment');
		$worldviewIdentifier = $answerNode->getProperty('worldview')->getIdentifier();

		$voteCookieId = 'vote_in_' . $questionPath;
		if ($httpRequest->hasCookie($voteCookieId)) {
			$error = Array();
			$error['data']['success'] = false;
			$error['error'] = 'You have already voted for this question!';
			return json_encode($error);
		}
		// Set Answer id as a value of a cookie, to be used in frontend
		$voteCookie = new Cookie($voteCookieId, $answerIdentifier, 0, 72000);
		$response->setCookie($voteCookie);

		if ($httpRequest->hasCookie('returning')) {
			$isReturning = true;
		} else {
			$isReturning = false;
			$returningCookie = new Cookie('returning', $now->format('U'));
			$response->setCookie($returningCookie);
		}

		// Update vote count on the answer node
		$voteCount = $answerNode->getProperty('voteCount');
		$voteCount = $voteCount ? $voteCount + 1 : 1;
		$answerNode->setProperty('voteCount', $voteCount);

		$vote = New Vote();
		$vote->setAnswerIdentifier($answerIdentifier);
		$vote->setQuestionIdentifier($questionIdentifier);
		$vote->setWorldviewIdentifier($worldviewIdentifier);
		$vote->setDateTime($now);
		$vote->setLanguage($language);
		$vote->setIsReturning($isReturning);
		$vote->setIpAddress($ipAddress);

		// Save the vote (for statistics and logging)
		$this->voteRepository->add($vote);

		$result = Array();
		$result['data']['success'] = true;
		return json_encode($result);
	}

	/*
	 * Display voting stats
	 */
	public function displayStatsAction() {
		$queryString = "SELECT worldviewIdentifier as worldview, count(*) AS count FROM sfi_encult_domain_model_vote GROUP BY worldviewIdentifier;";
		$rsm = new \Doctrine\ORM\Query\ResultSetMapping();
		$rsm->addScalarResult('worldview', 'worldview');
		$rsm->addScalarResult('count', 'count');
		$query = $this->entityManager->createNativeQuery($queryString, $rsm);
		$voteResults = array();
		foreach ($query->getResult() as $row) {
			if ($row['worldview']) {
				$worldviewNode = $this->liveContext->getNodeByIdentifier($row['worldview']);
				$voteResults[$worldviewNode->getProperty('title')] = $row['count'];
			}
		}
		$this->view->assign('voteResults', $voteResults);
	}
}
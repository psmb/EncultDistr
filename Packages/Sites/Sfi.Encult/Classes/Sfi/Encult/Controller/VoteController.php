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

class VoteController extends \TYPO3\Flow\Mvc\Controller\ActionController {

	/**
	 * @var VoteRepository
	 * @Flow\Inject
	 */
	protected $voteRepository;

	/**
	 * @Flow\Inject
	 * @var ContextFactoryInterface
	 */
	protected $contextFactory;

	/**
	 * @param Vote $vote
	 *
	 * @return void
	 */

	/*
	 * Trivial voting system, based on cookies.
	 * Tracks returning voters and ip-address
	 */
	public function castVoteAction(Vote $vote) {
		$httpRequest = $this->controllerContext->getRequest()->getHttpRequest();
		$response = $this->controllerContext->getResponse();

		$ipAddress = $httpRequest->getClientIpAddress();
		$now = new \DateTime('NOW');

		$voteCookieId = 'vote_in_' . $vote->getQuestionIdentifier();
		if ($httpRequest->hasCookie($voteCookieId)) {
			return "You have already voted for this question!";
		}
		// Set Answer id as a value of a cookie, to be used in frontend
		$voteCookie = new Cookie($voteCookieId, $vote->getAnswerIdentifier(), 0, 72000);
		$response->setCookie($voteCookie);

		if ($httpRequest->hasCookie('returning')) {
			$isReturning = true;
		} else {
			$isReturning = false;
			$returningCookie = new Cookie('returning', $now->format('U'));
			$response->setCookie($returningCookie);
		}


		$vote->setDateTime($now);
		$vote->setIsReturning($isReturning);
		$vote->setIpAddress($ipAddress);

		$livecontext = $this->contextFactory->create(array('workspaceName' => 'live'));

		$answerNode = $livecontext->getNodeByIdentifier($vote->getAnswerIdentifier());
		$voteCount = $answerNode->getProperty('voteCount');
		$voteCount = $voteCount ? $voteCount + 1 : 1;

		$answerNode->setProperty('voteCount', $voteCount);

		$this->voteRepository->add($vote);

		return "Success";
	}
}
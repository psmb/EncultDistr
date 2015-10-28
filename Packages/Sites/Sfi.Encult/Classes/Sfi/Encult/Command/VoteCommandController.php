<?php
namespace Sfi\Encult\Command;

/*																		*
 * This script belongs to the TYPO3 Flow package "Sfi.Encult".		 *
 *																		*
 *																		*/

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Eel\FlowQuery\FlowQuery;
use TYPO3\Flow\Cli\CommandController;
use TYPO3\TYPO3CR\Domain\Service\ContextFactoryInterface;

/**
 * @Flow\Scope("singleton")
 */
class VoteCommandController extends CommandController {

	/**
	 * @var \TYPO3\TYPO3CR\Domain\Service\Context
	 */
	protected $context;

	/**
	 * @Flow\Inject
	 * @var \Doctrine\Common\Persistence\ObjectManager
	 */
	protected $entityManager;

	public function __construct(ContextFactoryInterface $contextFactory) {
		parent::__construct();
		$this->context = $contextFactory->create(array('workspaceName' => 'live'));
	}

	/**
	 * Repair votes action
	 *
	 * Compare number of votes between nodes and vote log and repair, if not dryRun
	 *
	 * @param boolean $dryRun Don't do anything, but report actions
	 * @return string
	 */
	public function repairCommand($dryRun = TRUE) {
		if ($dryRun) {
			echo "Dry run, not making any changes\n";
		}
		$q = new FlowQuery(array($this->context->getRootNode()));
		$answerNodes = $q->find('[instanceof Sfi.Encult:Answer]')->get();
		foreach ($answerNodes as $answerNode) {
			/** @var \Doctrine\ORM\QueryBuilder $queryBuilder */
			$queryBuilder = $this->entityManager->createQueryBuilder();
			$nodes = $queryBuilder
				->select('v')
				->from('Sfi\Encult\Domain\Model\Vote', 'v')
				->andWhere('v.answerIdentifier = :answerIdentifier')
				->setParameters(array('answerIdentifier' => $answerNode->getIdentifier()))
				->getQuery()->getArrayResult();

			$dbCount = count($nodes);
			$crCount = $answerNode->getProperty('voteCount');
			$path =  $answerNode->getPath();
			if ($dbCount !== $crCount) {
				echo "Found mistake for $path (db: $dbCount vs. cr: $crCount)\n";
				if (!$dryRun) {
					echo "Fixed\n";
					$answerNode->setProperty('voteCount', $dbCount);
				}
			}
		}
		return "Done!\n";
	}
}

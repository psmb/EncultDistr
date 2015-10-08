<?php
namespace Sfi\Encult\Eel\FlowQueryOperations;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Eel\FlowQuery\Operations\AbstractOperation;
use TYPO3\TYPO3CR\Domain\Model\NodeInterface;

/**
 * Reduce Operation
 *
 * Takes an Eel Expression as a first argument and initial values as the second. Injects previousValue,
 * currentValue, index and array context variables.
 *
 * @see https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Array/Reduce
 */
class ReduceOperation extends AbstractOperation {
	/**
	 * {@inheritdoc}
	 *
	 * @var string
	 */
	static protected $shortName = 'reduce';

	/**
	 * {@inheritdoc}
	 *
	 * @var integer
	 */
	static protected $priority = 100;

	/**
	 * {@inheritdoc}
	 *
	 * @var boolean
	 */
	static protected $final = TRUE;

	/**
	 * {@inheritdoc}
	 *
	 * @param array (or array-like object) $context onto which this operation should be applied
	 * @return boolean TRUE if the operation can be applied onto the $context, FALSE otherwise
	 */
	public function canEvaluate($context) {
		return (isset($context[0]) && ($context[0] instanceof NodeInterface));
	}

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Eel\EelEvaluatorInterface
	 */
	protected $eelEvaluator;

	/**
	 * {@inheritdoc}
	 *
	 * @param \TYPO3\Eel\FlowQuery\FlowQuery $flowQuery
	 * @param array $arguments
	 * @return void
	 */
	public function evaluate(\TYPO3\Eel\FlowQuery\FlowQuery $flowQuery, array $arguments) {
		if (!isset($arguments[0]) || empty($arguments[0])) {
			throw new \TYPO3\Eel\FlowQuery\FlowQueryException('No Eel expression provided', 1332492243);
		}
		if (!isset($arguments[1]) || empty($arguments[1])) {
			$previousValue = null;
		} else {
			$previousValue = $arguments[1];
		}
		$expression = '${' . $arguments[0] . '}';
		$context = $flowQuery->getContext();
		foreach ($context as $key => $element) {
			$contextVariables = array(
				'previousValue' => $previousValue,
				'currentValue' => $element,
				'index' => $key,
				'array' => $context
			);
			$previousValue = \TYPO3\Eel\Utility::evaluateEelExpression($expression, $this->eelEvaluator, $contextVariables);
		}
		return $previousValue;
	}
}

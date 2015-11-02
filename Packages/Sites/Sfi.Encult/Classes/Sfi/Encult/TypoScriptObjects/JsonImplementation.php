<?php
namespace Sfi\Encult\TypoScriptObjects;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\TypoScript\TypoScriptObjects\ArrayImplementation;

/**
 * Evaluate sub objects to json
 */
class JsonImplementation extends ArrayImplementation {

	/**
	 * @return string
	 */
	public function evaluate() {
		$sortedChildTypoScriptKeys = $this->sortNestedTypoScriptKeys();

		if (count($sortedChildTypoScriptKeys) === 0) {
			return '';
		}

		$output = '';
		foreach ($sortedChildTypoScriptKeys as $key) {
			$value = $this->tsValue($key);
			$output[$key] = $value;
		}

		return json_encode($output, JSON_UNESCAPED_UNICODE);
	}
}

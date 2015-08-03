<?php
namespace Sfi\Encult\Domain\Model;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Sfi.Encult".            *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Flow\Entity
 */
class Vote {

	/**
	 * @var string
	 * @Flow\Validate(type="NotEmpty")
	 */
	protected $answerIdentifier;

	/**
	 * @var string
	 * @Flow\Validate(type="NotEmpty")
	 */
	protected $questionIdentifier;

	/**
	 * @var \DateTime
	 */
	protected $dateTime;

	/**
	 * @var string
	 */
	protected $ipAddress;

	/**
	 * @var string
	 */
	protected $language;

	/**
	 * @var boolean
	 */
	protected $isReturning;


    /**
     * Gets the value of answerIdentifier.
     *
     * @return string
     */
    public function getAnswerIdentifier()
    {
        return $this->answerIdentifier;
    }

    /**
     * Sets the value of answerIdentifier.
     *
     * @param string $answerIdentifier the answer identifier
     *
     * @return void
     */
    public function setAnswerIdentifier($answerIdentifier)
    {
        $this->answerIdentifier = $answerIdentifier;
    }

    /**
     * Gets the value of questionIdentifier.
     *
     * @return string
     */
    public function getQuestionIdentifier()
    {
        return $this->questionIdentifier;
    }

    /**
     * Sets the value of questionIdentifier.
     *
     * @param string $questionIdentifier the question identifier
     *
     * @return self
     */
    public function setQuestionIdentifier($questionIdentifier)
    {
        $this->questionIdentifier = $questionIdentifier;
    }

    /**
     * Gets the value of dateTime.
     *
     * @return \DateTime
     */
    public function getDateTime()
    {
        return $this->dateTime;
    }

    /**
     * Sets the value of dateTime.
     *
     * @param \DateTime $dateTime the date time
     *
     * @return void
     */
    public function setDateTime($dateTime)
    {
        $this->dateTime = $dateTime;
    }

    /**
     * Gets the value of ipAddress.
     *
     * @return string
     */
    public function getIpAddress()
    {
        return $this->ipAddress;
    }

    /**
     * Sets the value of ipAddress.
     *
     * @param string $ipAddress the ip address
     *
     * @return void
     */
    public function setIpAddress($ipAddress)
    {
        $this->ipAddress = $ipAddress;
    }

    /**
     * Gets the value of language.
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Sets the value of language.
     *
     * @param string $language the language
     *
     * @return void
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * Gets the value of isReturning.
     *
     * @return boolean
     */
    public function getIsReturning()
    {
        return $this->isReturning;
    }

    /**
     * Sets the value of isReturning.
     *
     * @param boolean $isReturning the is returning
     *
     * @return void
     */
    public function setIsReturning($isReturning)
    {
        $this->isReturning = $isReturning;
    }
}
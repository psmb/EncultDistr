<?php
namespace Sfi\Encult\Service;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\TYPO3CR\Domain\Model\NodeInterface;

/**
 * @Flow\Scope("singleton")
 */
class VimeoService {
	/**
	 * Fetch thumb url from Vimeo API
	 *
	 * @param NodeInterface $node
	 * @return void
	 */
	public function fetchVimeoThumb(NodeInterface $node) {
		$fullVideo = $node->getProperty('fullVideo');
		$fullVideoThumb = $node->getProperty('fullVideoThumb');
		if ($fullVideo && !$fullVideoThumb) {
			$url = 'https://vimeo.com/api/oembed.json?url=http%3A//vimeo.com/' . $fullVideo;
			$content = file_get_contents($url);
			$json = json_decode($content, true);
			$node->setProperty('fullVideoThumb', $json['thumbnail_url']);
		} else if (!$fullVideo && $fullVideoThumb) {
			// Clear thumb, if Vimeo video is gone. Useful for reloading thumb.
			$node->setProperty('fullVideoThumb', '');
		}
	}
}
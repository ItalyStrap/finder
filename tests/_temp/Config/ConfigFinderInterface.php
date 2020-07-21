<?php
declare(strict_types=1);

namespace ItalyStrap\Config;

use ItalyStrap\Finder\FinderInterface;

/**
 * Interface ConfigFinderInterface
 * @package ItalyStrap\Config
 */
interface ConfigFinderInterface extends FinderInterface {

	/**
	 * @inheritDoc
	 * @return array
	 */
	public function find( $slugs, $extension = 'php' ): array;
}

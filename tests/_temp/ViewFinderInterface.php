<?php
declare(strict_types=1);

namespace ItalyStrap\View;

use ItalyStrap\Finder\FinderInterface;

/**
 * Interface ViewFinderInterface
 * @package ItalyStrap\View
 */
interface ViewFinderInterface extends FinderInterface
{
	/**
	 * @inheritDoc
	 * @return string
	 */
	public function find( $slugs, $extension = 'php' ): string ;
}
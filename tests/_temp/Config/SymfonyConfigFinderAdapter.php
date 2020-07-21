<?php
declare(strict_types=1);

namespace ItalyStrap\Config;

use ItalyStrap\Finder\AbstractFinder;
use Symfony\Component\Finder\Finder;

class SymfonyConfigFinderAdapter extends AbstractFinder {

	/**
	 * @var Finder
	 */
	private $finder;

	/**
	 * SymfonyConfigFinderAdapter constructor.
	 * @param Finder $finder
	 */
	public function __construct( Finder $finder ) {
		$this->finder = clone $finder;
	}

	/**
	 * @inheritDoc
	 */
	protected function filter( array $files ): string {

		/**
		 * @param Finder $finder
		 */
		$finder = $this->finder::create();

		$finder
			->files() // Only files
			->name( $files ) // Files name
			->in( $this->dirs ); // In directories

		if ( ! $finder->hasResults() ) {
			return '';
		}

		/**
		 * Search file against directories
		 */
		foreach ( $files as $fileName ) {
			foreach ( $finder as $fileObj ) {
				if ( $fileObj->getfileName() === $fileName ) {
					return $fileObj->getRealPath();
				}
			}
		}

		return '';
	}
}

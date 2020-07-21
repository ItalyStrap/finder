<?php
declare(strict_types=1);

namespace ItalyStrap\View;

use Symfony\Component\Finder\Finder;

class SymfonyViewFinderAdapter extends AbstractViewFinder
{
	/**
	 * @var array
	 */
	private $paths = [];

	/**
	 * @var ViewFinder
	 */
	private $finder;

	/**
	 * SymfonyConfigFinderAdapter constructor.
	 * @param ViewFinder $finder
	 */
	public function __construct( Finder $finder ) {
		$this->finder = clone $finder;
	}

	/**
	 * @inheritDoc
	 */
	protected function filter( array $files ): string {

		$this->paths = [];

		$files = \array_map( function ( $file ) {
			$file = \trim( \str_replace('\\', '/', $file ), '/');

			$split = \explode( '/', $file );
			$end = \array_pop( $split );

			$this->paths = $split;

			return $end;
		}, $files );

		/**
		 * @param ViewFinder $finder
		 */
		$finder = $this->finder::create();

		$finder
			->files() // Only files
			->name( $files ) // Files name
			->in( $this->dirs ); // In directories

		$finder->depth( \count( $this->paths ) );
		$finder->path( $this->paths );

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
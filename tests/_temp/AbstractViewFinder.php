<?php
declare(strict_types=1);

namespace ItalyStrap\View;

use ItalyStrap\Finder\AbstractFinder;
use ItalyStrap\View\Exceptions\ViewNotFoundException;

abstract class AbstractViewFinder extends AbstractFinder implements ViewFinderInterface, \Countable
{
	/**
	 * @var string
	 */
	protected $separator = '-';

	/**
	 * @inheritDoc
	 */
	public function find( $slugs, $extension = 'php' ): string {

		$this->assertDirsIsNotEmpty();

		$slugs = (array) $slugs;

		$files = [];
		$this->generateSlugs( $slugs, $files, $extension );

		$this->assertHasFile( $files );

		return $this->files[ $this->generateKey( $files[0] ) ];
	}

	/**
	 * Check if the file exists and is readable
	 *
	 * @param array $files File(s) to search for, in order.
	 * @return bool        Return true if a file exists
	 */
	protected function has( array $files ): bool {

		$key = $this->generateKey( $files[0] );

		if ( empty( $this->files[ $key ] ) ) {
			$this->files[ $key ] = $this->filter( $files );
		}

		return \is_readable( $this->files[ $key ] );
	}

	/**
	 * Generate slugs from given array of names
	 * [ 'content', 'name', 'otherName' ]
	 *
	 * [
	 * 	'content-name-otherName.php',
	 * 	'content-name.php',
	 * 	'content.php',
	 * ]
	 *
	 * @param array $slugs
	 * @param array $files
	 * @param $extensions
	 */
	protected function generateSlugs( array $slugs, array &$files, $extensions ): void {

		foreach ( (array) $extensions as $extension ) {
			$url = '';

			foreach ( $slugs as $slug ) {
				$url .= $this->separator . $slug;
			}

			$url .= '.' . $extension;

			$files[] = \ltrim( $url, $this->separator );
		}

		if ( \count( $slugs ) > 1 ) {
			\array_pop( $slugs );
			$this->generateSlugs( $slugs, $files, $extensions );
		}
	}

	/**
	 *
	 */
	protected function assertDirsIsNotEmpty(): void {
		if ( 0 === \count( $this->dirs ) ) {
			throw new \LogicException( 'You must call ::in() method before calling ::find() method.' );
		}
	}

	/**
	 * @param array $files
	 */
	protected function assertHasFile( array $files ): void {
		if ( !$this->has( $files ) ) {
			throw new ViewNotFoundException(
				\sprintf( 'The file %s does not exists', $files[ 0 ] )
			);
		}
	}
}
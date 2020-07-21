<?php
declare(strict_types=1);

namespace ItalyStrap\Config;

use ItalyStrap\Finder\AbstractFinder;
use ItalyStrap\Config\Exceptions\ConfigFileNotFoundException;

abstract class AbstractConfigFinder extends AbstractFinder implements ConfigFinderInterface
{
	/**
	 * @inheritDoc
	 */
	public function find( $names, $extension = 'php' ): array {
		$names = (array) $names;

		$this->assertDirsIsNotEmpty();

		$this->assetHasFile( $names );

//		return $this->files[ $this->generateKey( $names[0] ) ];
		return $this->files;
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

//		return \is_readable( $this->files[ $key ] );
		return \is_array( $this->files[ $key ] );
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
	 * @param $names
	 */
	protected function assetHasFile( $names ): void {
		if ( ! $this->has( $names ) ) {
			throw new ConfigFileNotFoundException(
				\sprintf( 'The file %s does not exists', $names[ 0 ] )
			);
		}
	}
}
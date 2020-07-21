<?php
/**
 *
 */
declare(strict_types=1);

namespace ItalyStrap\View;

class ViewFinder extends AbstractViewFinder {

	/**
	 * @inheritDoc
	 */
	protected function filter( array $files ): string {

		foreach ( $files as $file ) {
			foreach ( $this->dirs as $dir ) {
				$dir = \rtrim( $dir, '/\\' );
				$temp_file = $dir . \DIRECTORY_SEPARATOR . $file;
				// We need this for Windows and Linux compatibility
				$temp_file = \str_replace( ['/', '\\'], \DIRECTORY_SEPARATOR, $temp_file );
				if ( \is_readable( $temp_file ) ) {
					return $temp_file;
				}
			}
		}

		return '';
	}
}
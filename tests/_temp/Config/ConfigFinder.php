<?php
declare(strict_types=1);

namespace ItalyStrap\Config;

class ConfigFinder extends AbstractConfigFinder
{
	/**
	 * @inheritDoc
	 */
	protected function filter( array $files ): array {

		$found = [];
		foreach ( $files as $file ) {
			foreach ( $this->dirs as $dir ) {
				$dir = \rtrim( $dir, '/'.\DIRECTORY_SEPARATOR );
				if ( \is_readable( $dir . \DIRECTORY_SEPARATOR . $file . '.' . $this->extension ) ) {
					$found[ $file ][] =  $dir . \DIRECTORY_SEPARATOR . $file . '.' . $this->extension;
				}
			}
		}

		return $found;
	}
}
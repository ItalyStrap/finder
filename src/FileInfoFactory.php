<?php
declare(strict_types=1);

namespace ItalyStrap\Finder;

class FileInfoFactory implements FileInfoFactoryInterface {

	/**
	 * @inheritDoc
	 */
	public function make( string $file ): \SplFileInfo {
		return new \SplFileInfo( $file );
	}
}

<?php
declare(strict_types=1);

namespace ItalyStrap\Finder;

/**
 * Trait FinderTrait
 * @package ItalyStrap\Finder
 */
trait FinderTrait
{
	/**
	 * @param array $files
	 * @example:
	 * $files = [
	 * 	'inDir/file-part.php',
	 * 	'inDir/file.php',
	 * 	'file-part.php',
	 * 	'file.php',
	 * ]
	 *
	 * @return string|array Return the first full path to a view found ( full/path/to/a/view.{$extension} )
	 *                      or return an array of files, depend on your implementation.
	 */
	abstract protected function filter( array $files );

	/**
	 * @var array List of full path directory to search
	 */
	protected $dirs = [];

	/**
	 * @var string File extension
	 */
	protected $extension = 'php';

	/**
	 * @var array List of files found
	 */
	protected $files = [];

	/**
	 * @inheritDoc
	 */
	public function count(): int {
		return \count( $this->files );
	}

	/**
	 * @inheritDoc
	 */
	public function in( $dirs ) {
		$this->dirs = (array) $dirs;
		return $this;
	}

	/**
	 * This method generate a unique key for storing a file found in a given directory
	 * With this generated key you can create new criteria for new directory to search on
	 *
	 * @param string  $fileName The name to prefix keys.
	 * @return string           Return the key for array
	 */
	protected function generateKey( string $fileName ): string {
		return $fileName . '-' . \md5( \json_encode( $this->dirs ) ) ;
	}
}
<?php
declare(strict_types=1);

namespace ItalyStrap\Finder;

use ItalyStrap\Finder\Exceptions\FileNotFoundException;
use LogicException;
use function array_pop;
use function count;
use function is_readable;
use function json_encode;
use function ltrim;
use function md5;
use function rtrim;
use function sprintf;
use function str_replace;
use function strval;
use const DIRECTORY_SEPARATOR;

/**
 * Class Finder
 * @package ItalyStrap\Finder
 */
final class Finder implements FinderInterface {

	/**
	 * @var array<string> List of full path directory to search
	 */
	protected $dirs = [];

	/**
	 * @var array<string> List of files found
	 */
	protected $files = [];

	/**
	 * @var SearchFileStrategy
	 */
	private $filter;

	/**
	 * Finder constructor.
	 * @param SearchFileStrategy $filter
	 */
	public function __construct( SearchFileStrategy $filter ) {
		$this->filter = $filter;
	}

	/**
	 * @inheritDoc
	 */
	public function in( $dirs ) {
		$this->dirs = (array) $dirs;
		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function find( $slugs, $extensions = 'php', $slugs_separator = '-' ) {
		$this->assertDirsIsNotEmpty();

		$slugs = (array) $slugs;

		/**
		 * @var array<string> An array of files
		 */
		$files = [];
		$this->generateSlugs( $slugs, $files, (array) $extensions, $slugs_separator );

		$this->searchAndAssertIfHasFile( $files );

		return $this->files[ $this->generateKey( $files[0] ) ];
	}

	/**
	 * @param array<string> $files
	 * @example:
	 * $files = [
	 * 	'inDir/file-part.php',
	 * 	'inDir/file.php',
	 * 	'file-part.php',
	 * 	'file.php',
	 * ]
	 *
	 * @return mixed Return the first full path to a view found ( full/path/to/a/view.{$extension} )
	 *                      or return an array of files, depend on your implementation.
	 */
	private function filter( array $files ) {
		return $this->filter->searchOne( $files, $this->dirs );
	}

	/**
	 * Check if the file exists and is readable
	 *
	 * @param array<string> $files File(s) to search for, in order.
	 * @return bool        Return true if a file exists
	 */
	private function has( array $files ): bool {

		$key = $this->generateKey( $files[0] );

		if ( empty( $this->files[ $key ] ) ) {
			$this->files[ $key ] = $this->filter( $files );
		}

		return \boolval( $this->files[ $key ]  );
	}

	/**
	 * Generate slugs from given array of names
	 * [ 'content', 'name', 'otherName' ]
	 *
	 * [
	 *    'content-name-otherName.php',
	 *    'content-name.php',
	 *    'content.php',
	 * ]
	 *
	 * @param array<string> $slugs
	 * @param array<string> $files
	 * @param array<string> $extensions
	 * @param string $slugs_separator
	 */
	private function generateSlugs(
		array $slugs,
		array &$files,
		array $extensions = ['php'],
		$slugs_separator = '-'
	): void {

		foreach ( $extensions as $extension ) {
			$file_name = '';

			foreach ( $slugs as $slug ) {
				$file_name .= $slugs_separator . $slug;
			}

			$file_name .= '.' . $extension;

			$files[] = ltrim( $file_name, $slugs_separator );
		}

		if ( count( $slugs ) > 1 ) {
			array_pop( $slugs );
			$this->generateSlugs( $slugs, $files, $extensions, $slugs_separator );
		}
	}

	/**
	 * This method generate a unique key for storing a file found in a given directory
	 * With this generated key you can create new criteria for new directory to search on
	 *
	 * @param string  $fileName The name to prefix keys.
	 * @return string           Return the key for array
	 */
	private function generateKey( string $fileName ): string {
		return $fileName . '-' . md5( strval( json_encode( $this->dirs ) ) ) ;
	}

	/**
	 *
	 */
	private function assertDirsIsNotEmpty(): void {
		if ( 0 === count( $this->dirs ) ) {
			throw new LogicException( sprintf(
				'You must call %1$s::in() method before calling %1$s::find() method.',
				__CLASS__
			) );
		}
	}

	/**
	 * @param array<string> $files
	 */
	private function searchAndAssertIfHasFile( array $files ): void {
		if ( !$this->has( $files ) ) {
			throw new FileNotFoundException(
				sprintf( 'The file %s does not exists', \strval( $files[ 0 ] ) )
			);
		}
	}
}

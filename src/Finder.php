<?php
declare(strict_types=1);

namespace ItalyStrap\Finder;

use InvalidArgumentException;
use ItalyStrap\Finder\Exceptions\FileNotFoundException;
use LogicException;
use SplFileInfo;
use function array_filter;
use function array_pop;
use function boolval;
use function call_user_func;
use function count;
use function func_get_args;
use function json_encode;
use function ltrim;
use function md5;
use function sprintf;
use function strval;

/**
 * Class Finder
 * @package ItalyStrap\Finder
 */
final class Finder implements FinderInterface {

	/**
	 * List of files found
	 * [
	 * 		'key'	=> SplFileInfo
	 * ]
	 * [
	 * 		'key'	=> [
	 * 			SplFileInfo,
	 * 			SplFileInfo,
	 * 			SplFileInfo,
	 * 		]
	 * ]
	 *
	 * @var array $files
	 */
	private $files = [];

	/**
	 * @var string[] List of full path directory to search
	 */
	private $dirs = [];

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
	 * @example:
	 * $files = [
	 *    'inDir/file-part.php',
	 *    'inDir/file.php',
	 *    'file-part.php',
	 *    'file.php',
	 * ]
	 * @inheritDoc
	 * @psalm-suppress MixedInferredReturnType
	 */
	public function firstFileBySlugs( $slugs, $extensions = 'php', $slugs_separator = '-' ): SplFileInfo {

		/**
		 * @psalm-suppress MixedReturnStatement
		 */
		return $this->searchOneOrAllFilesBySlugsOnContext(
			$slugs,
			$extensions,
			$slugs_separator,
			[$this, 'filterFirstOneFile']
		);
	}

	/**
	 * @inheritDoc
	 * @psalm-suppress MixedInferredReturnType
	 */
	public function allFilesBySlugs( $slugs, $extensions = 'php', $slugs_separator = '-' ): array {

		/**
		 * @psalm-suppress MixedReturnStatement
		 */
		return $this->searchOneOrAllFilesBySlugsOnContext(
			$slugs,
			$extensions,
			$slugs_separator,
			[$this, 'filterAllFiles']
		);
	}

	/**
	 * @param string|array<string> $slugs Add a slug or an array of slugs for search files
	 * @param string|array<string> $extensions Add a file extension or an array of files extension, Default is php
	 * @param string $slugs_separator
	 * @param callable $method_name
	 * @return mixed
	 */
	private function searchOneOrAllFilesBySlugsOnContext(
		$slugs,
		$extensions,
		string $slugs_separator,
		callable $method_name
	) {
		$this->assertDirsIsNotEmpty();

		$slugs = array_filter( (array) $slugs );

		if ( empty( $slugs ) ) {
			throw new InvalidArgumentException('$slugs must not be empty');
		}

		/** @var array<string> An array of files */
		$files = [];
		$this->generateSlugs( $slugs, $files, (array)$extensions, $slugs_separator );

		$this->searchAndAssertIfHasFile( $files, $method_name );

		/**
		 * @psalm-suppress MixedReturnStatement
		 */
		return $this->files[ $this->generateKey( $files[ 0 ] ) ];
	}

	/**
	 * @param array<string> $files
	 * @return SplFileInfo|string Return the first full path to a view found ( full/path/to/a/view.{$extension} )
	 *                            or return an array of files, depend on your implementation.
	 */
	private function filterFirstOneFile( array $files ) {
		return $this->filter->firstOneFile( $files, $this->dirs );
	}

	/**
	 * @param array<string> $files
	 * @return array
	 */
	private function filterAllFiles( array $files ): array {
		return $this->filter->allFiles( $files, $this->dirs );
	}

	/**
	 * Check if the file exists and is readable
	 *
	 * @param array<string> $files File(s) to search for, in order.
	 * @param callable $method_name
	 * @return bool        Return true if a file exists
	 */
	private function has( array $files, callable $method_name ): bool {

		$key = $this->generateKey( $files[0] );

		if ( empty( $this->files[ $key ] ) ) {
			$this->files[ $key ] = call_user_func( $method_name, $files );
		}

		return boolval( $this->files[ $key ]  );
	}

	/**
	 * @param array<string> $files
	 * @param callable $method_name
	 */
	private function searchAndAssertIfHasFile( array $files, callable $method_name ): void {
		if ( !$this->has( $files, $method_name ) ) {
			throw new FileNotFoundException(
				sprintf( 'The file %s does not exists', strval( $files[ 0 ] ) )
			);
		}
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
		array $extensions,
		string $slugs_separator
	): void {

		foreach ( $extensions as $extension ) {
			$file_name = '';

			foreach ( $slugs as $slug ) {
				$file_name .= $slugs_separator . $slug;
			}

			$file_name .= '.' . $extension;

			$files[] = ltrim( $file_name, $slugs_separator );
		}

		if ( count( $slugs ) >= 2 ) {
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
				'You must call %1$s::in() method before calling %1$s::firstFileReadable() method.',
				__CLASS__
			) );
		}
	}

	/**
	 * @param string|array<string> $slugs Add a slug or an array of slugs for search files
	 * @param string|array<string> $extensions Add a file extension or an array of files extension, Default is php
	 * @param string $slugs_separator
	 * @return SplFileInfo Return a full path of the file searched
	 * @deprecated
	 */
	public function find( $slugs, $extensions = 'php', $slugs_separator = '-' ): SplFileInfo {
		trigger_error( sprintf(
			'The method %2$s() is deprecated, use %1$s::firstFileBySlugs() instead.',
			__CLASS__,
			__METHOD__
		), E_USER_NOTICE);
		/**
		 * @psalm-suppress MixedArgument
		 */
		return $this->firstFileBySlugs(...func_get_args());
	}
}

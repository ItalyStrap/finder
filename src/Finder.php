<?php
declare(strict_types=1);

namespace ItalyStrap\Finder;

use InvalidArgumentException;
use ItalyStrap\Finder\Exceptions\InvalidStateException;
use ItalyStrap\Finder\Exceptions\FileNotFoundException;
use LogicException;
use SplFileInfo;
use function array_filter;
use function array_pop;
use function boolval;
use function call_user_func;
use function count;
use function implode;
use function json_encode;
use function md5;
use function sprintf;
use function strval;

/**
 * Class Finder
 * @package ItalyStrap\Finder
 */
final class Finder implements FinderInterface, \IteratorAggregate {

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
	 * @var array $files_to_search
	 */
	private $files_to_search = [];

	/**
	 * @var string[]
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

		if ( $this->dirs ) {
			throw new InvalidStateException('Directory to search has already been specified.');
		}

		$this->dirs = (array) $dirs;
		$this->filter->in( $this->dirs );
		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function names( $files ): void {
		$this->files = (array) \array_replace_recursive( $this->files, (array) $files );
		$this->filter->names( $this->files );
	}

	/**
	 * @inheritDoc
	 */
	public function getIterator() {
		return $this->filter->getIterator();
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
	public function firstFile( $segments, $extensions = 'php', $segments_separator = '-' ): SplFileInfo {

		/**
		 * @psalm-suppress MixedReturnStatement
		 */
		return $this->searchOneOrAllFilesBySegments(
			$segments,
			$extensions,
			$segments_separator,
			[$this, 'filterFirstOneFile']
		);
	}

	/**
	 * @inheritDoc
	 * @psalm-suppress MixedInferredReturnType
	 */
	public function allFiles( $segments, $extensions = 'php', $segments_separator = '-' ): array {

		/**
		 * @psalm-suppress MixedReturnStatement
		 */
		return $this->searchOneOrAllFilesBySegments(
			$segments,
			$extensions,
			$segments_separator,
			[$this, 'filterAllFiles']
		);
	}

	/**
	 * @param string|array<string> $segments Add a segment or an array of segments for search files
	 * @param string|array<string> $extensions Add a file extension or an array of files extension, Default is php
	 * @param string $segments_separator
	 * @param callable $method_name
	 * @return mixed
	 */
	private function searchOneOrAllFilesBySegments(
		$segments,
		$extensions,
		string $segments_separator,
		callable $method_name
	) {
		$this->assertDirsIsNotEmpty();

		$segments = array_filter( (array) $segments );

		$files = $this->generateFileNames( $segments,(array)$extensions, $segments_separator );
		$this->names( $files );

		$this->searchAndAssertIfHasFile( $method_name );

		/**
		 * @psalm-suppress MixedReturnStatement
		 */
		return $this->files_to_search[ $this->generateKey( $this->files[ 0 ] ) ];
	}

	/**
	 * @return SplFileInfo|string Return the first full path to a view found ( full/path/to/a/view.{$extension} )
	 *                            or return an array of files, depend on your implementation.
	 */
	private function filterFirstOneFile() {
		return $this->filter->firstFile();
	}

	/**
	 * @return array
	 */
	private function filterAllFiles(): array {
		return \iterator_to_array( $this->getIterator() );
	}

	/**
	 * Check if the file exists and is readable
	 *
	 * @param callable $method_name
	 * @return bool        Return true if a file exists
	 */
	private function has( callable $method_name ): bool {

		$key = $this->generateKey( $this->files[0] );

		if ( empty( $this->files_to_search[ $key ] ) ) {
			$this->files_to_search[ $key ] = call_user_func( $method_name );
		}

		return boolval( $this->files_to_search[ $key ]  );
	}

	/**
	 * @param callable $method_name
	 */
	private function searchAndAssertIfHasFile( callable $method_name ): void {
		if ( !$this->has( $method_name ) ) {
			throw new FileNotFoundException(
				sprintf( 'The file "%s" does not exists', \implode('" and "', $this->files) )
			);
		}
	}

	/**
	 * Generate slugs from a given array of segments
	 * [ 'content', 'name', 'otherName' ]
	 *
	 * [
	 *    'content-name-otherName.php',
	 *    'content-name.php',
	 *    'content.php',
	 * ]
	 *
	 * @param array<string> $segments
	 * @param array<string> $extensions
	 * @param string $segments_separator
	 * @return array
	 */
	private function generateFileNames(
		array $segments,
		array $extensions,
		string $segments_separator
	) {

		$files = [];

		if ( empty( $segments ) ) {
			throw new InvalidArgumentException('$segments must not be empty');
		}

		foreach ( $extensions as $extension ) {
			$files[] = implode($segments_separator, $segments) . '.' . $extension;
		}

		if ( count( $segments ) >= 2 ) {
			array_pop( $segments );
			$files = \array_merge($files, $this->generateFileNames( $segments, $extensions, $segments_separator ));
		}

		return $files;
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
	 * @param string|array<string> $segments Add a segment or an array of segments for search files
	 * @param string|array<string> $extensions Add a file extension or an array of files extension, Default is php
	 * @param string $segments_separator
	 * @return SplFileInfo Return a full path of the file searched
	 * @deprecated
	 */
//	public function find( $segments, $extensions = 'php', $segments_separator = '-' ): SplFileInfo {
//		trigger_error( sprintf(
//			'The method %2$s() is deprecated, use %1$s::firstFileBySlugs() instead.',
//			__CLASS__,
//			__METHOD__
//		), E_USER_NOTICE);
//		/**
//		 * @psalm-suppress MixedArgument
//		 */
//		return $this->firstFileBySlugs(...func_get_args());
//	}
}

<?php
declare(strict_types=1);

namespace ItalyStrap\Tests\Benchmark;

use ItalyStrap\Finder\FileInfoFactory;
use ItalyStrap\Finder\FilesHierarchyIterator;
use function realpath;

/**
 * @BeforeMethods({"init"})
 */
class SearchFileBench {

	/**
	 * @var FilesHierarchyIterator
	 */
	private $searchFilesHierarchy;

	private $dir_test_path;

	/**
	 * @var string
	 */
	private $file_full_path;

	private $dir;

	/**
	 * @var string
	 */
	private $file_name;

	public function init() {
		// phpcs:ignore
//		\tad\FunctionMockerLe\define('set_transient', function ($key, $value, $ttl) {
//			$this->store[ $key ] = $value;
//			return true;
//		});

		$this->searchFilesHierarchy = new FilesHierarchyIterator( new FileInfoFactory() );

		$this->dir_test_path = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..');
		$this->file_full_path = $this->dir_test_path . '\_data\fixtures\plugin\test.php';

		$this->dir = $this->dir_test_path . '\_data\fixtures\plugin';
		$this->file_name = 'test.php';
	}

	/**
	 * @Warmup(2)
	 * @Revs(1000)
	 * @Iterations(10)
	 */
	public function benchWithRealPathFunc() {
		foreach ( ['test.php'] as $file ) {
			foreach ( [$this->dir] as $dir ) {
				$temp_file = $dir . DIRECTORY_SEPARATOR . $file;
				$temp_file = str_replace( ['/', '\\'], DIRECTORY_SEPARATOR, $temp_file );
				$temp_file = \strval(realpath( $temp_file ));
				if ( is_readable( $temp_file ) ) {
					return $temp_file;
				}
			}
		}

		return '';
	}

	/**
	 * @Warmup(2)
	 * @Revs(1000)
	 * @Iterations(10)
	 */
	public function benchWithStrReplaceFunc() {

		foreach ( ['test.php'] as $file ) {
			foreach ( [$this->dir] as $dir ) {
				$dir = rtrim( $dir, '/\\' );
				$temp_file = $dir . DIRECTORY_SEPARATOR . $file;
				// We need this for Windows and Linux compatibility
				$temp_file = str_replace( ['/', '\\'], DIRECTORY_SEPARATOR, $temp_file );
				if ( is_readable( $temp_file ) ) {
					return $temp_file;
				}
			}
		}

		return '';
	}

	/**
	 * @Warmup(2)
	 * @Revs(1000)
	 * @Iterations(10)
	 */
	public function benchCodedSearch() {
		$this->searchFilesHierarchy->names(['test.php']);
		$this->searchFilesHierarchy->in([$this->dir]);
		$this->searchFilesHierarchy->firstFile();
	}
}

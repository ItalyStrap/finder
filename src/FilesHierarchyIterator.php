<?php
declare(strict_types=1);

namespace ItalyStrap\Finder;

use SplFileInfo;
use function realpath;
use function str_replace;
use function strval;

final class FilesHierarchyIterator implements SearchFileStrategy {

	const DS = DIRECTORY_SEPARATOR;

	/**
	 * @var FileInfoFactoryInterface
	 */
	private $factory;

	/**
	 * @var array
	 */
	private $dirs;

	/**
	 * @var array
	 */
	private $names;

	/**
	 * @var bool
	 */
	private $only_first_file;

	/**
	 * @inheritDoc
	 */
	public function in( array $dirs ) {
		$this->dirs = $dirs;
	}

	/**
	 * @inheritDoc
	 */
	public function names( array $names ) {
		$this->names = $names;
	}

	/**
	 * @inheritDoc
	 */
	public function onlyFirstFile() {
		$this->only_first_file = true;
	}

	/**
	 * SearchFilesHierarchy constructor.
	 * @param FileInfoFactoryInterface $factory
	 */
	public function __construct( FileInfoFactoryInterface $factory ) {
		$this->factory = $factory;
	}

	/**
	 * @inheritDoc
	 */
	public function firstFile() {
		foreach ( $this->getNames() as $file ) {
			foreach ( $this->getDirs() as $dir ) {
				$temp_file = $this->getFileInfo( $dir, $file );
				if ( $temp_file->isReadable() ) {
					return $temp_file;
				}
			}
		}

		return '';
	}

	/**
	 * @return \ArrayIterator
	 */
	private function buildIterator() {
		$iterator = new \ArrayIterator();
		foreach ($this->getNames() as $file) {
			foreach ($this->getDirs() as $dir) {
				$temp_file = $this->getFileInfo( $dir, $file );

				if ( $temp_file->isReadable() ) {
					$iterator->append( $temp_file );
				}

				if ( $this->only_first_file && $iterator->count() === 1 ) {
					return $iterator;
				}
			}
		}

		return $iterator;
	}

	/**
	 * @inheritDoc
	 */
	public function getIterator() {
		return $this->buildIterator();
	}

	/**
	 * @inheritDoc
	 */
	public function count() {
		return iterator_count($this->getIterator());
	}

	/**
	 * @param string $dir
	 * @param string $file
	 * @return string
	 */
	private function getRealPathOfFile( string $dir, string $file ): string {
		return strval(
			realpath(
				str_replace( '\\', DIRECTORY_SEPARATOR, $dir . self::DS . $file )
			)
		);
	}

	/**
	 * @param string $dir
	 * @param string $file
	 * @return SplFileInfo
	 */
	private function getFileInfo( $dir, $file ): SplFileInfo {
		return $this->factory->make( $this->getRealPathOfFile( $dir, $file ) );
	}

	/**
	 * @return mixed
	 */
	private function getDirs() {

		if ( empty( $this->dirs ) ) {
			throw new \LogicException('You must call ::in() method before iterate over ');
		}

		return $this->dirs;
	}

	/**
	 * @return mixed
	 */
	private function getNames() {

		if ( empty( $this->names ) ) {
			throw new \LogicException('You must call ::name() method before iterate over ');
		}

		return $this->names;
	}
}

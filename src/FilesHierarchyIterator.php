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
	 * @var string[]
	 */
	private $dirs = [];

	/**
	 * @var string[]
	 */
	private $names = [];

	/**
	 * @inheritDoc
	 */
	final public function in( array $dirs ): void {
		$this->dirs = $dirs;
	}

	/**
	 * @inheritDoc
	 */
	final public function names( array $names ): void {
		$this->names = $names;
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
	 * @return \Generator
	 */
	private function buildIterator() {
		/** @var string $file */
		foreach ($this->getNames() as $file) {
			/** @var string $dir */
			foreach ($this->getDirs() as $dir) {
				$temp_file = $this->getFileInfo( $dir, $file );
				if ( $temp_file->isReadable() ) {
					yield $temp_file;
				}
			}
		}
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

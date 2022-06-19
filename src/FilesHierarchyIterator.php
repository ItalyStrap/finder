<?php
declare(strict_types=1);

namespace ItalyStrap\Finder;

use SplFileInfo;
use function defined;
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
	private function buildIterator(): \Generator {
		foreach ( $this->buildFilesList() as $file_path ) {
			$glob_flags = (defined('GLOB_BRACE') ? \GLOB_BRACE : 0);
			foreach ((array) \glob( $file_path, $glob_flags ) as $file_found ) {
				$temp_file = $this->factory->make( $file_found );
				if ( $temp_file->isReadable() ) {
					yield $temp_file;
				}
			}
		}
	}

	private function buildFilesList(): \Generator {
		/** @var string $file */
		foreach ($this->getNames() as $file) {
			/** @var string $dir */
			foreach ($this->getDirs() as $dir) {
				yield $this->normalizeFilePath( $dir, $file );
			}
		}
	}

	/**
	 * @param string $dir
	 * @param string $file
	 * @return SplFileInfo
	 */
//	private function getFileInfo( $dir, $file ): SplFileInfo {
//		return $this->factory->make( $this->getRealPathOfFile( $dir, $file ) );
//	}

	/**
	 * @param string $dir
	 * @param string $file
	 * @return string
	 */
//	private function getRealPathOfFile( string $dir, string $file ): string {
//		return (string) realpath(
//			$this->normalizeFilePath( $dir, $file )
//		);
//	}

	/**
	 * @return array
	 */
	private function getDirs(): array {

		if ( empty( $this->dirs ) ) {
			throw new \LogicException('You must call ::in() method before iterate over ');
		}

		return $this->dirs;
	}

	/**
	 * @return string[]
	 */
	private function getNames(): array {

		if ( empty( $this->names ) ) {
			throw new \LogicException('You must call ::name() method before iterate over ');
		}

		return $this->names;
	}

	private function normalizeFilePath( string $dir, string $file ) {
		return str_replace( '\\', DIRECTORY_SEPARATOR, $dir . DIRECTORY_SEPARATOR . $file );
	}
}

<?php
declare(strict_types=1);

namespace ItalyStrap\Finder;

use function is_readable;
use function realpath;

final class SearchFilesHierarchy implements SearchFileStrategy {

	const DS = DIRECTORY_SEPARATOR;
	/**
	 * @var FileInfoFactoryInterface
	 */
	private $factory;

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
	public function first( array $file_names, array $dirs ) {
		foreach ( $file_names as $file ) {
			foreach ( $dirs as $dir ) {
				$temp_file = $this->getFileInfo( $dir, $file );
				if ( $temp_file->isReadable() ) {
					return $temp_file;
				}
			}
		}

		return '';
	}

	/**
	 * @inheritDoc
	 */
	public function all( array $file_names, array $dirs ): array {
		$all = [];
		foreach ( $file_names as $file ) {
			foreach ( $dirs as $dir ) {
				$temp_file = $this->getFileInfo( $dir, $file );
				if ( $temp_file->isReadable() ) {
					$all[] = $temp_file;
				}
			}
		}

		return $all;
	}

	/**
	 * @param string $dir
	 * @param string $file
	 * @return string
	 */
	private function getRealPathOfFile( string $dir, string $file ): string {
		return \strval( realpath( $dir . self::DS . $file ) );
	}

	/**
	 * @param $dir
	 * @param $file
	 * @return \SplFileInfo
	 */
	private function getFileInfo( $dir, $file ): \SplFileInfo {
		return $this->factory->make( $this->getRealPathOfFile( $dir, $file ) );
	}
}

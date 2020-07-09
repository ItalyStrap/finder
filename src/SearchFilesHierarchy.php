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
	public function searchOne( array $file_names, array $dirs ) {
		foreach ( $file_names as $file ) {
			foreach ( $dirs as $dir ) {
				$temp_file = $this->getRealPathOfFile( $dir, $file );
				if ( is_readable( $temp_file ) ) {
					return $this->factory->make( $temp_file );
				}
			}
		}

		return '';
	}

	/**
	 * @inheritDoc
	 */
	public function searchAll( array $file_names, array $dirs ): array {
		$all = [];
		foreach ( $file_names as $file ) {
			foreach ( $dirs as $dir ) {
				$temp_file = $this->getRealPathOfFile( $dir, $file );
				if ( is_readable( $temp_file ) ) {
					$all[] = $this->factory->make( $temp_file );
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
}

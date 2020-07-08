<?php
declare(strict_types=1);

namespace ItalyStrap\Finder;

use function is_readable;
use function realpath;

class SearchFilesHierarchy implements SearchFileStrategy {

	const DS = DIRECTORY_SEPARATOR;

	public function search( array $file_names, array $dirs ): string {

		foreach ( $file_names as $file ) {
			foreach ( $dirs as $dir ) {
				$temp_file = \strval( realpath( $dir . self::DS . $file ) );
				if ( is_readable( $temp_file ) ) {
					return $temp_file;
				}
			}
		}

		return '';
	}

	public function searchAll( array $file_names, array $dirs ): array {
		$all = [];
		foreach ( $file_names as $file ) {
			foreach ( $dirs as $dir ) {
				$temp_file = \strval( realpath( $dir . self::DS . $file ) );
				if ( is_readable( $temp_file ) ) {
					$all[] = $temp_file;
				}
			}
		}

		return $all;
	}
}

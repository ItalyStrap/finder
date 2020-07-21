<?php
declare(strict_types=1);

namespace ItalyStrap\Tests\Unit;

use ItalyStrap\View\CallbackViewFinder;

include_once 'BaseViewFinderTestUnit.php';
class CallbackViewFinderTest extends BaseViewFinderTestUnit
{

	protected function setType() {
		return CallbackViewFinder::class;
	}

	protected function setArgs() {
		return function (  $files, $dirs  ) {
			foreach ( $files as $file ) {
				foreach ( $dirs as $dir ) {
					$dir = \rtrim( $dir, '/\\' );
					$temp_file = $dir . \DIRECTORY_SEPARATOR . $file;
					// We need this for Windows and Linux compatibility
					$temp_file = \str_replace( ['/', '\\'], \DIRECTORY_SEPARATOR, $temp_file );
					if ( \is_readable( $temp_file ) ) {
						return $temp_file;
					}
				}
			}

			return '';
		};
	}

	/**
	 * @test
	 */
	public function it_should_be_Instantiable()
	{
		$finder = $this->getInstance();
		$this->assertInstanceOf( \ItalyStrap\View\CallbackViewFinder::class, $finder );
	}
}
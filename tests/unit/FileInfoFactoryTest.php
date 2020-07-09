<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use ItalyStrap\Finder\Exceptions\FileNotFoundException;
use ItalyStrap\Finder\FileInfoFactory;

class FileInfoFactoryTest extends \Codeception\Test\Unit {

	/**
	 * @var \UnitTester
	 */
	protected $tester;

	/**
	 * @return array
	 */
	private function getPaths(): array {
		return $this->tester->fixturesPaths();
	}

	// phpcs:ignore -- Method from Codeception
    protected function _before() {
		foreach ($this->getPaths() as $path ) {
			$this->assertDirectoryExists($path, '');
		}
	}

	// phpcs:ignore -- Method from Codeception
    protected function _after() {
	}

	/**
	 * @return FileInfoFactory
	 */
	private function getInstance(): FileInfoFactory {
		$sut = new FileInfoFactory();
		$this->assertInstanceOf( FileInfoFactory::class, $sut, '' );
		return $sut;
	}

	/**
	 * @test
	 */
	public function instanceOk() {
		return $this->getInstance();
	}

	/**
	 * @test
	 */
	public function canCreateInstanceOfSplFileInfo() {
		$sut = $this->getInstance();

		$dir = $this->getPaths()[$this->tester::PLUGIN_PATH];

		$expected = $dir . DIRECTORY_SEPARATOR . 'test.php';

		$fileInfo = $sut->make( $expected );

		$this->assertEquals( $expected, $fileInfo, '' );
		$this->assertInstanceOf(\SplFileInfo::class, $fileInfo, '');
	}
}

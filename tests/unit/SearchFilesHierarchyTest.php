<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use Codeception\Test\Unit;
use ItalyStrap\Finder\SearchFilesHierarchy;
use UnitTester;

class SearchFilesHierarchyTest extends Unit {

	/**
	 * @var UnitTester
	 */
	protected $tester;

	/**
	 * @return array
	 */
	private function getFixturesPaths(): array {
		return $this->tester->fixturesPaths();
	}

	/**
	 * @return array
	 */
	private function path( $key ): string {
		return $this->tester->fixturesPaths()[$key];
	}

	// phpcs:ignore -- Method from Codeception
	protected function _before() {
		foreach ($this->getFixturesPaths() as $path ) {
			$this->assertDirectoryExists($path, '');
		}
	}

	// phpcs:ignore -- Method from Codeception
	protected function _after() {
	}

	/**
	 * @return \ItalyStrap\Finder\SearchFilesHierarchy
	 */
	private function getInstance(): \ItalyStrap\Finder\SearchFilesHierarchy {
		$sut = new \ItalyStrap\Finder\SearchFilesHierarchy();
		$this->assertInstanceOf( SearchFilesHierarchy::class, $sut, '' );
		return $sut;
	}

	/**
	 * @test
	 */
	public function instanceOk() {
		$sut = $this->getInstance();
	}

	public function pathProvider() {
		return [
			'01'	=> [
				'test.php'
			],
			'02'	=> [
				'/test.php'
			],
			'03'	=> [
				'\test.php'
			],
			'04'	=> [
				'\/test.php'
			],
			'05'	=> [
				'\//test.php'
			],
			'06'	=> [
				'\/////////test.php'
			],
		];
	}

	/**
	 * @test
	 * @dataProvider pathProvider()
	 */
	public function itShouldSearch( $file ) {
		$sut = $this->getInstance();
		$dir = $this->path($this->tester::PLUGIN_PATH);

		$expected = $dir . DIRECTORY_SEPARATOR . $file;

		$file_name_found = $sut->search( [ $file ], [$this->path($this->tester::PLUGIN_PATH)] );

		$this->assertEquals($file_name_found, \realpath( $expected ), '');
	}

	/**
	 * @test
	 */
	public function itShouldReturnEmptyValueIfFileDoesNotExist() {
		$sut = $this->getInstance();

		$file_name_found = $sut->search(
			[ 'unreadable' ],
			[$this->path($this->tester::PLUGIN_PATH)]
		);

		$this->assertEmpty($file_name_found, '');
	}

	/**
	 * @test
	 */
	public function itShouldFindAsset() {
		$sut = $this->getInstance();

		$expected = \realpath(
			$this->path($this->tester::PLUGIN_PATH) . '/assets/css/style.css'
		);

		$file_name_found = $sut->search(
			[ 'style.css' ],
			[$this->path($this->tester::PLUGIN_PATH) . '/assets/css/']
		);

		$this->assertEquals($expected, $file_name_found, '');
	}

	/**
	 * @test
	 */
	public function itShouldFindAllFiles() {
		$sut = $this->getInstance();

		$expected = [];

		$expected[] = \realpath(
			$this->path($this->tester::PLUGIN_PATH) . '/test.php'
		);
		$expected[] = \realpath(
			$this->path($this->tester::PLUGIN_PATH) . '/config.php'
		);

		$files_found = $sut->searchAll(
			[
				'config.php'
			],
			$this->getFixturesPaths()
		);

		codecept_debug($files_found);

		$this->assertIsArray($files_found, '');

//		$this->assertContains($expected[0], $files_found, '');
		$this->assertContains($expected[1], $files_found, '');
	}
}

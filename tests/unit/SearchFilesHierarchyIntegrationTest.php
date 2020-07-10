<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use Codeception\Test\Unit;
use ItalyStrap\Finder\FileInfoFactory;
use ItalyStrap\Finder\SearchFilesHierarchy;
use ItalyStrap\Finder\SearchFileStrategy;

class SearchFilesHierarchyIntegrationTest extends Unit
{

	/**
	 * @var UnitTester
	 */
	protected $tester;

	/**
	 * @return array
	 */
	private function getPaths(): array {
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
		foreach ($this->getPaths() as $path ) {
			$this->assertDirectoryExists($path, '');
		}
	}

	// phpcs:ignore -- Method from Codeception
	protected function _after() {
	}

	/**
	 * @return SearchFilesHierarchy
	 */
	private function getInstance(): SearchFilesHierarchy {
		$sut = new SearchFilesHierarchy( new FileInfoFactory() );
		$this->assertInstanceOf( SearchFileStrategy::class, $sut, '' );
		$this->assertInstanceOf( SearchFilesHierarchy::class, $sut, '' );
		return $sut;
	}

	/**
	 * @test
	 */
	public function instanceOk() {
		$sut = $this->getInstance();
	}

	public function filesProvider() {
		$plugin_path = codecept_data_dir( 'fixtures/plugin' );
		$child_path = codecept_data_dir( 'fixtures/child' );
		$parent_path = codecept_data_dir( 'fixtures/parent' );

		$paths = [
			$plugin_path,
			$child_path,
			$parent_path,
		];

		return [
			'content from child'	=> [
				'content.php',
				$paths,
				$child_path . '/content.php',
			],
			'content from child 01'	=> [
				['content-ttt.php','content.php'],
				$paths,
				$child_path . '/content.php',
			],
			'content-single from child'	=> [
				'content-single.php',
				$paths,
				$child_path . '/content-single.php',
			],
			'content-none from parent'	=> [
				'content-none.php',
				$paths,
				$parent_path . '/content-none.php',
			],
			'config from plugin'	=> [
				'config.php',
				$paths,
				$plugin_path . '/config.php',
			],
		];
	}

	/**
	 * @test
	 * @dataProvider filesProvider()
	 */
	public function itShouldFind( $file, array $paths, string $expected ) {
		$expected = \strval( \realpath( $expected ) );
		$this->assertIsReadable($expected, '');

		$sut = $this->getInstance();

		/** @var \SplFileInfo $file_name_found */
		$file_name_found = $sut->first(
			(array) $file,
			$paths
		);

		$this->assertEquals($expected, $file_name_found->getRealPath(), '');
	}

	/**
	 * @test
	 */
	public function itShouldFindAllConfigFiles() {
		$sut = $this->getInstance();

		$expected = [
			$this->path($this->tester::PLUGIN_PATH) . '/config.php',
			$this->path($this->tester::CHILD_PATH) . '/config.php',
			$this->path($this->tester::PARENT_PATH) . '/config.php',
		];

		$expected = \array_map('realpath', $expected);

		$files_found = $sut->all(
			[
				'config.php'
			],
			$this->getPaths()
		);

		$this->assertIsArray($files_found, '');

		foreach ( $expected as $expect ) {
			$this->assertContains($expect, $files_found, '');
		}
	}
}

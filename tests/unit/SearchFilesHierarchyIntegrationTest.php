<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use Codeception\Test\Unit;
use ItalyStrap\Finder\FileInfoFactory;
use ItalyStrap\Finder\FilesHierarchyIterator;
use ItalyStrap\Finder\SearchFileStrategy;
use SplFileInfo;
use UnitTester;
use function array_map;
use function realpath;
use function str_replace;
use function strval;

class SearchFilesHierarchyIntegrationTest extends Unit {

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
	 * @return FilesHierarchyIterator
	 */
	private function getInstance(): FilesHierarchyIterator {
		$sut = new FilesHierarchyIterator( new FileInfoFactory() );
		$this->assertInstanceOf( SearchFileStrategy::class, $sut, '' );
		$this->assertInstanceOf( FilesHierarchyIterator::class, $sut, '' );
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

		$ds = DIRECTORY_SEPARATOR;

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
				['content-single.php','content.php'],
				$paths,
				$child_path . '/content-single.php',
			],
			'content-none from parent'	=> [
				['no-file', 'content-none.php'],
				$paths,
				$parent_path . '/content-none.php',
			],
			'config from plugin'	=> [
				'config.php',
				$paths,
				$plugin_path . '/config.php',
			],
			'style from child'	=> [
				'assets/css/style.css',
				$paths,
				$plugin_path . '/assets/css/style.css',
			],
			'js from child'	=> [
				'assets/js/script.js',
				$paths,
				$plugin_path . '/assets/js/script.js',
			],
			'custom.css from child'	=> [
				'assets/css/custom.css',
				$paths,
				$child_path . '/assets/css/custom.css',
			],
			'custom.js from child'	=> [
				'assets/js/custom.js',
				$paths,
				$child_path . '/assets/js/custom.js',
			],
		];
	}

	/**
	 * @test
	 * @dataProvider filesProvider()
	 */
	public function itShouldFind( $file, array $paths, string $expected ) {
		$expect = $expected;
		$expected = strval( realpath( $expected ) );
		$this->assertIsReadable($expected, 'Path not found: ' . $expect);

		$sut = $this->getInstance();
		$sut->in( $paths );
		$sut->names( (array) $file );

//		$sut->onlyFirstFile();
//		$this->assertCount(1, $sut, '');

		/** @var SplFileInfo $file_name_found */
		$file_name_found = $sut->firstFile();
//
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

		$expected = array_map(function (string $path) {
			return strval( realpath( $path ) );
		}, $expected);

		$sut->in( $this->getPaths() );
		$sut->names( [ 'config.php' ] );

		/** @var \SplFileInfo $item */
		foreach ( $sut as $key => $item ) {
			$this->assertInstanceOf( SplFileInfo::class, $item, '' );
			$this->assertSame($expected[$key], $item->getRealPath(), '');
		}
	}

	public function pathProvider() {
		return [
			'no dir separator'	=> [
				'test.php'
			],
			'one separator'	=> [
				'/test.php'
			],
			'one back separator'	=> [
				'\test.php'
			],
			'normal and back separator'	=> [
				'\/test.php'
			],
			'more separators'	=> [
				'\//test.php'
			],
			'many separator'	=> [
				'\\\\/////////test.php'
			],
			'many separator and dot'	=> [
				'.\/////////test.php'
			],
		];
	}

	/**
	 * @test
	 * @dataProvider pathProvider()
	 * @param $file
	 */
	public function itShouldSearchAndReturnTheCorrectFilePathEvenIfFileNameContains( $file ) {
//		$dir = $this->path($this->tester::PLUGIN_PATH);
		$dir = codecept_data_dir( 'fixtures/plugin' );
		$expected = realpath( $dir . DIRECTORY_SEPARATOR . 'test.php' );
		$real_path = strval( realpath(
//			$dir . DIRECTORY_SEPARATOR . $file
			str_replace( ['/', '\\'], DIRECTORY_SEPARATOR, $dir . DIRECTORY_SEPARATOR . $file )
		) );
		$this->assertIsReadable($expected, '');
		$this->assertIsReadable($real_path, '');

		$sut = $this->getInstance();
		$sut->in( [$dir] );
		$sut->names( (array) $file );

		/**
		 * @var $file_name_found SplFileInfo
		 */
		$file_name_found = $sut->firstFile();
		$this->assertEquals($expected, $file_name_found->getRealPath(), '');
		$this->assertInstanceOf( SplFileInfo::class, $file_name_found, '');

		$this->expectOutputString($expected);
		require $file_name_found->getRealPath();
	}

	/**
	 * @test
	 */
	public function itShouldGetIterator() {
		$sut = $this->getInstance();
		$this->assertInstanceOf(\IteratorAggregate::class, $sut, '');
		$this->assertInstanceOf(\Traversable::class, $sut, '');

		$sut->in( [ codecept_data_dir( 'fixtures/plugin' ) ] );
		$sut->names( ['test.php'] );

		$iterator = $sut->getIterator();
		$this->assertInstanceOf(\Iterator::class, $iterator, '');
	}

	/**
	 * @test
	 */
	public function itShouldIterateOverIterator() {
		$sut = $this->getInstance();
		$sut->in( [ codecept_data_dir( 'fixtures/plugin' ) ] );
		$sut->names( ['test.php'] );

		/** @var \SplFileInfo $item */
		foreach ( $sut as $item ) {
			$this->assertSame('test.php', $item->getFilename(), '');
		}
	}
}

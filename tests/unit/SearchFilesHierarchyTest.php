<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use Codeception\Test\Unit;
use ItalyStrap\Finder\FileInfoFactory;
use ItalyStrap\Finder\SearchFilesHierarchy;
use ItalyStrap\Finder\SearchFileStrategy;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use SplFileInfo;
use UnitTester;
use function is_readable;
use function realpath;

class SearchFilesHierarchyTest extends Unit {

	/**
	 * @var UnitTester
	 */
	protected $tester;

	/**
	 * @var ObjectProphecy
	 */
	private $file_info_factory;

	/**
	 * @return FileInfoFactory
	 */
	public function getFileInfoFactory(): FileInfoFactory {
		return $this->file_info_factory->reveal();
	}

	/**
	 * @var ObjectProphecy
	 */
	private $file_info_fake;

	/**
	 * @return SplFileInfo
	 */
	public function getFileInfoFake(): SplFileInfo {
		return $this->file_info_fake->reveal();
	}

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

		$this->file_info_factory = $this->prophesize( FileInfoFactory::class );
		$this->file_info_fake = $this->prophesize( SplFileInfo::class );
	}

	// phpcs:ignore -- Method from Codeception
	protected function _after() {
	}

	/**
	 * @return SearchFilesHierarchy
	 */
	private function getInstance(): SearchFilesHierarchy {
		$sut = new SearchFilesHierarchy( $this->getFileInfoFactory() );
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
	 */
	public function itShouldSearchAndReturnTheCorrectFilePathEvenIfFileNameContains( $file ) {
		$dir = $this->path($this->tester::PLUGIN_PATH);
		$expected = \strval( realpath( $dir . DIRECTORY_SEPARATOR . 'test.php' ) );
		$real_path = \strval( realpath( $dir . DIRECTORY_SEPARATOR . $file ) );

		$this->file_info_fake->isReadable()->willReturn(
			is_readable( $real_path )
		);

		$this->file_info_fake->__toString()->willReturn(
			$real_path
		);

		$this->file_info_fake->getRealPath()->willReturn(
			$real_path
		);

		$this->file_info_factory
			->make( Argument::type('string') )
			->willReturn( $this->getFileInfoFake() )->shouldBeCalled(1);

		$sut = $this->getInstance();

		/**
		 * @var $file_name_found SplFileInfo
		 */
		$file_name_found = $sut->firstOneFile( (array) $file, [$dir] );
		$this->assertEquals($file_name_found, $expected, '');
		$this->assertInstanceOf(\SplFileInfo::class, $file_name_found, '');

		$this->expectOutputString($expected);
		require $file_name_found->getRealPath();
	}

	/**
	 * @test
	 */
	public function itShouldReturnEmptyValueIfFileDoesNotExist() {
		$dir = $this->path($this->tester::PLUGIN_PATH);

		$this->file_info_fake->isReadable()->willReturn(
			false
		);

		$this->file_info_fake->getRealPath()->shouldNotBeCalled();

		$this->file_info_factory
			->make( Argument::type('string') )
			->willReturn( $this->getFileInfoFake() )->shouldBeCalled(1);


		$sut = $this->getInstance();

		$file_name_found = $sut->firstOneFile(
			[ 'unreadable' ],
			[$dir]
		);

		$this->assertEmpty($file_name_found, '');
		$this->assertEquals('', $file_name_found, '');
	}
}

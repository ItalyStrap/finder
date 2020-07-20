<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use Codeception\Test\Unit;
use ItalyStrap\Finder\FileInfoFactory;
use ItalyStrap\Finder\FilesHierarchyIterator;
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
	 * @return FilesHierarchyIterator
	 */
	private function getInstance(): FilesHierarchyIterator {
		$sut = new FilesHierarchyIterator( $this->getFileInfoFactory() );
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

	/**
	 * @test
	 */
	public function itShouldThrownExceptionIfNameIsNotCalled() {
		$sut = $this->getInstance();
		$this->expectException( \LogicException::class );
		$this->expectExceptionMessage( 'You must call ::name() method before iterate over' );
		$sut->getIterator();
	}

	/**
	 * @test
	 */
	public function itShouldThrownExceptionIfInIsNotCalled() {
		$sut = $this->getInstance();
		$sut->names(['test.php']);
		$this->expectException( \LogicException::class );
		$this->expectExceptionMessage( 'You must call ::in() method before iterate over' );
		$sut->getIterator();
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
	 * @dataProvider pathProvider()
	 */
	public function itShouldSearchAndReturnTheCorrectFilePathEvenIfFileNameContains( $file ) {
//		$dir = $this->path($this->tester::PLUGIN_PATH);
		$dir = codecept_data_dir( 'fixtures/plugin' );
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
		$sut->names((array) $file);
		$sut->in([$dir]);

		/**
		 * @var $file_name_found SplFileInfo
		 */
		$file_name_found = $sut->firstFile();
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
		$sut->in([$dir]);
		$sut->names([ 'unreadable' ]);

		$file_name_found = $sut->firstFile();

		$this->assertEmpty($file_name_found, '');
		$this->assertEquals('', $file_name_found, '');
	}
}

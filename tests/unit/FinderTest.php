<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use Codeception\Test\Unit;
use ItalyStrap\Finder\Exceptions\FileNotFoundException;
use ItalyStrap\Finder\FileInfoFactory;
use ItalyStrap\Finder\Finder;
use ItalyStrap\Finder\FinderInterface;
use ItalyStrap\Finder\SearchFilesHierarchy;
use ItalyStrap\Finder\SearchFileStrategy;
use LogicException;
use UnitTester;
use function codecept_data_dir;

class FinderTest extends Unit {

	/**
	 * @var UnitTester
	 */
	protected $tester;

	private $paths = [];

	/**
	 * @var \Prophecy\Prophecy\ObjectProphecy
	 */
	private $search_strategy;

	/**
	 * @return SearchFileStrategy
	 */
	public function getSearchStrategy(): SearchFileStrategy {
		return $this->search_strategy->reveal();
	}

	private function paths(): void {
		$this->paths = [
			'pluginPath' => codecept_data_dir( 'fixtures/plugin' ),
			'childPath' => codecept_data_dir( 'fixtures/child' ),
			'parentPath' => codecept_data_dir( 'fixtures/parent' ),
		];
	}

	// phpcs:ignore -- Method from Codeception
	protected function _before() {
		$this->paths();

		foreach ( $this->paths as $path ) {
			$this->assertDirectoryExists($path, '');
		}

		$this->search_strategy = $this->prophesize( SearchFileStrategy::class );
	}

	// phpcs:ignore -- Method from Codeception
	protected function _after() {
	}

	private function getInstance() {
		$sut = new Finder( new SearchFilesHierarchy( new FileInfoFactory() ) );
//		$sut = new Finder( $this->getSearchStrategy() );
		$this->assertInstanceOf( FinderInterface::class, $sut );
		$this->assertInstanceOf( Finder::class, $sut );
		return $sut;
	}

	/**
	 * @test
	 */
	public function itShouldBeInstantiable() {
		$sut = $this->getInstance();
	}

	/**
	 * @test
	 */
	public function itShouldThrownFileNotFoundExceptionIfFileDoesNotExists() {
		$sut = $this->getInstance();
		$sut->in( $this->paths );

		$this->expectException( FileNotFoundException::class );

		$files = $sut->find( ['file-name', 'does-not-exists'] );
	}

	/**
	 * @test
	 */
	public function itShouldThrownLogicExceptionIfInMethodIsNotCalled() {
		$sut = $this->getInstance();
//		$sut->in( [] );

		$this->expectException( LogicException::class );
		$files = $sut->find( ['file-name', 'does-not-exists'] );
	}

	/**
	 * @test
	 */
	public function itShouldThrownLogicExceptionIfNoDirectoriesAreProvided() {
		$sut = $this->getInstance();
		$sut->in( [] );

		$this->expectException( LogicException::class );
		$files = $sut->find( ['file-name', 'does-not-exists'] );
	}

	public function filesNamesProvider() {
		$this->paths();
		return [
			'findContentNoneFileInParentDirectory' => [
				$this->paths[ 'parentPath' ],
				['content', 'none', 'test'],
				'content-none.php'
			],
			'findFileTestInPluginDirectory' => [
				$this->paths[ 'pluginPath' ],
				['test'],
				'test.php'
			],
			'findFileTestNameInPluginDirectory' => [
				$this->paths[ 'pluginPath' ],
				'test',
				'test.php'
			],
			'fallbackToArchiveFileInParentDirectoryIfNoFilesAreFoundInPluginAndChildDirectory' => [
				$this->paths,
				['archive'],
				'archive.php'
			],
			'findContentFileAndTakeITFromChildInsteadOfParentAndPlugin' => [
				$this->paths,
				['content'],
				'content.php'
			],
			'findPartialFileIndex' => [
				$this->paths,
				['parts\subparts/index', 'jhlkjn'],
				'index.php'
			],
//			'findPartialFileIndexdsdf' => [
//				$this->paths,
//				['parts\subparts/index.kk', 'jhlkjn'],
//				'parts\subparts\index.kk.php'
//			],
		];
	}

	/**
	 * @test
	 * @dataProvider filesNamesProvider()
	 */
	public function itShould( $path, $to_find, $expected ) {

		$sut = $this->getInstance();
		$sut->in( $path );

		/**
		 * @var $full_path_to_file \SplFileInfo
		 */
		$full_path_to_file = $sut->find( $to_find );
		$this->assertFileIsReadable( $full_path_to_file->getRealPath(), '' );
		$this->assertStringContainsString( $expected, $full_path_to_file->getFilename(), '' );
	}
}

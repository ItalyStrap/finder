<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use Codeception\Test\Unit;
use ItalyStrap\Finder\Exceptions\FileNotFoundException;
use ItalyStrap\Finder\FileInfoFactory;
use ItalyStrap\Finder\Finder;
use ItalyStrap\Finder\FinderInterface;
use ItalyStrap\Finder\SearchFilesHierarchy;
use LogicException;
use SplFileInfo;
use UnitTester;
use function codecept_data_dir;

class FinderIntegrationTest extends Unit {

	/**
	 * @var UnitTester
	 */
	protected $tester;

	private $paths = [];

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
	}

	// phpcs:ignore -- Method from Codeception
	protected function _after() {
		$this->paths = [];
	}

	private function getInstance() {
		$sut = new Finder( new SearchFilesHierarchy( new FileInfoFactory() ) );
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
		$this->expectExceptionMessage('The file file-name-does-not-exists.php does not exists');

		$files = $sut->firstFileBySlugs( ['file-name', 'does-not-exists'] );
	}

	/**
	 * @test
	 */
	public function itShouldThrownLogicExceptionIfInMethodIsNotCalled() {
		$sut = $this->getInstance();
//		$sut->in( [] );

		$this->expectException( LogicException::class );
		$this->expectExceptionMessage(
			\sprintf(
				'You must call %s method before calling %s method.',
				Finder::class . '::in()',
				Finder::class . '::firstFileReadable()'
			)
		);
		$files = $sut->firstFileBySlugs( ['test'] );
	}

	/**
	 * @test
	 */
	public function itShouldThrownLogicExceptionIfNoDirectoriesAreProvided() {
		$sut = $this->getInstance();
		$sut->in( [] );

		$this->expectException( LogicException::class );
		$files = $sut->firstFileBySlugs( ['test'] );
	}

	/**
	 * @test
	 */
	public function itShouldChain() {
		$sut = $this->getInstance();
		$files = $sut->in( $this->paths )->firstFileBySlugs( ['test'] );
	}

	public function emptySlugsProvider() {
		return [
			'nullIsProvided'	=> [
				null,
			],
			'falseIsProvided'	=> [
				false,
			],
			'emptyStringIsProvided'	=> [
				''
			],
			'emptyArrayProvided'	=> [
				[]
			],
			'arrayWithEmptyStringIsProvided'	=> [
				['']
			],
			'arrayWithNullValueIsProvided'	=> [
				[null]
			],
		];
	}

	/**
	 * @test
	 * @dataProvider emptySlugsProvider()
	 * @param $slug_or_slugs
	 */
	public function itShouldThrownInvalidArgumentExceptionIf( $slug_or_slugs ) {
		$sut = $this->getInstance();
		$this->expectException( \InvalidArgumentException::class );
		$this->expectExceptionMessage('$slugs must not be empty');
		$files = $sut->in( $this->paths )->firstFileBySlugs( $slug_or_slugs );
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
//			'findPartialFileIndexggg' => [
//				$this->paths,
//				[
//					'test',
//					['test'],
//					['content'],
//				],
//				'index.php'
//			],
		];
	}

	/**
	 * @test
	 * @dataProvider filesNamesProvider()
	 * @param $path
	 * @param $to_find
	 * @param string $expected
	 */
	public function itShould( $path, $to_find, string $expected ) {

		$sut = $this->getInstance();
		$sut->in( $path );

		/**
		 * @var $full_path_to_file SplFileInfo
		 */
		$full_path_to_file = $sut->firstFileBySlugs( $to_find );
		$this->assertFileIsReadable( $full_path_to_file->getRealPath(), '' );
		$this->assertStringContainsString( $expected, $full_path_to_file->getFilename(), '' );
	}

	/**
	 * @test
	 */
	public function itShouldReturnSameFile() {

		$sut = $this->getInstance();
		$sut->in( $this->paths );

		/**
		 * @var $full_path_to_file01 SplFileInfo
		 */
		$full_path_to_file01 = $sut->firstFileBySlugs( 'content' );
		$full_path_to_file02 = $sut->firstFileBySlugs( 'content' );
		$this->assertSame( $full_path_to_file01, $full_path_to_file02, '' );

		$full_path_to_file03 = $sut->firstFileBySlugs(['content', 'none']);
		$this->assertNotSame( $full_path_to_file01, $full_path_to_file03, '' );
	}

	/**
	 * @test
	 */
	public function itShouldFindAll3ConfigFiles() {

		$sut = $this->getInstance();
		$sut->in( $this->paths );

		$configs = $sut->allFilesBySlugs('config');

		$this->assertCount(3, $configs, '');

		$i = 0;
		foreach ( $this->paths as $key => $path ) {
			$this->assertStringContainsString(
				\strval( \realpath( $this->paths[ $key ] ) ),
				$configs[ $i ]->getRealPath(),
				''
			);
			$i++;
		}
	}

	/**
	 * @test
	 */
	public function allConfigFilesShouldBeRequireableAndReturnArray() {

		$sut = $this->getInstance();
		$sut->in( $this->paths );

		$configs = $sut->allFilesBySlugs('config');

		$expect = [
			'Plugin config',
			'Child config',
			'Parent config',
		];
		foreach ( $configs as $key => $config ) {
			$value_on_config = require $config;
			$this->assertStringContainsString( $expect[$key], $value_on_config['key'], '' );
		}
	}

	/**
	 * @test
	 */
	public function mergeAllConfig() {

		$sut = $this->getInstance();
		$sut->in( $this->paths );

		/** @var array<\SplFileInfo> $configs */
		$configs = $sut->allFilesBySlugs('config');

		/** @var array $result */
		$result = array_map(function ( $config ) {
			return require $config;
		}, \array_reverse($configs) );

		/** @var array $result */
		$result = \array_replace_recursive( ...$result );// require $config;
		$this->assertArrayHasKey('plugin-key', $result, '');
		$this->assertStringContainsString('Plugin config', $result['key'], '');
//		codecept_debug( $result );
	}

	/**
	 * @test
	 */
	public function searchAsset() {

		$paths = \array_map(function ($path) {
			$path .= '/assets/css';
			$path = \strval( \realpath( $path ) );
			$this->assertIsReadable($path, '');
			return $path;
		}, $this->paths);

		$sut = $this->getInstance();
		$sut->in( $paths );

		/** @var array<\SplFileInfo> $configs */
		$css = $sut->firstFileBySlugs('style', 'css');
		$this->assertStringContainsString($paths['pluginPath'], $css->getRealPath(), '');
		$this->assertEquals('style.css', $css->getFilename(), '');

		/** @var array<\SplFileInfo> $configs */
		$css = $sut->firstFileBySlugs('custom', 'css');
		$this->assertStringContainsString($paths['childPath'], $css->getRealPath(), '');
		$this->assertEquals('custom.css', $css->getFilename(), '');

		$files = [
			['no'],
			['no-file', 'min'],
			['style', 'min'],
			['custom', 'min'],
		];

		$css = '';
		$count = \count($files) - 1;
		foreach ( $files as $key => $slugs ) {
			try {
				$css = $sut->firstFileBySlugs( $slugs, 'css', '.' );
				if ( $css->isReadable() ) {
					break;
				}
			} catch (\Exception $e) {
				//@TODO implement logic in case no files are found
			}
		}

		$this->assertStringContainsString($paths['pluginPath'], $css->getRealPath(), '');
		$this->assertEquals('style.css', $css->getFilename(), '');
	}
}

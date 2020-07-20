<?php
declare(strict_types=1);

namespace ItalyStrap\Finder;

use SplFileInfo;

/**
 * Interface FinderInterface
 * @package ItalyStrap\Finder
 */
interface FinderInterface {

	/**
	 * @param string|array<string> $dirs Add a directory or an array of directories for searching files
	 * @return $this Return the object instance.
	 */
	public function in( $dirs );

	/**
	 * @param string[] $files
	 * @return void
	 */
	public function names( $files );

	/**
	 * Load a template part into a template
	 *
	 * Makes it easy for a theme to reuse sections of code in a easy to overload way
	 * for child themes.
	 *
	 * Includes the named template part for a theme or if a name is specified then a
	 * specialised part will be included. If the theme contains no {slug}.php file
	 * then no template will be included.
	 *
	 * The template is included using require, not require_once, so you may include the
	 * same template part multiple times.
	 *
	 * For the $name parameter, if the file is called "{slug}-special.php" then specify
	 * "special".
	 *
	 * @see get_template_part() - wp-includes/general-template.php
	 *
	 * @param string|array $slugs The slug name for the generic template.
	 *
	 * @return string            Return the file part rendered
	 */
	/**
	 * Retrieve the name of the highest priority template file that exists.
	 *
	 * Searches in the STYLESHEETPATH before TEMPLATEPATH and wp-includes/theme-compat
	 * so that themes which inherit from a parent theme can just overload one file.
	 *
	 * @param string|array $slugs Template file(s) to search for, in order.
	 *
	 * @return string Return the full path of the template filename if one is located.
	 */
	/**
	 * @param string|array<string> $slugs Add a slug or an array of slugs for search files
	 * @param string|array<string> $extension Add a file extension or an array of files extension, Default is php
	 * @param string $slugs_separator
	 * @return mixed Return a full path of the file searched
	 */
	public function firstFile( $slugs, $extension = 'php', $slugs_separator = '-' );

	/**
	 * @param string|array<string> $slugs
	 * @param string|array<string> $extensions
	 * @param string $slugs_separator
	 * @return array
	 */
	public function allFiles( $slugs, $extensions = 'php', $slugs_separator = '-' ): array;
}

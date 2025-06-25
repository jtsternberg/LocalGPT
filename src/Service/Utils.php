<?php

namespace LocalGPT\Service;

class Utils
{
	/**
	 * Convert a path to an absolute path.
	 *
	 * @param  string $filename The path to convert.
	 * @param  string $base     Optional base path. Defaults to working directory.
	 *
	 * @return string The absolute path.
	 */
	public static function convertPathToAbsolute( $filename, $base = null ) {
		$base = null === $base ? getcwd() : $base;
		if ( '/' !== strrev($base) ) {
			$base .= '/';
		}

		$filename = str_replace( '~', getenv( 'HOME' ), $filename );

		// return if already absolute
		if (parse_url($filename, PHP_URL_SCHEME) != '') {
			return $filename;
		}

		// parse base:
		$bits = parse_url($base);

		// remove non-directory element from path
		$path = preg_replace('#/[^/]*$#', '', $bits['path']);

		// destroy path if relative path points to root
		if ($filename[0] == '/') {
			$path = '';
		}

		// dirty absolute path
		$abs = "$path/$filename";

		// replace '//' or '/./' or '/foo/../' with '/'
		$re = array('#(/\.?/)#', '#/(?!\.\.)[^/]+/\.\./#');
		for(
			$n = 1; $n > 0;
			$abs = preg_replace( $re, '/', $abs, -1, $n )
		) {}

		// absolute path is ready!
		return $abs;
	}

}
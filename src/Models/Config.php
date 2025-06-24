<?php

namespace LocalGPT\Models;

class Config
{
	public function __construct(protected array $config)
	{
		if (empty($this->config['path'])) {
			$config = print_r($this->config, true);
			throw new \InvalidArgumentException("Configuration file invalid: {$config}");
		}
	}

	public function get(string $key): mixed
	{
		return $this->config[$key] ?? null;
	}

	public function getPath(): string
	{
		return $this->config['path'];
	}

	public function getProvider(): string
	{
		return $this->config['provider'];
	}

	public function getModel(): string
	{
		return $this->config['model'];
	}

	public function getSystemPrompt(): string
	{
		return $this->config['system_prompt'] ?? '';
	}

	public function getSystemPromptText(): string
	{
		$systemPrompt = $this->getSystemPrompt();
		if (empty($systemPrompt)) {
			return '';
		}

		$systemPrompt = file_get_contents($this->convertPathToAbsolute($systemPrompt));

		return trim($systemPrompt);
	}

	public function getReferenceFiles(): array
	{
		return $this->config['reference_files'] ?? [];
	}

	protected function getReferenceFileData(string $path): array
	{
		$absolutePath = $this->convertPathToAbsolute($path);
		if (is_file($absolutePath) && is_readable($absolutePath)) {
			return [
				'path' => $absolutePath,
				'content' => trim(file_get_contents($absolutePath)),
			];
		}

		return [];
	}

	public function getReferenceFilesWithContent(): array
	{
		$referenceFiles = $this->getReferenceFiles();
		$files          = [];
		if (!empty($referenceFiles)) {
			foreach ($referenceFiles as $path) {
				$data = $this->getReferenceFileData($path);
				if (!empty($data)) {
					$files[] = $data;
				}
			}
		}

		return $files;
	}

	/**
	 * Convert a path to an absolute path.
	 *
	 * @param  string $filename The path to convert.
	 * @param  string $base     Optional base path. Defaults to working directory.
	 *
	 * @return string The absolute path.
	 */
	protected function convertPathToAbsolute( $filename, $base = null ) {
		$base = null === $base ? $this->getPath() : $base;
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

	public function __set( $key, $value ) {
		$this->config[$key] = $value;
	}
}
<?php

namespace LocalGPT\Tests\Service;

use LocalGPT\Service\Utils;
use PHPUnit\Framework\TestCase;

class UtilsTest extends TestCase
{
	public function testConvertPathToAbsolute()
	{
		// Test with an already absolute path
		$this->assertEquals('/some/path/file.txt', Utils::convertPathToAbsolute('/some/path/file.txt'));

		// Test with a relative path
		$this->assertEquals(getcwd() . '/relative/path/file.txt', Utils::convertPathToAbsolute('relative/path/file.txt'));

		// Test with ~ for home directory
		$this->assertEquals(getenv('HOME') . '/file.txt', Utils::convertPathToAbsolute('~/file.txt'));

		// Test with . and ..
		$this->assertEquals(getcwd() . '/path/file.txt', Utils::convertPathToAbsolute('./path/file.txt'));
		$this->assertEquals(dirname(getcwd()) . '/file.txt', Utils::convertPathToAbsolute('../file.txt'));
		$this->assertEquals(getcwd() . '/file.txt', Utils::convertPathToAbsolute('some/path/../../file.txt'));

		// Test with a base path
		$this->assertEquals('/base/path/file.txt', Utils::convertPathToAbsolute('file.txt', '/base/path'));
		$this->assertEquals('/base/path/relative/file.txt', Utils::convertPathToAbsolute('relative/file.txt', '/base/path/'));
	}
}
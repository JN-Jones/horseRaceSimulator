<?php
namespace Misc;

/**
 * Class Random
 *
 * Simple class to generate a random number
 */
class Random
{
	/**
	 * Returns a float between 0.0 and 1.0
	 *
	 * @return float The random float between 0.0 and 1.0
	 */
	public static function getFloat()
	{
		return (float)rand() / (float)getrandmax();
	}
}
<?php

namespace Framework;

/**
 * Class Module
 *
 * Define how a module should look. Implements that class on all your custom modules
 *
 * @package Framework
 */
abstract class Module
{
    /**
     * Path to Config definitions, see /config/config.php for more infos
     */
    const DEFINITIONS = null;

    /**
     * Path to migrations - see /sphinx.php for more infos
     */
    const MIGRATIONS = null;

    /**
     * Path to Seedings - see /sphinx.php for more infos
     */
    const SEEDS = null;
}

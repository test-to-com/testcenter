<?php

/* Test Center - Compliance Testing Application (Metadata Service)
 * Copyright (C) 2014 Paulo Ferreira <pf at sourcenotes.org>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Registering an autoloader
 */
$loader = new \Phalcon\Loader();

// Register Namespace Directories for Search
$loader->registerNamespaces(
        $config->namespaces->toArray()
);

// Register Individual Directories for Search
$loader->registerDirs(
        array(
            $config->application->modelsDir,
            $config->application->controllersDir
        )
);

// Ready Loader
$loader->register();
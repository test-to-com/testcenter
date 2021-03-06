<?php

/**
 * Test Center - Compliance Testing Application (Web Services)
 * Copyright (C) 2012 - 2015 Paulo Ferreira <pf at sourcenotes.org>
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
/*
 * USER MODE: Project Services
 * 
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2015 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
use Phalcon\Mvc\Micro\Collection as MicroCollection;

/*
 * Test Services
 */
$controller = new controllers\user\TestsController();

/*
 * Tests Actions
 */
$prefix = '/test';

$app->map($prefix . '/create/{name}', array($controller, 'create'));
$app->map($prefix . '/create/{name}/{folder:[0-9]+}', array($controller, 'create'));
$app->map($prefix . '/read/{id:[0-9]+}', array($controller, 'read'));
$app->map($prefix . '/update/{id:[0-9]+}', array($controller, 'update'));
$app->map($prefix . '/delete/{id:[0-9]+}', array($controller, 'delete'));

/*
 * List All Tests on Just Tests in a Particular Folder
 */
$prefix = '/tests';

$app->map($prefix . '/list', array($controller, 'listInProject'));
$app->map($prefix . '/list/{filter}', array($controller, 'listInProject'));
$app->map($prefix . '/list/{filter}/{sort}', array($controller, 'listInProject'));
$app->map($prefix . '/list/{folder:[0-9]+}', array($controller, 'listInFolder'));
$app->map($prefix . '/count', array($controller, 'countInProject'));
$app->map($prefix . '/count/{filter}', array($controller, 'countInProject'));
$app->map($prefix . '/list/{folder:[0-9]+}/{filter}', array($controller, 'listInFolder'));
$app->map($prefix . '/list/{folder:[0-9]+}/{filter}/{sort}', array($controller, 'listInFolder'));
$app->map($prefix . '/count/{folder:[0-9]+}', array($controller, 'countInFolder'));
$app->map($prefix . '/count/{folder:[0-9]+}/{filter}', array($controller, 'countInFolder'));

/*
 * List Sets associated with a Test
 */

/*
 * Set<-->Test Controller
 */
$controller = new controllers\user\SetTestsController();

$prefix = '/test/{test:[0-9]+}/sets';

$app->map($prefix . '/list', array($controller, 'listSets'));
$app->map($prefix . '/count', array($controller, 'countSets'));


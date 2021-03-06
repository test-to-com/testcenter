<?php

/**
 * Test Center - Compliance Testing Application (Web Services)
 * Copyright (C) 2012-2015 Paulo Ferreira <pf at sourcenotes.org>
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

namespace api\controller;

use common\utility\Arrays;
use common\utility\Strings;

/**
 * Class to Provide a Working Context for a Controller Action.
 *
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2015 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
class ActionContext {

  protected $m_sAction;
  protected $m_arParameters;
  protected $m_vResults;
  protected $m_oResponse;

  /**
   *
   * @param type $action
   */
  public function __construct($action) {
    // Set the Base Action for the Context
    $this->m_sAction = self::getActionName($action);
    assert('isset($this->m_sAction)');

    $this->m_arParameters = array();
  }

  /**
   *
   * @return type
   */
  public function getAction() {
    return $this->m_sAction;
  }

  /**
   *
   * @param type $key
   * @return type
   */
  public function hasParameter($key) {
    $key = Strings::nullOnEmpty($key);
    assert('isset($key)');
    return isset($key) ? array_key_exists($key, $this->m_arParameters) : false;
  }

  /**
   *
   * @param type $key
   * @param type $default
   * @return type
   */
  public function getParameter($key, $default = null) {
    $key = Strings::nullOnEmpty($key);
    assert('isset($key)');
    return isset($key) ? Arrays::extract($this->m_arParameters, $key, $default) : $default;
  }

  /**
   *
   * @param type $keys
   * @param type $default
   * @return type
   */
  public function getOneOfParameters($keys, $default = null) {
    // Validate Parameter
    $keys = Arrays::nullOnEmpty($keys);
    assert('isset($keys)');

    // Search for a Key in the Parameters
    foreach ($keys as $key) {
      if (array_key_exists($key, $this->m_arParameters)) {
        return $this->m_arParameters[$key];
      }
    }

    return $default;
  }

  /**
   *
   * @param type $key
   * @param type $value
   * @return \TestCenter\ServiceBundle\API\ActionContext
   */
  public function setParameter($key, $value) {
    $key = Strings::nullOnEmpty($key);
    assert('isset($key)');
    if (isset($key)) {
      $this->m_arParameters[$key] = $value;
    }

    return $this;
  }

  /**
   *
   * @return type
   */
  public function getParameters() {
    return $this->m_arParameters;
  }

  /**
   *
   * @param type $values
   * @return \TestCenter\ServiceBundle\API\ActionContext
   */
  public function setParameters($values) {
    assert('!isset($values) || is_array($values)');
    if (isset($values) && is_array($values) && (count($values) > 0)) {
      foreach ($values as $key => $value) {
        $this->m_arParameters[$key] = $value;
      }
    }

    return $this;
  }

  /**
   *
   * @param type $value1
   * @param type $value2
   * @return type
   */
  public function setFirstNotNullOf($key, $value1, $value2) {
    $key = Strings::nullOnEmpty($key);
    assert('isset($key)');
    if (isset($key)) {
      if (isset($value1)) {
        $this->m_arParameters[$key] = $value1;
      } else if (isset($value2)) {
        $this->m_arParameters[$key] = $value2;
      }
    }

    return $this;
  }

  /**
   *
   * @param type $array
   * @param type $key
   * @param type $value
   * @return type
   */
  public function setIfNotNull($key, $value) {
    $key = Strings::nullOnEmpty($key);
    assert('isset($key)');
    if (isset($key) && isset($value)) {
      $this->m_arParameters[$key] = $value;
    }

    return $this;
  }

  /**
   * 
   * @param string $key
   * @param type $value
   */
  public function setRequiredInteger($key, $value) {
    if (isset($value)) {
      if (is_string($value)) {
        $value = Strings::nullOnEmpty($value);
        $value = isset($value) ? (integer) $value : null;
      } else if (!is_integer($value)) {
        $value = null;
      }
    }

    if (!isset($value)) {
      throw new \Exception("Require Integer Parameter [{$key}] is Missing or Invalid.", 1);
    }

    return $this->setParameter($key, $value);
  }

  /**
   * 
   * @param string $key
   * @param type $value
   */
  public function setOptionalInteger($key, $value) {
    if (isset($value)) {
      if (is_string($value)) {
        $value = Strings::nullOnEmpty($value);
        $value = isset($value) ? (integer) $value : null;
      } else if (!is_integer($value)) {
        $value = null;
      }
    }

    return $this->setIfNotNull($key, $value);
  }

  /**
   * 
   * @param string $key
   * @param string $value
   */
  public function setRequiredString($key, $value) {
    $value = isset($value) && is_string($value) ? Strings::nullOnEmpty($value) : null;
    if (!isset($value)) {
      throw new \Exception("Require String Parameter [{$key}] is Missing or Invalid.", 1);
    }

    return $this->setParameter($key, $value);
  }

  /**
   * 
   * @param type $key
   * @param type $value
   */
  public function setOptionalString($key, $value) {
    $value = isset($value) && is_string($value) ? Strings::nullOnEmpty($value) : null;
    return $this->setIfNotNull($key, $value);
  }

  /**
   * 
   * @param string $key
   * @param string $value
   * @return ActionContext return $this;
   */
  public function setRequiredBool($key, $value) {
    if (isset($value)) {
      if (is_string($value)) {
        $value = Strings::nullOnEmpty($value);
        $value = isset($value) ? (bool) $value : null;
      } else if (!is_bool($value)) {
        $value = null;
      }
    }

    if (!isset($value)) {
      throw new \Exception("Require Boolean Parameter [{$key}] is Missing or Invalid.", 1);
    }

    return $this->setParameter($key, $value);
  }

  /**
   * 
   * @param type $key
   * @param type $value
   * @param boolean $default
   * @return ActionContext return $this;
   * 
   */
  public function setOptionalBool($key, $value, $default = null) {
    if (isset($value)) {
      if (is_string($value)) {
        $value = Strings::nullOnEmpty($value);
        $value = isset($value) ? (bool) $value : $default;
      } else if (!is_bool($value)) {
        $value = $default;
      }
    }

    return $this->setIfNotNull($key, $value);
  }

  /**
   *
   * @return type
   */
  public function getActionResult() {
    return $this->m_vResults;
  }

  /**
   *
   * @param type $results
   * @return \TestCenter\ServiceBundle\API\ActionContext
   */
  public function setActionResult($results) {
    if (isset($results)) {
      $this->m_vResults = $results;
    }

    return $this;
  }

  /**
   *
   * @return \TestCenter\ServiceBundle\API\ActionContext
   */
  public function clearResults() {
    $this->m_vResults = null;
    return $this;
  }

  /**
   *
   * @return type
   */
  public function getResponse() {
    return $this->m_oResponse;
  }

  /**
   *
   * @param type $results
   * @return \TestCenter\ServiceBundle\API\ActionContext
   */
  public function setResponse($response) {
    if (isset($response)) {
      $this->m_oResponse = $response;
    }

    return $this;
  }

  /**
   *
   * @param type $action
   * @return type
   */
  public static function getActionName($action) {
    assert('isset($action) && is_string($action)');

    $action = Strings::nullOnEmpty($action);
    $action = explode('_', $action);
    $action = array_map('strtolower', $action);
    $action = array_map('ucfirst', $action);
    $action = implode($action);
    return $action;
  }

}

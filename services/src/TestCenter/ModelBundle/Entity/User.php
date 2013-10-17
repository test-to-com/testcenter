<?php

/* Test Center - Compliance Testing Application
 * Copyright (C) 2012 Paulo Ferreira <pf at sourcenotes.org>
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

namespace TestCenter\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TestCenter\ModelBundle\Entity\User
 *
 * @ORM\Table(name="t_users")
 * @ORM\Entity(repositoryClass="TestCenter\ModelBundle\Repository\UserRepository")
 * 
 * @author Paulo Ferreira
 */
class User extends AbstractEntity {

  /**
   * @var integer $id
   *
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id;

  /**
   * @var string $name
   *
   * @ORM\Column(name="name", type="string", length=40)
   */
  protected $name;

  /**
   * @var string $first_name
   *
   * @ORM\Column(name="first_name", type="string", length=40, nullable=true)
   */
  protected $first_name;

  /**
   * @var string $last_name
   *
   * @ORM\Column(name="last_name", type="string", length=80, nullable=true)
   */
  protected $last_name;

  /**
   * @var string $password
   *
   * @ORM\Column(name="password", type="string", length=64)
   */
  protected $password;

  /**
   * @var text $s_description
   *
   * @ORM\Column(name="s_description", type="string", length=80, nullable=true)
   */
  protected $s_description;

  /**
   * @var text $l_description
   *
   * @ORM\Column(name="l_description", type="text", nullable=true)
   */
  protected $l_description;

  /**
   * @ORM\ManyToOne(targetEntity="User")
   * @ORM\JoinColumn(name="id_creator", referencedColumnName="id")
   */
  private $creator;

  /**
   * @var datetime $date_created
   *
   * @ORM\Column(name="dt_creation", type="datetime", nullable=false)
   */
  protected $date_created;

  /**
   * @ORM\ManyToOne(targetEntity="User")
   * @ORM\JoinColumn(name="id_modifier", referencedColumnName="id", nullable=true)
   */
  private $last_modifier;

  /**
   * @var datetime $date_modified
   *
   * @ORM\Column(name="dt_modified", type="datetime", nullable=true)
   */
  protected $date_modified;

  public function __construct() {
    $this->date_created = new \DateTime();
  }
  
  /**
   * Get id
   *
   * @return integer
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Set name
   *
   * @param string $name
   */
  public function setName($name) {
    $this->name = $name;
  }

  /**
   * Get name
   *
   * @return string
   */
  public function getName() {
    return $this->name;
  }

  /**
   * Set password
   *
   * @param string $password
   */
  public function setPassword($password) {
    $this->password = $password;
  }

  /**
   * Get password
   *
   * @return string
   */
  public function getPassword() {
    return $this->password;
  }

  /**
   * Set first_name
   *
   * @param string $firstName
   */
  public function setFirstName($firstName) {
    $this->first_name = $firstName;
  }

  /**
   * Get first_name
   *
   * @return string 
   */
  public function getFirstName() {
    return $this->first_name;
  }

  /**
   * Set last_name
   *
   * @param string $lastName
   */
  public function setLastName($lastName) {
    $this->last_name = $lastName;
  }

  /**
   * Get last_name
   *
   * @return string 
   */
  public function getLastName() {
    return $this->last_name;
  }

  /**
   * Set s_description
   *
   * @param string $sDescription
   */
  public function setSDescription($sDescription) {
    $this->s_description = $sDescription;
  }

  /**
   * Get s_description
   *
   * @return string 
   */
  public function getSDescription() {
    return $this->s_description;
  }

  /**
   * Set l_description
   *
   * @param text $lDescription
   */
  public function setLDescription($lDescription) {
    $this->l_description = $lDescription;
  }

  /**
   * Get l_description
   *
   * @return text 
   */
  public function getLDescription() {
    return $this->l_description;
  }

  /**
   * Set creator
   *
   * @param TestCenter\ModelBundle\Entity\User $creator
   */
  public function setCreator(\TestCenter\ModelBundle\Entity\User $creator) {
    $this->creator = $creator;
  }

  /**
   * Get creator
   *
   * @return TestCenter\ModelBundle\Entity\User 
   */
  public function getCreator() {
    return $this->creator;
  }

  /**
   * Set date_created
   *
   * @param datetime $dateCreated
   */
  public function setDateCreated($dateCreated) {
    $this->date_created = $dateCreated;
  }

  /**
   * Get date_created
   *
   * @return datetime 
   */
  public function getDateCreated() {
    return $this->date_created;
  }

  /**
   * Set last_modifier
   *
   * @param TestCenter\ModelBundle\Entity\User $lastModifier
   */
  public function setLastModifier(\TestCenter\ModelBundle\Entity\User $lastModifier) {
    $this->last_modifier = $lastModifier;
  }

  /**
   * Get last_modifier
   *
   * @return TestCenter\ModelBundle\Entity\User 
   */
  public function getLastModifier() {
    return $this->last_modifier;
  }

  /**
   * Set date_modified
   *
   * @param datetime $dateModified
   */
  public function setDateModified($dateModified) {
    $this->date_modified = $dateModified;
  }

  /**
   * Get date_modified
   *
   * @return datetime 
   */
  public function getDateModified() {
    return $this->date_modified;
  }

  /**
   * @return array
   */
  public function toArray() {
    $array = parent::toArray();

    $array = $this->addProperty($array, 'id');
    $array = $this->addProperty($array, 'name');
    $array = $this->addPropertyIfNotNull($array, 'first_name');
    $array = $this->addPropertyIfNotNull($array, 'last_name');
    $array = $this->addPropertyIfNotNull($array, 's_description');
    $array = $this->addPropertyIfNotNull($array, 'l_description');
    $array = $this->addReferencePropertyIfNotNull($array, 'creator');
    $array = $this->addProperty($array, 'date_created');
    $array = $this->addReferencePropertyIfNotNull($array, 'last_modifier');
    $array = $this->addPropertyIfNotNull($array, 'date_modified');
    return $array;
  }

  /**
   * @return string
   */
  public function __toString() {
    return strval($this->id);
  }

  /**
   * 
   * @return type
   */
  protected function entityName() {
    $i = strlen(__NAMESPACE__);
    return substr(__CLASS__, $i + 1);
  }

}
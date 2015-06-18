<?php

/**
 * @file
 * Definition of \Drupal\commerce_order\Tests\CommerceOrderTestBase.
 */

namespace Drupal\commerce_order\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Defines base class for shortcut test cases.
 */
abstract class CommerceOrderTestBase extends WebTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['commerce', 'commerce_order', 'inline_entity_form'];

  /**
   * User with permission to administer products.
   */
  protected $adminUser;

  protected function setUp() {
    parent::setUp();
    $this->adminUser = $this->drupalCreateUser(array(
      'administer orders',
      'administer order types',
      'access administration pages',
    ));
    $this->drupalLogin($this->adminUser);
  }

  /**
   * Creates a new entity
   *
   * @param string $entityType
   * @param array $values
   *   An array of settings.
   *   Example: 'id' => 'foo'.
   *
   * @return \Drupal\Core\Entity\EntityInterface
   */
  protected function createEntity($entityType, $values) {
    $entity = \Drupal::entityManager()->getStorage($entityType)->create($values);
    $status = $entity->save();

    $this->assertEqual(
      $status,
      SAVED_NEW,
      SafeMarkup::format('Created %label entity %type.', [
          '%label' => $entity->getEntityType()->getLabel(),
          '%type' => $entity->id()]
      )
    );

    return $entity;
  }
}

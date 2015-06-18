<?php

/**
 * @file
 * Definition of \Drupal\commerce_order\Tests\CommerceOrderTestBase.
 */

namespace Drupal\commerce_order\Tests;

use Drupal\Component\Utility\SafeMarkup;
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

  /**
   * The store to test against
   */
  protected $commerce_store;

  /**
   * The product to test against
   */
  protected $commerce_product;


  protected function setUp() {
    parent::setUp();

    $name = strtolower($this->randomMachineName(8));

    $store_type = $this->createEntity('commerce_store_type', [
        'id' => 'foo',
        'label' => 'Label of foo',
      ]
    );

    $this->commerce_store = $this->createEntity('commerce_store', [
        'type' => $store_type->id(),
        'name' => $name,
        'mail' => \Drupal::currentUser()->getEmail(),
        'default_currency' => 'EUR',
      ]
    );

    $this->commerce_store = $this->createEntity('commerce_product', [
      'sku' => $this->randomMachineName(),
      'title' => $this->randomMachineName(),
      'type' => 'product',
      'store_id' => $this->commerce_store->id()
    ]);

    $this->adminUser = $this->drupalCreateUser([
      'administer orders',
      'administer order types',
      'access administration pages',
    ]);

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
    $entity = \Drupal::entityManager()
      ->getStorage($entityType)
      ->create($values);
    $status = $entity->save();

    $this->assertEqual(
      $status,
      SAVED_NEW,
      SafeMarkup::format('Created %label entity %type.', [
          '%label' => $entity->getEntityType()->getLabel(),
          '%type' => $entity->id()
        ]
      )
    );

    return $entity;
  }
}

<?php

/**
 * @file
 * Contains \Drupal\commerce_order\Tests\OrderTest.
 */

namespace Drupal\commerce_order\Tests;

use Drupal\commerce_order\Entity\Order;

/**
 * Tests the commerce_order entity forms.
 *
 * @group commerce
 */
class OrderTest extends CommerceOrderTestBase {

  /**
   * Tests creating a Order programmatically and through the add form.
   */
  public function testCreateOrder() {
    // Create a order programmaticaly.
    $order = $this->createEntity('commerce_order', array(
        'type' => 'order',
        'mail' => $this->loggedInUser->getEmail(),
      )
    );

    $orderExists = (bool) Order::load($order->id());
    $this->assertTrue($orderExists, 'The new order has been created in the database.');
    $this->assertEqual($order->id(), $order->getOrderNumber(), 'The order number matches the order ID');

  }

  /**
   * Tests creating a Order programmatically and through the add form.
   */
  public function testCreateOrderAdmin() {
    // Create a order through the add form.
    $this->drupalGet('/admin/commerce/orders');
    $this->clickLink('Create a new order');

    $values = array(
      'mail[0][value]' => $this->loggedInUser->getEmail(),
      'line_items[form][inline_entity_form][product][0][target_id]' => $this->commerce_product->id(),
      'line_items[form][inline_entity_form][quantity][0][value]' => 1,
      'line_items[form][inline_entity_form][unit_price][0][amount]' => 9.99,
      'line_items[form][inline_entity_form][unit_price][0][currency_code]' => 'USD'
    );
    $this->drupalPostForm(NULL, $values, t('Save'));
  }

  /**
   * Tests deleting a order.
   */
  public function testDeleteOrder() {
    // Create a new order.
    $order = $this->createEntity('commerce_order', array(
        'type' => 'order',
        'mail' => $this->loggedInUser->getEmail(),
      )
    );
    $orderExists = Order::load($order->id());
    $this->assertTrue((bool) $orderExists, 'The order has been created in the database.');
    $orderExists->delete();

    // Remove the entity from cache and check if the order is deleted.
    \Drupal::entityManager()->getStorage('commerce_order')->resetCache(array($order->id()));
    $orderExists = (bool) Order::load('commerce_order', $order->id());
    $this->assertFalse($orderExists, 'The order has been deleted from the database.');
  }
}

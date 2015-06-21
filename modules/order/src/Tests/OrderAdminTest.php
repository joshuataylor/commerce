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
class OrderAdminTest extends CommerceOrderTestBase {

  /**
   * Tests creating a Order programmatically and through the add form.
   */
  public function testCreateOrderAdmin() {
    // Create a order through the add form.
    $this->drupalGet('/admin/commerce/orders');
    $this->clickLink('Create a new order');

    $values = array(
      'line_items[form][inline_entity_form][product][0][target_id]' => $this->commerce_product->getTitle(),
      'line_items[form][inline_entity_form][quantity][0][value]' => 1,
      'line_items[form][inline_entity_form][unit_price][0][amount]' => 9.99,
      'line_items[form][inline_entity_form][unit_price][0][currency_code]' => 'USD',
      'store_id' => 1,
    );
    $this->drupalPostForm(NULL, $values, t('Save'));

    $this->assertText(t("The order @id has been successfully saved", ['@id' => 1]), "Commerce Order success text is showing");
    $this->drupalGet('admin/commerce/orders/' . 1);
    $this->assertResponse(200);
  }

  /**
   * Tests deleting a order.
   */
  public function testDeleteOrderAdmin() {
    // Create a new order.
    $order = $this->createEntity('commerce_order', array(
        'type' => 'order',
        'mail' => $this->loggedInUser->getEmail(),
      )
    );
    $orderExists = (bool) Order::load($order->id());
    $this->assertTrue($orderExists, 'The order has been created in the database.');

    $this->drupalGet('admin/commerce/orders/' . $order->id() . '/delete');
    $this->assertRaw(
      t('Are you sure you want to delete the order %label?', array(
        '%label' => $order->label(),
      ))
    );
    $this->assertText(t('This action cannot be undone.'), 'The order deletion confirmation form is available');
    $this->drupalPostForm(NULL, NULL, t('Delete'));
    // Remove the entity from cache and check if the order is deleted.
    \Drupal::entityManager()->getStorage('commerce_order')->resetCache(array($order->id()));
    $orderExists = (bool) Order::load('commerce_order', $order->id());
    $this->assertFalse($orderExists, 'The order has been deleted from the database.');
  }
}

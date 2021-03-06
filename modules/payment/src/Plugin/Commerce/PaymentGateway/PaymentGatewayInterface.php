<?php

namespace Drupal\commerce_payment\Plugin\Commerce\PaymentGateway;

use Drupal\commerce\PluginForm\PluginWithFormsInterface;
use Drupal\Component\Plugin\ConfigurablePluginInterface;
use Drupal\Component\Plugin\DerivativeInspectionInterface;
use Drupal\Core\Plugin\PluginFormInterface;

/**
 * Defines the base interface for payment gateways.
 */
interface PaymentGatewayInterface extends PluginWithFormsInterface, ConfigurablePluginInterface, PluginFormInterface, DerivativeInspectionInterface {

  /**
   * Gets the payment gateway label.
   *
   * The label is admin-facing and usually includes the name of the used API.
   * For example: "Braintree (Hosted Fields)".
   *
   * @return mixed
   *   The payment gateway label.
   */
  public function getLabel();

  /**
   * Gets the payment gateway display label.
   *
   * The display label is customer-facing and more generic.
   * For example: "Braintree".
   *
   * @return string
   *   The payment gateway display label.
   */
  public function getDisplayLabel();

  /**
   * Gets the mode in which the payment gateway is operating.
   *
   * @return string
   *   The machine name of the mode.
   */
  public function getMode();

  /**
   * Gets the supported modes.
   *
   * @return string[]
   *   The mode labels keyed by machine name.
   */
  public function getSupportedModes();

  /**
   * Gets the payment method types handled by the payment gateway.
   *
   * @return \Drupal\commerce_payment\Plugin\Commerce\PaymentMethodType\PaymentMethodTypeInterface[]
   *   The payment method types.
   */
  public function getPaymentMethodTypes();

  /**
   * Gets the credit card types handled by the gateway.
   *
   * @return \Drupal\commerce_payment\CreditCardType[]
   *   The credit card types.
   */
  public function getCreditCardTypes();

}

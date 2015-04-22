<?php

/**
 * @file
 * Contains Drupal\commerce_product\Form\ProductMultistepForm.
 */

namespace Drupal\commerce_product\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class ProductMultistepForm.
 *
 * @package Drupal\commerce_product\Form
 */
class ProductMultistepForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'commerce_product.productmultistep_config'
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'product_multistep_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $stores = entity_load_multiple('commerce_store');
    $store_names = [];
    foreach ($stores as $store) {
      $store_names[$store->id()] = $store->getName();
    }
    $config = $this->config('commerce_product.productmultistep_config');
    $form['store'] = array(
      '#type' => 'select',
      '#title' => $this->t('Store'),
      '#description' => $this->t('Choose the store for this product.'),
      '#options' => $store_names,
      '#default_value' => reset($store_names),
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
  }

}

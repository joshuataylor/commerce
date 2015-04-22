<?php

/**
 * @file
 * Contains \Drupal\commerce_product\Form\ProductForm.
 */

namespace Drupal\commerce_product\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for the product edit form.
 */
class ProductAddForm extends ContentEntityForm {

  protected $step;

  /**
   * {@inheritdoc}
   *
   * Fills in a few default values, and then invokes hook_commerce_product_prepare()
   * on all modules.
   */
  protected function prepareEntity() {
    /* @var \Drupal\commerce_product\Entity\Product $product */
    $product = $this->entity;
    // Set up default values, if required.
    $productType = entity_load('commerce_product_type', $product->bundle());
    if (!$product->isNew()) {
      $product->setRevisionLog(NULL);
    }
    // Always use the default revision setting.
    $product->setNewRevision($productType->revision);
    $this->step = 1;
    if ($product->getStore()) {
      $this->step = 2;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    /* @var \Drupal\commerce_product\Entity\Product $product */
    $product = $this->entity;
    $currentUser = $this->currentUser();

    $form = parent::form($form, $form_state);
    $form['step'] = [
      '#type' => 'hidden',
      '#default_value' => $this->step
    ];

    if (!$product->getStore()) {
      // Unset all fields but store_id and type from this form
      foreach ($product->getFields(TRUE) as $field) {
        if ($field->getName() != "store_id" && $field->getName() != "type") {
          $form[$field->getName()]['#access'] = 0;
        }
      }
    }
    else {
      // Mark the store as disabled, as they have already selected the store
      // on the previous step.
      $form["store_id"]['#disabled'] = TRUE;
      $form['advanced'] = [
        '#type' => 'vertical_tabs',
        '#attributes' => ['class' => ['entity-meta']],
        '#weight' => 99,
      ];
      xdebug_break();

      // Add a log field if the "Create new revision" option is checked, or if the
      // current user has the ability to check that option.
      $form['revision_information'] = [
        '#type' => 'details',
        '#group' => 'advanced',
        '#title' => t('Revision information'),
        // Open by default when "Create new revision" is checked.
        '#open' => $product->isNewRevision(),
        '#attributes' => [
          'class' => ['product-form-revision-information'],
        ],
        '#attached' => [
          'library' => ['commerce_product/drupal.commerce_product'],
        ],
        '#weight' => 20,
        '#optional' => TRUE,
        '#access' => $product->isNewRevision() || $currentUser->hasPermission('administer products'),
      ];

      $form['revision'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Create new revision'),
        '#default_value' => $product->isNewRevision(),
        '#access' => $currentUser->hasPermission('administer products'),
        '#group' => 'revision_information',
      ];

      $form['revision_log'] += [
        '#states' => [
          'visible' => [
            ':input[name="revision"]' => ['checked' => TRUE],
          ],
        ],
        '#group' => 'revision_information',
      ];

      // Product author information for administrators.
      $form['author'] = [
        '#type' => 'details',
        '#title' => t('Authoring information'),
        '#group' => 'advanced',
        '#attributes' => [
          'class' => ['product-form-author'],
        ],
        '#attached' => [
          'library' => ['commerce_product/drupal.commerce_product'],
        ],
        '#weight' => 90,
        '#optional' => TRUE,
      ];

      if (isset($form['uid'])) {
        $form['uid']['#group'] = 'author';
      }

      if (isset($form['created'])) {
        $form['created']['#group'] = 'author';
      }
    }

    return $form;
  }

//  public function step1Form($form, $form_state) {
//    $form2 = parent::form($form, $form_state);
//
//    xdebug_break();
//    return $form;
//  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    /** @var \Drupal\commerce_product\entity\Product $product */
    $product = $this->getEntity();
    $step = (int) $form_state->getValue('step');
    if ($this->step === 1) {
      $form_state->setRedirect('entity.commerce_product.add_form_step_2', [
        'commerce_product_type' => $product->getType(),
        'commerce_store' => $form_state->getValue('store_id')[0]["target_id"]
      ]);
    }
    parent::submitForm($form, $form_state);

    // Save as a new revision if requested to do so.
    if (!$form_state->isValueEmpty('revision')) {
      $product->setNewRevision();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    if ($this->step !== 1) {
      /** @var \Drupal\commerce_product\entity\Product $product */
      $product = $this->getEntity();
      try {
        $product->save();
        drupal_set_message($this->t('The product %product_label has been successfully saved.', ['%product_label' => $product->label()]));
        $form_state->setRedirect('entity.commerce_product.canonical', ['commerce_product' => $product->id()]);
      } catch (\Exception $e) {
        drupal_set_message($this->t('The product %product_label could not be saved.', ['%product_label' => $product->label()]), 'error');
        $this->logger('commerce_product')->error($e);
        $form_state->setRebuild();
      }
    }
  }

}

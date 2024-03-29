<?php

namespace Drupal\language_switcher_extended\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerTrait;

/**
 * Configure language_switcher_extended settings for this site.
 */
class LanguageSwitcherExtendedSettingsForm extends ConfigFormBase {

  use MessengerTrait;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'language_switcher_extended_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'language_switcher_extended.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('language_switcher_extended.settings');

    $form['mode'] = [
      '#type' => 'radios',
      '#title' => $this->t('Language Switcher Mode'),
      '#description' => $this->t('Choose the preferred Language Switcher behavior.'),
      '#default_value' => $config->get('mode'),
      '#options' => [
        'default' => $this->t('Default core'),
        'always_link_to_front' => $this->t('Always link to the frontpage'),
        'process_untranslated' => $this->t('Alter the language switcher for untranslated content entities'),
      ],
      'default' => ['#description' => $this->t('Use the default core processor.')],
      'always_link_to_front' => ['#description' => $this->t('Always link all language switcher items to their corresponding frontpage.')],
      'process_untranslated' => ['#description' => $this->t('Choose between different processor methods for resolving untranslated languages for the current content entity.')],
      '#required' => TRUE,
    ];
    $form['untranslated_handler'] = [
      '#type' => 'select',
      '#title' => $this->t('Untranslated Handler'),
      '#description' => $this->t('How should an untranslated language switcher item be resolved.'),
      '#default_value' => $config->get('untranslated_handler'),
      '#options' => [
        'hide_link' => $this->t('Hide the language switcher link'),
        'link_to_front' => $this->t('Link to the frontpage'),
        'no_link' => $this->t('Display the language without link'),
      ],
      '#states' => [
        'visible' => [
          ':input[name="mode"]' => ['value' => 'process_untranslated'],
        ],
        'required' => [
          ':input[name="mode"]' => ['value' => 'process_untranslated'],
        ],
      ],
    ];
    $form['hide_single_link'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Hide single link'),
      '#description' => $this->t('Hide the remaining language switcher links, if non existing translations were hidden.'),
      '#default_value' => $config->get('hide_single_link'),
      '#states' => [
        'visible' => [
          'select[name="untranslated_handler"]' => ['value' => 'hide_link'],
        ],
      ],
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('language_switcher_extended.settings');
    $config->set('mode', $form_state->getValue('mode'));
    $config->set('untranslated_handler', $form_state->getValue('untranslated_handler'));
    $config->set('hide_single_link', $form_state->getValue('hide_single_link'));
    $config->save();

    parent::submitForm($form, $form_state);
  }

}

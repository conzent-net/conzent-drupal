<?php

namespace Drupal\conzent_drupal\Form;

use Drupal\Component\Utility\Html;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use GuzzleHttp\Exception\RequestException;

/**
 * Conzent CMP settings form.
 */
class ConzentSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['conzent_drupal.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'conzent_drupal_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $config = $this->config('conzent_drupal.settings');

    $form['website_key'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('Website Key'),
      '#default_value' => $config->get('website_key') ?? '',
      '#description'   => $this->t('Find this in your Conzent dashboard under Site Settings.'),
      '#required'      => FALSE,
      '#maxlength'     => 128,
    ];

    $form['server_url'] = [
      '#type'          => 'url',
      '#title'         => $this->t('Server URL'),
      '#default_value' => $config->get('server_url') ?? '',
      '#description'   => $this->t('Leave empty for Conzent Cloud. For self-hosted (OCI), enter your server URL.'),
      '#placeholder'   => 'https://app.getconzent.com',
      '#required'      => FALSE,
      '#maxlength'     => 512,
    ];

    if ($config->get('verified') === 'yes') {
      $form['status'] = [
        '#markup' => '<div class="messages messages--status"><p>' . $this->t('Website key verified successfully.') . '</p></div>',
        '#weight' => -10,
      ];
    }

    $server = $config->get('server_url') ?: 'https://app.getconzent.com';
    $form['dashboard_link'] = [
      '#markup' => '<p><a href="' . Html::escape($server) . '" target="_blank" rel="noopener noreferrer">' . $this->t('Open Conzent Dashboard') . '</a></p>',
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    parent::validateForm($form, $form_state);

    $server_url = trim($form_state->getValue('server_url'));
    if (!empty($server_url) && !filter_var($server_url, FILTER_VALIDATE_URL)) {
      $form_state->setErrorByName('server_url', $this->t('Please enter a valid URL for the server.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $config      = $this->config('conzent_drupal.settings');
    $website_key = trim($form_state->getValue('website_key'));
    $server_url  = trim($form_state->getValue('server_url'));

    $config->set('server_url', $server_url);

    if (!empty($website_key)) {
      $site_info = $this->verifyWebsite($website_key, $server_url);

      if (!empty($site_info) && isset($site_info['domain'])) {
        $config->set('website_key', $site_info['website_key'] ?? $website_key);
        $config->set('verified', 'yes');
        $this->messenger()->addStatus($this->t('Settings saved and website verified.'));
      }
      else {
        $config->set('website_key', $website_key);
        $config->set('verified', '');
        $this->messenger()->addWarning($this->t('Settings saved. Could not verify website key — please check your key and server URL.'));
      }
    }
    else {
      $config->set('website_key', '');
      $config->set('verified', '');
      $this->messenger()->addStatus($this->t('Settings saved.'));
    }

    $config->save();
    parent::submitForm($form, $form_state);
  }

  /**
   * Verifies the website key against the Conzent API.
   *
   * @param string $website_key
   *   The website key to verify.
   * @param string $server_url
   *   The server URL to use for verification.
   *
   * @return array
   *   The site information array, or empty array on failure.
   */
  private function verifyWebsite(string $website_key, string $server_url): array {
    if (empty($server_url)) {
      $server_url = 'https://app.getconzent.com';
    }
    $api_url = rtrim($server_url, '/') . '/api/v1/verify?website_id=' . rawurlencode($website_key);

    try {
      /** @var \GuzzleHttp\ClientInterface $client */
      $client   = \Drupal::httpClient();
      $response = $client->get($api_url, ['timeout' => 10]);
      $body     = (string) $response->getBody();
      $data     = json_decode($body, TRUE);
      return is_array($data) ? $data : [];
    }
    catch (RequestException $e) {
      $this->messenger()->addWarning($this->t('Could not connect to Conzent server for verification. Settings have been saved.'));
      return [];
    }
  }

}

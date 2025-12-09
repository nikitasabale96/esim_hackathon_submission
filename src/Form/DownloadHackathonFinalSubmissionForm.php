<?php

namespace Drupal\hackathon_submission\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\Core\Routing\RouteMatchInterface;

class DownloadHackathonFinalSubmissionForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'download_hackathon_final_submission_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    // Get submission ID from route.
    $submission_id = \Drupal::routeMatch()->getParameter('submission_id');

    if (!$submission_id) {
      $form['error'] = [
        '#type' => 'markup',
        '#markup' => '<p>Invalid submission ID.</p>',
      ];
      return $form;
    }

    // Load literature survey data.
    $connection = \Drupal::database();

    $literature_data = $connection->select('hackathon_literature_survey', 'hls')
      ->fields('hls')
      ->condition('id', $submission_id)
      ->execute()
      ->fetchObject();

    // Load final submission data.
    $final_data = $connection->select('hackathon_final_submission', 'hfs')
      ->fields('hfs')
      ->condition('literature_survey_id', $submission_id)
      ->execute()
      ->fetchObject();

    // If no record found.
    if (!$literature_data) {
      $form['error'] = [
        '#type' => 'markup',
        '#markup' => '<p>No submission found.</p>',
      ];
      return $form;
    }

    // Display fields.
    $form['participant_name'] = [
      '#title' => $this->t('Participant Name'),
      '#type' => 'item',
      '#markup' => $literature_data->participant_name,
    ];

    $form['institute'] = [
      '#title' => $this->t('Name of the college/institute'),
      '#type' => 'item',
      '#markup' => $literature_data->institute,
    ];

    $form['circuit_name'] = [
      '#title' => $this->t('Circuit Name'),
      '#type' => 'item',
      '#markup' => $literature_data->circuit_name,
    ];

    // Download link using Link class.
    $url = Url::fromUri('internal:/hackathon-submission/download/final-submission/' . $literature_data->id);

    $form['download_files'] = [
      '#type' => 'item',
      // '#title' => $this->t('Final Report & Project Files'),
      '#markup' => Link::fromTextAndUrl($this->t('Download Final Report and Project Files'), $url)->toString(),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // No submit actions needed.
  }

}

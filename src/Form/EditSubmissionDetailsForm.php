<?php

/**
 * @file
 * Contains \Drupal\hackathon_submission\Form\EditSubmissionDetailsForm.
 */

namespace Drupal\hackathon_submission\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;

class EditSubmissionDetailsForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'edit_submission_details_form';
  }

  public function buildForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state, $no_js_use = NULL) {
    $user = \Drupal::currentUser();
    $submission_id = arg(3);
    if ($user->uid == 0) {
      $msg = \Drupal::messenger()->addError(t('It is mandatory to log in on this website to edit your submission. If you are new user please create a new account first.'));
      //drupal_goto('esim-circuit-simulation-project');
      drupal_goto('user/login', [
        'query' => drupal_get_destination()
        ]);
      return $msg;
    } //$user->uid == 0
    $query = \Drupal::database()->select('hackathon_literature_survey');
    $query->fields('hackathon_literature_survey');
    //$query->condition('uid', $user->uid);
    $query->condition('id', $submission_id);
    //$query->range(0, 1);
    $submission_q = $query->execute();
    $submission_data = $submission_q->fetchObject();
    $form = [];
    $form['participant_name'] = [
      '#title' => t('Participant Name'),
      '#type' => 'textfield',
      '#size' => 70,
      '#maxlength' => 70,
      '#default_value' => $submission_data->participant_name,
    ];
    $form['participant_email'] = [
      '#type' => 'textfield',
      '#title' => t('Email'),
      '#size' => 30,
      '#value' => $user->mail,
      '#disabled' => TRUE,
    ];
    $form['institute'] = [
      '#type' => 'textfield',
      '#title' => t('Name of the college/institute'),
      '#size' => 70,
      '#maxlength' => 70,
      '#required' => TRUE,
      '#default_value' => $submission_data->institute,
    ];
    $form['circuit_name'] = [
      '#title' => t('Circuit Name'),
      '#type' => 'textfield',
      '#size' => 70,
      '#maxlength' => 70,
      '#required' => TRUE,
      '#default_value' => $submission_data->circuit_name,
    ];
    $form['abstract'] = [
      '#title' => t('Abstract'),
      '#type' => 'textarea',
      '#description' => t('The abstract should contain minimum 600 characters and not exceed more than 725 characters'),
      '#rows' => 5,
      '#minlength' => 600,
      '#maxlength' => 725,
      '#required' => TRUE,
      '#default_value' => $submission_data->abstract,
      '#disabled' => TRUE,
      /*'#ajax' => array(
        'callback' => 'update_length_ajax_callback',
        'wrapper' => 'length-div',
        'method' => 'replace',
        'effect' => 'fade',
        ),*/
    ];
    $form['circuit_details'] = [
      '#title' => t('Circuit Details'),
      '#type' => 'textarea',
      '#description' => t('The circuit details should contain minimum 1334 characters and not exceed more than 1600 characters'),
      '#rows' => 5,
      '#minlength' => 1334,
      '#maxlength' => 1600,
      '#required' => TRUE,
      '#default_value' => $submission_data->circuit_details,
      '#disabled' => TRUE,
    ];

    $form["submit"] = [
      "#type" => "submit",
      '#weight' => '6',
      "#value" => "Submit",
    ];
    return $form;
  }

  public function submitForm(array &$form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $submission_id = arg(3);
    $user = \Drupal::currentUser();
    $v = $form_state->getValues();
    $query = "UPDATE hackathon_literature_survey SET 
                circuit_name = :circuit_name,
                participant_name=:participant_name,
                institute=:institute
                WHERE id=:submission_id";
    $args = [
      ":circuit_name" => $v['circuit_name'],
      ':participant_name' => $v['participant_name'],
      ':institute' => $v['institute'],
      ':submission_id' => $submission_id,
    ];
    $result = \Drupal::database()->query($query, $args);
    \Drupal::messenger()->addStatus('Updated successfully');
    drupal_goto('hackathon-submission/all-submissions');
  }

}
?>

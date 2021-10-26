<?php

namespace Drupal\pulso_battool\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Implements a MentalResilienceStressForm.
 */
class MentalResilienceForm extends FormBase {

  /**
   * @var array|mixed|null
   */
  private $options;

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $this->options = \Drupal::config('pulso_battool.settings')->get("mental_resilience_block");

    if ($form_state->has('page')) {
      switch ($form_state->get('page')) {
        case "stress_result":
          $form_state->set("options", $this->options["block_settings"]["stress"]["result"]);
          $form_state->set("risk", $this->options["block_settings"]["risk"]);
          return self::stressResultForm($form, $form_state);
        case "burn-out":
          $form_state->set("blockSettings", $this->options["block_settings"]["burnout"]);
          $form_state->set("options", $this->options["fields"]["burnout"]);
          return self::formBurnOut($form, $form_state);
        case "burn_out_result":
          $form_state->set("options", $this->options["block_settings"]["burnout"]["result"]);
          $form_state->set("risk", $this->options["block_settings"]["risk"]);
          return self::burnOutResultForm($form, $form_state);
        case "work-situation":
          $form_state->set("blockSettings", $this->options["block_settings"]["worksituation"]);
          $form_state->set("options", $this->options["fields"]["worksituation"]);
          return self::formWorkSituation($form, $form_state);
        case "thanks" :
          $form_state->set("blockSettings", $this->options["block_settings"]["thanks"]);
          return self::thanksForm($form, $form_state);
        default:
          $form_state->set("options", $this->options["fields"]["stress"]);
          $form_state->set("blockSettings", $this->options["block_settings"]["stress"]);
          return self::formStress($form, $form_state);
      }
    }
    else {
      $form_state->set("options", $this->options["fields"]["stress"]);
      $form_state->set("blockSettings", $this->options["block_settings"]["stress"]);
      return self::formStress($form, $form_state);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'mental_resilience_stress_form';
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();

    foreach ($values as $key => $value) {
      if ($key !== 'captcha' && !$value) {
        $form_state
          ->setErrorByName($key, $this
            ->t('Please answer all the questions.'));
      }
    }
  }

  /**
   * Method for render form for Stress Test.
   *
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *
   * @return array
   */
  public function formStress(array &$form, FormStateInterface $form_state) {

    $form["title"] = [
      '#type' => 'html_tag',
      '#tag' => 'h2',
      'child' => [
        '#type' => 'html_tag',
        '#tag' => 'span',
        '#value' => $form_state->get("blockSettings")["title"],
      ],
    ];

    $form["introduction"] = [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#attributes' => [
        'class' => ["bat-introduction"],
      ],
      '#value' => $form_state->get("blockSettings")["introduction"],
    ];

    $form["instruction"] = [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#attributes' => [
        'class' => ["bat-instruction"],
      ],
      '#value' => $form_state->get("blockSettings")["instruction"],
    ];

    $form["separator"] = [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#attributes' => [
        'class' => ["bat-tool-th"],
      ],
      '#value' => $form_state->get("blockSettings")["separator"],
    ];

    foreach ($form_state->get("options") as $key => $value) {
      $form['questions']["stress_$key"] = [
        '#type' => 'radios',
        '#title' => $this->t($value),
        '#default_value' => NULL,
        '#options' => $this->getOptions(),
      ];
    }

    $form['captcha'] = array(
      '#type' => 'captcha',
      '#captcha_type' => 'recaptcha/reCAPTCHA',
    );

    $form['actions'] = [
      '#type' => 'actions',
    ];

    $form['actions']['next'] = [
      '#type' => 'submit',
      '#value' => $this->t('Next: Your result'),
      '#submit' => ['::submitFormStress'],
      '#validate' => ['::validateForm'],
    ];

    $form['#theme'] = 'mental_resilience_form';

    return $form;
  }

  /**
   * Method for submit formStress form.
   *
   * @param array $form
   * @param FormStateInterface $form_state
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function submitFormStress(array &$form, FormStateInterface $form_state) {
    $values = $this->prepareValues($form_state->getValues(), "stress");

    $btr = \Drupal::entityTypeManager()
      ->getStorage("bat_tool_result")
      ->create($values);
    $btr->save();

    $testResult = $this->getResult($values);

    $form_state
      ->set('results', $values)
      ->set('testResult', $testResult)
      ->set('bat_tool_result', $btr->id())
      ->set('page', "stress_result")
      ->setRebuild(TRUE);
  }

  /**
   * Method for render results for Stress Test.
   *
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *
   * @return array
   */
  public function stressResultForm(array &$form, FormStateInterface $form_state) {

    $form["title"] = [
      '#type' => 'html_tag',
      '#tag' => 'h2',
      'child' => [
        '#type' => 'html_tag',
        '#tag' => 'span',
        '#value' => $form_state->get("options")["title"],
      ],
    ];

    $testResult = $form_state->get("testResult");
    if ($testResult < 2.85) {
      $testResultRisk = "green";
    }
    elseif ($testResult >= 2.85 && $testResult < 3.35) {
      $testResultRisk = "orange";
    }
    else {
      $testResultRisk = "red";
    }

    $form["svg"] = [
      '#type' => 'html_tag',
      '#tag' => 'img',
      '#attributes' => [
        "src" => drupal_get_path('module', 'pulso_battool') . "/assets/gauge.svg",
        "class" => "bat-tool-gauge img-responsive",
        "data-bat-tool-norm" => "stress",
        "data-bat-tool-score" => $testResult,
        "data-bat-tool-risk-profile" => $form_state->get("risk")[$testResultRisk]
      ],
    ];

    $form["text"] = [
      '#type' => 'markup',
      '#markup' => $form_state->get("options")[$testResultRisk]["value"],
    ];

    $form['actions'] = [
      '#type' => 'actions',
    ];

    $form['actions']['next'] = [
      '#type' => 'submit',
      '#value' => $this->t('Next: Burn-out'),
      '#submit' => ['::submitStressResult'],
      '#validate' => ['::validateForm'],
    ];

    $form['#theme'] = 'mental_resilience_results';

    return $form;
  }

  /**
   * Method for submit form with stress result.
   *
   * @param array $form
   * @param FormStateInterface $form_state
   */
  public function submitStressResult(array &$form, FormStateInterface $form_state) {
    $form_state
      ->set('page', "burn-out")
      ->setRebuild(TRUE);
  }

  /**
   * Method for render form for Burn-Out Test.
   *
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *
   * @return array
   */
  public function formBurnOut(array &$form, FormStateInterface $form_state) {

    $form["title"] = [
      '#type' => 'html_tag',
      '#tag' => 'h2',
      'child' => [
        '#type' => 'html_tag',
        '#tag' => 'span',
        '#value' => $form_state->get("blockSettings")["title"],
      ],
    ];

    $form["introduction"] = [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#attributes' => [
        'class' => ["bat-introduction"],
      ],
      '#value' => $form_state->get("blockSettings")["introduction"],
    ];

    $form["instruction"] = [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#attributes' => [
        'class' => ["bat-instruction"],
      ],
      '#value' => $form_state->get("blockSettings")["instruction"],
    ];

    $form["separator"] = [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#attributes' => [
        'class' => ["bat-tool-th"],
      ],
      '#value' => $form_state->get("blockSettings")["separator"],
    ];

    foreach ($form_state->get("options") as $key => $value) {
      $form['questions']["burnout_$key"] = [
        '#type' => 'radios',
        '#title' => $this->t($value),
        '#default_value' => NULL,
        '#options' => $this->getOptions(),
      ];
    }

    $form['actions'] = [
      '#type' => 'actions',
    ];

    $form['actions']['next'] = [
      '#type' => 'submit',
      '#value' => $this->t('Next: Your result'),
      '#submit' => ['::submitBurnOut'],
      '#validate' => ['::validateForm'],
    ];

    $form['#theme'] = 'mental_resilience_form';

    return $form;
  }

  /**
   * Method for submit Burn-Out test form.
   *
   * @param array $form
   * @param FormStateInterface $form_state
   */
  public function submitBurnOut(array &$form, FormStateInterface $form_state) {
    $values = $this->prepareValues($form_state->getValues(), "burnout");

    $btr = \Drupal::entityTypeManager()
      ->getStorage("bat_tool_result")
      ->load($form_state->get("bat_tool_result"));

    foreach ($values as $key => $value) {
      $btr->set($key, $value);
    }
    $btr->save();

    $testResult = $this->getResult($values);

    $form_state
      ->set('testResult', $testResult)
      ->set('bat_tool_result', $btr->id())
      ->set('page', "burn_out_result")
      ->setRebuild(TRUE);
  }

  /**
   * Method for render results for Stress Test.
   *
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *
   * @return array
   */
  public function burnOutResultForm(array &$form, FormStateInterface $form_state) {

    $form["title"] = [
      '#type' => 'html_tag',
      '#tag' => 'h2',
      'child' => [
        '#type' => 'html_tag',
        '#tag' => 'span',
        '#value' => $form_state->get("options")["title"],
      ],
    ];

    $testResult = $form_state->get("testResult");
    if ($testResult < 2.59) {
      $testResultRisk = "green";
    }
    elseif ($testResult >= 2.59 && $testResult < 3.02) {
      $testResultRisk = "orange";
    }
    else {
      $testResultRisk = "red";
    }

    $form["svg"] = [
      '#type' => 'html_tag',
      '#tag' => 'img',
      '#attributes' => [
        "src" => drupal_get_path('module', 'pulso_battool') . "/assets/gauge.svg",
        "class" => "bat-tool-gauge img-responsive",
        "data-bat-tool-norm" => "burnout",
        "data-bat-tool-score" => $testResult,
        "data-bat-tool-risk-profile" => $form_state->get("risk")[$testResultRisk]
      ],
    ];

    $form["text"] = [
      '#type' => 'markup',
      '#markup' => $form_state->get("options")[$testResultRisk]["value"],
    ];

    $form['actions'] = [
      '#type' => 'actions',
    ];

    $form['actions']['next'] = [
      '#type' => 'submit',
      '#value' => $this->t('Next: Your work situation and personal characteristics'),
      '#submit' => ['::submitBurnOutResultForm'],
      '#validate' => ['::validateForm'],
    ];

    $form['#theme'] = 'mental_resilience_results';

    return $form;
  }

  /**
   * Method for submit form with stress result.
   *
   * @param array $form
   * @param FormStateInterface $form_state
   */
  public function submitBurnOutResultForm(array &$form, FormStateInterface $form_state) {
    $form_state
      ->set('page', "work-situation")
      ->setRebuild(TRUE);
  }

  /**
   * Method for render form for Work Situation Test.
   *
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *
   * @return array
   */
  public function formWorkSituation(array &$form, FormStateInterface $form_state) {

    $form["title"] = [
      '#type' => 'html_tag',
      '#tag' => 'h2',
      'child' => [
        '#type' => 'html_tag',
        '#tag' => 'span',
        '#value' => $form_state->get("blockSettings")["title"],
      ],
    ];

    $form["title2"] = [
      '#type' => 'html_tag',
      '#tag' => 'h2',
      'child' => [
        '#type' => 'html_tag',
        '#tag' => 'span',
        '#value' => $form_state->get("blockSettings")["title2"],
      ],
    ];

    $form["introduction"] = [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#attributes' => [
        'class' => ["bat-introduction"],
      ],
      '#value' => $form_state->get("blockSettings")["introduction"],
    ];

    $form["instruction"] = [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#attributes' => [
        'class' => ["bat-instruction"],
      ],
      '#value' => $form_state->get("blockSettings")["instruction"],
    ];

    foreach ($form_state->get("options") as $key => $value) {
      $form['questions']["worksituation_$key"] = [
        '#type' => 'radios',
        '#title' => $this->t($value),
        '#default_value' => NULL,
        '#options' => $this->getSevenOptions(),
      ];
    }

    $range = range(18, 100);
    $options = ["none" => "- Select -"];
    foreach ($range as $key => $value) {
      $options[$value] = $value;
    }

    $form['personal_characteristics']['age'] = [
      '#type' => 'select',
      '#title' => $this->t('What is your age?'),
      '#options' => $options
    ];

    $form['personal_characteristics']["gender"] = [
      '#type' => 'radios',
      '#title' => $this->t("What is your gender?"),
      '#default_value' => NULL,
      '#options' => [
        "male" => $this->t("Male"),
        "female" => $this->t("Female"),
        "other" => $this->t("Other"),
      ],
    ];

    $form['personal_characteristics']["diplom"] = [
      '#type' => 'radios',
      '#title' => $this->t("What is the highest diploma you have obtained?"),
      '#default_value' => NULL,
      '#options' => [
        "primary_education" => $this->t("Primary education"),
        "lower_secondary_education" => $this->t("Lower secondary education (up to 3rd year)"),
        "upper_secondary_education" => $this->t("Upper secondary education (up to and including 7th year)"),
        "non_university_higher_education" => $this->t("Non-university higher education"),
        "university_higher_education" => $this->t("University higher education"),
      ],
    ];

    $form['personal_characteristics']["sector"] = [
      '#type' => 'radios',
      '#title' => $this->t("What sector do you work in?"),
      '#default_value' => NULL,
      '#options' => [
        "agriculture" => $this->t("Agriculture"),
        "manufacturing" => $this->t("Manufacturing"),
        "services" => $this->t("Services"),
        "health_care" => $this->t("Health Care"),
        "public_administration" => $this->t("Public administration"),
        "education" => $this->t("Education"),
        "self_employed" => $this->t("Self-employed or liberal profession"),
      ],
    ];

    $form['personal_characteristics']["occupation"] = [
      '#type' => 'radios',
      '#title' => $this->t("What is your occupation?"),
      '#default_value' => NULL,
      '#options' => [
        "unskilled_worker" => $this->t("Unskilled worker (e.g. machine operator/operator, production worker, ...)"),
        "skilled_worker" => $this->t("Skilled worker or foreman (e.g. electrician, mechanic, welder, etc.)"),
        "executive" => $this->t("Executive or administrative employee (e.g. typist, secretary, telephonist, shop assistant,...)"),
        "medium_level" => $this->t("Medium-level employee or manager of employees (e.g. ICT expert, teacher, sales representative,...)"),
        "management" => $this->t("Senior, lower or middle management (e.g. business manager, sales manager, office manager, engineer, teacher, etc.)"),
        "director" => $this->t("Senior manager or director (e.g. head of department, senior manager, head of school, ...)"),
        "self_employed" => $this->t("Self-employed or liberal profession"),
      ],
    ];

    $form['personal_characteristics']["work_time"] = [
      '#type' => 'radios',
      '#title' => $this->t("Do you work full-time or part-time?"),
      '#default_value' => NULL,
      '#options' => [
        "full" => $this->t("Full-time"),
        "part" => $this->t("Part-time (e.g. 4/5 or 1/2)"),
      ],
    ];

    $form['personal_characteristics']["contract"] = [
      '#type' => 'radios',
      '#title' => $this->t("What type of contract do you have?"),
      '#default_value' => NULL,
      '#options' => [
        "permanent" => $this->t("Permanent contract (open-ended; statutory)"),
        "temporary" => $this->t("Temporary contract (fixed term, contractual, agency work, etc.)"),
      ],
    ];

    $form['personal_characteristics']["family_situation"] = [
        '#type' => 'radios',
        '#title' => $this->t("What is your family situation?"),
        '#default_value' => NULL,
        '#options' => [
          "single" => $this->t("Single"),
          "single_1_child" => $this->t("Single with one child"),
          "single_several_children" => $this->t("Single with several children"),
          "with_partner_no_children" => $this->t("Living together or married without children"),
          "with_partner_1_child" => $this->t("Living together or married with one child"),
          "with_partner_several_children" => $this->t("Living together or married with several children"),
          "newly_formed_family" => $this->t("Newly formed family (with one or more children)"),
          "single_with_elderly" => $this->t("Single, with one or more elderly relatives living at home (parents, aunt/uncle, …)"),
          "family_without_children" => $this->t("Family without children, with one or more elderly relatives living at home (parents, aunt/uncle, …)"),
          "family_with_children" => $this->t("Family with one or more children, and with one or more elderly relatives living at home (parents, aunt/uncle, …)"),
        ],
      ];
  
      $form['actions'] = [
      '#type' => 'actions',
    ];

    $form['actions']['next'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#submit' => ['::submitWorkSituation'],
      '#validate' => ['::validateForm'],
    ];

    $form['#theme'] = 'mental_resilience_work_situation_form';

    return $form;
  }

  /**
   * Method for submit Work Situation test form.
   *
   * @param array $form
   * @param FormStateInterface $form_state
   */
  public function submitWorkSituation(array &$form, FormStateInterface $form_state) {
    $results = $form_state->getValues();
    $values = $this->prepareValues($results, "worksituation");
    $additional = ["age", "gender", "diplom", "sector", "occupation", "work_time", "contract", "family_situation"];

    $btr = \Drupal::entityTypeManager()
      ->getStorage("bat_tool_result")
      ->load($form_state->get("bat_tool_result"));

    foreach ($values as $key => $value) {
      $btr->set($key, $value);
    }
    foreach ($additional as $key) {
      $btr->set($key, $results[$key]);
    }

    $btr->save();

    $form_state
      ->set('bat_tool_result', $btr->id())
      ->set('page', "thanks")
      ->setRebuild(TRUE);
  }

  /**
   * Method for render results for Stress Test.
   *
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *
   * @return array
   */
  public function thanksForm(array &$form, FormStateInterface $form_state) {

    $form["title"] = [
      '#type' => 'html_tag',
      '#tag' => 'h2',
      'child' => [
        '#type' => 'html_tag',
        '#tag' => 'span',
        '#value' => $form_state->get("blockSettings")["title"],
      ],
    ];

    $form["introduction"] = [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#attributes' => [
        'class' => ["bat-introduction"],
      ],
      '#value' => $form_state->get("blockSettings")["introduction"],
    ];

    $form["instruction"] = [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#attributes' => [
        'class' => ["bat-instruction"],
      ],
      '#value' => $form_state->get("blockSettings")["instruction"],
    ];

    $form['#theme'] = 'mental_resilience_results';

    return $form;
  }

  /**
   * Array of radios options
   *
   * @return array
   */
  private function getOptions() {
    return [
      1 => $this->t('Never'),
      2 => $this->t('Rarely'),
      3 => $this->t('Sometimes'),
      4 => $this->t('Often'),
      5 => $this->t('Always'),
    ];
  }

  /**
   * Array of radios options
   *
   * @return array
   */
  private function getSevenOptions() {
    return [
      1 => $this->t('Strongly disagree'),
      2 => $this->t('Disagree'),
      3 => $this->t('Rather disagree'),
      4 => $this->t('Neither agree nor disagree'),
      5 => $this->t('Rather agree'),
      6 => $this->t('Agree'),
      7 => $this->t('Fully agree'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->messenger()->addStatus($this->t('The form has been submitted'));
  }

  /**
   * Method return test result.
   *
   * @param $values
   *
   * @return float|int
   */
  public function getResult($values) {
    return array_sum($values) / count($values);
  }

  /**
   * Method for convert array values to int.
   *
   * @param $values
   *
   * @return array
   */
  public function arrayToInt($values): array {
    return array_map(function ($value) {
      return (int) $value;
    }, $values);
  }

  /**
   * Method to prepare array with values for saving.
   *
   * @param $values
   * @param null $filter
   *
   * @return array
   */
  public function prepareValues($values, $filter = NULL): array {

    if ($filter) {
      foreach ($values as $key => $value) {
        if (FALSE === strpos($key, $filter)) {
          unset($values[$key]);
        }
      }
    }

    return $this->arrayToInt($values);
  }

  /**
   * Method for get secondary entity fields name.
   */
  public function getDefaultEntityFields(): array {
    $fields = [];
    foreach ($this->options["fields"] as $questionKey => $values) {
      foreach ($values as $key => $value) {
        $fields[$questionKey . "_" . $key] = 0;
      }
    }

    return $fields;
  }
}

<?php

/**
 * @file
 * Contains \Drupal\pulso_battool\Entity\BatToolResult.
 */

namespace Drupal\pulso_battool\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;
use Drupal\Core\Entity\EntityChangedTrait;

/**
 * Defines the BatToolResult entity.
 *
 * @ingroup pulso_battool
 *
 *
 * @ContentEntityType(
 * id = "bat_tool_result",
 * label = @Translation("Bat Tool Result"),
 * handlers = {
 * "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 * "translation", "Drupal\content_translation\ContentTranslationHandler",
 * "list_builder" =
 *   "Drupal\pulso_battool\Entity\Controller\BatToolResultListBuilder",
 * "form" = {
 * "add" = "Drupal\pulso_battool\Form\BatToolResultForm",
 * "edit" = "Drupal\pulso_battool\Form\BatToolResultForm",
 * "delete" = "Drupal\pulso_battool\Form\BatToolResultDeleteForm",
 * },
 * "access" = "Drupal\pulso_battool\BatToolResultAccessControlHandler",
 * },
 * list_cache_contexts = { "user" },
 * base_table = "bat_tool_result",
 * translatable = TRUE,
 * admin_permission = "administer bat_tool_result entity",
 * entity_keys = {
 * "id" = "id",
 * "uuid" = "uuid",
 * "created" = "created",
 * "langcode" = "langcode",
 * },
 * links = {
 * "canonical" = "/bat-tool-result/{bat_tool_result}",
 * "edit-form" = "/bat-tool-result/{bat_tool_result}/edit",
 * "delete-form" = "/bat-tool-result/{bat_tool_result}/delete",
 * "collection" = "/bat-tool-result/list"
 * },
 * field_ui_base_route = "entity.bat_tool_result.bat_tool_result_settings",
 * )
 */
class BatToolResult extends ContentEntityBase {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   *
   * When a new entity instance is added, set the user_id entity reference to
   * the current user as the creator of the instance.
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
  }

  /**
   * {@inheritdoc}
   *
   * Define the field properties here.
   *
   * Field name, type and size determine the table structure.
   *
   * In addition, we can define how the field and its content can be manipulated
   * in the GUI. The behaviour of the widgets used can be determined here.
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    // Standard field, used as unique if primary index.
    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('ID'))
      ->setDescription(t('The ID of the Term entity.'))
      ->setReadOnly(TRUE);

    // Standard field, unique outside of the scope of the current project.
    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The UUID of the Contact entity.'))
      ->setReadOnly(TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['langcode'] = BaseFieldDefinition::create('language')
      ->setName('langcode')
      ->setDefaultValue('x-default')// x-default is the sites default language.
      ->setStorageRequired(TRUE)
      ->setLabel(t('Language code'))
      ->setDescription(t('The language code.'))
      ->setTranslatable(TRUE);

    $fields['default_langcode'] = BaseFieldDefinition::create('boolean')
      ->setName('default_langcode')
      ->setLabel(t('Default Language code'))
      ->setDescription(t('Indicates if this is the default language.'))
      ->setDefaultValue(1) // Default this to 1.
      ->setTargetEntityTypeId('bat_tool_result')
      ->setTargetBundle(NULL)
      ->setStorageRequired(TRUE)
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE);

    //    PERSONAL CHARACTERISTICS
    $fields['age'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('What is your age?'))
      ->setSettings([
        'allowed_values' => range(18, 100),
      ])
      ->setDefaultValue("")
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -2,
      ])
      ->setDisplayOptions('form', [
        'type' => 'options_select',
        'weight' => -2,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setTranslatable(TRUE);

    $fields['gender'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('What is your gender?'))
      ->setSettings([
        'allowed_values' => [
          "male" => "Male",
          "female" => "Female",
          "other" => "Other",
        ],
      ])
      ->setCardinality(1)
      ->setDefaultValue("")
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -2,
      ])
      ->setDisplayOptions('form', [
        'type' => 'options_select',
        'weight' => -2,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setTranslatable(TRUE);

    $fields['diplom'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('What is the highest diploma you have obtained?'))
      ->setSettings([
        'allowed_values' => [
          "primary_education" => "Primary education",
          "lower_secondary_education" => "Lower secondary education (up to 3rd year)",
          "upper_secondary_education" => "Upper secondary education (up to and including 7th year)",
          "non_university_higher_education" => "Non-university higher education",
          "university_higher_education" => "University higher education",
        ],
      ])
      ->setCardinality(1)
      ->setDefaultValue("")
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -2,
      ])
      ->setDisplayOptions('form', [
        'type' => 'options_select',
        'weight' => -2,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setTranslatable(TRUE);

    $fields['sector'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('What sector do you work in?'))
      ->setSettings([
        'allowed_values' => [
          "agriculture" => "Agriculture",
          "manufacturing" => "Manufacturing",
          "services" => "Services",
          "health_care" => "Health Care",
          "public_administration" => "Public administration",
          "education" => "Education",
          "self_employed" => "Self-employed or liberal profession",
        ],
      ])
      ->setCardinality(1)
      ->setDefaultValue("")
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -2,
      ])
      ->setDisplayOptions('form', [
        'type' => 'options_select',
        'weight' => -2,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setTranslatable(TRUE);


    $fields['occupation'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('What is your occupation?'))
      ->setSettings([
        'allowed_values' => [
          "unskilled_worker" => "Unskilled worker (e.g. machine operator/operator, production worker, ...)",
          "skilled_worker" => "Skilled worker or foreman (e.g. electrician, mechanic, welder, etc.)",
          "executive" => "Executive or administrative employee (e.g. typist, secretary, telephonist, shop assistant,...)",
          "medium_level" => "Medium-level employee or manager of employees (e.g. ICT expert, teacher, sales representative,...)",
          "management" => "Senior, lower or middle management (e.g. business manager, sales manager, office manager, engineer, teacher, etc.)",
          "director" => "Senior manager or director (e.g. head of department, senior manager, head of school, ...)",
          "self_employed" => "Self-employed or liberal profession",
        ],
      ])
      ->setCardinality(1)
      ->setDefaultValue("")
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -2,
      ])
      ->setDisplayOptions('form', [
        'type' => 'options_select',
        'weight' => -2,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setTranslatable(TRUE);

    $fields['work_time'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('Do you work full-time or part-time?'))
      ->setSettings([
        'allowed_values' => [
          "full" => "Full-time",
          "part" => "Part-time (e.g. 4/5 or 1/2)",
        ],
      ])
      ->setCardinality(1)
      ->setDefaultValue("")
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -2,
      ])
      ->setDisplayOptions('form', [
        'type' => 'options_select',
        'weight' => -2,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setTranslatable(TRUE);

    $fields['contract'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('What type of contract do you have?'))
      ->setSettings([
        'allowed_values' => [
          "permanent" => "Permanent contract (open-ended; statutory)",
          "temporary" => "Temporary contract (fixed term, contractual, agency work, etc.)",
        ],
      ])
      ->setCardinality(1)
      ->setDefaultValue("")
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -2,
      ])
      ->setDisplayOptions('form', [
        'type' => 'options_select',
        'weight' => -2,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setTranslatable(TRUE);

    // Questions
    $questions = [
      "stress" => [
        1 => "I have trouble falling or staying asleep",
        2 => "I tend to worry",
        3 => "I feel tense and stressed",
        4 => "I feel anxious and/or suffer from panic attacks",
        5 => "Noise and crowds disturb me",
        6 => "I suffer from palpitations or chest pain",
        7 => "I suffer from stomach and/or intestinal complaints",
        8 => "I suffer from headaches",
        9 => "I suffer from muscle pain, for example in the neck, shoulder or back",
        10 => "I often get sick",
      ],
      "burnout" => [
        1 => "At work, I feel mentally exhausted",
        2 => "Everything I do at work requires a great deal of effort",
        3 => "After a day at work, I find it hard to recover my energy",
        4 => "At work, I feel physically exhausted",
        5 => "When I get up in the morning, I lack the energy to start a new day at work",
        6 => "I want to be active at work, but somehow I am unable to manage",
        7 => "When I exert myself at work, I quickly get tired",
        8 => "At the end of my working day, I feel mentally exhausted and drained",
        9 => "I struggle to find any enthusiasm for my work",
        10 => "At work, I do not think much about what I am doing and I function on autopilot",
        11 => "I feel a strong aversion towards my job",
        12 => "I feel indifferent about my job",
        13 => "I’m cynical about what my work means to others",
        14 => "At work, I have trouble staying focused",
        15 => "At work I struggle to think clearly",
        16 => "I’m forgetful and distracted at work",
        17 => "When I’m working, I have trouble concentrating",
        18 => "I make mistakes in my work because I have my mind on other things",
        19 => "At work, I feel unable to control my emotions",
        20 => "I do not recognize myself in the way I react emotionally at work",
        21 => "During my work I become irritable when things don’t go my way",
        22 => "I get upset or sad at work without knowing why",
        23 => "At work I may overreact unintentionally",
      ],
      "worksituation" => [
        1 => "My work content matches my capabilities.",
        2 => "I think I have to work too hard.",
        3 => "I can easily combine my work with my private life.",
        4 => "I can count on the support of my direct line manager.",
        5 => "I can take many decisions myself in my job.",
        6 => "I worry about losing my job.",
        7 => "I can count on the support of my colleagues.",
        8 => "In my job, I encounter situations that make it emotionally difficult for me.",
        9 => "My opinion is taken into account when making decisions.",
        10 => "I know exactly what others expect of me at work.",
      ],
    ];

    if ($questions) {
      foreach ($questions as $questionKey => $values) {
        foreach ($values as $key => $value) {
          $fields[$questionKey . '_' . $key] = BaseFieldDefinition::create('string')
            ->setLabel(t($value))
            ->setRevisionable(FALSE)
            ->setRequired(FALSE)
            ->setDefaultValue("")
            ->setTranslatable(TRUE)      ->setDefaultValue("")
            ->setDisplayOptions('view', [
              'label' => 'above',
              'type' => 'string',
              'weight' => -2,
            ])
            ->setDisplayOptions('form', [
              'type' => 'options_select',
              'weight' => -2,
            ])
            ->setDisplayConfigurable('form', TRUE)
            ->setDisplayConfigurable('view', TRUE);
        }
      }
    }

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

}

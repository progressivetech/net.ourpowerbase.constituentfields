<?php

// Create our custom group and fields
return array(
  0 => array(
    'entity' => 'CustomGroup',
    'name' => 'constituentfields_individual_fields',
    'update' => 'never',
    'params' => array (
      'version' => 3,
      'is_active' => 1,
      'name' => 'constituentfields_individual_fields',
      'title' => 'Constituent Info - Individuals',
      'extends' => 'Individual',
      'style' => 'inline',
      'collapse_display' => '0',
      'is_active' => '1',
      'is_multiple' => '0',
      'collapse_adv_display' => '0',
      'is_reserved' => '0',
      'is_public' => '1',
      'api.custom_field.create' => array(
        array(
          'custom_group_id' => '$value.id',
          'label' => 'Individual Constituent Type',
          'name' => 'constituentfields_individual_constituent_type',
          'data_type' => 'String',
          'html_type' => 'CheckBox',
          'is_required' => '1',
          'is_searchable' => '1',
          'is_search_range' => '0',
          'weight' => '10',
          'is_active' => '1',
          'is_view' => '0',
          'options_per_line' => '2',
          'text_length' => '255',
          'note_columns' => '60',
          'note_rows' => '4',
          'in_selector' => '0'
        ),
        array(
          'custom_group_id' => '$value.id',
          'name' => 'constituentfields_staff_responsible',
          'label' => 'Staff Responsible',
          'data_type' => 'ContactReference',
          'html_type' => 'Autocomplete-Select',
          'is_searchable' => '1',
          'is_search_range' => '0',
          'weight' => '20',
          'is_active' => '1',
          'text_length' => '255',
          'note_columns' => '60',
          'note_rows' => '4',
          'filter' => 'action=lookup&group=287',
          'in_selector' => '0'
        ),
        array(
          'custom_group_id' => '$value.id',
          'label' => 'Date Started',
          'name' => 'constituentfields_date_started',
          'data_type' => 'Date',
          'html_type' => 'Select Date',
          'is_required' => '0',
          'is_searchable' => '1',
          'is_search_range' => '1',
          'weight' => '30',
          'is_active' => '1',
          'is_view' => '0',
          'text_length' => '255',
          'start_date_years' => '30',
          'date_format' => 'mm/dd/yy',
          'note_columns' => '60',
          'note_rows' => '4',
          'in_selector' => '0'
        ),
        array(
          'custom_group_id' => '$value.id',
          'label' => 'How Started',
          'name' => 'constituentfields_how_started',
          'data_type' => 'String',
          'html_type' => 'Select',
          'is_required' => '0',
          'is_searchable' => '1',
          'is_search_range' => '0',
          'weight' => '40',
          'help_post' => 'Describe how an individual first came to the organization.',
          'is_active' => '1',
          'is_view' => '0',
          'text_length' => '255',
          'note_columns' => '60',
          'note_rows' => '4',
          'in_selector' => '0'
        ), 
        array(
          'custom_group_id' => '$value.id',
          'label' => 'Preferred Gender Pronouns',
          'name' => 'constituentfields_preferred_gender_pronouns',
          'data_type' => 'String',
          'html_type' => 'Text',
          'is_required' => '0',
          'is_searchable' => '1',
          'is_search_range' => '0',
          'weight' => '40',
          'help_post' => '',
          'is_active' => '1',
          'is_view' => '0',
          'text_length' => '255',
          'note_columns' => '60',
          'note_rows' => '4',
          'in_selector' => '0'
        ),
        array(
          'custom_group_id' => '$value.id',
          'label' => 'Leadership Level',
          'name' => 'constituentfields_leadership_level',
          'data_type' => 'String',
          'html_type' => 'Select',
          'is_required' => '0',
          'is_searchable' => '1',
          'is_search_range' => '0',
          'weight' => '10',
          'is_active' => '1',
          'is_view' => '0',
          'in_selector' => '0'
        ),
        array(
          'custom_group_id' => '$value.id',
          'label' => 'Known Languages',
          'name' => 'constituentfields_known_languages',
          'data_type' => 'String',
          'html_type' => 'Multi-Select',
          'is_required' => '0',
          'is_searchable' => '1',
          'is_search_range' => '0',
          'weight' => '10',
          'is_active' => '1',
          'is_view' => '0',
          'in_selector' => '0'
        ),

      ),
    ),
  ),
  1 => array(
    'entity' => 'CustomGroup',
    'name' => 'constituentfields_organization_fields',
    'update' => 'never',
    'params' => array (
      'version' => 3,
      'is_active' => 1,
      'name' => 'constituentfields_organization_fields',
      'title' => 'Constituent Info - Organization',
      'extends' => 'Organization',
      'style' => 'inline',
      'collapse_display' => '0',
      'is_active' => '1',
      'is_multiple' => '0',
      'collapse_adv_display' => '0',
      'is_reserved' => '0',
      'is_public' => '1',
      'api.custom_field.create' => array(
        array(
          'custom_group_id' => '$value.id',
          'label' => 'Organization Constituent Type',
          'name' => 'constituentfields_organization_constituent_type',
          'data_type' => 'String',
          'html_type' => 'CheckBox',
          'is_required' => '0',
          'is_searchable' => '1',
          'is_search_range' => '0',
          'weight' => '10',
          'is_active' => '1',
          'is_view' => '0',
          'options_per_line' => '2',
          'text_length' => '255',
          'note_columns' => '60',
          'note_rows' => '4',
          'in_selector' => '0'
        ),
      ),
    ),
  ),
  2 => array(
    'entity' => 'OptionGroup',
    'name' => 'constituentfields_how_started_values',
    'update' => 'never',
    'params' => array (
      'version' => 3,
      'name' => 'constituentfields_how_started_values',
      'title' => 'How Started Values',
      'is_reserved' => '1',
      'is_active' => '1',
      'is_locked' => '0',
      'api.option_value.create' => array(
        array(
          'option_group_id' => '$value.id',
          'label' => 'Meeting',
          'name' => 'constituentfields_meeting',
          'value' => '1',
          'is_default' => '0',
          'weight' => '10',
          'is_optgroup' => '0',
          'is_reserved' => '0',
          'is_active' => '1'
        ),
        array(
          'option_group_id' => '$value.id',
          'label' => 'Event',
          'name' => 'constituentfields_event',
          'value' => '2',
          'is_default' => '0',
          'weight' => '20',
          'is_optgroup' => '0',
          'is_reserved' => '0',
          'is_active' => '1'
        ),
        array(
          'option_group_id' => '$value.id',
          'label' => 'Phone',
          'name' => 'constituentfields_phone',
          'value' => '3',
          'is_default' => '0',
          'weight' => '30',
          'is_optgroup' => '0',
          'is_reserved' => '0',
          'is_active' => '1'
        ),
        array(
          'option_group_id' => '$value.id',
          'label' => 'Action',
          'name' => 'constituentfields_action',
          'value' => '4',
          'is_default' => '0',
          'weight' => '40',
          'is_optgroup' => '0',
          'is_reserved' => '0',
          'is_active' => '1'
        ),
        array(
          'option_group_id' => '$value.id',
          'label' => 'Training',
          'name' => 'constituentfields_training',
          'value' => '5',
          'is_default' => '0',
          'weight' => '50',
          'is_optgroup' => '0',
          'is_reserved' => '0',
          'is_active' => '1'
        ),
      ),
    ),
  ),
  3 => array(
    'entity' => 'OptionGroup',
    'name' => 'constituentfields_individual_constituent_type_values',
    'update' => 'never',
    'params' => array (
      'version' => 3,
      'name' => 'constituentfields_individual_constituent_type_values',
      'title' => 'Individual Constituent Type Values',
      'is_reserved' => '1',
      'is_active' => '1',
      'is_locked' => '0',
      'api.option_value.create' => array(
        array(
          'option_group_id' => '$value.id',
          'label' => 'Organizing Prospect',
          'name' => 'constituentfields_organizing_prospect',
          'value' => '1',
          'is_default' => '0',
          'weight' => '10',
          'is_optgroup' => '0',
          'is_reserved' => '0',
          'is_active' => '1'
        ),
        array(
          'option_group_id' => '$value.id',
          'label' => 'Donor Prospect',
          'name' => 'constituentfields_donor_prospect',
          'value' => '2',
          'is_default' => '0',
          'weight' => '20',
          'is_optgroup' => '0',
          'is_reserved' => '0',
          'is_active' => '1'
        ),
        array(
          'option_group_id' => '$value.id',
          'label' => 'Vendor',
          'name' => 'constituentfields_vendor',
          'value' => '3',
          'is_default' => '0',
          'weight' => '30',
          'is_optgroup' => '0',
          'is_reserved' => '0',
          'is_active' => '1'
        ),
        array(
          'option_group_id' => '$value.id',
          'label' => 'Supporter',
          'name' => 'constituentfields_supporter',
          'value' => '4',
          'is_default' => '0',
          'weight' => '40',
          'is_optgroup' => '0',
          'is_reserved' => '0',
          'is_active' => '1'
        ),
      ),
    ),
  ),
  4 => array(
    'entity' => 'OptionGroup',
    'name' => 'constituentfields_organization_constituent_type_values',
    'update' => 'never',
    'params' => array (
      'version' => 3,
      'name' => 'constituentfields_organization_constituent_type_values',
      'title' => 'Organization Constituent Type Values',
      'is_reserved' => '1',
      'is_active' => '1',
      'is_locked' => '0',
      'api.option_value.create' => array(
        array(
          'option_group_id' => '$value.id',
          'label' => 'Organization',
          'name' => 'constituentfields_organization',
          'value' => '2',
          'is_default' => '0',
          'weight' => '20',
          'is_optgroup' => '0',
          'is_reserved' => '0',
          'is_active' => '1'
        ),
        array(
          'option_group_id' => '$value.id',
          'label' => 'Government',
          'name' => 'constituentfields_government',
          'value' => '3',
          'is_default' => '0',
          'weight' => '30',
          'is_optgroup' => '0',
          'is_reserved' => '0',
          'is_active' => '1'
        ),
        array(
          'option_group_id' => '$value.id',
          'label' => 'Business',
          'name' => 'constituentfields_business',
          'value' => '4',
          'is_default' => '0',
          'weight' => '40',
          'is_optgroup' => '0',
          'is_reserved' => '0',
          'is_active' => '1'
        ),
      ),
    ),
  ),
  5 => array(
    'entity' => 'Group',
    'name' => 'constituentfields_staff_group',
    'update' => 'never',
    'params' => array (
      'version' => 3,
      'title' => 'Staff',
      'name' => 'constituentfields_staff_group',
      'description' => "All current staff should be placed in this group.",
      'is_active' => '1',
      'visibility' => 'User and User Admin Only', 
      'is_hidden' => 0,
    ),
  ),
  6 => array(
    'entity' => 'CustomGroup',
    'name' => 'constituentfields_communications_fields',
    'update' => 'never',
    'params' => array (
      'version' => 3,
      'is_active' => 1,
      'name' => 'constituentfields_communications_fields',
      'title' => 'Communication Details',
      'extends' => 'Contact',
      'style' => 'inline',
      'collapse_display' => '0',
      'is_active' => '1',
      'is_multiple' => '0',
      'collapse_adv_display' => '0',
      'is_reserved' => '0',
      'is_public' => '1',
      'api.custom_field.create' => array(
        array(
          'custom_group_id' => '$value.id',
          'label' => 'Subscribe to Newsletter',
          'name' => 'constituentfields_subscribe_to_newsletter',
          'data_type' => 'Boolean',
          'html_type' => 'Radio',
          'is_required' => '0',
          'is_searchable' => '1',
          'is_search_range' => '0',
          'weight' => '10',
          'is_active' => '1',
          'is_view' => '0',
          'options_per_line' => '2',
          'text_length' => '255',
          'note_columns' => '60',
          'note_rows' => '4',
          'in_selector' => '0'
        ),
      ),
    ),
  ),
  7 => array(
    'entity' => 'OptionGroup',
    'name' => 'constituentfields_language_spoken_values',
    'update' => 'never',
    'params' => array (
      'version' => 3,
      'name' => 'constituentfields_known_languages_values',
      'title' => 'Known Languages Values',
      'is_reserved' => '1',
      'is_active' => '1',
      'is_locked' => '0',
      'api.option_value.create' => array(
        array(
          'option_group_id' => '$value.id',
          'label' => 'English',
          'name' => 'constituentfields_english',
          'value' => '1',
          'is_default' => '0',
          'weight' => '10',
          'is_optgroup' => '0',
          'is_reserved' => '0',
          'is_active' => '1'
        ),
        array(
          'option_group_id' => '$value.id',
          'label' => 'Spanish',
          'name' => 'constituentfields_spanish',
          'value' => '2',
          'is_default' => '0',
          'weight' => '20',
          'is_optgroup' => '0',
          'is_reserved' => '0',
          'is_active' => '1'
        ),
        array(
          'option_group_id' => '$value.id',
          'label' => 'Mandarin',
          'name' => 'constituentfields_mandarin',
          'value' => '3',
          'is_default' => '0',
          'weight' => '30',
          'is_optgroup' => '0',
          'is_reserved' => '0',
          'is_active' => '1'
        ),
      ),
    ),
  ),
  8 => array(
    'entity' => 'OptionGroup',
    'name' => 'constituentfields_leadership_level_values',
    'update' => 'never',
    'params' => array (
      'version' => 3,
      'name' => 'constituentfields_leadership_level_values',
      'title' => 'Leadership Level Values',
      'is_reserved' => '1',
      'is_active' => '1',
      'is_locked' => '0',
      'api.option_value.create' => array(
        array(
          'option_group_id' => '$value.id',
          'label' => 'Highly Engaged (1)',
          'name' => 'constituentfields_highly_engaged',
          'value' => '1',
          'is_default' => '0',
          'weight' => '10',
          'is_optgroup' => '0',
          'is_reserved' => '0',
          'is_active' => '1'
        ),
        array(
          'option_group_id' => '$value.id',
          'label' => 'Medium (2)',
          'name' => 'constituentfields_medium',
          'value' => '2',
          'is_default' => '0',
          'weight' => '20',
          'is_optgroup' => '0',
          'is_reserved' => '0',
          'is_active' => '1'
        ),
        array(
          'option_group_id' => '$value.id',
          'label' => 'Beginning Leader (3)',
          'name' => 'constituentfields_beginner',
          'value' => '3',
          'is_default' => '0',
          'weight' => '30',
          'is_optgroup' => '0',
          'is_reserved' => '0',
          'is_active' => '1'
        ),
      ),
    ),
  )
);

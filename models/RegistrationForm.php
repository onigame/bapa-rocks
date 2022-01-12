<?php

namespace app\models;

class RegistrationForm extends \dektrium\user\models\RegistrationForm {

  public $check;

  public function rules() {
    $rules = parent::rules();
    $rules['checkRequired'] = ['check', 'required'];
    $rules['checkLength'] = ['check', 'string', 'max' => 7];
    $rules['checkValid'] = ['check', 'match', 'pattern' => '/^pinball$/'];
 
    return $rules;
  }

}

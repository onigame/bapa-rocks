<?php

namespace app\models;

use dektrium\user\models\Profile as BaseProfile;

class Profile extends BaseProfile
{
    public function rules()
    {
        $rules = parent::rules();
        // add some rules
        $rules['ifpaLength']   = ['ifpa', 'integer'];
        
        return $rules;
    }

    public function attributeLabels() {
        $labels = parent::attributeLabels();
   
        $labels['public_email'] = \Yii::t('user', 'Email');
        $labels['ifpa'] = \Yii::t('user', 'IFPA number');

        return $labels;
    }

}

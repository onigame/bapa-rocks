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
        $rules['initials']   = ['initials', 'string'];
        $rules['phone_number']   = ['phone_number', 'string'];
        
        return $rules;
    }

    public function attributeLabels() {
        $labels = parent::attributeLabels();
   
        $labels['public_email'] = \Yii::t('user', 'Public Email (not used)');
        $labels['ifpa'] = \Yii::t('user', 'IFPA number');
        $labels['initials'] = \Yii::t('user', 'High Score Initials');
        $labels['phone_number'] = \Yii::t('user', 'Phone Number');
        $labels['vaccination'] = \Yii::t('user', 'Vacc. Card Status');

        return $labels;
    }

}

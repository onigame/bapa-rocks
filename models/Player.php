<?php

namespace app\models;

use app\models\Session;
use dektrium\user\models\User as BaseUser;

class Player extends User {

  public function getName() {
    return $this->profile->name;
  }

}

<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $model app\models\Vote */

?>

<table class="vote-view">
<tr>

<th><?= $model->pollChoice->name ?>: </th>

<?php

  $names = ['Might Not Show Up', 'Rather Not', 'Is OK', 'Works Great'];

  for ($val = 0; $val <= 3; ++$val) {
    echo '<td>';
    if ($model->value == $val) {
      echo Html::submitButton($names[$val].' (current selection)', ['class' => 'btn btn-success btn-xs disabled']);
    } else {
      $form = ActiveForm::begin([
        'options' => ['data' => ['pjax' => true]],
        'action' => '/vote/modify-vote',
        'id' => 'user-vote-form'
      ]);
      echo Html::hiddenInput('id', $model->id);
      echo Html::hiddenInput('value', $val);
      echo Html::submitButton($names[$val], ['class' => 'btn btn-success btn-xs']);
      $form = ActiveForm::end();
      echo ' ';
    }
    echo '</td>';
  }

?>

</tr>
</table>


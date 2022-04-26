<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\PollChoice;
use app\models\Vote;

/* @var $this yii\web\View */
/* @var $model app\models\Poll */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Polls', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="poll-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'status',
            'name',
            'description',
            'created_at',
            'updated_at',
        ],
    ]) ?>

    <?php
if (Yii::$app->user->can('GenericManagerPermission')) {
    ?>

    <h2>Poll Status</h2>

    <?php

    $pcs = PollChoice::find()->where(['poll_id' => $model->id])->all();
    $pc_count = PollChoice::find()->where(['poll_id' => $model->id])->count();

    $names = ['Might Not Show Up', 'Rather Not', 'Is OK', 'Works Great'];

    $pita = array();
    $allnames = array();

    $points = array();
    $approve = array();
    $love = array();
    $approve_pita = array();
    $love_pita = array();

    foreach ($pcs as $pc) {
      $votes = Vote::find()->where(['pollchoice_id' => $pc->id])->andWhere(['<>', 'value', 0])->all();
      foreach ($votes as $vote) {
        $user = $vote->user;
        $uid = $user->id;
        if (! array_key_exists($uid, $pita))
          $pita[$uid] = 0;
        $pita[$uid]++;
        $allnames[$uid] = $user->name;
      }
      $points[$pc->id] = 0;
      $approve[$pc->id] = 0;
      $love[$pc->id] = 0;
      $approve_pita[$pc->id] = 0;
      $love_pita[$pc->id] = 0;
    }

    echo "<h3>Raw Votes</h3>";

    foreach ($pcs as $pc) {
      echo $pc->name . ": ";
      echo "<ul>\n";
      for ($val = 0; $val <= 3; ++$val) {
        echo "<li><b>" . $names[$val] . "</b>: \n";
        $votes = Vote::find()->where(['pollchoice_id' => $pc->id, 'value' => $val])->all();
        $people = array();
        foreach ($votes as $vote) {
          $user = $vote->user;
          $people[] = $user->name;
          if ($val != 0) {
            $points[$pc->id] += $val;
            if ($val > 1) $points[$pc->id] ++;

            $approve[$pc->id] += 1;
            $approve_pita[$pc->id] += 1 * $pita[$user->id] / $pc_count;
            $love[$pc->id] += $val;
            $love_pita[$pc->id] += $val * $pita[$user->id] / $pc_count;
          }
        }
        echo "(" . count($people) . " votes) ";
        echo join(', ', $people);
      }
      echo "</ul>\n";
    }

    echo "<h3>PITA Factor</h3>";

    echo "<ul>\n";
    foreach ($pita as $uid => $pita_score) {
      echo "<li>$allnames[$uid]: ($pita_score/$pc_count)";
    }
    echo "</ul>\n";

    echo "<h3>Scores</h3>";
    echo "<table border>\n";
    echo "<tr><th>Choice</th><th>Points</th><th>Approve</th><th>(PITA)</th><th>Love</th><th>(PITA)</th></tr>";
    foreach ($pcs as $pc) {
      echo "<tr>";
      echo "<td>$pc->name</td>";
      echo "<td>".$points[$pc->id]."</td>";
      echo "<td>".$approve[$pc->id]."</td>";
      echo "<td>".$approve_pita[$pc->id]."</td>";
      echo "<td>".$love[$pc->id]."</td>";
      echo "<td>".$love_pita[$pc->id]."</td>";
      echo "</tr>";
    }
    echo "</table>\n";
}
    ?>

</div>

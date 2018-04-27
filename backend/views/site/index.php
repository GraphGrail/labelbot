<?php

/* @var $this yii\web\View */

$this->title = 'My Yii Application';

$entities = [
    'Task' => '',
    'User' => '',
    'Moderator' => '',
    'Dataset' => '',
    'Data' => '',
    'LabelGroup' => '',
    'Label' => '',
    'BlockchainCallback' => '',

];

?>
<div class="site-index">

    <div class="jumbotron">
        <h1>LabelApp Admin Panel</h1>
        <p class="lead">Choose entity you want to work with.</p>
    </div>

    <div class="body-content">
        <div class="row">
<?php foreach ($entities as $name => $description) {
    echo $this->render('_entity', ['name' => $name, 'description' =>$description]);
}
?>
        </div>
    </div>
</div>

<?php 

        require_once("./model/DataWorker.php");

        $dataWorker = new DataWorker();

?>

<h1>Import rates</h1>

<form method="post" id="import-rates-form">
    <div class="row valign-wrapper">
        <div class="file-field input-field col s12 m6">
            <div class="btn">
                <span>Choose file</span>
                <input id="import-rates-file" name="import-rates-file" type="file">
            </div>
            <div class="file-path-wrapper">
                <input  id="import-rates-file-path" class="file-path validate" type="text">
            </div>
        </div>
        <div class="col s12 m6">
            <button type="button" class="btn cyan darken-1" id="import-rates-button">Import</button>         
        </div>
    </div>
</form>

<div id="import-rates-result"></div>
<?php

    require_once("./model/DataWorker.php");

    $dataWorker = new DataWorker();
    $cities = $dataWorker->get_all_cities();
    $cargo = $dataWorker->get_all_cargo();

    if (isset($_SESSION['routes'])) {
        $routes = $_SESSION['routes'];
    }
    else {
        $routes = null;
    }
?>
<h1>Request</h1>

<div class="row">
    <form class="col s12">
        <div class="row">

            <div class="input-field col s12 m12" style="display: none">
                <input hidden value="<?= $user['id'] ?>" id="profile-id" type="text" class="validate">
                <label for="profile-id">User id</label>
            </div>

            <div class="input-field col s12 m6">
                <select id="start-point-select">  
                    <option value="" disabled selected>Choose your option</option>                  
                    <?php foreach ($cities as $city) {?>
                        <option value="<?= $city['name'] ?>"><?= $city['name'] ?></option>
                    <?php }?>
                </select>
                <label>From point</label>
                <span class="helper-text" id="start-point-helper" data-error="wrong" data-success="right">Please choose start point</span>
            </div>
            <div class="input-field col s12 m6">
                <select id="end-point-select">
                    <option value="" disabled selected>Choose your option</option>
                    <?php foreach ($cities as $city) {?>
                        <option value="<?= $city['name'] ?>"><?= $city['name'] ?></option>
                    <?php }?>
                </select>
                <label>To point</label>
                <span class="helper-text" id="end-point-helper" data-error="wrong" data-success="right">Please choose end point</span>
            </div>
            <div class="input-field col s12 m6">
                <select id="cargo-select">
                    <option value="" disabled selected>Choose your option</option>
                    <?php foreach ($cargo as $cargo_item) {?>
                        <option value="<?= $cargo_item['id'] ?>"><?= $cargo_item['custom_code'] . " " . $cargo_item['name'] ?></option>
                    <?php }?>
                </select>
                <label>Cargo</label>
                <span class="helper-text" id="cargo-helper" data-error="wrong" data-success="right">Please choose cargo</span>
            </div>
            <div class="input-field col s12 m6">
                <input id="cargo-gw-input" type="text" class="validate">
                <label for="cargo-gw-input">Gross weight, mt</label>
                <span class="helper-text" data-error="wrong" data-success="right"></span>
            </div>

        </div>

        <div class="row">
            <div class="col s6 m3 right">
                <button type="button" class="btn right cyan darken-1" id="request-button">Send</button>
            </div>
        </div>
        <div class="row">
            <div class="col s12 m12">
                <duv id="request-result-message"></div>
            </div>
        </div>
    </form>
<div>

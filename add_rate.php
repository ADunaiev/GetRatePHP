<?php 

        require_once("./model/DataWorker.php");

        $dataWorker = new DataWorker();

        $cities = $dataWorker->get_all_cities();
        $suppliers = $dataWorker->get_all_suppliers();
        $transport_types = $dataWorker->get_all_transport_types();
        $currencies = $dataWorker->get_all_currencies();

        if (isset($_SESSION['start-point'])) {
            $start_point = $_SESSION['start-point'];
        }
        else {
                $start_point = "";
        }

        if (isset($_SESSION['end-point'])) {
            $end_point = $_SESSION['end-point'];
        }
        else {
                $end_point = "";
        }
?>

<h1>Add rate</h1>

<div class="row">
    <form class="col s12">
        <div class="row">

            <div class="input-field col s12 m12" style="display: none">
                <input hidden value="<?= $user['id'] ?>" id="add-rate-user" type="text" class="validate">
                <label for="profile-id">User id</label>
            </div>

            <div class="input-field col s12 m6">
                <i class="material-icons prefix cyan-text text-darken-1">rowing</i>
                <select id="add-rate-supplier-select">  
                    <option value="" disabled selected>Choose your option</option>                  
                    <?php foreach ($suppliers as $supplier) {?>
                        <option value="<?= $supplier['trinity_code'] ?>"><?= $supplier['name'] ?></option>
                    <?php }?>
                </select>
                <label>Supplier</label>
                <span class="helper-text" id="add-rate-supplier-helper" data-error="wrong" data-success="right">Please choose supplier</span>
            </div>


            <div class="input-field col s12 m6">
                <i class="material-icons prefix cyan-text text-darken-1">directions_boat</i>
                <select id="add-rate-transport-type-select">  
                    <option value="" disabled selected>Choose your option</option>                  
                    <?php foreach ($transport_types as $type) {?>
                        <option value="<?= $type['trinity_code'] ?>"><?= $type['name'] ?></option>
                    <?php }?>
                </select>
                <label>Transport type</label>
                <span class="helper-text" id="add-rate-transport-type-helper" data-error="wrong" data-success="right">Please choose transport type</span>
            </div>


            <div class="input-field col s12 m6">
                <i class="material-icons prefix cyan-text text-darken-1">location_on</i>
                <select id="add-rate-start-point-select">  
                    <option value="" disabled selected>Choose your option</option>                  
                    <?php foreach ($cities as $city) {?>
                        <option value="<?= $city['name'] ?>" <?= $start_point == $city['name'] ? "selected" : "" ?>><?= $city['name'] ?></option>
                    <?php }?>
                </select>
                <label>From point</label>
                <span class="helper-text" id="add-rate-start-point-helper" data-error="wrong" data-success="right">Please choose start point</span>
            </div>

            <div class="input-field col s12 m6">
                <i class="material-icons prefix cyan-text text-darken-1">location_on</i>
                <select id="add-rate-end-point-select">
                    <option value="" disabled selected>Choose your option</option>
                    <?php foreach ($cities as $city) {?>
                        <option value="<?= $city['name'] ?>" <?= $end_point == $city['name'] ? "selected" : "" ?>><?= $city['name'] ?></option>
                    <?php }?>
                </select>
                <label>To point</label>
                <span class="helper-text" id="add-rate-end-point-helper" data-error="wrong" data-success="right">Please choose end point</span>
            </div>

            <div class="input-field col s8 m3">
                <i class="material-icons prefix cyan-text text-darken-1">attach_money</i>
                <input id="add-rate-amount-input" type="number">
                <label for="add-rate-amount-input">Amount</label>
                <span class="helper-text" data-error="wrong" data-success="right" id="add-rate-amount-helper"></span>
            </div>

            <div class="input-field col s4 m3">
                <select id="add-rate-currency-select">  
                    <option value="" disabled selected>Choose</option>                  
                    <?php foreach ($currencies as $currency) {?>
                        <option value="<?= $currency['r030'] ?>"><?= $currency['cc'] ?></option>
                    <?php }?>
                </select>
                <label>currency</label>
                <span class="helper-text" id="add-rate-currency-helper" data-error="wrong" data-success="right">Please choose currency</span>
            </div>

            <div class="input-field col s12 m3">
                <i class="material-icons prefix cyan-text text-darken-1">update</i>
                <input type="text" class="datepicker" id="add-rate-validity-input">
                <label for="add-rate-validity-input">Validity</label>
                <span class="helper-text" id="add-rate-validity-helper" data-error="wrong" data-success="right">Please choose currency</span>
            </div>

        </div>

        <div id="add-rate-service-block" style="display: none"> <!-- inline -->
            
            <div class="input-field col s12 m6">
                <i class="material-icons prefix cyan-text text-darken-1">access_time</i>
                <input id="add-rate-transit-time-input" type="number">
                <label for="add-rate-transit-time-input">Transit time, days</label>
                <span class="helper-text" data-error="wrong" data-success="right" id="add-rate-transit-time-helper"></span>
            </div>

            <div class="input-field col s12 m6">
                <i class="material-icons prefix cyan-text text-darken-1">vertical_align_top</i>
                <input id="add-rate-payload-input" type="number">
                <label for="add-rate-payload-input">Unit payload, mt</label>
                <span class="helper-text" data-error="wrong" data-success="right" id="add-rate-unit-payload-helper"></span>
            </div>


        </div>

        <div class="row valign-wrapper">

            <div class="col s12 m6">
                <button type="button" class="btn right cyan darken-1" id="add-rate-button">Save</button>
            </div>
        </div>

    </form>
<div>

<div class="row">
    <div class="col s12 m12 l12">
        <div class="row flow-text" id="add-rate-result">

        </div>
    </div>
</div>
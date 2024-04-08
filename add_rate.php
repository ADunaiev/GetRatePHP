<?php 

        require_once("./model/DataWorker.php");

        $dataWorker = new DataWorker();

        $cities = $dataWorker->get_all_cities();
        $suppliers = $dataWorker->get_all_suppliers();
        $transport_types = $dataWorker->get_all_transport_types();
        $currencies = $dataWorker->get_all_currencies();
        $lines = $dataWorker->get_all_lines();
        $container_types = $dataWorker->get_all_container_types();

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

        if (isset($_SESSION['supplier'])) {
            $supplier_name = $_SESSION['supplier'];
        }
        else {
                $supplier_name = "";
        }

        if (isset($_SESSION['transport-type'])) {
            $transport_type_name = $_SESSION['transport-type'];
        }
        else {
                $transport_type_name = "";
        }

        if (isset($_SESSION['amount'])) {
            $amount = $_SESSION['amount'];
        }
        else {
                $amount = "";
        }

        if (isset($_SESSION['currency'])) {
            $currency_cc = $_SESSION['currency'];
        }
        else {
                $currency_cc = "";
        }

        if (isset($_SESSION['etd'])) {
            $etd = $_SESSION['etd'];
        }
        else {
                $etd = "";
        }

        if (isset($_SESSION['validity'])) {
            $validity = $_SESSION['validity'];
        }
        else {
                $validity = "";
        }

        if (isset($_SESSION['container-type'])) {
            $container_type_name = $_SESSION['container-type'];
        }
        else {
                $container_type_name = "";
        }

        if (isset($_SESSION['line'])) {
            $line_name = $_SESSION['line'];
        }
        else {
            $line_name = "";
        }

        if (isset($_SESSION['remark'])) {
            $remark = $_SESSION['remark'];
        }
        else {
            $remark = "";
        }

?>

<h1>Add rate</h1>

<p>
    <?= $line_name ?></br>
    <?= $remark ?>
</p>

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
                        <option value="<?= $supplier['trinity_code'] ?>" <?= $supplier_name == $supplier['name'] ? "selected" : "" ?>><?= $supplier['name'] ?></option>
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
                        <option value="<?= $type['trinity_code'] ?>" <?= $transport_type_name == $type['name'] ? "selected" : "" ?>><?= $type['name'] ?></option>
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
                <input id="add-rate-amount-input" type="number" value="<?= $amount != "" ? $amount : "" ?>">
                <label for="add-rate-amount-input">Amount</label>
                <span class="helper-text" data-error="wrong" data-success="right" id="add-rate-amount-helper"></span>
            </div>

            <div class="input-field col s4 m3">
                <select id="add-rate-currency-select">  
                    <option value="" disabled selected>Choose</option>                  
                    <?php foreach ($currencies as $currency) {?>
                        <option value="<?= $currency['r030'] ?>" <?= $currency_cc == $currency['cc'] ? "selected" : "" ?>><?= $currency['cc'] ?></option>
                    <?php }?>
                </select>
                <label>currency</label>
                <span class="helper-text" id="add-rate-currency-helper" data-error="wrong" data-success="right">Please choose currency</span>
            </div>

        </div>

        <!-- Необов'язкові параметри -->
        <ul class="collapsible">
            <li>
                <div class="collapsible-header"><i class="material-icons cyan-text text-darken-1">add</i>Additional data</div>

                <div class="row collapsible-body">

                    <div class="input-field col s12 m3">
                        <i class="material-icons prefix cyan-text text-darken-1">update</i>
                        <input type="text" class="datepicker" id="add-rate-etd-input" value="<?= $etd != "" ? $etd : "" ?>">
                        <label for="add-rate-etd-input">ETD</label>
                        <span class="helper-text" id="add-rate-etd-helper" data-error="wrong" data-success="right">Please choose ETD</span>
                    </div>

                    <div class="input-field col s12 m3">
                        <i class="material-icons prefix cyan-text text-darken-1">update</i>
                        <input type="text" class="datepicker" id="add-rate-validity-input" value="<?= $validity != "" ? $validity : "" ?>">
                        <label for="add-rate-validity-input">Validity</label>
                        <span class="helper-text" id="add-rate-validity-helper" data-error="wrong" data-success="right">Please choose validity</span>
                    </div>

                    <div class="input-field col s6 m3">
                        <select id="add-rate-container-type-select">  
                            <option value="" disabled selected>Choose</option>                  
                            <?php foreach ($container_types as $type) {?>
                                <option value="<?= $type['trinity_code'] ?>" <?= $container_type_name == $type['name'] ? "selected" : "" ?>><?= $type['name'] ?></option>
                            <?php }?>
                        </select>
                        <label>Container type</label>
                        <span class="helper-text" id="add-rate-container-type-helper" data-error="wrong" data-success="right">Please choose container type</span>
                    </div>

                    <div class="input-field col s6 m3">
                        <select id="add-rate-line-select">  
                            <option value="" disabled selected>Choose</option>                  
                            <?php foreach ($lines as $line) {?>
                                <option value="<?= $line['trinity_code'] ?>" <?= $line_name == $line['name'] ? "selected" : "" ?>><?= $line['name'] ?></option>
                            <?php }?>
                        </select>
                        <label>Line</label>
                        <span class="helper-text" id="add-rate-line-helper" data-error="wrong" data-success="right">Please choose line</span>
                    </div>

                    <div class="input-field col s12">
                        <textarea id="add-rate-remark-textarea" class="materialize-textarea"><?= $remark != "" ? $remark : "" ?></textarea>
                        <label for="add-rate-remark-textarea">Remark</label>
                    </div>

                </div>
            </li>
        </ul>

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
<?php 

        require_once("./model/DataWorker.php");

        $dataWorker = new DataWorker();

        $cities = $dataWorker->get_all_cities();
        $suppliers = $dataWorker->get_all_suppliers();
        $transport_types = $dataWorker->get_all_transport_types();
        $contrainer_types = $dataWorker->get_all_container_types();
        $lines = $dataWorker->get_all_lines();

        if (isset($_SESSION['start-point'])) {
            $start_point = $_SESSION['start-point'];
        }
        else {
            $start_point = null;
        }

        if (isset($_SESSION['end-point'])) {
            $end_point = $_SESSION['end-point'];
        }
        else {
            $end_point = null;
        }

        if (isset($_SESSION['all-rates'])) {
            $all_rates = $_SESSION['all-rates'];
        }
        else {
            $all_rates = null;
        }

        if (isset($_SESSION['supplier-trinity-code'])) {
            $supplier_trinity_code = $_SESSION['supplier-trinity-code'];
        }
        else {
            $supplier_trinity_code = null;
        }

        if (isset($_SESSION['supplier-trinity-code'])) {
            $supplier_trinity_code = $_SESSION['supplier-trinity-code'];
        }
        else {
            $supplier_trinity_code = null;
        }

        if (isset($_SESSION['line'])) {
            $line_id = $_SESSION['line'];
        }
        else {
                $line_id = null;
        }

        if (isset($_SESSION['transport-type-trinity-code'])) {
            $transport_type_code = $_SESSION['transport-type-trinity-code'];
        }
        else {
                $transport_type_code = null;
        }

        if (isset($_SESSION['container-type'])) {
            $contrainer_type_code = $_SESSION['container-type'];
        }
        else {
                $contrainer_type_code = null;
        }

?>

<h1>All rates</h1>

<div class="row">
    <form class="col s12">
        <div class="row">

            <div class="input-field col s12 m12" style="display: none">
                <input hidden value="<?= $user['id'] ?>" id="all-rates-user" type="text" class="validate">
                <label for="all-rates-user">User id</label>
            </div>

            <!-- supplier -->
            <div class="input-field col s12 m4">
                <i class="material-icons prefix cyan-text text-darken-1">rowing</i>
                <select id="all-rates-supplier-select">  
                    <option value="" selected>Choose your option</option>                  
                    <?php foreach ($suppliers as $supplier) {?>
                        <option value="<?= $supplier['trinity_code'] ?>" <?= $supplier['trinity_code'] == $supplier_trinity_code ? "selected" : "" ?>><?= $supplier['name'] ?></option>
                    <?php }?>
                </select>
                <label>Supplier</label>
            </div>

            <!-- transport type -->
            <div class="input-field col s12 m4">
                <i class="material-icons prefix cyan-text text-darken-1">directions_boat</i>
                <select id="all-rates-transport-type-select">  
                    <option value="" selected>Choose your option</option>                  
                    <?php foreach ($transport_types as $type) {?>
                        <option value="<?= $type['trinity_code'] ?>" <?= $type['trinity_code'] == $transport_type_code ? "selected" : "" ?>><?= $type['name'] ?></option>
                    <?php }?>ll
                </select>
                <label>Transport type</label>
            </div>

            <!-- container_type -->
            <div class="input-field col s6 m3">
                <select id="all-rates-container-type-select">  
                    <option value="" selected>Choose</option>                  
                    <?php foreach ($contrainer_types as $type) {?>
                        <option value="<?= $type['trinity_code'] ?>" <?= $type['trinity_code'] == $contrainer_type_code ? "selected" : "" ?>><?= $type['name'] ?></option>
                    <?php }?>
                </select>
                <label>container type</label>
            </div>
            
            <!-- start-point -->
            <div class="input-field col s12 m4">
                <i class="material-icons prefix cyan-text text-darken-1">location_on</i>
                <select id="all-rates-start-point-select">  
                    <option value="" selected>Choose your option</option>                  
                    <?php foreach ($cities as $city) {?>
                        <option value="<?= $city['name'] ?>" <?= $city['name'] == $start_point ? "selected" : "" ?>><?= $city['name'] ?></option>
                    <?php }?>
                </select>
                <label>From point</label>
            </div>

            <!-- end-point -->
            <div class="input-field col s12 m4">
                <i class="material-icons prefix cyan-text text-darken-1">location_on</i>
                <select id="all-rates-end-point-select">
                    <option value="" selected>Choose your option</option>
                    <?php foreach ($cities as $city) {?>
                        <option value="<?= $city['name'] ?>" <?= $city['name'] == $end_point ? "selected" : "" ?>><?= $city['name'] ?></option>
                    <?php }?>
                </select>
                <label>To point</label>
            </div>

            <!-- line -->
            <div class="input-field col s6 m3">
                <select id="all-rates-line-select">  
                    <option value="" selected>Choose</option>                  
                    <?php foreach ($lines as $line) {?>
                        <option value="<?= $line['trinity_code'] ?>" <?= $line['trinity_code'] == $line_id ? "selected" : "" ?>><?= $line['name'] ?></option>
                    <?php }?>
                </select>
                <label>shipping line</label>
            </div>

        </div>

        <div class="row valign-wrapper">

            <div class="col s12 m6">
                <button type="button" class="btn right cyan darken-1" id="get-rates-button">Get</button>
            </div>
        </div>

    </form>
<div>

<div id="all-rates-search-results">
    <table>
        <thead>
            <tr>
                <th>Line</th>
                <th>POL</th>
                <th>POD</th>
                <th>Amount</th>
                <th>Unit</th>
                <th>ETD</th>
                <th>Validity</th>
                <th>Remark</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach($all_rates as $rate) { ?>
                <tr>
                    <td><?= $dataWorker->get_line_name_by_id( $rate['line']) ?></td>
                    <td><?= $rate['pol'] ?></td>
                    <td><?= $rate['pod'] ?></td>
                    <td><?= $rate['amount'] . " ". $rate['cc'] ?></td>
                    <td><?= $dataWorker->get_container_type_name_by_id( $rate['cont_type']) ?></td>
                    <td><?= $rate['etd'] ?></td>
                    <td><?= $rate['validity'] ?></td>
                    <td><?= $rate['remark'] ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>




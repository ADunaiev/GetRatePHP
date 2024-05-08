<?php 

        require_once("./model/DataWorker.php");
        require 'vendor/autoload.php';

        use PhpOffice\PhpSpreadsheet\Spreadsheet;
        use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

        $dataWorker = new DataWorker();

        if (isset($_SESSION['imported_rates'])) {
            $imported_rates = $_SESSION['imported_rates'];
        }
        else {
            $imported_rates = null;
        }

        if (isset($_SESSION['filename'])) {
            $file_name = $_SESSION['filename'];
        }
        else {
            $file_name = null;
        }

        if (isset($_SESSION['validation'])) {
            $validation = $_SESSION['validation'];
        }
        else {
            $validation = null;
        }

?>

<h1>Import freight rates</h1>

<form method="post" id="import-rates-form">
    <div class="row valign-wrapper">

        <div class="input-field col s12 m12" style="display: none">
            <input hidden value="<?= $user['id'] ?>" id="import-rates-user" type="text" class="validate">
            <label for="import-rates-user">User id</label>
        </div>

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

<div id="import-rates-result">
    <table class="highlight">
        <thead>
            <tr>
                <th>No</th>
                <th>Line</th>
                <th>POL</th>
                <th>POD</th>
                <th>Amount</th>
                <th>Currency</th>
                <th>Container</th>
                <th>ETD</th>
                <th>Validity</th>
                <th>Remark</th>
                <th>Route message</th>
                <th>Service message</th>
                <th>db message</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($imported_rates != null) {
                $count = 1;
                foreach($imported_rates as $rate) { 
                    if ($rate['rate_item'][0] != "line") { ?>
                        <tr>
                            <td><?= $count++ ?></td>
                            <?php if (isset($rate['rate_item'][0])) {
                                if ($dataWorker->validate_line($rate['rate_item'][0])) { ?>
                                    <td class="cyan-text text-darken-1"><?= $rate['rate_item'][0] ?></td>
                                <?php }
                                else { ?>
                                    <td class="red-text" title="Wrong line name!"><b><?= $rate['rate_item'][0] ?></b></td>
                                <?php }
                            } 
                            else { ?>
                                <td class="red-text" title="Field is empty!"><b>empty</b></td>
                            <?php }?>

                            <?php if (isset($rate['rate_item'][1])) {
                                if ($dataWorker->validate_port($rate['rate_item'][1])) { ?>
                                    <td class="cyan-text text-darken-1"><?= $rate['rate_item'][1] ?></td>
                                <?php }
                                else { ?>
                                    <td class="red-text" title="Wrong port name!"><b><?= $rate['rate_item'][1] ?></b></td>
                                <?php }
                            } 
                            else { ?>
                                <td class="red-text" title="Field is empty!"><b>empty</b></td>
                            <?php }?>

                            <?php if (isset($rate['rate_item'][2])) {
                                if ($dataWorker->validate_port($rate['rate_item'][2])) { ?>
                                    <td class="cyan-text text-darken-1"><?= $rate['rate_item'][2] ?></td>
                                <?php }
                                else { ?>
                                    <td class="red-text" title="Wrong port name!"><b><?= $rate['rate_item'][2] ?></b></td>
                                <?php }
                            } 
                            else { ?>
                                <td class="red-text" title="Field is empty!"><b>empty</b></td>
                            <?php }?>

                            <?php if (isset($rate['rate_item'][3])) {
                                if ($rate['rate_item'][3] > 0 && is_numeric($rate['rate_item'][3])) { ?>
                                    <td class="cyan-text text-darken-1"><?= $rate['rate_item'][3] ?></td>
                                <?php }
                                else { ?>
                                    <td class="red-text" title="Wrong amount!"><b><?= $rate['rate_item'][3] ?></b></td>
                                <?php }
                            } 
                            else { ?>
                                <td class="red-text" title="Field is empty!"><b>empty</b></td>
                            <?php }?>

                            <?php if (isset($rate['rate_item'][4])) {
                                if ($dataWorker->validate_currency($rate['rate_item'][4])) { ?>
                                    <td class="cyan-text text-darken-1"><?= $rate['rate_item'][4] ?></td>
                                <?php }
                                else { ?>
                                    <td class="red-text" title="Wrong currency!"><b><?= $rate['rate_item'][4] ?></b></td>
                                <?php }
                            } 
                            else { ?>
                                <td class="red-text" title="Field is empty!"><b>empty</b></td>
                            <?php }?>

                            <?php if (isset($rate['rate_item'][5])) {
                                if ($dataWorker->validate_container_type($rate['rate_item'][5])) { ?>
                                    <td class="cyan-text text-darken-1"><?= $rate['rate_item'][5] ?></td>
                                <?php }
                                else { ?>
                                    <td class="red-text" title="Wrong container type!"><b><?= $rate['rate_item'][5] ?></b></td>
                                <?php }
                            } 
                            else { ?>
                                <td class="red-text" title="Field is empty!"><b>empty</b></td>
                            <?php }?>

                            <?php if (isset($rate['rate_item'][6])) {
                                if ($dataWorker->validateDate($rate['rate_item'][6])) { ?>
                                    <td class="cyan-text text-darken-1"><?= $rate['rate_item'][6] ?></td>
                                <?php }
                                else { ?>
                                    <td class="red-text" title="Wrong container type!"><b><?= $rate['rate_item'][6] ?></b></td>
                                <?php }
                            } 
                            else { ?>
                                <td class="red-text" title="Field is empty!"><b>empty</b></td>
                            <?php }?>

                            <?php if (isset($rate['rate_item'][7])) {
                                if ($dataWorker->validateDate($rate['rate_item'][7])) { ?>
                                    <td class="cyan-text text-darken-1"><?= $rate['rate_item'][7] ?></td>
                                <?php }
                                else { ?>
                                    <td class="red-text" title="Wrong container type!"><b><?= $rate['rate_item'][7] ?></b></td>
                                <?php }
                            } 
                            else { ?>
                                <td class="red-text" title="Field is empty!"><b>empty</b></td>
                            <?php }?>

                            <td><?= isset($rate['rate_item'][8]) ? $rate['rate_item'][8] : "" ?></td>

                            <?php if (isset($rate['is_route'])) {
                                if ($rate['is_route']) { ?>
                                    <td class="cyan-text text-darken-1"><?= $rate['route_message'] ?></td>
                                <?php }
                                else { ?>
                                    <td class="red-text"><b><?= $rate['route_message'] ?></b></td>
                                <?php }
                            } ?>

                            <?php if (isset($rate['is_service'])) {
                                if ($rate['is_service']) { ?>
                                    <td class="cyan-text text-darken-1"><?= $rate['service_message'] ?></td>
                                <?php }
                                else { ?>
                                    <td class="red-text"><b><?= $rate['service_message'] ?></b></td>
                                <?php }
                            } ?>

                            <?php if (isset($rate['is_saved'])) {
                                if ($rate['is_saved']) { ?>
                                    <td class="cyan-text text-darken-1"><?= $rate['db_message'] ?></td>
                                <?php }
                                else { ?>
                                    <td class="red-text"><b><?= $rate['db_message'] ?></b></td>
                                <?php }
                            } ?>
                            
                        </tr>
                    <?php } ?>
                <?php }
            } ?>
            
        </tbody>
    </table>
</div>

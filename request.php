<?php

    require_once("./model/DataWorker.php");

    $dataWorker = new DataWorker();
    $cities = $dataWorker->get_all_cities();
    $cargo = $dataWorker->get_all_cargo();
    $currencies = $dataWorker->get_all_currencies();

    if (isset($_SESSION['routes'])) {
        $routes = $_SESSION['routes'];
    }
    else {
        $routes = null;
    }

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

    if (isset($_SESSION['cargo-id'])) {
            $cargo_id = $_SESSION['cargo-id'];
            $cargo_name = $dataWorker->get_cargo_name_by_id($cargo_id);
    }
    else {
            $cargo_id = "";
            $cargo_name = "";
    }

    if (isset($_SESSION['cargo-gw'])) {
            $cargo_gw = $_SESSION['cargo-gw'];
    }
    else {
            $cargo_gw = "";
    }

    if (isset($_SESSION['with-rates'])) {
        $withRates = $_SESSION['with-rates'];
    }
    else {
            $withRates = "";
    }

    if (isset($_SESSION['valid-rates'])) {
        $validRates = $_SESSION['valid-rates'];
    }
    else {
            $validRates = "";
    }

    if (isset($_SESSION['sort-by'])) {
        $sortBy = $_SESSION['sort-by'];
    }
    else {
            $sortBy = "";
    }

    if (isset($_SESSION['all_routes_and_rates'])) {
            $all_routes_and_rates = $_SESSION['all_routes_and_rates'];
    }
    else {
            $all_routes_and_rates = null;
    }

    if (isset($_SESSION['currency'])) {
        $currency_selected = $_SESSION['currency'];
    }
    else {
            $currency_selected = "";
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
                <i class="material-icons prefix cyan-text text-darken-1">location_on</i>
                <select id="start-point-select">  
                    <option value="" disabled selected>Choose your option</option>                  
                    <?php foreach ($cities as $city) {?>
                        <option value="<?= $city['name'] ?>" <?= $start_point == $city['name'] ? "selected" : "" ?>><?= $city['name'] ?></option>
                    <?php }?>
                </select>
                <label>From point</label>
                <span class="helper-text" id="start-point-helper" data-error="wrong" data-success="right">Please choose start point</span>
            </div>
            <div class="input-field col s12 m6">
                <i class="material-icons prefix cyan-text text-darken-1">location_on</i>
                <select id="end-point-select">
                    <option value="" disabled selected>Choose your option</option>
                    <?php foreach ($cities as $city) {?>
                        <option value="<?= $city['name'] ?>" <?= $end_point == $city['name'] ? "selected" : "" ?>><?= $city['name'] ?></option>
                    <?php }?>
                </select>
                <label>To point</label>
                <span class="helper-text" id="end-point-helper" data-error="wrong" data-success="right">Please choose end point</span>
            </div>
            <div class="input-field col s12 m6">
                <i class="material-icons prefix cyan-text text-darken-1">center_focus_strong</i>
                <select id="cargo-select">
                    <option value="" disabled selected>Choose your option</option>
                    <?php foreach ($cargo as $cargo_item) {?>
                        <option value="<?= $cargo_item['id'] ?>" <?= $cargo_id == $cargo_item['id'] ? "selected" : "" ?>><?= $cargo_item['custom_code'] . " " . $cargo_item['name'] ?></option>
                    <?php }?>
                </select>
                <label>Cargo</label>
                <span class="helper-text" id="cargo-helper" data-error="wrong" data-success="right">Please choose cargo</span>
            </div>

            <div class="input-field col s12 m6">
                <i class="material-icons prefix cyan-text text-darken-1">file_upload</i>
                <input id="cargo-gw-input" type="text" class="validate" value="<?= $cargo_gw != 0 ? $cargo_gw : "" ?>">
                <label for="cargo-gw-input">Gross weight, mt</label>
                <span class="helper-text" data-error="wrong" data-success="right"></span>
            </div>

        </div>

        <div class="row">

            <div class="input-field col s6 m3">
                <i class="material-icons prefix cyan-text text-darken-1">attach_money</i>
                <select id="request-currency-select">  
                    <option value="" disabled selected>Choose</option>                  
                    <?php foreach ($currencies as $currency) {?>
                        <option value="<?= $currency['r030'] ?>" <?= $currency['r030'] == $currency_selected ? "selected" : "" ?>><?= $currency['cc'] ?></option>
                    <?php }?>
                </select>
                <label>currency</label>
                <span class="helper-text" id="request-currency-helper" data-error="wrong" data-success="right">Please choose currency</span>
            </div>

            <div class="input-field col s6 m3">
                <i class="material-icons prefix cyan-text text-darken-1">sort</i>
                <select id="sort-select">
                    <option value="price">Price</option>
                    <option value="time" <?= $sortBy == "time" ? "selected" : "" ?>>Time</option>
                </select>
                <label>Sort by</label>
            </div>

            <div class="col s12 m3 input-field">
                <label>
                    <input type="checkbox" id="with-rates-checkbox" <?= $withRates == "true"? "checked" : "" ?> />
                    <span>Show options with rates</span>
                </label>
            </div>

            <div class="col s12 m3 input-field">
                <label>
                    <input type="checkbox" id="valid-rates-checkbox" <?= $validRates == "true" ? "checked" : "" ?>/>
                    <span>Show valid rates</span>
                </label>
            </div>

        </div>

        <div class="row valign-wrapper">

            <div class="col s12 m6">
                <button type="button" class="btn right cyan darken-1" id="request-button">Send</button>
            </div>
        </div>

        <div class="row center">
            <div class="preloader-wrapper big active" id="request-preloader" style="display: none">

                <div class="spinner-layer spinner-blue">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div>
                    <div class="gap-patch">
                        <div class="circle"></div>
                    </div>
                    <div class="circle-clipper right">
                        <div class="circle"></div>
                    </div>
                </div>

                <div class="spinner-layer spinner-red">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div>
                    <div class="gap-patch">
                        <div class="circle"></div>
                    </div>
                    <div class="circle-clipper right">
                        <div class="circle"></div>
                    </div>
                </div>

                <div class="spinner-layer spinner-yellow">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div>
                    <div class="gap-patch">
                        <div class="circle"></div>
                    </div>
                    <div class="circle-clipper right">
                        <div class="circle"></div>
                    </div>
                </div>

                <div class="spinner-layer spinner-green">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div>
                    <div class="gap-patch">
                        <div class="circle"></div>
                    </div>
                    <div class="circle-clipper right">
                        <div class="circle"></div>
                    </div>
                </div>

            </div>
        </div>


        <div class="row">
            <div class="col s12 m12">
                <div id="request-result-message">             
                <?php  if ($all_routes_and_rates) { ?>
                        <h4 style="font: blue">
                                <?= count($all_routes_and_rates) ?> routes were found
                        </h4>
                        <?php $num = 1;
                          
                        // Універсальний вивід для всіх маршрутів
                        foreach($all_routes_and_rates as $route) { ?>
                                <h6><b>Option <?= $num++ ?> new </b></h6>
                                <ul class="collapsible">
                                        <?php foreach($route['routes'] as $route_item) { ?>
                                                
                                                <li>
                                                        <div class="collapsible-header valign-wrapper">

                                                                <div class="col s2 m3 l3 xl3"><img src="img/<?=$route_item['transport_type_img']?>" class="transport-type-img" alt="transport" style="max-height:50px;max-width:50px"/></div>
                                                                <div class="col s4 m3 l3 xl3"><?= $route_item['transport_type_name'] ?></div>
                                                                <div class="col s3 m3 l3 xl3"><?= $route_item['start_point_name'] ?></div>
                                                                <div class="col s3 m3 l3 xl3"><?= $route_item['end_point_name'] ?></div>
                                                                <div class="col s3 m2 l3 xl3"><b><?= $route_item['units_quantity'] . " units"  ?></b></div>
                                                                <div class="col s3 m2 l3 xl3"><b><?= $route_item['route_rate_amount'] . " " . $route_item['route_rate_currency'] . " / unit" ?></b></div>
                                                                <div class="col s3 m2 l3 xl3"><b><?= $route_item['route_transit_time'] . " days" ?></b></div>


                                                        </div>
                                                        <div class="collapsible-body">

                                                                <ul class="collection">
                                                                                <li class="collection-item valign-wrapper">
                                                                                        <div class="col s12 m6 l3"><b>Supplier</b></div>
                                                                                        <div class="col s12 m6 l3"><b>Date</b></div>
                                                                                        <div class="col s12 m6 l3"><b>Amount</b></div>
                                                                                        <div class="col s12 m6 l3"><b>Transit time</b></div>
                                                                                        <div class="col s12 m6 l3"><b>Validity</b></div>      
                                                                                </li>
                                                                        <?php foreach($route_item['rates'] as $rate) { 
                                                                                $rate_currency = $dataWorker->get_currency_cc_by_r030($rate['currency_r030']); ?>
                                                                                <li class="collection-item valign-wrapper">
                                                                                        <div class="col s12 m6 l3"><?= $rate['name'] ?></div>
                                                                                        <div class="col s12 m6 l3"><?= $rate['rate_day'] ?></div>
                                                                                        <div class="col s12 m6 l3"><?= $rate['amount'] . " " . $rate_currency . " / unit" ?></div>
                                                                                        <div class="col s12 m6 l3"><?= $rate['transit_time'] . " days" ?></div>
                                                                                        <div class="col s12 m6 l3"><?= $rate['validity'] != "" ? $rate['validity'] : "" ?></div>
                                                                                </li>
                                                                        <?php } ?>
                                                                </ul>
                                                        </div>
                                                </li>
                                        <?php } ?>
                                </ul>
                                <h6><b>Total: <?= $route['total_sum'] . " " . $route['currency'] . " per lot, transit time: " . $route['total_transit_time'] . " days" ?></b></h6></br>
                        <?php }


                } else { ?>
                        <p></p>
                <?php } ?>
                </div>
            </div>
        </div>
    </form>
<div>

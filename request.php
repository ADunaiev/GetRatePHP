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
                        <option value="<?= $city['name'] ?>" <?= $start_point == $city['name'] ? "selected" : "" ?>><?= $city['name'] ?></option>
                    <?php }?>
                </select>
                <label>From point</label>
                <span class="helper-text" id="start-point-helper" data-error="wrong" data-success="right">Please choose start point</span>
            </div>
            <div class="input-field col s12 m6">
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
                <input id="cargo-gw-input" type="text" class="validate" value="<?= $cargo_gw != 0 ? $cargo_gw : "" ?>">
                <label for="cargo-gw-input">Gross weight, mt</label>
                <span class="helper-text" data-error="wrong" data-success="right"></span>
            </div>



        </div>

        <div class="row valign-wrapper">
            <div class="col s9 m6">
                <label>
                    <input type="checkbox" id="with-rates-checkbox" <?= $withRates == "true"? "checked" : "" ?> />
                    <span>Show options with rates</span>
                </label>
            </div>

            <div class="input-field col s3 m6">
                <select id="sort-select">
                    <option value="price">Price</option>
                    <option value="time" <?= $sortBy == "time" ? "selected" : "" ?>>Time</option>
                </select>
                <label>Sort by</label>
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
                <duv id="request-result-message">             
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
                                                                <div class="col s12 m2 l1"><img src="img/<?=$route_item['transport_type_img']?>" class="transport-type-img" alt="transport" style="max-height:50px;max-width:50px"/></div>
                                                                <div class="col s12 m6 l3"><?= $route_item['transport_type_name'] ?></div>
                                                                <div class="col s12 m6 l3"><?= $route_item['start_point_name'] ?></div>
                                                                <div class="col s12 m6 l3"><?= $route_item['end_point_name'] ?></div>
                                                                <div class="col s12 m6 l3"><b><?= $route_item['units_quantity'] . " units"  ?></b></div>
                                                                <div class="col s12 m6 l3"><b><?= $route_item['route_rate_amount'] . " " . $route_item['route_rate_currency'] . " / unit" ?></b></div>
                                                                <div class="col s12 m6 l3"><b><?= $route_item['route_transit_time'] . " days" ?></b></div>
                                                        </div>
                                                        <div class="collapsible-body">

                                                                <ul class="collection">
                                                                        <?php foreach($route_item['rates'] as $rate) { ?>
                                                                                <li class="collection-item valign-wrapper">
                                                                                        <div class="col s12 m6 l3"><?= $rate['name'] ?></div>
                                                                                        <div class="col s12 m6 l3"><?= $rate['rate_day'] ?></div>
                                                                                        <div class="col s12 m6 l3"><?= $rate['amount'] . " " . $rate['currency'] . " / unit" ?></div>
                                                                                        <div class="col s12 m6 l3"><?= $rate['transit_time'] . " days" ?></div>
                                                                                </li>
                                                                        <?php } ?>
                                                                </ul>
                                                        </div>
                                                </li>
                                        <?php } ?>
                                </ul>
                                <h6><b>Total: <?= $route['total_sum'] . " per lot, transit time: " . $route['total_transit_time'] . " days" ?></b></h6></br>
                        <?php }


                } else { ?>
                        <p></p>
                <?php } ?>
                </div>
            </div>
        </div>
    </form>
<div>

<?php 

        require_once("./model/DataWorker.php");

        $dataWorker = new DataWorker();

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

        if (isset($_SESSION['found_1item_routes'])) {
                $found_1item_routes = $_SESSION['found_1item_routes'];
        }
        else {
                $found_1item_routes = null;
        }

        if (isset($_SESSION['found_2item_routes'])) {
                $found_2item_routes = $_SESSION['found_2item_routes'];
        }
        else {
                $found_2item_routes = null;
        }

        if (isset($_SESSION['found_3item_routes'])) {
                $found_3item_routes = $_SESSION['found_3item_routes'];
        }
        else {
                $found_3item_routes = null;
        }
?>

<div class="row">

                <?php if ($found_1item_routes || $found_2item_routes || $found_3item_routes) { ?>
                        <h1 style="font: blue">
                                Rates 
                        </h1>
                        <h5>
                                Route: <?= $start_point ?> - <?= $end_point ?></br>
                                Cargo: <?= $cargo_name ?> </br>
                                Gross Weight: <?= $cargo_gw . " mt" ?>
                        </h5></br></br>
                        <?php $num = 1;

                        foreach($found_1item_routes as $route) { ?>
                                <h6><b>Option <?= $num++ ?></b></h6>
                                <ul class="collapsible">
                                        <li>
                                                <?php $rates = $dataWorker->get_route_rates(
                                                                $start_point,
                                                                $end_point,
                                                                $route['transport_type_trinity_code']
                                                ); 
                                                $units_number = $rates != null ? ceil( $cargo_gw / $rates[0]['unit_payload']) : 0;
                                                $sum = $rates != null ? ($rates[0]['amount'] * $units_number) : "";
                                                ?>
                                                <div class="collapsible-header valign-wrapper">
                                                        <div class="col s12 m2 l1"><img src="img/<?=$route['image']?>" class="transport-type-img" alt="transport" style="max-height:50px;max-width:50px"/></div>
                                                        <div class="col s12 m6 l3"><?= $route['transport_type'] ?></div>
                                                        <div class="col s12 m6 l3"><?= $route['start_point_name'] ?></div>
                                                        <div class="col s12 m6 l3"><?= $route['end_point_name'] ?></div>
                                                        <div class="col s12 m6 l3"><b><?= $units_number . " units"  ?></b></div>
                                                        <div class="col s12 m6 l3"><b><?= $rates != null ? $rates[0]['amount'] . " " . $rates[0]['currency'] . " / unit" : "" ?></b></div>
                                                        <div class="col s12 m6 l3"><b><?= $rates != null ? $rates[0]['transit_time'] . " days" : "" ?></b></div>
                                                </div>
                                                <div class="collapsible-body">

                                                        <ul class="collection">
                                                                <?php foreach($rates as $rate) { ?>
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
                                </ul>
                                <h6><b>Total: <?= $sum . " " . $rates[0]['currency'] . " per lot, transit time: " . $rates[0]['transit_time'] . " days" ?></b></h6></br>
                        <?php }

                        foreach($found_2item_routes as $route) { ?>
                                <h6><b>Option <?= $num++ ?></b></h6>

                                <ul class="collapsible">
                                        <li>
                                                <?php $rates_1st = $dataWorker->get_route_rates(
                                                                $start_point,
                                                                $route['middle_point'],
                                                                $route['first_transport']
                                                ); 
                                                $units_number_1st = $rates_1st != null ? ceil( $cargo_gw / $rates_1st[0]['unit_payload']) : 0;
                                                $sum_1st = $rates_1st != null ? ($rates_1st[0]['amount'] * $units_number_1st) : "";

                                                $image_first = $dataWorker->get_transport_image_by_trinity_code($route['first_transport']);
                                                $image_second = $dataWorker->get_transport_image_by_trinity_code($route['second_transport']);
                                                ?>
                                                <div class="collapsible-header valign-wrapper">
                                                        <div class="col s12 m2 l1"><img src="img/<?=$image_first?>" class="transport-type-img" alt="transport" style="max-height:50px;max-width:50px"/></div>
                                                        <div class="col s12 m6 l3"><?= $dataWorker->get_transport_type_name_by_trinity_code($route['first_transport']) ?></div>
                                                        <div class="col s12 m6 l3"><?= $route['start_point_name'] ?></div>
                                                        <div class="col s12 m6 l3"><?= $route['middle_point'] ?></div>
                                                        <div class="col s12 m6 l3"><b><?= $units_number_1st . " units"  ?></b></div>
                                                        <div class="col s12 m6 l3"><b><?= $rates_1st != null ? $rates_1st[0]['amount'] . " " . $rates_1st[0]['currency'] . " / unit" : "" ?></b></div>
                                                        <div class="col s12 m6 l3"><b><?= $rates_1st != null ? $rates_1st[0]['transit_time'] . " days" : "" ?></b></div>
                                                </div>
                                                <div class="collapsible-body">

                                                        <ul class="collection">
                                                                <?php foreach($rates_1st as $rate) { ?>
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
                                </ul>

                                <ul class="collapsible">
                                        <li>
                                                <?php $rates_2nd = $dataWorker->get_route_rates(
                                                                $route['middle_point'],
                                                                $end_point,
                                                                $route['second_transport']
                                                ); 
                                                $units_number_2nd = $rates_2nd != null ? ceil( $cargo_gw / $rates_2nd[0]['unit_payload']) : 0;
                                                $sum_2nd = $rates_2nd != null ? ($rates_2nd[0]['amount'] * $units_number_2nd) : "";
                                                ?>
                                                <div class="collapsible-header valign-wrapper">
                                                        <div class="col s12 m2 l1"><img src="img/<?=$image_second?>" class="transport-type-img" alt="transport" style="max-height:50px;max-width:50px"/></div>
                                                        <div class="col s12 m6 l3"><?= $dataWorker->get_transport_type_name_by_trinity_code($route['second_transport']) ?></div>
                                                        <div class="col s12 m6 l3"><?= $route['middle_point'] ?></div>
                                                        <div class="col s12 m6 l3"><?= $route['end_point_name'] ?></div>
                                                        <div class="col s12 m6 l3"><b><?= $units_number_2nd . " units"  ?></b></div>
                                                        <div class="col s12 m6 l3"><b><?= $rates_2nd != null ? $rates_2nd[0]['amount'] . " " . $rates_2nd[0]['currency'] . " / unit" : "" ?></b></div>
                                                        <div class="col s12 m6 l3"><b><?= $rates_2nd != null ? $rates_2nd[0]['transit_time'] . " days" : "" ?></b></div>
                                                </div>
                                                <div class="collapsible-body">

                                                        <ul class="collection">
                                                                <?php foreach($rates_2nd as $rate) { ?>
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
                                </ul>

                                <?php
                                        if ($sum_1st != "" && $sum_2nd != "") {
                                                if ($rates_1st[0]['currency'] == $rates_2nd[0]['currency']) {
                                                        $sum_total = ($sum_1st + $sum_2nd) . " " . $rates_1st[0]['currency'];
                                                }
                                                else {
                                                        $sum_total = $sum_1st . " " . $rates_1st[0]['currency'] . ", " 
                                                                . $sum_2nd . " " . $rates_2nd[0]['currency'];
                                                }
                                                $total_transit_time = $rates_1st[0]['transit_time'] + $rates_2nd[0]['transit_time'];
                                        }
                                        else {
                                                $sum_total = "";
                                                $total_transit_time = "";
                                        }

                                 ?>

                                <h6><b>Total: <?= $sum_total . " per lot, transit time: " . $total_transit_time . " days" ?></b></h6></br>
                        <?php }

                        foreach($found_3item_routes as $route) { ?>
                                <div class="col s12 m12">
                                        <ul class="collection with-header">
                                                <li class="collection-header"><h6>Option <?= $num++ ?></h6></li> 

                                                <li class="collection-item valign-wrapper">
                                                        <div class="col s12 m6 l3"><img src="img/<?=$route['image_first']?>" class="transport-type-img" alt="transport" style="max-height:50px;max-width:50px"/></div>
                                                        <div class="col s12 m6 l3"><?= $route['1st_transport'] ?></div>
                                                        <div class="col s12 m6 l3"><?= $route['start_point_name'] ?></div>
                                                        <div class="col s12 m6 l3"><?= $route['middle_point1'] ?></div>
                                                </li> 

                                                <li class="collection-item valign-wrapper">
                                                        <div class="col s12 m6 l3"><img src="img/<?=$route['image_second']?>" class="transport-type-img" alt="transport" style="max-height:50px;max-width:50px"/></div>
                                                        <div class="col s12 m6 l3"><?= $route['2nd_transport'] ?></div>
                                                        <div class="col s12 m6 l3"><?= $route['middle_point1'] ?></div>
                                                        <div class="col s12 m6 l3"><?= $route['middle_point2'] ?></div>
                                                </li> 

                                                <li class="collection-item valign-wrapper">
                                                        <div class="col s12 m6 l3"><img src="img/<?=$route['image_third']?>" class="transport-type-img" alt="transport" style="max-height:50px;max-width:50px"/></div>
                                                        <div class="col s12 m6 l3"><?= $route['3rd_transport'] ?></div>
                                                        <div class="col s12 m6 l3"><?= $route['middle_point2'] ?></div>
                                                        <div class="col s12 m6 l3"><?= $route['end_point_name'] ?></div>
                                                </li> 

                                        </ul>
                                </div>
                        <?php }


                } else { ?>
                        <p>routes are empty</p>
                <?php } ?>
</div>


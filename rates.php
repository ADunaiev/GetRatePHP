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

        if (isset($_SESSION['all_routes_and_rates'])) {
                $all_routes_and_rates = $_SESSION['all_routes_and_rates'];
        }
        else {
                $all_routes_and_rates = null;
        }
?>

<div class="row">

                <?php if ($all_routes_and_rates) { ?>
                        <h1 style="font: blue">
                                Rates 
                        </h1>
                        <h5>
                                Route: <?= $start_point ?> - <?= $end_point ?></br>
                                Cargo: <?= $cargo_name ?> </br>
                                Gross Weight: <?= $cargo_gw . " mt" ?>
                        </h5></br></br>
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
                        <p>routes are empty</p>
                <?php } ?>
</div>


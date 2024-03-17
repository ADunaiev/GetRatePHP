<div class="col s12 m12">
                                        <ul class="collection with-header">
                                                <li class="collection-header"><h6>Option <?= $num++ ?></h6></li>                                          
                                                <li class="collection-item">
                                                        <div class="col s12 m12 valign-wrapper">
                                                                <div class="col s12 m6 l3"><img src="img/<?=$route['image']?>" class="transport-type-img" alt="transport" style="max-height:50px;max-width:50px"/></div>
                                                                <div class="col s12 m6 l3"><?= $route['transport_type'] ?></div>
                                                                <div class="col s12 m6 l3"><?= $route['start_point_name'] ?></div>
                                                                <div class="col s12 m6 l3"><?= $route['end_point_name'] ?></div>
                                                        </div>
                                                        <?php $rates = $dataWorker->get_route_rates(
                                                                $start_point,
                                                                $end_point,
                                                                $route['transport_type_trinity_code']
                                                        ); ?>
                                                        <div class="">
                                                                <?php foreach($rates as $rate) { ?>
                                                                        <li class="collection-item valign-wrapper">
                                                                                <div class="col s12 m6 l3"><?= $rate['name'] ?></div>
                                                                                <div class="col s12 m6 l3"><?= $rate['rate_day'] ?></div>
                                                                                <div class="col s12 m6 l3"><?= $rate['amount'] . " " . $rate['currency'] ?></div>
                                                                        </li>
                                                                <?php } ?>
                                                        </div>
                                                </li>

                                        </ul>
                                </div>




                                //2 items routes
                                <div class="col s12 m12">
                                        <ul class="collection with-header">
                                                <li class="collection-header"><h6>Option <?= $num++ ?></h6></li>                                          
                                                <li class="collection-item valign-wrapper">
                                                        <div class="col s12 m6 l3"><img src="img/<?=$route['image_first']?>" class="transport-type-img" alt="transport" style="max-height:50px;max-width:50px"/></div>
                                                        <div class="col s12 m6 l3"><?= $route['first_transport'] ?></div>
                                                        <div class="col s12 m6 l3"><?= $route['start_point_name'] ?></div>
                                                        <div class="col s12 m6 l3"><?= $route['middle_point'] ?></div>
                                                </li> 
                                                <li class="collection-item valign-wrapper">
                                                        <div class="col s12 m6 l3"><img src="img/<?=$route['image_second']?>" class="transport-type-img" alt="transport" style="max-height:50px;max-width:50px"/></div>
                                                        <div class="col s12 m6 l3"><?= $route['second_transport'] ?></div>
                                                        <div class="col s12 m6 l3"><?= $route['middle_point'] ?></div>
                                                        <div class="col s12 m6 l3"><?= $route['end_point_name'] ?></div>
                                                </li>        
                                        </ul>
                                </div>
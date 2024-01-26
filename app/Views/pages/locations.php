<div class="row">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-body">
                <div class="float-right">
                </div>
                <h4 class="card-title mb-4"><?=lang('locs_pending_lang_en.main_panel')?></h4>
                <div class="row">
                    <div class="col-xl-12">
                        <div id="faqs-accordion" class="custom-accordion mt-5 mt-xl-0">
                            <?php //var_dump(gettype($posts));?>
                            <?php if (isset($locs) && gettype($locs) === 'array') { ?>
                                <div class="table-responsive mb-4">
                                    <table class="table table-top table-nowrap mb-0 table-borderless">
                                        <tbody>
                                        <?php foreach ($locs as $key => $loc_p) { ?>
                                        <?php $expanded = $key === 0 ? true : false; ?>
                                        <?php $collapsed = $key !== 0 ? 'collapsed' : ''; ?>
                                        <?php $show = $key === 0 ? 'show' : ''; ?>
                                            <tr>
                                                <td class="p-0">
                                                    <a href="#loc_p-<?=$loc_p['loc_id']?>" class="text-dark <?=$collapsed?>" data-toggle="collapse" aria-expanded="<?=$expanded?>" aria-controls="loc_p-<?=$loc_p['loc_id']?>">
                                                        <div class="media align-items-center bg-light mb-1">
                                                            <div class="ml-3">
                                                                <div class="avatar-xs">
                                                                    <div class="avatar-title rounded-circle font-size-22">
                                                                        <i class="mdi mdi-chevron-up accor-down-icon font-size-16"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="media-body overflow-hidden">
                                                                <h5 class="font-size-16 p-xl-3 mb-0" style="line-height: 20px;"><?=$loc_p['loc_title']?></h5>
                                                            </div>
                                                        </div>
                                                    </a>
                                                    <div id="loc_p-<?=$loc_p['loc_id']?>" class="collapse <?=$show?>" data-parent="#faqs-accordion" style="min-height:700px">
                                                        <div id="map-<?=$loc_p['loc_id']?>" class="mt-1 map-container" data-loc_id="<?=$loc_p['loc_id']?>"></div>
                                                        <div class="row no-gutters">
                                                            <div class="col-6 text-center">
                                                                <div class="py-4">
                                                                    <?php if ($loc_p['loc_rating']) : ?>
                                                                        <h5 class="font-size-15 mb-3">Current rating: <span class="value"></span></h5>
                                                                        <div class="stars-example-fontawesome-o">
                                                                            <select id="rating-current-fontawesome-o" name="rating" data-current-rating="5.6" autocomplete="off">
                                                                                <option value="1">1</option>
                                                                                <option value="2">2</option>
                                                                                <option value="3">3</option>
                                                                                <option value="4">4</option>
                                                                                <option value="5">5</option>
                                                                                <option value="6">6</option>
                                                                                <option value="7">7</option>
                                                                                <option value="8">8</option>
                                                                                <option value="9">9</option>
                                                                                <option value="10">10</option>
                                                                            </select>
                                                                            <span class="title your-rating hidden">
                                                                                Your rating: <span class="value"></span>&nbsp;
                                                                                <a href="#" class="clear-rating"><i class="fa fa-times-circle"></i></a>
                                                                            </span>
                                                                        </div>
                                                                    <?php else : ?>
                                                                        <h5 class="font-size-15 mb-3">This location hasn't been rated yet!</h5>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                            <div class="col-6 text-center">
                                                                <div class="py-4">
                                                                    <?php if ($loc_p['loc_records']) : ?>
                                                                        <?php $records = json_decode($loc_p['loc_records']); ?>
                                                                        <h5 class="font-size-15 mb-3">Records:</h5>
                                                                        <?php foreach ($records as $key => $record) : ?>
                                                                            <h6><?=($key+1)?>st place: <?=$record->time?></h6>
                                                                            <div class="progress progress-xl animated-progess mb-4 p-1">
                                                                                <div class="progress-bar bg-success" role="progressbar" style="width: 100%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                                                            </div>
                                                                        <?php endforeach; ?>
                                                                    <?php else : ?>
                                                                        <h5 class="font-size-15 mb-3">No records on this location has been set yet!</h5>
                                                                    <?php endif; ?>                                            
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php } else { ?>
                                <div class="card border shadow-none"><?=isset($locs) ? $locs :'No locs table found.'?></div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
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
                                        <?php foreach ($locs as $key => $loc) { ?>
                                        <?php $expanded = $key === 0 ? true : false; ?>
                                        <?php $collapsed = $key !== 0 ? 'collapsed' : ''; ?>
                                        <?php $show = $key === 0 ? 'show' : ''; ?>
                                            <tr>
                                                <td class="p-0">
                                                    <a href="#loc-<?=$loc['loc_id']?>" class="text-dark <?=$collapsed?>" data-toggle="collapse" aria-expanded="<?=$expanded?>" aria-controls="loc-<?=$loc['loc_id']?>">
                                                        <div class="media align-items-center bg-light mb-1">
                                                            <div class="ml-3">
                                                                <div class="avatar-xs">
                                                                    <div class="avatar-title rounded-circle font-size-22">
                                                                        <i class="mdi mdi-chevron-up accor-down-icon font-size-16"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="media-body overflow-hidden">
                                                                <h5 class="font-size-16 p-xl-3 mb-0" style="line-height: 20px;"><?=$loc['loc_title']?></h5>
                                                            </div>
                                                        </div>
                                                    </a>
                                                    <div id="loc-<?=$loc['loc_id']?>" class="collapse <?=$show?>" data-parent="#faqs-accordion">
                                                        <div id="map-<?=$loc['loc_id']?>" class="mt-1 map-container" data-loc_id="<?=$loc['loc_id']?>"></div>
                                                        <div class="row no-gutters">
                                                            <div class="col-6 text-center">
                                                                <form action="locs_pending/approve" method="post" enctype="multipart/form-data" name="delete_loc_<?=$loc['loc_id']?>" class="p-xl-3 m-0 text-danger">
                                                                    <input type="hidden" value="<?=$loc['loc_city']?>" name="loc_city">
                                                                    <input type="hidden" value="<?=$loc['loc_id']?>" name="loc_id">
                                                                    <button type="submit" class="btn btn-success waves-effect waves-light">Approve</button>
                                                                </form>
                                                            </div>
                                                            <div class="col-6 text-center">
                                                                <form action="locs_pending/delete" method="post" enctype="multipart/form-data" name="delete_loc_<?=$loc['loc_id']?>" class="p-xl-3 m-0 text-danger">
                                                                    <input type="hidden" value="<?=$loc['loc_city']?>" name="loc_city">
                                                                    <input type="hidden" value="<?=$loc['loc_id']?>" name="loc_id">
                                                                    <button type="submit" class="btn btn-danger waves-effect waves-light">Delete</button>
                                                                </form>
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
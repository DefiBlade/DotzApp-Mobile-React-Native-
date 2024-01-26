<div class="row">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-body">
                <div class="float-right">
                </div>
                <h4 class="card-title mb-4"><?=lang('Activities_lang_en.main_panel')?></h4>
                <div class="row">
                    <div class="col-xl-12">
                        <div id="faqs-accordion" class="custom-accordion mt-5 mt-xl-0">
                            <?php //var_dump(gettype($posts));?>
                            <?php if (isset($activities) && gettype($activities) === 'array') { ?>
                                <div class="table-responsive mb-4">
                                    <table class="table table-top table-nowrap mb-0 table-borderless">
                                        <tbody>
                                        <?php foreach ($activities as $key => $activity) { ?>
                                        <?php $expanded = $key === 0 ? true : false; ?>
                                        <?php $collapsed = $key !== 0 ? 'collapsed' : ''; ?>
                                        <?php $show = $key === 0 ? 'show' : ''; ?>
                                        <?php $activity_info = json_encode( $activities[$key] );?>
                                            <tr>
                                                <td class="p-0">
                                                    <a href="#activity-<?=$activity['activity_id']?>" class="text-dark <?=$collapsed?>" data-toggle="collapse" aria-expanded="<?=$expanded?>" aria-controls="activity-<?=$activity['activity_id']?>">
                                                        <div class="media align-items-center bg-light mb-1">
                                                            <div class="ml-3">
                                                                <div class="avatar-xs">
                                                                    <div class="avatar-title rounded-circle font-size-22">
                                                                        <i class="mdi mdi-chevron-up accor-down-icon font-size-16"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="media-body overflow-hidden">
                                                                <h5 class="font-size-16 p-xl-3 mb-0" style="line-height: 20px;"><?=$activity['activity_name']?></h5>
                                                            </div>
                                                        </div>
                                                    </a>
                                                    <div id="activity-<?=$activity['activity_id']?>" class="collapse <?=$show?>" data-parent="#faqs-accordion">
                                                        <div class="p-1 p-xl-4">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <img src="/uploads/activities/<?=$activity['activity_img']?>" alt="" style="max-width:100%;height:auto;">
                                                                </div>
                                                                <div class="col-md-6">
                                                                <p class="text-muted"><?=$activity['activity_description']?></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="p-0" style="width:50px">
                                                    <ul class="list-inline mb-0 bg-light">
                                                        <li class="list-inline-item">
                                                            <form action="/activities/delete_activity" method="post" enctype="multipart/form-data" name="delete_activity_<?=$activity['activity_id']?>" class="p-xl-3 m-0 text-primary" data-toggle="tooltip" data-placement="top" title="Edit" style="padding: 0 10px;">
                                                                <input type="hidden" value="<?=$activity['activity_id']?>" name="activity_id">
                                                                    <button type="button" data-toggle="modal" data-target="#editActivity" data-activity_info="<?=htmlspecialchars($activity_info, ENT_QUOTES, 'UTF-8')?>" class="text-primary p-0" style="background:none;border: none;height: 20px;"><i class="uil uil-pen font-size-18"></i></button>
                                                            </form>
                                                        </li>
                                                        <li class="list-inline-item">
                                                            <form action="/activities/delete_activity" method="post" enctype="multipart/form-data" name="delete_activity_<?=$activity['activity_id']?>" class="p-xl-3 m-0 text-danger" data-toggle="tooltip" data-placement="top" title="Delete" style="padding: 0 10px;">
                                                                <input type="hidden" value="<?=$activity['activity_id']?>" name="activity_id">
                                                                <input type="hidden" value="<?=$activity['activity_name']?>" name="activity_name">
                                                                    <button type="submit" class="text-danger p-0" style="background:none;border: none;height: 20px;"><i class="uil uil-trash-alt font-size-18"></i></button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php } else { ?>
                                <div class="card border shadow-none"><?=isset($activities) ? $activities :'No activities table found.'?></div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card">
            <div class="card-body">

                <h4 class="card-title"><?=lang('Activities_lang_en.form_panel')?></h4>

                <form action="/activities/addActivity" method="post" enctype="multipart/form-data" name="addActivity" id="addActivity">
                    <div class="form-group row">
                        <label for="example-text-input" class="col-xl-12 col-md-2 col-form-label"><?=lang('Activities_lang_en.form_label_title')?></label>
                        <div class="col-xl-12 col-md-10">
                            <input type="text" class="form-control" name="activity_name" id="activity_name" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="example-text-input" class="col-xl-12 col-md-2 col-form-label"><?=lang('Activities_lang_en.form_label_description')?></label>
                        <div class="col-xl-12 col-md-10">
                            <textarea type="text" class="form-control" name="activity_description" id="activity_description" rows="6"></textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="example-text-input" class="col-xl-12 col-md-2 col-form-label"><?=lang('Activities_lang_en.form_label_image')?></label>
                        <div class="col-xl-12 col-md-10">
                            <input type="file" class="form-control-file" name="activity_img" id="activity_img" required>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary waves-effect waves-light"><?=lang('Activities_lang_en.form_button_submit')?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
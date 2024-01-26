<?php
    if (isset($Translations)) {
        # code...
    }
?>

<div class="row">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-body">
                <div class="float-right"></div>
                <h4 class="card-title mb-4">Update</h4>
                <div class="row">
                    <div class="col-xl-12">
                        <form action="<?=base_url('update_translations')?>" method="post" enctype="multipart/form-data" name="update_translations" id="update_translations">
                            <?php if (isset($Translations)) : ?>
                                <?php foreach ($Translations as $name => $trans) : ?>
                                    <div class="row">
                                        <div class="col-xl-12">
                                            <h5 class="font-size-14 mb-4"><i class="mdi mdi-arrow-right text-primary mr-1"></i><?=$name?></h5>
                                            <div class="form-group">
                                                <?php foreach ($trans as $key => $value) : ?>
                                                    <div class="row">
                                                        <div class="col-xl-3">
                                                            <label for="translations[<?=$name?>][<?=$key?>]"><?=$key?></label>
                                                        </div>
                                                        <div class="col-xl-8">
                                                            <input class="form-control" type="text" value="<?=$value?>" name="translations[<?=$name?>][<?=$key?>]">
                                                        </div>
                                                        <div class="col-xl-1">
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">×</span>
                                                            </button>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-primary waves-effect waves-light">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card">
            <div class="card-body">

                <h4 class="card-title">Create</h4>

                <form action="create_translations" method="post" enctype="multipart/form-data" name="create_post" id="create_post">
                    <div class="form-group row">
                        <label for="example-text-input" class="col-xl-12 col-md-2 col-form-label">name</label>
                        <div class="col-xl-12 col-md-10">
                            <select class="form-control" name="tr_name" id="tr_name" required>
                                <?php if (isset($Translations)) : ?>
                                <?php foreach ($Translations as $key => $value) : ?>
                                    <option value="<?=$key?>"><?=$key?></option>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="example-text-input" class="col-xl-12 col-md-2 col-form-label">new_key</label>
                        <div class="col-xl-12 col-md-10">
                            <input class="form-control" type="text" value="" name="new_key" id="example-text-input" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="example-text-input" class="col-xl-12 col-md-2 col-form-label">new_value</label>
                        <div class="col-xl-12 col-md-10">
                        <input class="form-control" type="text" value="" name="new_value" id="example-text-input" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="example-text-input" class="col-xl-12 col-md-2 col-form-label">language</label>
                        <div class="col-xl-12 col-md-10">
                        <input class="form-control" type="text" value="english" name="lang" id="example-text-input" disabled>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary waves-effect waves-light">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
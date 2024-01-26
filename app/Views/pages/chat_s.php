<div class="row">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-body">
                <div class="float-right"></div>
                <h4 class="card-title mb-4">Chat settings</h4>
                <div class="row">
                    <div class="col-xl-12">
                        <form action="<?=base_url('chat-sets/update_chat_sets')?>" method="post" enctype="multipart/form-data" name="update_chat_sets" id="update_chat_sets">
                            <?php if ( isset($chat_sets) && !empty($chat_sets) ) : ?>
                                <div class="row">
                                    <div class="col-xl-12">
                                        <h5 class="font-size-14 mb-4"><i class="mdi mdi-arrow-right text-primary mr-1"></i>General chat settings</h5>
                                        <?php foreach ($chat_sets as $key => $setting) : ?>
                                            <div class="form-group">
                                                <div class="row mb-2">
                                                    <input type="hidden" value="<?=$setting['cs_id']?>" name="chat_sets[<?=$key?>][cs_id]">
                                                    <div class="col-xl-3">
                                                        <label for="chat_sets[<?=$key?>][<?=$setting['cs_set_name']?>]"><?=lang('Settings_Chat.'.$setting['cs_set_name'])?></label>
                                                    </div>
                                                    <div class="col-xl-9">
                                                        <input class="form-control" type="text" value="<?=$setting['cs_set_val']?>" name="chat_sets[<?=$key?>][<?=$setting['cs_set_name']?>]">
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php elseif ( isset($chat_sets) && empty($chat_sets) ) : ?>
                                <div class="row">
                                    <div class="col-xl-12">
                                        <h5 class="font-size-14 mb-4"><i class="mdi mdi-arrow-right text-primary mr-1"></i>General chat settings</h5>
                                        <div class="form-group">
                                            <div class="row mb-2">
                                                <div class="col-xl-3">
                                                    <label for="chat_sets[cs_server_link]">Chat Server Link</label>
                                                </div>
                                                <div class="col-xl-9">
                                                    <input class="form-control" type="text" value="" name="chat_sets[cs_server_link]">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-xl-3">
                                                    <label for="chat_sets[cs_server_port]">Chat Server Port</label>
                                                </div>
                                                <div class="col-xl-9">
                                                    <input class="form-control" type="text" value="" name="chat_sets[cs_server_port]">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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

                <h4 class="card-title"></h4>
            </div>
        </div>
    </div>
</div>
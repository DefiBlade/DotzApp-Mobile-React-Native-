<?php
/**
 * Register view
 */
$pagerDetails = $pager->getDetails('group1');
// var_dump($pagerDetails);
// die();
$pagerCurrentItemsMin = ($pagerDetails['currentPage'] - 1) * $pager->getPerPage('group1') + 1;
$pagerCurrentItemsMax = $pagerDetails['currentPage'] !== $pagerDetails['pageCount'] ? $pager->getPerPage('group1') : $pagerDetails['total'];
?>

<div class="row">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-body">
                <div class="float-right"></div>
                <h4 class="card-title mb-4"><?=lang('Register.main_panel')?></h4>
                <?php if (isset($users) && gettype($users) === 'array') { ?>
                    <div class="table-responsive mb-4">
                        <table class="table table-top table-nowrap mb-0 table-borderless">
                            <thead>
                                <tr>
                                    <th scope="col"><?=lang('Register.user_name')?></th>
                                    <th scope="col"><?=lang('Register.role')?></th>
                                    <th scope="col"><?=lang('Register.email')?></th>
                                    <th scope="col" style="width: 50px;"><?=lang('Register.actions')?></th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($users as $key => $user) { ?>
                                <?php $user_info = json_encode( $users[$key] );?>
                                <tr id="user-<?=$user['user_id']?>">
                                    <td>
                                        <a href="#" class="text-body"><?=$user['nice_name']?></a>
                                    </td>
                                    <td><?=$user['role']?></td>
                                    <td><?=$user['email']?></td>
                                    <td style="width:50px">
                                        <ul class="list-inline mb-0">
                                            <li class="list-inline-item">
                                                <button type="button" class="px-2 text-primary" data-toggle="modal" data-target="#editUser" data-user_info="<?=htmlspecialchars($user_info, ENT_QUOTES, 'UTF-8')?>" style="background:none;border: none;height: 24px;" title="Edit">Edit<i class="uil uil-pen font-size-18"></i></button>
                                            </li>
                                            <li class="list-inline-item">
                                                <form action="delete_user" method="post" enctype="multipart/form-data" name="delete_user_<?=$user['user_id']?>" class="px-2 mb-0 text-danger" data-toggle="tooltip" data-placement="top" title="Delete">
                                                    <input type="hidden" value="<?=$user['user_id']?>" name="user_id">
                                                    <button type="submit" class="text-danger p-0" style="background:none;border: none;height: 24px;"><i class="uil uil-trash-alt font-size-18"></i>Delete</button>
                                                </form>
                                            </li>
                                            <li class="list-inline-item">
                                                <button type="button" class="px-2 text-primary" data-toggle="modal" data-target="#resetPass" data-user_id="<?=$user['user_id']?>" style="background:none;border: none;height: 24px;" title="Edit"><i class="uil uil-ellipsis-v font-size-18"></i>Reset</button>
                                            </li>
                                        </ul>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="row mt-4">
                        <div class="col-sm-6">
                            <div>
                                <p class="mb-sm-0">Showing <?=$pagerCurrentItemsMin?> to <?=$pagerCurrentItemsMax?> of <?=$pagerDetails['total']?> entries</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="float-sm-right">
                                <?= $pager->links('group1', 'table_users') ?>
                            </div>
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="card border shadow-none"><?=isset($users) ? $users :'No users table found.'?></div>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card">
            <div class="card-body">

                <h4 class="card-title"><?=lang('Register.form_panel')?></h4>
            
                <?php if (isset($validation)): ?>
                    <div class="col-12">
                    <div class="alert alert-danger" role="alert">
                        <?= $validation->listErrors() ?>
                    </div>
                    </div>
                <?php endif; ?>

                <div class="p-2 mt-2">
                    <form action="<?php echo base_url('/register') ?>" method="post">

                        <div class="form-group">
                            <label for="username"><?=lang('Register.form_reg_email')?></label>
                            <input type="text" class="form-control" id="username" name="email" placeholder="<?=lang('Register.form_reg_ph_email')?>" required>
                        </div>

                        <div class="form-group">
                            <label for="userpassword"><?=lang('Register.form_reg_pass')?></label>
                            <input type="password" class="form-control" id="userpassword" name="password" placeholder="<?=lang('Register.form_reg_ph_pass')?>" required>
                        </div>

                        <div class="form-group">
                            <label for="userpassword"><?=lang('Register.form_reg_nice_name')?></label>
                            <input type="text" class="form-control" id="nice_name" name="nice_name" placeholder="<?=lang('Register.form_reg_ph_nice_name')?>">
                        </div>

                        <div class="form-group">
                            <label for="role" class="col-form-label"><?=lang('Register.form_reg_role')?></label>
                            <select class="form-control" name="role" id="role">
                                <?php foreach ($roles as $role) : ?>
                                    <option value="<?=$role?>"><?=$role?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mt-3 text-right">
                            <button class="btn btn-primary w-sm waves-effect waves-light" type="submit"><?=lang('Register.form_reg_submit')?></button>
                        </div>
                    </form>
                </div>

            </div>
        </div>

    </div>
</div>
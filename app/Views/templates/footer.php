<?php
    $page = isset($page) ? $page : 'not-set';
    $page_class = 'page-' . $page;
    switch ($page) {
        case 'login':
            $page_class .= ' authentication-bg';
            break;
        
        default:
        $page_class .= '';
            break;
    }
?>

<?php if ($page !== 'login') : ?>
            </div> <!-- container-fluid -->
        </div>
        <!-- End Page-content -->


        <?php // $this->include('partials/footer') ?>
    </div>
    <!-- end main content-->

</div>
<!-- END layout-wrapper -->
<?php endif; ?>

<?php if ( $page === 'activities' ) : ?>
<!-- editUser modal content -->
<div id="editActivity" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="editActivityLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title mt-0" id="editActivityLabel"><?=lang('Activities_lang_en.modal_editActivity_title')?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="/activities/updateActivity" method="post" enctype="multipart/form-data" name="updateActivity" id="updateActivity">
                    <input type="hidden" class="hidden" id="activity_id" name="activity_id">
                    <div class="form-group">
                        <label for="activity_name"><?=lang('Activities_lang_en.form_activity_name')?></label>
                        <input type="text" class="form-control" id="activity_name" name="activity_name" placeholder="Enter activity name">
                    </div>

                    <div class="form-group">
                        <label for="activity_description"><?=lang('Activities_lang_en.form_activity_description')?></label>
                        <input type="text" class="form-control" id="activity_description" name="activity_description" placeholder="Enter activity description">
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-3">
                                <img src="" id="current_activity_img" alt="" style="max-width:100%;height:auto;">
                            </div>
                            <div class="col-md-9">
                                <label for="activity_description"><?=lang('Activities_lang_en.form_activity_description')?></label>
                                <input type="file" class="form-control-file" name="activity_img" id="activity_img">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3 text-right">
                        <button type="submit" class="btn btn-primary w-sm waves-effect waves-light"><?=lang('Activities_lang_en.modal_editActivity_submit')?></button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php endif; ?>

<?php if ( $page === 'clubs' ) : ?>
<!-- editUser modal content -->
<div id="editClub" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="editClubLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title mt-0" id="editClubLabel"><?=lang('Clubs_lang_en.modal_editClub_title')?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="/clubs/updateClub" method="post" enctype="multipart/form-data" name="updateClub" id="updateClub">
                    <input type="hidden" class="hidden" id="club_id" name="club_id">
                    <div class="form-group">
                        <label for="club_name"><?=lang('Clubs_lang_en.form_club_name')?></label>
                        <input type="text" class="form-control" id="club_name" name="club_name" placeholder="Enter club name">
                    </div>

                    <div class="form-group">
                        <label for="club_description"><?=lang('Clubs_lang_en.form_club_description')?></label>
                        <input type="text" class="form-control" id="club_description" name="club_description" placeholder="Enter club description">
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-3">
                                <img src="" id="current_club_img" alt="" style="max-width:100%;height:auto;">
                            </div>
                            <div class="col-md-9">
                                <label for="club_description"><?=lang('Clubs_lang_en.form_club_description')?></label>
                                <input type="file" class="form-control-file" name="club_img" id="club_img">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3 text-right">
                        <button type="submit" class="btn btn-primary w-sm waves-effect waves-light"><?=lang('Clubs_lang_en.modal_editClub_submit')?></button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php endif; ?>

<?php if ($page === 'register') : ?>
<!-- editUser modal content -->
<div id="editUser" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="editUserLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title mt-0" id="editUserLabel"><?=lang('Register.modal_editUser_title')?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?php echo base_url('/update_user') ?>" method="post">
                    <input type="hidden" class="hidden" id="user_id" name="user_id">
                    <div class="form-group">
                        <label for="username"><?=lang('Register.form_reg_email')?></label>
                        <input type="text" class="form-control" id="username" name="email" placeholder="Enter username">
                    </div>

                    <div class="form-group">
                        <label for="userpassword"><?=lang('Register.form_reg_nice_name')?></label>
                        <input type="text" class="form-control" id="nice_name" name="nice_name" placeholder="Enter nice_name">
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
                        <button type="submit" class="btn btn-primary w-sm waves-effect waves-light"><?=lang('Register.modal_editUser_submit')?></button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- editUser modal content -->
<div id="resetPass" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="editUserLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title mt-0" id="editUserLabel"><?=lang('Register.modal_resetPass_title')?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?php echo base_url('/reset_user_pass') ?>" method="post">
                    <input type="hidden" class="hidden" id="user_id" name="user_id">

                    <div class="form-group">
                        <label for="userpassword"><?=lang('Login.pass')?></label>
                        <input type="password" class="form-control" id="userpassword" name="password" placeholder="Enter password" required>
                    </div>
                    
                    <div class="mt-3 text-right">
                        <button type="submit" class="btn btn-primary w-sm waves-effect waves-light"><?=lang('Register.modal_resetPass_submit')?></button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php endif; ?>

<?php if( $page === 'profiles' ) : ?>
<div id="editProfile" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="editProfileLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title mt-0" id="editProfileLabel">Edit profile data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?php echo base_url('/update_profile') ?>" method="post">
                    <input type="hidden" class="hidden" id="profile_id" name="profile_id">

                    <div class="mt-3 text-right">
                        <button type="submit" class="btn btn-primary w-sm waves-effect waves-light">Update</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php endif; ?>

<!-- JAVASCRIPT -->
<script src="/assets/libs/jquery/jquery.min.js"></script>
<script src="/assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="/assets/libs/metismenu/metisMenu.min.js"></script>
<script src="/assets/libs/simplebar/simplebar.min.js"></script>
<script src="/assets/libs/node-waves/waves.min.js"></script>
<script src="/assets/libs/waypoints/lib/jquery.waypoints.min.js"></script>
<script src="/assets/libs/jquery.counterup/jquery.counterup.min.js"></script>

<!-- Sweet Alerts js -->
<script src="assets/libs/sweetalert2/sweetalert2.min.js"></script>

<?php if ($page === 'profiles') : ?>
<!-- Required datatable js -->
<script src="assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
        
<!-- Responsive examples -->
<script src="assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js"></script>

<!-- init js -->
<script src="assets/js/pages/ecommerce-datatables.init.js"></script>
<?php endif; ?>

<script src="assets/js/app.js"></script>
<script type="text/javascript">
    let pageData = {}
    <?php if (isset($msg) && !empty($msg)) : ?>
    pageData.msg = "<?=htmlspecialchars($msg, ENT_QUOTES, 'UTF-8')?>"
    <?php endif; ?>
</script>
<script src="assets/js/common.js"></script>
<?php switch ($page) {
        case 'register':
            echo '<script src="assets/js/pages/register.js"></script>';
            break;
        case 'activities':
            echo '<script src="assets/js/pages/activities.js"></script>';
            break;
        case 'clubs':
            echo '<script src="assets/js/pages/clubs.js"></script>';
            break;
        case 'locs_pending':
            ?>
            <script src='https://api.mapbox.com/mapbox-gl-js/v1.12.0/mapbox-gl.js'></script>
            <script type="text/javascript">
                pageData.locs = <?=json_encode($locs)?>
            </script>
            <script src="assets/js/pages/locations-pending.js"></script>
            
            <?php break;
        case 'locations':
            ?>
            <script src='https://api.mapbox.com/mapbox-gl-js/v1.12.0/mapbox-gl.js'></script>
            <script type="text/javascript">
                pageData.locs = <?=json_encode($locs)?>
            </script>
            <script src="assets/js/pages/locations.js"></script>
            <script src="assets/libs/jquery-bar-rating/jquery.barrating.min.js"></script>
            <script src="assets/js/pages/rating-init.js"></script>
            
            <?php break;
        case 'location_create':
            ?>
            <script src='https://api.mapbox.com/mapbox-gl-js/v1.12.0/mapbox-gl.js'></script>
            <script src="https://d3js.org/d3.v3.min.js" charset="utf-8"></script>
            <script src="assets/js/pages/location_create.js"></script>
            
            <?php break;
        case 'profiles':
            ?>
            <script src="assets/js/pages/profiles.js"></script>
            <?php break;
        case 'chat':
            ?>
            <div id="pageData" data-uid="<?= session()->get('user_id') ?>" data-un="<?= session()->get('nice_name') ?>"></div>
            <script type="text/javascript">
                pageData.user_id            = <?= session()->get('user_id') ?>;
                pageData.user_name          = "<?= session()->get('nice_name') ?>";
                pageData.chat_server_domain = "<?=$chat_settings['chat_server_domain']?>";
                <?= $chat_settings['chat_server_port'] ? 'pageData.chat_server_port   =' . $chat_settings['chat_server_port'] . ";" : '' ?>
                <?= $chat_settings['chat_server_con_prot'] ? 'pageData.chat_server_con_prot = "' . $chat_settings['chat_server_con_prot'] . '";' : '' ?>
            </script>
            <script src="assets/js/pages/chat.js"></script>
            <?php break;
        case 'schat': ?>
            <script src="assets/js/pages/schat.js"></script>
            <script type="text/javascript">
                pageData.user_id            = 31;
                pageData.user_name          = "Helen";
                pageData.chat_server_domain = "<?=$chat_settings['chat_server_domain']?>";
                 <?= $chat_settings['chat_server_port'] ? 'pageData.chat_server_port   =' . $chat_settings['chat_server_port'] . ";" : '' ?>
            </script>
            <?php break;
        case 'tchat': ?>
            <div id="pageData" data-uid="<?= session()->get('user_id') ?>" data-un="<?= session()->get('nice_name') ?>"></div>
            <script src="assets/js/pages/tchat.js"></script>
            <?php break;
        
        default:
        $page_class .= '';
            break;
    }
?>

</body>
</html>
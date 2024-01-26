<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive mb-4">
                    <table class="table datatable table-centered table-nowrap mb-0">
                        <thead>
                            <tr>
                                <th scope="col">Name</th>
                                <th scope="col">Email</th>
                                <th scope="col">City</th>
                                <th scope="col">Friends</th>
                                <th scope="col">Activities</th>
                                <th scope="col">Verified</th>
                                <th scope="col" style="width: 200px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if( isset($profiles) ) : ?>
                            <?php foreach ($profiles as $key => $profile) : ?>
                                <?php if ( isset($profile['friends']) ) $friends = $profile['friends']; ?>
                                <?php if ( isset($profile['activities']) ) $activities = $profile['activities']; ?>
                                <?php $text_color = 'body' === 'alert' ? 'danger' : 'body' ?>
                                <tr>
                                    <td>
                                        <?php if ( isset($profile['profile_img_ava']) ) : ?>
                                            <img src="uploads/profiles/<?=$profile['profile_img_ava']?>" alt="" class="avatar-xs rounded-circle mr-2">
                                            <a href="#" class="text-<?=$text_color?>"><?=$profile['app_user_name']?></a>
                                        <?php else : ?>
                                            <div class="avatar-xs d-inline-block mr-2">
                                                <div class="avatar-title bg-soft-primary rounded-circle text-primary">
                                                    <i class="mdi mdi-account-circle m-0"></i>
                                                </div>
                                            </div>
                                            <a href="#" class="text-<?=$text_color?>"><?=$profile['app_user_name']?></a>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-<?=$text_color?>"><?=$profile['email']?></td>
                                    <td class="text-<?=$text_color?>">
                                        <?php if ( isset($profile['profile_city']) ) : ?>
                                            <?=$profile['profile_city']?>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-<?=$text_color?>">
                                        <?php if ( isset($friends) && !empty($friends) ) : ?>
                                            <div class="dropdown">
                                                <button class="btn btn-light dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    Friends List <i class="mdi mdi-chevron-down"></i>
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    <?php foreach ($friends as $friend) : ?>
                                                        <?php if( isset($friend['app_user_name']) ) : ?>
                                                            <a class="dropdown-item" href="#"><?=$friend['app_user_name']?></a>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-<?=$text_color?>">
                                        <?php if ( isset($activities) && !empty($activities) ) : ?>
                                            <div class="dropdown">
                                                <button class="btn btn-light dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    Activities List <i class="mdi mdi-chevron-down"></i>
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    <?php foreach ($activities as $activity) : ?>
                                                        <a class="dropdown-item" href="#">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <?=$activity['activity_name']?>
                                                                <img src="uploads/activities/<?=$activity['activity_img']?>">
                                                            </div>
                                                        </a>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        <?php else : ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (isset($profile['profile_id'])) : ?>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input profile-verified-status" name="verified_<?=$profile['profile_id']?>" id="verified_<?=$profile['profile_id']?>" <?=$profile['profile_verified'] == 1 ? 'checked' : ''?>>
                                                <label class="custom-control-label" for="verified_<?=$profile['profile_id']?>"></label>
                                            </div>
                                        <?php else : ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <ul class="list-inline mb-0">
                                            <!-- <li class="list-inline-item">
                                                <a href="javascript:void(0);" class="px-2 text-primary" data-toggle="tooltip" data-placement="top" title="Edit">Edit<i class="uil uil-pen font-size-18"></i></a>
                                            </li> -->

                                            <li class="list-inline-item">
                                                <form action="delete_profile" method="post" enctype="multipart/form-data" name="delete_profile_<?=$profile['app_user_id']?>" class="px-2 mb-0 text-danger" data-toggle="tooltip" data-placement="top" title="Delete">
                                                    <input type="hidden" value="<?=$profile['app_user_id']?>" name="app_user_id">
                                                    <button type="submit" class="text-danger p-0" style="background:none;border: none;height: 24px;"><i class="uil uil-trash-alt font-size-18"></i>Delete</button>
                                                </form>
                                            </li>
                                            <!-- <li class="list-inline-item dropdown">
                                                <a class="text-muted dropdown-toggle font-size-18 px-2" href="javascript:void(0);" role="button" data-toggle="dropdown" aria-haspopup="true">
                                                    :<i class="uil uil-ellipsis-v"></i>
                                                </a>
                                            
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a class="dropdown-item" href="#">Action</a>
                                                    <a class="dropdown-item" href="#">Another action</a>
                                                    <a class="dropdown-item" href="#">Something else here</a>
                                                </div>
                                            </li> -->
                                        </ul>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end row -->
<div class="d-lg-flex mb-4">
    <div class="chat-leftsidebar card">
        <div class="p-3 px-4">
            <div class="media">
                <div class="align-self-center mr-3">
                    <img src="assets/images/users/avatar-4.jpg" class="avatar-xs rounded-circle" alt="">
                </div>
                <div class="media-body">
                    <h5 class="font-size-16 mt-0 mb-1">
                        <a href="#" class="text-dark"><?=session()->get('nice_name')?>
                            <i id="connStatusColor" class="mdi mdi-circle text-warning align-middle font-size-10 ml-1"></i>
                        </a>
                    </h5>
                    <p class="text-muted mb-0" id="connStatusText">Connecting</p>
                </div>

                <div>
                    <div class="dropdown chat-noti-dropdown">
                        <button class="btn dropdown-toggle py-0" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="uil uil-ellipsis-h"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="#">Profile</a>
                            <a class="dropdown-item" href="#">Edit</a>
                            <a class="dropdown-item" href="#">Add Contact</a>
                            <a class="dropdown-item" href="#">Setting</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="pb-3">
            <div data-simplebar style="max-height: 470px;">

                <div class="p-4 border-top">
                    <div>
                        <h5 class="font-size-16 mb-3"><i class="uil uil-user mr-1"></i>Chat Users</h5>

                        <ul class="list-unstyled chat-list">
                            <li>
                                <a href="#">
                                    <div class="media">
                                        
                                        <div class="user-img online align-self-center mr-3">
                                            <img src="assets/images/users/avatar-2.jpg" class="rounded-circle avatar-xs" alt="">
                                            <span class="user-status"></span>
                                        </div>
                                        
                                        <div class="media-body overflow-hidden">
                                            <h5 class="text-truncate font-size-14 mb-1">John Howard</h5>
                                            <p class="text-truncate mb-0">Hey! there I'm available</p>
                                        </div>
                                        <div class="font-size-11">02 min</div>
                                    </div>
                                </a>
                            </li>
                            <li class="unread">
                                <a href="#">
                                    <div class="media">
                                        <div class="user-img online align-self-center mr-3">
                                            <div class="avatar-xs align-self-center">
                                                <span class="avatar-title rounded-circle bg-soft-primary text-primary">
                                                    G
                                                </span>
                                            </div>
                                            <span class="user-status"></span>
                                        </div>
                                        
                                        <div class="media-body overflow-hidden">
                                            <h5 class="text-truncate font-size-14 mb-1">Galen Rizo</h5>
                                            <p class="text-truncate mb-0">I've finished it! See you so</p>
                                        </div>
                                        <div class="font-size-11">10 min</div>
                                        <div class="unread-message">
                                            <span class="badge badge-danger badge-pill">01</span>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <div class="media">
                                        <div class="user-img away align-self-center mr-3">
                                            <img src="assets/images/users/avatar-3.jpg" class="rounded-circle avatar-xs" alt="">
                                            <span class="user-status"></span>
                                        </div>
                                        
                                        <div class="media-body overflow-hidden">
                                            <h5 class="text-truncate font-size-14 mb-1">Bernard Spencer</h5>
                                            <p class="text-truncate mb-0">This theme is awesome!</p>
                                        </div>
                                        <div class="font-size-11">22 min</div>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <div class="media">
                                        
                                        <div class="user-img align-self-center mr-3">
                                            <img src="assets/images/users/avatar-4.jpg" class="rounded-circle avatar-xs" alt="">
                                            <span class="user-status"></span>
                                        </div>
                                        
                                        <div class="media-body overflow-hidden">
                                            <h5 class="text-truncate font-size-14 mb-1">Annie Holder</h5>
                                            <p class="text-truncate mb-0">Nice to meet you</p>
                                        </div>
                                        <div class="font-size-11">01 Hr</div>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <div class="media">
                                        
                                        <div class="user-img online align-self-center mr-3">
                                            <div class="avatar-xs align-self-center">
                                                <span class="avatar-title rounded-circle bg-soft-primary text-primary">
                                                    V
                                                </span>
                                            </div>
                                            <span class="user-status"></span>
                                        </div>
                                        
                                        <div class="media-body overflow-hidden">
                                            <h5 class="text-truncate font-size-14 mb-1">Vernon Smith</h5>
                                            <p class="text-truncate mb-0">Wow that's great</p>
                                        </div>
                                        <div class="font-size-11">04 Hrs</div>
                                    </div>
                                </a>
                            </li>

                            
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- end chat-leftsidebar -->

    <div class="w-100 user-chat mt-4 mt-sm-0 ml-lg-1">
        <div class="card">
            <div class="p-3 px-lg-4 border-bottom">
                <div class="row">
                    <div class="col-md-4 col-6">
                        <h5 class="font-size-16 mb-1 text-truncate"><a href="#" class="text-dark">Designers</a></h5>
                        <p class="text-muted text-truncate mb-0"><i class="uil uil-users-alt mr-1"></i> 12 Members</p>
                    </div>
                    <div class="col-md-8 col-6">
                        <ul class="list-inline user-chat-nav text-right mb-0">
                            <li class="list-inline-item">
                                <div class="dropdown">
                                    <button class="btn nav-btn dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="uil uil-search"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-md">
                                        <form class="p-2">
                                            <div>
                                                <input type="text" class="form-control rounded" placeholder="Search...">
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </li>
                            

                            <li class="list-inline-item">
                                <div class="dropdown">
                                    <button class="btn nav-btn dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="uil uil-ellipsis-h"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="#">Profile</a>
                                        <a class="dropdown-item" href="#">Archive</a>
                                        <a class="dropdown-item" href="#">Muted</a>
                                        <a class="dropdown-item" href="#">Delete</a>
                                    </div>
                                </div>
                            </li>

                        </ul>
                    </div>
                </div>
            </div>

            <div class="px-lg-2">
                <div class="chat-conversation p-3">
                    <ul class="list-unstyled mb-0" id="messages" data-simplebar style="max-height: 455px;min-height: 455px;">
                        
                    </ul>
                </div>
            </div>

            <div class="p-3 chat-input-section">
                <div class="row">
                    <div class="col">
                        <div class="position-relative">
                            <input type="text" class="form-control chat-input rounded" id="message-input" placeholder="Enter Message...">
                            
                        </div>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary chat-send w-md waves-effect waves-light" id="send"><span class="d-none d-sm-inline-block mr-2">Send</span> <i class="mdi mdi-send float-right"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End d-lg-flex  -->
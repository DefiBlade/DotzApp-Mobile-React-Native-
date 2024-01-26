<!-- ========== Left Sidebar Start ========== -->
<div class="vertical-menu">

    <!-- LOGO -->
    <div class="navbar-brand-box">
        <a href="/" class="logo logo-dark">
            <span class="logo-sm">
                <img src="assets/images/dotz_logo.png" alt="" height="30">
            </span>
            <span class="logo-lg">
                <img src="assets/images/dotz_logo_no_padding.png" alt="" height="70">
            </span>
        </a>

        <a href="/" class="logo logo-light">
            <span class="logo-sm">
                <img src="assets/images/logo-sm.png" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="assets/images/logo-light.png" alt="" height="20">
            </span>
        </a>
    </div>

    <button type="button" class="btn btn-sm px-3 font-size-16 header-item waves-effect vertical-menu-btn">
        <i class="fa fa-fw fa-bars"></i>
    </button>

    <div data-simplebar class="sidebar-menu-scroll">

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                <li class="menu-title">Menu</li>

                <li>
                    <a href="/locs_pending">
                        <i class="uil-map-marker-plus"></i>
                        <?= isset($sidebar_data['posts']) ? "<span class=\"badge badge-pill badge-primary float-right\">{$sidebar_data['posts']}</span>" : '' ?>
                        <span><?=lang('Sidebar.locs_pending')?></span>
                    </a>
                </li>
                <li>
                    <a href="/locations">
                        <i class="uil-location-point"></i>
                        <?= isset($sidebar_data['posts']) ? "<span class=\"badge badge-pill badge-primary float-right\">{$sidebar_data['posts']}</span>" : '' ?>
                        <span><?=lang('Sidebar.locations')?></span>
                    </a>
                </li>
                <li>
                    <a href="/activities">
                        <i class="uil-location-point"></i>
                        <?= isset($sidebar_data['posts']) ? "<span class=\"badge badge-pill badge-primary float-right\">{$sidebar_data['posts']}</span>" : '' ?>
                        <span><?=lang('Sidebar.activities')?></span>
                    </a>
                </li>
                <li>
                    <a href="/clubs">
                        <i class="uil-location-point"></i>
                        <?= isset($sidebar_data['posts']) ? "<span class=\"badge badge-pill badge-primary float-right\">{$sidebar_data['posts']}</span>" : '' ?>
                        <span><?=lang('Sidebar.clubs')?></span>
                    </a>
                </li>
                <li>
                    <a href="/profiles">
                        <i class="uil-users-alt"></i>
                        <?= isset($sidebar_data['posts']) ? "<span class=\"badge badge-pill badge-primary float-right\">{$sidebar_data['posts']}</span>" : '' ?>
                        <span><?=lang('Sidebar.profiles')?></span>
                    </a>
                </li>
                <li>
                    <a href="/chat">
                        <i class="uil-envelope-exclamation"></i>
                        <?= isset($sidebar_data['posts']) ? "<span class=\"badge badge-pill badge-primary float-right\">{$sidebar_data['posts']}</span>" : '' ?>
                        <span><?=lang('Sidebar.Chat')?></span>
                    </a>
                </li>
                <?php if (isset($isAdmin) && $isAdmin === true) : ?>
                    <li>
                        <a href="javascript: void(0);" class="has-arrow waves-effect">
                            <i class="uil-cog"></i>
                            <span><?=lang('Sidebar.dotz_settings')?></span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li><a href="register"><?=lang('Sidebar.User_reg')?></a></li>
                            <li><a href="location_create"><?=lang('Sidebar.location_create')?></a></li>
                        </ul>
                    </li>
                <?php endif; ?>
                <?php if (isset($isSAdmin) && $isSAdmin === true) : ?>
                    <li>
                        <a href="javascript: void(0);" class="has-arrow waves-effect">
                            <i class="uil-cog"></i>
                            <span><?=lang('Sidebar.Platform_Settings')?></span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li><a href="settings"><i class="uil-globe"></i><?=lang('Sidebar.Translations')?></a></li>
                            <li>
                                <a href="javascript: void(0);" class="has-arrow waves-effect">
                                    <i class="uil-comments"></i>
                                    <span><?=lang('Sidebar.Chat_Settings')?></span>
                                </a>
                                <ul class="sub-menu" aria-expanded="true">
                                    <li><a href="chat-sets"><i class="uil-cog"></i><?=lang('Sidebar.Chat_sets')?></a></li>
                                    <li><a href="schat"><i class="uil-comments-alt"></i><?=lang('Sidebar.Schat')?></a></li>
                                    <li><a href="tchat"><i class="uil-comments-alt"></i><?=lang('Sidebar.Tchat')?></a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
<!-- Left Sidebar End -->
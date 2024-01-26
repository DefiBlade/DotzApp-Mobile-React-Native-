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
<html>
<head>
    <meta charset="utf-8" />
    <title><?= isset($title) ? lang('Files.'.$title.'') : 'Platform' ?> | DOTZ</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php /* ?>
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    <?php */ ?>

    <!-- App favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="<?= base_url('assets/favicon');?>/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('assets/favicon');?>/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url('assets/favicon');?>/favicon-16x16.png">
    <link rel="manifest" href="<?= base_url('assets/favicon');?>/site.webmanifest">
    <link rel="mask-icon" href="<?= base_url('assets/favicon');?>/safari-pinned-tab.svg" color="#5953a1">
    <link rel="shortcut icon" href="<?= base_url('assets/favicon');?>/favicon.ico">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-config" content="<?= base_url('assets/favicon');?>/browserconfig.xml">
    <meta name="theme-color" content="#ffffff">
    <!-- Light layout Bootstrap Css -->
    <link href="/assets/css/bootstrap-dark.min.css" id="bootstrap-dark-style" disabled="disabled" rel="stylesheet" type="text/css" />
    <link href="/assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Plugins css -->
    <link href="assets/libs/dropzone/min/dropzone.min.css" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="/assets/css/app-dark.min.css" id="app-dark-style" disabled="disabled" rel="stylesheet" type="text/css" />
    <link href="/assets/css/app-rtl.min.css" id="app-rtl-style" disabled="disabled" rel="stylesheet" type="text/css" />
    <link href="/assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />
    <link href="/assets/css/custom.css" id="custom-style" rel="stylesheet" type="text/css" />
    <!-- Sweet Alert-->
    <link href="assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />
    <?php if ($page === 'profiles') : ?>
        <!-- DataTables -->
        <link href="assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />

        <!-- Responsive datatable examples -->
        <link href="assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <?php elseif ($page === 'locs_pending' || $page === 'locations' || $page === 'location_create') : ?>
        <link href='https://api.mapbox.com/mapbox-gl-js/v1.12.0/mapbox-gl.css' rel='stylesheet' />
    <?php elseif ($page === 'locations') : ?>
        <!-- jquery-bar-rating css -->
        <link href="assets/libs/jquery-bar-rating/themes/bars-1to10.css" rel="stylesheet" type="text/css" />
        <link href="assets/libs/jquery-bar-rating/themes/bars-horizontal.css" rel="stylesheet" type="text/css" />
        <link href="assets/libs/jquery-bar-rating/themes/bars-movie.css" rel="stylesheet" type="text/css" />
        <link href="assets/libs/jquery-bar-rating/themes/bars-pill.css" rel="stylesheet" type="text/css" />
        <link href="assets/libs/jquery-bar-rating/themes/bars-reversed.css" rel="stylesheet" type="text/css" />
        <link href="assets/libs/jquery-bar-rating/themes/bars-square.css" rel="stylesheet" type="text/css" />
        <link href="assets/libs/jquery-bar-rating/themes/css-stars.css" rel="stylesheet" type="text/css" />
        <link href="assets/libs/jquery-bar-rating/themes/fontawesome-stars-o.css" rel="stylesheet" type="text/css" />
        <link href="assets/libs/jquery-bar-rating/themes/fontawesome-stars.css" rel="stylesheet" type="text/css" />
    <?php endif; ?>
</head>
<body class="<?=$page_class?>">
<?php if ($page !== 'login') : ?>
    <!-- Begin page -->
    <div id="layout-wrapper">

    <?= $this->include('partials/menu') ?>

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">

            <div class="page-content">
                <div class="container-fluid">
<?php endif; ?>
<?php
/**
 * Location create
 */
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title"><?=lang('location_create.main_panel')?></h4>
                <div id="map" class="map-container" style="height: 600px"></div>

                <div class="row mt-4">
                    <div class="col-6 text-center">
                        <button type="button" id="saveRoute" class="btn btn-success waves-effect waves-light">Send route to pending</button>
                    </div>
                    <div class="col-6 text-center">
                        <button type="button" id="clearRoute" class="btn btn-danger waves-effect waves-light">Clear Route</button>
                    </div>
                </div>
            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div> <!-- end col-->
</div>
<!-- end row-->

<div class="row">
</div>
<!-- end row -->
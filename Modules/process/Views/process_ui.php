<?php
    defined('EMONCMS_EXEC') or die('Restricted access');
    global $path, $feed_settings, $redis_enabled;
    $domain2 = "process_messages";
    bindtextdomain($domain2, "Modules/process/locale");
    bind_textdomain_codeset($domain2, 'UTF-8');
?>
<style>
  .modal-processlist {
    width: 94%; left: 3%; /* (100%-width)/2 */
    margin-left:auto; margin-right:auto; 
    overflow-y: hidden;
  }
  .modal-processlist .modal-body {
     max-height: none; 
     overflow-y: auto;
   }

   #process-table th:nth-of-type(6), td:nth-of-type(6) {
    text-align: right;
   }
</style>
<script type="text/javascript" src="<?php echo $path; ?>Modules/process/Views/process_ui.js"></script>

<script>
  processlist_ui.engines_hidden = <?php echo json_encode($feed_settings['engines_hidden']); ?>;
  <?php if ($redis_enabled) echo "processlist_ui.has_redis = 1;"; ?>

  $(window).resize(function(){
    processlist_ui.adjustmodal() 
  });
</script>

<div id="processlistModal" class="modal hide keyboard modal-processlist" tabindex="-1" role="dialog" aria-labelledby="processlistModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-header">
        <button type="button" class="close" id="close">×</button>
        <h3><b><span id="contextname"></span></b> <?php echo dgettext('process_messages','process list setup'); ?></h3>
    </div>
    <div class="modal-body" id="processlist-ui">
        <p><?php echo dgettext('process_messages','Processes are executed sequentially with the result value being passed down for further processing to the next processor on this processing list.'); ?></p>
        
            <div id="noprocess" class="alert"><?php echo dgettext('process_messages','You have no processes defined'); ?></div>
            
            <table id="process-table" class="table table-hover">
                <tr>
                    <th style='width:5%;'></th>
                    <th style='width:5%;'><?php echo dgettext('process_messages','Order'); ?></th>
                    <th><?php echo dgettext('process_messages','Process'); ?></th>
                    <th><?php echo dgettext('process_messages','Arg'); ?></th>
                    <th></th>
                    <th colspan='2'><?php echo dgettext('process_messages','Actions'); ?></th>
                </tr>
                <tbody id="process-table-elements"></tbody>
            </table>

            <table class="table">
            <tr><th>
                <span id="process-header-add"><?php echo dgettext('process_messages','Add process'); ?>:</span>
                <span id="process-header-edit"><?php echo dgettext('process_messages','Edit process'); ?>:</span>
            </th></tr>
            <tr>
                <td>
                        <select id="process-select" class="input-large"></select>

                        <span id="type-value" style="display:none">
                            <div class="input-prepend">
                                <span class="add-on value-select-label"><?php echo dgettext('process_messages','Value'); ?></span>
                                <input type="text" id="value-input" class="input-medium" placeholder="<?php echo dgettext('process_messages','Type value...'); ?>" />
                            </div>
                        </span>
                        
                        <span id="type-text" style="display:none">
                            <div class="input-prepend">
                                <span class="add-on text-select-label"><?php echo dgettext('process_messages','Text'); ?></span>
                                <input type="text" id="text-input" class="input-large" placeholder="<?php echo dgettext('process_messages','Type text...'); ?>" />
                            </div>
                        </span>

                        <span id="type-input" style="display:none">
                            <div class="input-prepend">
                                <span class="add-on input-select-label"><?php echo dgettext('process_messages','Input'); ?></span>                   
                                <div class="btn-group">
                                    <select id="input-select" class="input-medium"></select>
                                </div>
                            </div>
                        </span>

                        <span id="type-schedule" style="display:none">
                            <div class="input-prepend">
                                <span class="add-on schedule-select-label"><?php echo dgettext('process_messages','Schedule'); ?></span>
                                <div class="btn-group">
                                    <select id="schedule-select" class="input-large"></select>
                                </div>
                            </div>
                        </span>
                        
                        <span id="type-feed"> 
                            <div class="input-prepend">
                                <span class="add-on feed-select-label"><?php echo dgettext('process_messages','Data'); ?></span>
                                <div class="btn-group">
                                    <select id="feed-data-type" class="input-medium" style="width: 105px;" readonly>
                                        <option value=0>Any type</option>
                                        <option value=1>Realtime</option>
                                        <option value=2>Daily</option>
                                        <option value=3>Histogram</option>
                                    </select>
                                </div>
                            </div>

                            <div class="input-prepend">
                                <span class="add-on feed-select-label"><?php echo dgettext('process_messages','Feed'); ?></span>
                                <div class="btn-group">
                                    <select id="feed-select" class="input-medium"></select>
                                    <input type="text" id="new-feed-name" style="width:140px" placeholder="<?php echo dgettext('process_messages','Type feed name...'); ?>" />
                                    <input type="hidden" id="new-feed-tag"/>
                                </div>
                            </div>
                            
                            <div class="input-prepend">
                                <span class="add-on feed-engine-label"><?php echo dgettext('process_messages','Engine'); ?></span>
                                <div class="btn-group">
                                    <select id="feed-engine" class="input-medium">
<?php // All supported engines must be here, add to engines_hidden array in settings.php to hide them from user ?>
                                        <option value=6>PHPFIWA Fixed Interval With Averaging</option>
                                        <option value=5>PHPFINA Fixed Interval No Averaging</option>
                                        <option value=2>PHPTIMESERIES Variable Interval No Averaging</option>
                                        <option value=0>MYSQL TimeSeries</option>
                                        <option value=8>MYSQL Memory (RAM data lost on power off)</option>
                                        <option value=10>CASSANDRA TimeSeries</option>
                                    </select>

                                    <select id="feed-interval" class="input-mini">
                                        <option value=""><?php echo dgettext('process_messages','Select interval'); ?></option>
                                        <option value=5>5<?php echo dgettext('process_messages','s'); ?></option>
                                        <option value=10>10<?php echo dgettext('process_messages','s'); ?></option>
                                        <option value=15>15<?php echo dgettext('process_messages','s'); ?></option>
                                        <option value=20>20<?php echo dgettext('process_messages','s'); ?></option>
                                        <option value=30>30<?php echo dgettext('process_messages','s'); ?></option>
                                        <option value=60>60<?php echo dgettext('process_messages','s'); ?></option>
                                        <option value=120>2<?php echo dgettext('process_messages','m'); ?></option>
                                        <option value=300>5<?php echo dgettext('process_messages','m'); ?></option>
                                        <option value=600>10<?php echo dgettext('process_messages','m'); ?></option>
                                        <option value=900>15<?php echo dgettext('process_messages','m'); ?></option>
                                        <option value=1200>20<?php echo dgettext('process_messages','m'); ?></option>
                                        <option value=1800>30<?php echo dgettext('process_messages','m'); ?></option>
                                        <option value=3600>1<?php echo dgettext('process_messages','h'); ?></option>
                                        <option value=86400>1<?php echo dgettext('process_messages','d'); ?></option>
                                    </select>
                                </div>
                            </div>
                        </span>
                        <span id="type-btn-add">
                            <div class="input-prepend">
                                <button id="process-add" class="btn btn-info" style="border-radius: 4px;"><?php echo dgettext('process_messages','Add'); ?></button>
                            </div>
                        </span>
                        <span id="type-btn-edit" style="display:none">
                            <div class="input-prepend">
                                <button id="process-edit" class="btn btn-info" style="border-radius: 4px;"><?php echo dgettext('process_messages','Edit'); ?></button>
                            </div>
                            <div class="input-prepend">
                                <button id="process-cancel" class="btn" style="border-radius: 4px;"><?php echo dgettext('process_messages','Cancel'); ?></button>
                            </div>
                        </span>
                </td>
            </tr>
            <tr>
              <td><div id="description" class="alert alert-info"></div></td>
            </tr>
            </table>
    </div>
    <div class="modal-footer">
        <button class="btn" id="close"><?php echo dgettext('process_messages','Close'); ?></button>
        <button id="save-processlist" class="btn btn-success" style="float:right"><?php echo dgettext('process_messages','Not modified'); ?></button>
    </div>
</div>

<?php
/**
*MENU
 */
function worktmp_menu()
{
    add_menu_page('Your Calendar', 'Work Calendar', 'edit_pages', 'worktmp_calendar_list', 'worktmp_calendar_list', plugins_url('worktmplugin/img/icon.png'));
    add_submenu_page('worktmp_calendar_list', 'Your List', 'Your List', 'edit_pages', 'worktmp_absence_list', 'worktmp_absence_list');
    add_submenu_page('worktmp_calendar_list', 'Add Absence', 'Add Absence', 'edit_pages', 'worktmp_add_absence_multiform', 'worktmp_add_absence_multiform',);
    add_submenu_page('worktmp_calendar_list', 'Admin Calendar', 'Admin Calendar', 'administrator', 'worktmp_admin_calendar', 'worktmp_admin_calendar');
    add_submenu_page('worktmp_calendar_list', 'Admin List', 'Admin List', 'administrator', 'worktmp_admin_list', 'worktmp_admin_list');
    add_submenu_page('worktmp_calendar_list', 'Statistics', 'Statistics', 'administrator', 'worktmp_statistics', 'worktmp_statistics');
}
add_action('admin_menu', 'worktmp_menu');
/**
 *Your Work Calendar
 */
function worktmp_calendar_list()
{
    add_action('admin_footer', 'worktmp_fullcalendar_js');
    ?>
    <div class="wrap">
    <h1 style="text-align: center;">Your Calendar</h1>
          <div id="multiForm" class="container" style="display: flex;" >
                    <div id="calendar" style="width: 60%; background: #FFFFFF">
                    </div>
              <div style="flex-grow: 1;">
                        <div id="list" style="background: #FFFFFF">
                        </div>
              </div>

              <div id="myModal" class="modal">
                  <div class="modal-content">
                      <div class="modal-header">
                          <h2 id="modalheader"></h2>
                      </div>
                      <div class="modal-body">
                              <h3>Start: <span id="modal-from-p"></span>, <span id="modal-from-day"></span></h3>
                              <h3>End: <span id="modal-to-p"></span>, <span id="modal-to-day"></span></h3>

                          <h2 style="text-align: center;">Description</h2>
                          <div id="modal-comment-div"></div>
                      </div>
                      <div class="modal-footer">
                      </div>
                  </div>
              </div>
          </div>

      <script>
          var items = <?php echo json_encode(worktmp_fullcalendar_events());  ?>;
      </script>
    <?php
    ?>
    </div>

<?php
}
/**
 * ADD absence multiform
 */
function worktmp_add_absence_multiform(){
    add_action('admin_footer', 'worktmp_multiform_js');
    /**
     * SUBMITING FORM
     */
    if(isset($_POST))
    { worktmp_add_absence($_REQUEST['select_type'], $_REQUEST['absence_start'], $_REQUEST['absence_end'], stripslashes($_POST['mycustomeditor']));
    }
 /**
 * MULTI FORM
 */
                ?>
            <body>
                <h1>Add Absence</h1>
                <form id="multiForm" method="post" >
                    <h1>Absence details</h1>
                        <div class="tab">
                            <div class="s_t_wrapper">
                             <input type="radio" name="select_type" value="Absence" id="option-1" checked>
                             <input type="radio" name="select_type" value="Remote work" id="option-2">
                               <label for="option-1" class="option option-1">
                                  <span>Absence</span>
                                  </label>
                               <label for="option-2" class="option option-2">
                                  <span>Remote Work</span>
                               </label>
                            </div>
                        </div>
                        <div class="tab">
                            <label for="start">FROM:</label>
                            <input class="csstab" type="date" id="absence_start" name="absence_start"
                                   value="<?php echo date('Y-m-d'); ?>"
                                   min="2021-01-01" max="2021-12-31">

                            <label for="end">TO:</label>
                            <input class="csstab" type="date" id="absence_end" name="absence_end"
                                   value="<?php echo date('Y-m-d'); ?>"
                                   min="2021-01-01" max="2021-12-31">
                        </div>

                        <div class="tab">
                            <h2>COMMENT:</h2>
                              <?php
                                $content='';
                                $editor_id = 'mycustomeditor';
                                wp_editor(stripslashes($content), $editor_id);
                                ?>
                        </div>

                        <div style="overflow:auto;">
                            <div style="float:right;">
                                <button type="button" id="prevBtn" onclick="nextPrev(-1)">Previous</button>
                                <button type="button" id="nextBtn" onclick="nextPrev(1)">Next</button>
                            </div>
                        </div>

                        <div style="text-align:center;margin-top:40px;">
                            <span class="step"></span>
                            <span class="step"></span>
                            <span class="step"></span>
                        </div>
                </form>
            </body>
            <?php

            }
/**
*Your absence list + crud
 */
function worktmp_absence_list(){
    $current_user_id=get_current_user_id();
    add_action('admin_footer', 'worktmp_list_js');
    ?>
    <body>
    <h1>Your List</h1>
    <div id="multiForm">
        <div class="worktmp_reloadtable">
            <input type="month" id="searchdate" placeholder="2021-07-07"/>
            <button  id='worktmp_downloadbutton'>Download</button>


            <table id="worktmp_tabled" class="workat">
                <thead>
                <tr>
                    <th>Type</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Added</th>
                    <th>Last Edit</th>
                    <th>Edit/View</th>
                </tr>
                </thead>
                <tbody id="worktmploaddata"></tbody>
            </table>

        </div>


        <div id="editabsence">
            <div class="s_t_wrapper">
                <input type="radio" name="select_type"  value="Absence" id="option-1">
                <input type="radio" name="select_type"  value="Remote work" id="option-2">
                <label for="option-1" class="option option-1">
                    <span>Absence</span>
                </label>
                <label for="option-2" class="option option-2">
                    <span>Remote Work</span>
                </label>
            </div>
            <div>
                <label for="start">FROM:</label>
                <input class="csstab" type="date" id="absence_start" name="absence_start"
                       value="" >

                <label for="end">TO:</label>
                <input class="csstab" type="date" id="absence_end" name="absence_end"
                       value="" >
            </div>
            <h2>COMMENT:</h2>
            <div id="mywpeditor">
                <?php
                $content='';
                $editor_id = 'mycustomeditor';
                wp_editor($content, $editor_id, array( 'textarea_name' => 'mycustomeditorname' ));
                ?>
            </div>
            <button id="worktmp_saveedit">Save</button>
            <button id="worktmp_discardedit">Discard</button>
        </div>

    </div>
    <script>
        var userId = <?php echo $current_user_id; ?>;
    </script>
    </body>
    <?php
}
/**
 * admin work calendar
 */
 function worktmp_admin_calendar(){
     add_action('admin_footer', 'worktmp_fullcalendar_js');
     ?>
<body>
<h1 style="text-align: center;">Admin Calendar</h1>
<div id="multiForm" class="container" style="display: flex;" >
    <div id="calendar" style="width: 60%; background: #FFFFFF">
    </div>
    <div style="flex-grow: 1;">
        <div id="list" style="background: #FFFFFF">
        </div>
    </div>

    <div id="myModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalheader"></h2>
            </div>
            <div class="modal-body">
                <h3>Start: <span id="modal-from-p"></span>, <span id="modal-from-day"></span></h3>
                <h3>End: <span id="modal-to-p"></span>, <span id="modal-to-day"></span></h3>

                <h2 style="text-align: center;">Description</h2>
                <div id="modal-comment-div"></div>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>
<script>
    var items= <?php echo json_encode(worktmp_all_fullcalendar_events());  ?>;
</script>
<?php
?>
</body>
<?php
}
/**
 * all work list + crud Admin
 */
function worktmp_admin_list(){
    add_action('admin_footer', 'worktmp_list_js');
    ?>
    <body>
    <h1>Admin List</h1>
    <div id="multiForm">
        <div class="worktmp_reloadtable">
            <input type="text" id="searchname" placeholder="search by name" list="users"/>
            <datalist id="users" >
                <?php
                $worktmp_users = worktmp_get_users_from_table();
                foreach ( $worktmp_users as $worktmp_users ) {
                    echo '<option>' . $worktmp_users . '</option>';
                }
                ?>
            </datalist>
            <input type="month" id="searchdate" placeholder="2021-07-07"/>
            <button  id='worktmp_downloadbutton'>Download</button>


            <table id="worktmp_tabled" class="workat">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Added</th>
                    <th>Last Edit</th>
                    <th>Edit/View</th>
                </tr>
                </thead>
                <tbody id="worktmploaddata"></tbody>
            </table>

        </div>


        <div id="editabsence">
            <div class="s_t_wrapper">
                <input type="radio" name="select_type"  value="Absence" id="option-1">
                <input type="radio" name="select_type"  value="Remote work" id="option-2">
                <label for="option-1" class="option option-1">
                    <span>Absence</span>
                </label>
                <label for="option-2" class="option option-2">
                    <span>Remote Work</span>
                </label>
            </div>
            <div>
                <label for="start">FROM:</label>
                <input class="csstab" type="date" id="absence_start" name="absence_start"
                       value="" >

                <label for="end">TO:</label>
                <input class="csstab" type="date" id="absence_end" name="absence_end"
                       value="" >
            </div>
            <h2>COMMENT:</h2>
            <div id="mywpeditor">
                <?php
                $content='';
                $editor_id = 'mycustomeditor';
                wp_editor($content, $editor_id, array( 'textarea_name' => 'mycustomeditorname' ));
                ?>
            </div>
            <button id="worktmp_saveedit">Save</button>
            <button id="worktmp_discardedit">Discard</button>
        </div>

    </div>
    <script>
        var userId= 'none';
    </script>
    </body>
    <?php
}
/**
 * Statistics
 */
function worktmp_statistics(){
add_action( 'admin_footer', 'worktmp_statistics_js');
?>
<body>
<h1>Statistics</h1>
<div id="multiForm">
    <ul class="stat-menu">
        <li class="active">Overall</li>
        <li>By User through Month & Year</li>
    </ul>
    <div class="stat-page">
        <div>
            <h2>Absence Type to Month</h2>
            <canvas id="type_month_radar_chart"></canvas>
            <h2>Remote Work to Absence</h2>
            <canvas id="absence_to_remote"></canvas>
        </div>
        <div>
            <h2>Select User And Month</h2>
            <input type="month" id="searchdate" value="<?php echo date('Y-m'); ?>" >
            <input type="text" id="searchname" placeholder="Choose User" list="users"/>
            <datalist id="users" >
                <?php
                $worktmp_users = worktmp_get_users_from_table();
                foreach ( $worktmp_users as $worktmp_users ) {
                    echo '<option>' . $worktmp_users . '</option>';
                }
                ?>
            </datalist>
            <button  id='worktmp_statistic_button' class="worktmp_editbutton">Submit</button>

            <h2 id="selected_user_show" style="text-align: center;"></h2>

            <h2 id="statistic_header1"></h2>
            <div style="display: flex;">
                <div style="flex: 1;"><canvas id="absence_to_remote_to_normal"></canvas></div>
                <div style="width: 300px;"></div>
            </div>

            <h2 id="statistic_year_header"></h2>
            <div style="display: flex;">
                <div style="flex: 1;"><canvas id="statistic_year_user"></canvas></div>
                <div style="width: 300px;"></div>
            </div>


        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</body>
<?php
}



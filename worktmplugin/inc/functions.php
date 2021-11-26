<?php
register_activation_hook(__FILE__, 'worktmplugin_activation');
/**
 * plugin activation
 * 1.create database table
 * 2.fullcalendar init data
 * 3.load styles
 */
function worktmplugin_activation() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'worktmp_absence';
    if ($wpdb->get_var("SHOW TABLES LIKE '" . $table_name . "'") != $table_name) {
        $query = "CREATE TABLE " . $table_name . " (
        id int(9) NOT NULL AUTO_INCREMENT,
        user_id TEXT NOT NULL,
        user_name TEXT NOT NULL,
        absence_type TEXT NOT NULL,
        absence_start DATE NOT NULL,
        absence_end DATE NOT NULL,
        absence_comment TEXT,    
        absence_add DATE,
        absence_edit DATE,   
        PRIMARY KEY  (id)
        )";
        $wpdb->query($query);
        worktmp_add_test_data();
        $wpdb->insert($table_name, array(
            'user_id' => get_current_user_id(),
            'absence_type' => 'init',
            'absence_start' => '1960-10-25',
            'absence_end' => '1960-10-25',
        ));
   }
    add_action('admin_enqueue_scripts', 'worktmp_load_style');
    add_filter('wp_editor_settings', 'worktmp_editor');

}


function worktmp_add_test_data(){
    $time = date('Y-m-d H:i:s');
    global $wpdb;
    $tablename = $wpdb->prefix . 'worktmp_absence';
    $wpdb->insert($tablename, array('user_id' => '11', 'user_name' => 'tester11', 'absence_type' => "Absence", 'absence_start' => "2021-11-02", 'absence_end' => "2021-11-02",'absence_add'=>$time));
    $wpdb->insert($tablename, array('user_id' => '11', 'user_name' => 'tester11', 'absence_type' => "Absence", 'absence_start' => "2021-10-02", 'absence_end' => "2021-10-02",'absence_add'=>$time));
    $wpdb->insert($tablename, array('user_id' => '11', 'user_name' => 'tester11', 'absence_type' => "Absence", 'absence_start' => "2021-09-02", 'absence_end' => "2021-09-02",'absence_add'=>$time));
    $wpdb->insert($tablename, array('user_id' => '11', 'user_name' => 'tester11', 'absence_type' => "Absence", 'absence_start' => "2021-07-02", 'absence_end' => "2021-07-02",'absence_add'=>$time));
    $wpdb->insert($tablename, array('user_id' => '12', 'user_name' => 'tester12', 'absence_type' => "Remote work", 'absence_start' => "2021-02-02", 'absence_end' => "2021-02-02",'absence_add'=>$time));
    $wpdb->insert($tablename, array('user_id' => '12', 'user_name' => 'tester12', 'absence_type' => "Remote work", 'absence_start' => "2021-04-02", 'absence_end' => "2021-04-02",'absence_add'=>$time));
    $wpdb->insert($tablename, array('user_id' => '12', 'user_name' => 'tester12', 'absence_type' => "Remote work", 'absence_start' => "2021-01-02", 'absence_end' => "2021-01-02",'absence_add'=>$time));
    $wpdb->insert($tablename, array('user_id' => '12', 'user_name' => 'tester12', 'absence_type' => "Remote work", 'absence_start' => "2021-12-02", 'absence_end' => "2021-12-02",'absence_add'=>$time));

    ?>
    <div class="notice_updated" id="nu_mutiform">
        <p>Dane Testowe zaladowane</p>
    </div>
    <?php
}
/**
 * CSS and Scripts
 */
function worktmp_load_style(){
    wp_enqueue_style('worktmp_style',plugins_url('/worktmplugin/inc/css/styles.css'));
    wp_enqueue_style('fullcalendar_style',plugins_url('/worktmplugin/inc/fullcalendar/main.min.css'));
}
function worktmp_multiform_js(){
    wp_enqueue_script( 'multiform_script', plugins_url('/worktmplugin/inc/js/multiform.js') );
}
function worktmp_fullcalendar_js(){
    wp_enqueue_script ('fullcalendar_script', plugins_url('/worktmplugin/inc/fullcalendar/main.js') );
    wp_enqueue_script("calendarmenu_script", plugins_url('/worktmplugin/inc/js/calendarmenu.js'));
}
function worktmp_list_js(){
    wp_enqueue_script("absence_list_script", plugins_url('/worktmplugin/inc/js/list.js'));
}
function worktmp_statistics_js(){
    wp_enqueue_script("absence_statistics_script", plugins_url('/worktmplugin/inc/js/stats.js'));

}
/**
 * Multiform database add data
 * @param $type
 * @param $start
 * @param $end
 * @param $comment
 */
function worktmp_add_absence($type, $start, $end, $comment)
{
    if(($start !=NULL) && ($start <= $end) ) {
        global $wpdb;
        $tablename = $wpdb->prefix . 'worktmp_absence';
        $userid = get_current_user_id();
        $time = date('Y-m-d H:i:s');
        wp_get_current_user();
        global $current_user;
        $username = $current_user->user_firstname .= ' ' . $current_user->user_lastname;
        $wpdb->insert($tablename, array(
            'user_id' => $userid,
            'user_name' => $username,
            'absence_type' => $type,
            'absence_start' => $start,
            'absence_end' => $end,
            'absence_comment' => $comment,
            'absence_add' => $time,
            'absence_edit' => $time,
        ));

        ?>
        <div class="notice_updated" id="nu_mutiform">
            <p>Absence Successfully Added</p>
        </div>
        <?php
    } elseif (($start > $end)){
        ?>
        <div class="notice_updated" id="ni_mutiform">
            <p> Invalid date range</p>
        </div>
        <?php
    }



}
/**
 * Ajax ABSENCE TABLE
 * 1.delete absence from table by id
 * 2.get field data by id
 * 3.edit absence in table
 * 4.get table content
 */
add_action('wp_ajax_worktmp_delete_absence', 'worktmp_delete_absence');
function worktmp_delete_absence()
{
    global $wpdb;
    $element_id = $_POST['data_id'];
    $tablename = $wpdb->prefix . 'worktmp_absence';
    $wpdb->delete($tablename, array('id' => $element_id));
}
add_action('wp_ajax_worktmp_get_field_data', 'worktmp_get_field_data');
function worktmp_get_field_data(){
    $absenceid = $_POST['data_id'];
    $field_name = $_POST['field_name'];
    global $wpdb;
    $tablename = $wpdb->prefix . 'worktmp_absence';
    $output = $wpdb->get_results("SELECT $field_name FROM $tablename WHERE id = $absenceid" );
    $output1= $output[0]->$field_name;
    echo $output1;
    wp_die(); //aby wp_ajax nie zwracal dodatkowego 0 po wykonaniu funkcji
}
add_action('wp_ajax_worktmp_edit_absence', 'worktmp_edit_absence');
function worktmp_edit_absence(){
    $id = $_POST['data_id'];
    $type = $_POST['absence_type'];
    $start = $_POST['absence_start'];
    $end = $_POST['absence_end'];
    $comment = stripslashes($_POST['absence_comment']);
    $time = date('Y-m-d H:i:s');
    global $wpdb;
    $tablename = $wpdb->prefix . 'worktmp_absence';
    $wpdb->update($tablename, array(
        'absence_type' => $type,
        'absence_start' => $start,
        'absence_end' => $end,
        'absence_comment' => $comment,
        'absence_edit' => $time,),

        array(
            'id'=>$id
        ));
    ?>
    <div class="notice_updated" id="nu_mutiform">
        <p>Absence Edited</p>
    </div>
    <?php
}
add_action('wp_ajax_worktmp_show_table', 'worktmp_show_table');
function worktmp_show_table()
{
    $userid = $_POST['user_id'];
    global $wpdb;
    json_decode($userid, true);
    $table_name = $wpdb->prefix . "worktmp_absence";
    if ($userid == "none") {
        $absence_data = $wpdb->get_results("SELECT * FROM $table_name");
        foreach ($absence_data as $absence_data) {
            echo "<tr id='$absence_data->id'>";
                echo "<td class='worktmptd'>$absence_data->user_name</td>";
                echo "<td class='worktmptd'>$absence_data->absence_type</td>";
                echo "<td class='worktmptd'>$absence_data->absence_start</td>";
                echo "<td class='worktmptd'>$absence_data->absence_end</td>";
                echo "<td class='worktmptd'>$absence_data->absence_add</td>";
                echo "<td class='worktmptd'>$absence_data->absence_edit</td>";
                echo "<td class='worktmptd'><button  class='worktmp_deletebutton' value='$absence_data->id' >Delete</button>
                           <button  class='worktmp_editbutton' value='$absence_data->id' > Edit/View</button></td>";
            echo "</tr>";
        }

    } else {
        $absence_data = $wpdb->get_results("SELECT * FROM $table_name WHERE user_id = $userid");
        foreach ($absence_data as $absence_data) {
            echo "<tr id='$absence_data->id'>";
                echo "<td class='worktmptd'>$absence_data->absence_type</td>";
                echo "<td class='worktmptd'>$absence_data->absence_start</td>";
                echo "<td class='worktmptd'>$absence_data->absence_end</td>";
                echo "<td class='worktmptd'>$absence_data->absence_add</td>";
                echo "<td class='worktmptd'>$absence_data->absence_edit</td>";
                echo "<td class='worktmptd'><button  class='worktmp_deletebutton' value='$absence_data->id' >Delete</button>
                           <button  class='worktmp_editbutton' value='$absence_data->id' > Edit/View</button></td>";
            echo "</tr>";
        }
    }
    wp_die();
}
/**
 * AJAX data for Statistics
 * 1.absence type counter
 * return counted absence type
 * 2.absence type to month
 * return @array
 * 3. Counter of remote days
 */
add_action('wp_ajax_worktmp_absence_overall_count', 'worktmp_absence_overall_count');
function worktmp_absence_overall_count(){
    $type = $_POST['absence_type'];
    global $wpdb;
    $table_name = $wpdb->prefix . 'worktmp_absence';
    $field_name = 'absence_type';
    $statement =$wpdb->get_results("SELECT $field_name FROM $table_name");
    $statement = json_decode(json_encode($statement), true);
    $absence_counter = 0;
    $remote_counter = 0;
    for($j = 0; $j < sizeof($statement); $j++) {
        if ($statement[$j]['absence_type'] == 'Absence') $absence_counter++;
        else $remote_counter++;
    }
    if ($type == 'absence'){
        echo $absence_counter;
    }
    else echo $remote_counter;
    wp_die();
}
add_action('wp_ajax_worktmp_absence_type_to_month', 'worktmp_absence_type_to_month');
function worktmp_absence_type_to_month(){
    $type = $_POST['absence_type'];
    global $wpdb;
    $table_name = $wpdb->prefix . 'worktmp_absence';
    $statement =$wpdb->get_results("SELECT * FROM $table_name");
    $statement = json_decode(json_encode($statement), true);
    $monthCounter = 0;
    $finalCounter = array();
    for($i = 1; $i <= 12; $i++) {
        for ($j = 0; $j < sizeof($statement); $j++) {
            if ((($statement[$j]['absence_type']) == $type) && ($i ==
                    (substr($statement[$j]['absence_start'], -5, 2)) + 0)
            ) $monthCounter++;
        }
        $finalCounter[]=$monthCounter;
        $monthCounter = 0;
    }
    echo json_encode($finalCounter);
    wp_die();
}
add_action('wp_ajax_worktmp_absence_type_to_month_and_username', 'worktmp_absence_type_to_month_and_username');
function worktmp_absence_type_to_month_and_username(){
    $username = $_POST['user_name'];
    $month = $_POST['month'];
    $type = $_POST['absence_type'];
    global $wpdb;
    $table_name = $wpdb->prefix . 'worktmp_absence';
    $statement =$wpdb->get_results("SELECT * FROM $table_name WHERE user_name = '".$username."'");
    $statement = json_decode(json_encode($statement), true);
    $absenceCounter =0;
    for ($j = 0; $j < sizeof($statement); $j++) {
        if ((($statement[$j]['absence_type']) == $type) && ($month ==
                (substr($statement[$j]['absence_start'], 0, -3)))
        ) $absenceCounter++;
    }

    echo json_encode($absenceCounter);
    wp_die();
}
add_action('wp_ajax_worktmp_absence_throught_year', 'worktmp_absence_throught_year');
function worktmp_absence_throught_year(){
    $username = $_POST['user_name'];
    $dates = $_POST['dates'];
    $type = $_POST['absence_type'];
    global $wpdb;
    $table_name = $wpdb->prefix . 'worktmp_absence';
    $statement =$wpdb->get_results("SELECT * FROM $table_name WHERE user_name = '".$username."'");
    $statement = json_decode(json_encode($statement), true);
    $counter=array();
    for($i=0; $i<12; $i++ ) {
        $absenceCounter = 0;
        for ($j = 0; $j < sizeof($statement); $j++) {
            if ((($statement[$j]['absence_type']) == $type) && ($dates[$i] ==
                    (substr($statement[$j]['absence_start'], 0, -3)))
            ) $absenceCounter++;
        }
        array_push($counter, $absenceCounter);
    }
    echo json_encode($counter);
    wp_die();
}
/**
 * fullcalendar events for user
 * return @array of user events
 */
function worktmp_fullcalendar_events(){
    global $wpdb;
    $table_name = $wpdb->prefix . 'worktmp_absence';
    $userid = get_current_user_id();
    $statement =$wpdb->get_results("SELECT * FROM $table_name WHERE user_id = $userid"  );
    $statement = json_decode(json_encode($statement), true);

    for($j = 0; $j < sizeof($statement); $j++) {
        $fullcalendar_events[$j]['title']=$statement[$j]['absence_type'];
        $fullcalendar_events[$j]['start']=($statement[$j]['absence_start']);
        $fullcalendar_events[$j]['end']=$statement[$j]['absence_end'];
        $fullcalendar_events[$j]['description']=$statement[$j]["absence_comment"];
    }
    return $fullcalendar_events;
}
/**
 * fullcalendar events for admin
 * @return array of all events
 */
function worktmp_all_fullcalendar_events(){
    global $wpdb;
    $table_name = $wpdb->prefix . 'worktmp_absence';
    $statement =$wpdb->get_results("SELECT * FROM $table_name");
    $statement = json_decode(json_encode($statement), true);
    for($j = 0; $j < sizeof($statement); $j++) {
        $fullcalendar_events[$j]['title']=$statement[$j]['user_name'] . ' ' . $statement[$j]['absence_type'];
        $fullcalendar_events[$j]['start']=($statement[$j]['absence_start']);
        $fullcalendar_events[$j]['end']=$statement[$j]['absence_end'];
        $fullcalendar_events[$j]['description']=$statement[$j]["absence_comment"];
    }
    return $fullcalendar_events;
}

/**
 * FUNCTIONS
 */

/**
 * @return array of users in table
 */
function worktmp_get_users_from_table(){
    global $wpdb;
    $table_name = $wpdb->prefix . 'worktmp_absence';
    $statement = $wpdb->prepare( "SELECT user_name FROM $table_name" );
    $users_from_table = $wpdb->get_col( $statement );
    $users_from_table = array_unique($users_from_table);
    return $users_from_table;
}

/**
 * @param $settings
 * @return mixed
 * set wp_editor disable html editor -> unable to put js code into comment
 */
function worktmp_editor($settings) {
    $settings['quicktags'] = false;
    return $settings;
}

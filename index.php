<?php
/*
Plugin Name: RiskCurb Prompts ShortCode Plugin
Plugin URI: https://riskcurb.com
Description: This is a multisite plugin for https://wordpress.org/ users to create 
    new site and manage custom plugins
Author: RiskCurb
Author URI: riskcurb@curbsoftware.com
Version: 1.2.0
*/

if (!defined('ABSPATH')) {
  echo "Nothing l can do when called directly";
  die;
}

//create site table for admin

function database_creation()
{
  global $wpdb;
  $table_name = 'riskcurb_fields';
  $site_details = $wpdb->prefix . $table_name;
  $charset = $wpdb->get_charset_collate;

  $table = "CREATE TABLE " . $site_details . "
  (
  id int(11) NOT NULL AUTO_INCREMENT,
  belongs text DEFAULT NULL,
  name text DEFAULT NULL,
  value text DEFAULT NULL,
  PRIMARY KEY (id)
  ) $charset;
  ";

  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

  dbDelta($table);
}

register_activation_hook(__FILE__, 'database_creation');

function save_prompt($prompt_data, $belongs)
{

  global $wpdb;
  $table_name = $wpdb->prefix . 'riskcurb_fields';
  $get_id = $wpdb->get_var("SELECT `id` FROM $table_name ORDER BY id DESC LIMIT 1");


  $wpdb->insert(
    $wpdb->prefix . 'riskcurb_fields',
    [
      'id' => $get_id + 1,
      'name' => $prompt_data,
      'belongs' => $belongs,
    ]
  );
}
function update_prompt($answer, $site_id)
{

  global $wpdb;
  $wpdb->update(
    $wpdb->prefix . 'riskcurb_fields',
    [
      'value' => $answer,

    ],
    ['id' => $site_id]
  );
}
function delete_prompt($question_id)
{

  global $wpdb;
  $wpdb->delete($wpdb->prefix . 'riskcurb_fields', array('id' => $question_id));
}

function riskcurb_prompts()
{

  $subsites = get_sites();

  if (isset($_POST['question'])) {
    save_prompt($_POST['question'], $_POST['belongs']);
    // exit(json_encode(array('status'=>200,'message'=>'data saved successfully')));
  }

  if (isset($_POST['delete_prompt'])) {
    delete_prompt($_POST['site_id']);
  }

  $form_data = "";
  $form_data .= '
  <form method="POST" style="display:flex;flex-direction: column;">
   <input name="question" placeholder="please enter question"/>

   <select name="belongs" style="color:black;">
   ';

  foreach ($subsites as $subsite) {
     $exploded_sites = explode(".", $subsite->domain);
    $replaced_sitename = str_replace('/', '', strtolower($exploded_sites[0]));

    $form_data .= "
  <option value='$replaced_sitename'>$replaced_sitename</option>
    ";
  }
  $form_data .= '
  
  </select>
  <button type="submit">Save Question</button>
  
  </form>
';

  // echo $form_data;
  print_r($form_data);

  global $wpdb;

  $table_name = $wpdb->prefix . 'riskcurb_fields';

  $data_questions = $wpdb->get_results("SELECT * FROM $table_name ");

  $html = "";
  $html .= '

  <table>

  <thead>
  <th>ID</th>
  <th>questions</th>
  <th>site</th>
  <th>delete</th>

  </thead>

  <tbody>
  ';

  foreach ($data_questions as $data) {
    $html .= "
    <tr>
  <td>$data->id</td>
  <td>$data->name</td>
  <td>
$data->value
  </td>
  <td>
  <form method='POST'>
  <input type='hidden' value='$data->id' name='site_id' />
 <button type='submit' name='delete_prompt'>Delete</button>

</form>
  </td>
  </tr>
    ";
  }

  $html .= '
  

  </tbody>

  </table>
    
    ';

  echo $html;
	
}
add_shortcode('get_clients_prompts_admin', 'riskcurb_prompts');

function get_prompt_persite()
{


  if (isset($_POST['update_question'])) {
    update_prompt($_POST['answer'], $_POST['site_id']);
  }

  $str = $_SERVER['HTTP_REFERER'];
  $exploded_url = explode(".", $str);
  $get_second_array_from_first = explode("//", strtolower($exploded_url[0]));
  $sub_site_name =  $get_second_array_from_first[1];

  // $page_url = $_SERVER['HTTP_HOST'];
  // $exploded_url = explode(".", $page_url);
  // $page_extension = "";
  // if (count($exploded_url) > 1) {
  //   $page_extension = $exploded_url[0];
  // } else {
  //   $page_extension = "";
  // }
  // $replaced_extension = str_replace('/', '', strtolower($page_extension));


  global $wpdb;

  $table_name = $wpdb->prefix . 'riskcurb_fields';
//   print_r($sub_site_name);
  $data_filtered = $wpdb->get_results("SELECT * FROM $table_name ");

  $html = "";
  $html .= '

  <table>

  <thead>
  <th>ID</th>
  <th>questions</th>
  <th>answer</th>
  <th>save</th>

  </thead>

  <tbody>
  ';

  foreach ($data_filtered as $data) {
    $html .= "
    <tr>
  <td>$data->id</td>
  <td>$data->name</td>
  <td>
  <form method='POST'>
  <input type='hidden' value='$data->id' name='site_id' />
  <textarea name='answer'> $data->value </textarea>
  <button type='submit' id='submit_form_2' name='update_question'>Answer</button>
  </form>
  </td>
  
  </tr>
    ";
  }

  $html .= '
  

  </tbody>

  </table>
    
    ';

  echo $html;
}

add_shortcode('get_prompt_persite_code', 'get_prompt_persite');


add_action("admin_menu", "addMenu");

function addMenu()
{
  $icon_url = plugins_url( '/includes/icons/logo.jpg', __FILE__ );
    add_menu_page("RiskCurb App Client Page", "RC App", 4, "riskcurb-app", "getAdminIframe",$icon_url);

}

function getAdminIframe(){

  $content = "";

  $content .= "
  
  <style type='text/css'>
  #my-iframe{
  width: 100%;
  height: 100vh;
  overflow: hidden;
  }
  </style>
  <iframe title='dashboard' src='https://curb.pw?profile=https://gene.riskcurb.com' scolling='no' id='my-iframe'></frame>
  
  <script>
  window.onload = function(){
  let myIframe = document.getElementById('my-iframe');
  let doc = myIframe.contentDocument;
  
  doc.body.innerHTML = doc.body.innerHTML + '
  <style>
  #wpadminbar{display:none !important} body{background-color:none}
  </style>
  ';
  }
  </script>
  ";

  echo $content;
}
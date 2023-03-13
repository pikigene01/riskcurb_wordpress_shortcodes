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
  $table_name = 'riskcurb_prompts';
  $site_details = $wpdb->prefix . $table_name;
  $charset = $wpdb->get_charset_collate;

  $table = "CREATE TABLE " . $site_details . "
  (
  id int NOT NULL,
  belongs text DEFAULT NULL,
  question text DEFAULT NULL,
  answers text DEFAULT NULL,
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
  $table_name = $wpdb->prefix . 'riskcurb_prompts';
  $get_id = $wpdb->get_var("SELECT `id` FROM $table_name ORDER BY id DESC LIMIT 1");


  $wpdb->insert(
    $wpdb->prefix . 'riskcurb_prompts',
    [
      'id' => $get_id + 1,
      'question' => $prompt_data,
      'belongs' => $belongs,
    ]
  );
}
function update_prompt($answer, $site_id)
{

  global $wpdb;
  $wpdb->update(
    $wpdb->prefix . 'riskcurb_prompts',
    [
      'answers' => $answer,

    ],
    ['id' => $site_id]
  );
}
function delete_prompt($question_id)
{

  global $wpdb;
  $wpdb->delete($wpdb->prefix . 'riskcurb_prompts', array('id' => $question_id));
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

   <select name="belongs">
   ';

  foreach ($subsites as $subsite) {
    $replaced_sitename = str_replace('/', '', strtolower($subsite->path));

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

  $table_name = $wpdb->prefix . 'riskcurb_prompts';

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
  <td>$data->question</td>
  <td>
$data->belongs
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




  $page_url = $_SERVER['HTTP_HOST'];
  $exploded_url = explode(".", $page_url);
  $page_extension = "";
  if (count($exploded_url) > 1) {
    $page_extension = $exploded_url[0];
  } else {
    $page_extension = "";
  }
  $replaced_extension = str_replace('/', '', strtolower($page_extension));


  global $wpdb;

  $table_name = $wpdb->prefix . 'riskcurb_prompts';

  $data_filtered = $wpdb->get_results("SELECT * FROM $table_name WHERE belongs = '$replaced_extension' ");

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
  <td>$data->question</td>
  <form method='POST'>
  <input type='hidden' value='$data->id' name='site_id' />
  <td>
  <textarea name='answer'> $data->answers </textarea>
  </td>
  <td>
  <button type='submit' name='update_question'> Save </button>
  </td>
  </form>
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

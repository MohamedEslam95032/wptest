<?php
if ( ! class_exists( 'Houzez_CRM_Notes' ) ) {

	class Houzez_CRM_Notes {

		
		public function __construct() {
			add_action( 'wp_ajax_houzez_crm_add_note', array( $this, 'add_note' ) );
			add_action( 'wp_ajax_houzez_delete_note', array( $this, 'delete_note') );
		}

		public function add_note() {
			global $wpdb;

			$note = sanitize_textarea_field( $_POST['note'] );
			$note_type = sanitize_text_field( $_POST['note_type'] );
			$belong_to = sanitize_text_field( $_POST['belong_to'] );
			
			$nonce = $_POST['security'];
	        if ( ! wp_verify_nonce( $nonce, 'note_add_nonce' ) ) {
	            $ajax_response = array( 'success' => false , 'msg' => esc_html__( 'Security check failed!', 'houzez-crm' ) );
	            echo json_encode( $ajax_response );
	            die;
	        }

	        if ( empty($note) ) {
	            $ajax_response = array( 'success' => false , 'msg' => esc_html__( 'Please enter note!', 'houzez-crm' ) );
	            echo json_encode( $ajax_response );
	            die;
	        }

			$table_name  = $wpdb->prefix . 'houzez_crm_notes';

	        $data = array(
	        	'user_id'   => get_current_user_id(),
                'belong_to' => $belong_to,
                'note'  	=> $note,
                'type'  	=> $note_type,
                
            );

            $format = array(
                '%d',
                '%d',
                '%s',
                '%s',
            );

            $added = $wpdb->insert($table_name, $data, $format);


            if($added) {
            	$inserted_id = $wpdb->insert_id;

            	$single_note = self::get_single_note($inserted_id);

            	$ajax_response = array( 'success' => true , 'noteHtml' => $single_note, 'note_id' => $inserted_id, 'msg' => esc_html__( 'Add successfully', 'houzez-crm' ) );
	            echo json_encode( $ajax_response );
	            die;
            }	
            
            wp_die();
		}

	
		public static function get_single_note($note_id) {
			global $wpdb;
            $table_name = $wpdb->prefix . 'houzez_crm_notes';

			$sql = $wpdb->prepare("SELECT * FROM $table_name WHERE note_id = %d", $note_id);
			$result = $wpdb->get_row($sql, OBJECT);

            if( is_object( $result ) && ! empty( $result ) ) {

            	$datetime = strtotime($result->time);
            	ob_start(); ?>

            	<div id="note-<?php echo intval($note_id); ?>" class="note-block border-top py-4 border-top" style="display: none;">
	                <div class="d-flex justify-content-between align-items-start mb-3">
	                    <div>
	                        <p class="text-muted mb-0 activity-time">
	                            <?php printf( __( '%s ago', 'houzez-crm' ), human_time_diff( $datetime, current_time( 'timestamp' ) ) ); ?>
	                        </p>
	                    </div>
	                    <div class="d-flex gap-2">
	                        <a href="#" class="text-muted delete_note" data-id="<?php echo intval($result->note_id); ?>">
	                            <i class="houzez-icon icon-bin"></i>
	                        </a>
	                    </div>
	                </div>
	                <p class="mb-0"><?php echo esc_attr($result->note); ?></p>
	            </div>


            <?php
            	$output = ob_get_contents();
		        ob_end_clean();
		        return $output;
            }
            return '';
		}

		public static function get_notes($belong_to, $type) {
		    global $wpdb;
		    $table_name = $wpdb->prefix . 'houzez_crm_notes';
		    $user_id = get_current_user_id();

		    // Use prepare() to secure the SQL query
		    $sql = $wpdb->prepare(
		        "SELECT * FROM $table_name WHERE type = %s AND belong_to = %d AND user_id = %d ORDER BY note_id DESC",
		        $type,
		        $belong_to,
		        $user_id
		    );

		    $results = $wpdb->get_results($sql, OBJECT);

		    return $results;
		}


		public function delete_note() {
		    global $wpdb;
		    $table_name = $wpdb->prefix . 'houzez_crm_notes';

		    $user_id = get_current_user_id();

		    // Check if 'note_id' is set and is a number
		    if (!isset($_POST['note_id']) || !is_numeric($_POST['note_id'])) {
		        // Return an error response if 'note_id' is not valid
		        echo json_encode(array(
		            'success' => false,
		            'reason' => esc_html__("Invalid note_id provided", 'houzez-crm')
		        ));
		        die;
		    }
		    $note_id = intval($_POST['note_id']); // Convert to integer for extra safety

		    $deleted = $wpdb->query($wpdb->prepare("DELETE FROM {$table_name} WHERE note_id = %d AND user_id = %d", $note_id, $user_id));

		    if($deleted) {
		        $ajax_response = array('success' => true, 'reason' => '');
		    } else {
		        $ajax_response = array('success' => false, 'reason' => esc_html__("You don't have rights to perform this action", 'houzez-crm'));
		    }
		    echo json_encode($ajax_response);
		    die;
		}



	} // end Houzez_CRM_Notes

	new Houzez_CRM_Notes();
}
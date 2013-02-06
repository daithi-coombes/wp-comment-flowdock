<?php
/**
 * The dashboard settings page for the WP Post Flowdock plugin.
 *
 * @package api-connection-manager
 * @subpackage wp-comment-flowdock
 * @author daithi coombes
 */
class WP_Post_Flowdock_Dash{
	
	private $flows;
	private $option_name = "wp-post-flowdock";
	private $logger;
	
	/**
	 * Construct
	 */
	function __construct() {
		
		//set params
		$this->flows = get_option($this->option_name, array());
		$this->logger = Logger::getLogger(__CLASS__);
		
		//actions
		add_action('admin_menu', array(&$this, 'dash_menu'));
		add_action( 'add_meta_boxes', array(&$this, 'meta_boxes' ));
		//add_action('save_post', array(&$this, 'post_to_flowdock'));
		add_action('edit_post', array(&$this, 'post_to_flowdock'));
	}
	
	/**
	 * Display the dashboard page html
	 * @global API_Connection_Manager $API_Connection_Manager
	 */
	public function dash_page(){
		
		//vars
		global $API_Connection_Manager;
		$module = $API_Connection_Manager->get_service("flowdock/index.php");
		$nonce = wp_create_nonce("wp-post-flowdock-dash");
		
		//add a new flow
		if(@$_REQUEST['apikey']){
			
			//check nonce
			if(!wp_verify_nonce($_REQUEST['_wpnonce'], "wp-post-flowdock-dash"))
				print "<h4>Invalid Nonce</h4>";
			
			//check we have a name
			elseif(!@$_REQUEST['flowname'])
				print "<h4>Please enter a name for your flow</h4>";
			
			//add apikey
			else{
				$options = get_option($this->option_name, array());
				$options[$_REQUEST['flowname']] = $_REQUEST['apikey'];
				update_option($this->option_name, $options);
			}
		}
		//end add a new flow
		
		//get flows
		$flows = get_option($this->option_name, array());
		
		?>
		<h1>WP Post Flowdock</h1>
		<form method="post">
			<input type="hidden" name="_wpnonce" value="<?php echo $nonce; ?>"/>
			<ul>
				<li><label for="apikey">Flow API Key</label>
					<input type="text" name="apikey" id="apikey" required/></li>
				<li><label for="flowname">Flow Name</label>
					<input type="text" name="flowname" id="flowname" required/></li>
				<li><input type="submit" value="Add Flow"/></li>
			</ul>
		</form>
		<?php
		
		if(count($flows)){
			
			?><hr/>
			<table>
				<tr><th>Flow</th><th>API Key</th><th>&nbsp;</th></tr>
			<?php
			foreach($flows as $flow=>$apikey){
				?><tr>
					<td><?php echo $flow; ?></td>
					<td><?php echo $apikey; ?></td>
					<td><form method="post">
							<input type="hidden" name="_wpnonce" value="<?php echo $nonce; ?>"/>
							<input type="hidden" name="flow" value="<?php echo $flow; ?>"/>
							<input type="submit" value="Delete"/>
						</form>
					</td>
				</tr><?php
			}
			?></table><?php
		}
	}
	
	/**
	 * Callback for 'admin_menu' action
	 */
	public function dash_menu(){
		add_posts_page("Flowdock", "Flowdock", "manage_options", "wp-post-flowdock", array(&$this, 'dash_page'));
	}
	
	public function meta_boxes(){
		add_meta_box(
			'wp_post_flowdock_metabox',
			__( 'WP Post Flowdock'),
			array(&$this, 'meta_box'),
			'post',
			'normal',
			'high'
		);
	}
	
	public function meta_box(){
		
		$count=0;
		
		if(count($this->flows)){
			?><ul><?php
			foreach($this->flows as $flow=>$apikey){
				?>
				<li>
					<label for="flow-<?php echo $count; ?>"><?php echo $flow; ?></label>
					<input type="checkbox" name="flowdock-flows[]" id="flow-<?php echo $count; ?>" value="<?php echo $flow; ?>"/>
				</li>
				<?php
				$count++;
			}
			?></ul><?php
		}
	}
	
	public function post_to_flowdock($post_id){
		
		global $API_Connection_Manager;
		global $current_user;
		get_currentuserinfo();
		$post = get_post($post_id);

		$this->logger->info("Setting data for {$post_id}");
		if(@$_REQUEST['flowdock-flows'] && !empty($_REQUEST['flowdock-flows'])){
			
			//post to flowdock
			$params = array(
				'source' => 'Wordpress Post to Flowdock',
				'from_address' => $current_user->user_email,
				'subject' => $post->post_title,
				'content' => $post->post_content
			);
			$module = $API_Connection_Manager->get_service("flowdock/index.php");
			$this->logger->info($params);
			
			//send to flows
			foreach($_REQUEST['flowdock-flows'] as $flow){
				$apikey = $this->flows[$flow];
				$res = $module->request(
					"https://api.flowdock.com/v1/messages/team_inbox/{$apikey}",
					"post",
					$params);
			}
			
			$this->logger->info($res);
		}
			
	}
}
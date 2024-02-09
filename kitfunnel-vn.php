<?php
/*
Plugin Name: KitFunnel VN
Plugin URI: https://kitfunnel.com
Description: Personalización VN
Version: 1.1.14
Author: KitFunnel
License: GPL 2+
License URI: https://kitfunnel.com */ 

require_once "kitfunnelvn-base.php";
class KitFunnelVN {
    public $plugin_file=__FILE__;
    public $response_obj;
    public $license_message;
    public $show_message=false;
    public $slug="kitfunnel-vn";
    public $plugin_version='';
    public $text_domain='';
    function __construct() {
        add_action( 'admin_print_styles', [ $this, 'set_admin_style' ] );
        $this->set_plugin_data();
	    $main_lic_key="KitFunnelVN_lic_Key";
	    $lic_key_name =Kit_Funnel_V_N_Base::get_lic_key_param($main_lic_key);
        $license_key=get_option($lic_key_name,"");
        if(empty($license_key)){
	        $license_key=get_option($main_lic_key,"");
	        if(!empty($license_key)){
	            update_option($lic_key_name,$license_key) || add_option($lic_key_name,$license_key);
            }
        }
        $lice_email=get_option( "KitFunnelVN_lic_email","");
        Kit_Funnel_V_N_Base::add_on_delete(function(){
           update_option("KitFunnelVN_lic_Key","");
        });
        if(Kit_Funnel_V_N_Base::check_wp_plugin($license_key,$lice_email,$this->license_message,$this->response_obj,__FILE__)){
            add_action( 'admin_menu', [$this,'active_admin_menu'],99999);
            add_action( 'admin_post_KitFunnelVN_el_deactivate_license', [ $this, 'action_deactivate_license' ] );
            //$this->licenselMessage=$this->mess;



add_action('admin_enqueue_scripts','bs_custom_admin_styles');function bs_custom_admin_styles(){$css_version='1.16';$css_url=add_query_arg('v',$css_version,plugins_url('/css/styles-admin.css',__FILE__));wp_enqueue_style('custom-admin-styles',$css_url);}add_action('wp_enqueue_scripts','bs_custom_theme_styles');function bs_custom_theme_styles(){$css_version='1.37';$css_url=add_query_arg('v',$css_version,plugins_url('/css/styles-theme.css',__FILE__));wp_enqueue_style('custom-theme-styles',$css_url);}add_filter('woocommerce_payment_complete_order_status','virtual_order_payment_complete_order_status',10,2);function virtual_order_payment_complete_order_status($order_status,$order_id){$order=new WC_Order($order_id);if('processing'==$order_status&&('on-hold'==$order->status||'pending'==$order->status||'failed'==$order->status)){$virtual_order=null;if(count($order->get_items())>0){foreach($order->get_items()as $item){if('line_item'==$item['type']){$_product=$order->get_product_from_item($item);if(!$_product->is_virtual()){$virtual_order=false;break;}else{$virtual_order=true;}}}}if($virtual_order){return 'completed';}}return $order_status;}add_filter('pre_user_first_name','ayudawp_sincronizar_nombre_usuario_wp_woo');function ayudawp_sincronizar_nombre_usuario_wp_woo($first_name){if(isset($_POST['billing_first_name'])){$first_name=$_POST['billing_first_name'];}return $first_name;}add_filter('pre_user_last_name','ayudawp_sincronizar_apellidos_usuario_wp_woo');function ayudawp_sincronizar_apellidos_usuario_wp_woo($last_name){if(isset($_POST['billing_last_name'])){$last_name=$_POST['billing_last_name'];}return $last_name;}add_filter('action_scheduler_retention_period','wpb_action_scheduler_purge');function wpb_action_scheduler_purge(){return DAY_IN_SECONDS*3;}function ocultar_wc_add_to_cart_message($message,$product_id){return '';};add_filter('wc_add_to_cart_message','ocultar_wc_add_to_cart_message',10,2);add_action('woocommerce_before_cart_contents','allow_only_the_last_product_added');add_action('woocommerce_before_checkout_form','allow_only_the_last_product_added');function allow_only_the_last_product_added(){$lastone_key=null;if(WC()->cart->get_cart_contents_count()>1){$cart_content=WC()->cart->get_cart();$lastone_key=key(array_slice($cart_content,-1,1,TRUE));foreach($cart_content as $key=>$product){if($key!=$lastone_key){WC()->cart->remove_cart_item($key);}}}}add_action('template_redirect','redireccion_checkout_condicional');function redireccion_checkout_condicional(){if(is_cart()&&WC()->cart->get_cart_contents_count()>0){wp_safe_redirect(wc_get_checkout_url());exit;}}function my_text_strings($translated_text,$text,$domain){switch($translated_text){case ' Welcome Back %1$s ( %2$s )':$translated_text=__('Hola %1$s [%2$s]','woocommerce');break;case 'Invalid username, email address or password.':$translated_text=__('Email o contraseña no válido.','woocommerce');break;}return $translated_text;}add_filter('gettext','my_text_strings',20,3);function new_submenu_woocommerce(){add_submenu_page('woocommerce','Cupones','Cupones','manage_options','mi-enlace-submenu-con-slug','slug_cupon');}add_action('admin_menu','new_submenu_woocommerce');function slug_cupon(){$slug='wp-admin/edit.php?post_type=shop_coupon';wp_safe_redirect(home_url($slug));exit;}function ayudawp_quitar_comprobar_clave(){if(wp_script_is('wc-password-strength-meter','enqueued')){wp_dequeue_script('wc-password-strength-meter');}}add_action('wp_print_scripts','ayudawp_quitar_comprobar_clave',100);add_action('init','redireccionar_urls');function redireccionar_urls(){$redirecciones=array('/mi-cuenta/lost-password/'=>'/cn/wp-login.php?action=lostpassword',);$ruta_actual=$_SERVER['REQUEST_URI'];foreach($redirecciones as $ruta_ant=>$ruta_upd){if(strpos($ruta_actual,$ruta_ant)!==false){$ruta_upd_completa=$ruta_upd;header("Cache-Control: no-cache, must-revalidate");wp_redirect($ruta_upd_completa,301);exit();}}}add_action('wp_footer','open_link_in_new_tab');function open_link_in_new_tab(){if(is_account_page()&&is_wc_endpoint_url('view-subscription')){ ?><script>jQuery(document).ready(function(t){t(".shop_table.subscription_details a.subscription_renewal_early").attr("target","_blank")})</script><?php }}



        }else{
            if(!empty($license_key) && !empty($this->license_message)){
               $this->show_message=true;
            }
            update_option($license_key,"") || add_option($license_key,"");
            add_action( 'admin_post_KitFunnelVN_el_activate_license', [ $this, 'action_activate_license' ] );
            add_action( 'admin_menu', [$this,'inactive_menu']);
        }
    }
    public function set_plugin_data(){
		if ( ! function_exists( 'get_plugin_data' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		if ( function_exists( 'get_plugin_data' ) ) {
			$data = get_plugin_data( $this->plugin_file );
			if ( isset( $data['Version'] ) ) {
				$this->plugin_version = $data['Version'];
			}
			if ( isset( $data['TextDomain'] ) ) {
				$this->text_domain = $data['TextDomain'];
			}
		}
    }
	private static function &get_server_array() {
		return $_SERVER;
	}
	private static function get_raw_domain(){
		if(function_exists("site_url")){
			return site_url();
		}
		if ( defined( "WPINC" ) && function_exists( "get_bloginfo" ) ) {
			return get_bloginfo( 'url' );
		} else {
			$server = self::get_server_array();
			if ( ! empty( $server['HTTP_HOST'] ) && ! empty( $server['SCRIPT_NAME'] ) ) {
				$base_url  = ( ( isset( $server['HTTPS'] ) && $server['HTTPS'] == 'on' ) ? 'https' : 'http' );
				$base_url .= '://' . $server['HTTP_HOST'];
				$base_url .= str_replace( basename( $server['SCRIPT_NAME'] ), '', $server['SCRIPT_NAME'] );
				
				return $base_url;
			}
		}
		return '';
	}
	private static function get_raw_wp(){
		$domain=self::get_raw_domain();
		return preg_replace("(^https?://)", "", $domain );
	}
	public static function get_lic_key_param($key){
		$raw_url=self::get_raw_wp();
		return $key."_s".hash('crc32b',$raw_url."vtpbdapps");
	}
	public function set_admin_style() {
        wp_register_style( "KitFunnelVNLic", plugins_url("_lic_style.css",$this->plugin_file),10,time());
        wp_enqueue_style( "KitFunnelVNLic" );
    }
	public function active_admin_menu(){
        
		add_menu_page (  "KitFunnelVN", "KitFunnel VN", "activate_plugins", $this->slug, [$this,"activated"], " dashicons-screenoptions ");
		//add_submenu_page(  $this->slug, "KitFunnelVN License", "License Info", "activate_plugins",  $this->slug."_license", [$this,"activated"] );

    }
	public function inactive_menu() {
        add_menu_page( "KitFunnelVN", "KitFunnel VN", 'activate_plugins', $this->slug,  [$this,"license_form"], " dashicons-screenoptions " );

    }
    function action_activate_license(){
        check_admin_referer( 'el-license' );
        $license_key=!empty($_POST['el_license_key'])?sanitize_text_field(wp_unslash($_POST['el_license_key'])):"";
        $license_email=!empty($_POST['el_license_email'])?sanitize_email(wp_unslash($_POST['el_license_email'])):"";
        update_option("KitFunnelVN_lic_Key",$license_key) || add_option("KitFunnelVN_lic_Key",$license_key);
        update_option("KitFunnelVN_lic_email",$license_email) || add_option("KitFunnelVN_lic_email",$license_email);
        update_option('_site_transient_update_plugins','');
        wp_safe_redirect(admin_url( 'admin.php?page='.$this->slug));
    }
    function action_deactivate_license() {
        check_admin_referer( 'el-license' );
        $message="";
	    $main_lic_key="KitFunnelVN_lic_Key";
	    $lic_key_name =Kit_Funnel_V_N_Base::get_lic_key_param($main_lic_key);
        if(Kit_Funnel_V_N_Base::remove_license_key(__FILE__,$message)){
            update_option($lic_key_name,"") || add_option($lic_key_name,"");
            update_option('_site_transient_update_plugins','');
        }
        wp_safe_redirect(admin_url( 'admin.php?page='.$this->slug));
    }
    function activated(){
        ?>
        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
            <input type="hidden" name="action" value="KitFunnelVN_el_deactivate_license"/>
            <div class="el-license-container">
                <h3 class="el-license-title"><i class="dashicons-before dashicons-screenoptions"></i> <?php esc_html_e("KitFunnel VN","kitfunnel-vn");?> </h3>
                <hr>
                <ul class="el-license-info">
                <li>
                    <div>
                        <span class="el-license-info-title"><?php esc_html_e("Status","kitfunnel-vn");?></span>

                        <?php if ( $this->response_obj->is_valid ) : ?>
                            <span class="el-license-valid"><?php esc_html_e("Valid","kitfunnel-vn");?></span>
                        <?php else : ?>
                            <span class="el-license-valid"><?php esc_html_e("Invalid","kitfunnel-vn");?></span>
                        <?php endif; ?>
                    </div>
                </li>

                <li>
                    <div>
                        <span class="el-license-info-title"><?php esc_html_e("License Type","kitfunnel-vn");?></span>
                        <?php echo esc_html($this->response_obj->license_title,"kitfunnel-vn"); ?>
                    </div>
                </li>

               <li>
                   <div>
                       <span class="el-license-info-title"><?php esc_html_e("License Expired on","kitfunnel-vn");?></span>
                       <?php echo esc_html($this->response_obj->expire_date,"kitfunnel-vn");
                       if(!empty($this->response_obj->expire_renew_link)){
                           ?>
                           <a target="_blank" class="el-blue-btn" href="<?php echo esc_url($this->response_obj->expire_renew_link); ?>">Renew</a>
                           <?php
                       }
                       ?>
                   </div>
               </li>

               <li>
                   <div>
                       <span class="el-license-info-title"><?php esc_html_e("Support Expired on","kitfunnel-vn");?></span>
                       <?php
                           echo esc_html($this->response_obj->support_end,"kitfunnel-vn");;
                        if(!empty($this->response_obj->support_renew_link)){
                            ?>
                               <a target="_blank" class="el-blue-btn" href="<?php echo esc_url($this->response_obj->support_renew_link); ?>">Renew</a>
                            <?php
                        }
                       ?>
                   </div>
               </li>
                <li>
                    <div>
                        <span class="el-license-info-title"><?php esc_html_e("Your License Key","kitfunnel-vn");?></span>
                        <span class="el-license-key"><?php echo esc_attr( substr($this->response_obj->license_key,0,9)."XXXXXXXX-XXXXXXXX".substr($this->response_obj->license_key,-9) ); ?></span>
                    </div>
                </li>
                </ul>
                <div class="el-license-active-btn">
                    <?php wp_nonce_field( 'el-license' ); ?>
                    <?php submit_button('Desactivar'); ?>
                </div>
            </div>
        </form>
    <?php
    }

    function license_form() {
        ?>
    <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
        <input type="hidden" name="action" value="KitFunnelVN_el_activate_license"/>
        <div class="el-license-container">
            <h3 class="el-license-title"><i class="dashicons-before dashicons-screenoptions"></i> <?php esc_html_e("KitFunnel VN","kitfunnel-vn");?></h3>
            <hr>
            <?php
            if(!empty($this->show_message) && !empty($this->license_message)){
                ?>
                <div class="notice notice-error is-dismissible">
                    <p><?php echo esc_html($this->license_message,"kitfunnel-vn"); ?></p>
                </div>
                <?php
            }
            ?>
            <p><?php esc_html_e("Ingresa tu clave de licencia y correo electrónico de compra para activar KitFunnel VN y habilitar las actualizaciones.","kitfunnel-vn");?></p>
			<p><br></p>

            <div class="el-license-field">
                <label for="el_license_key"><?php echo esc_html("Código de licencia","kitfunnel-vn");?></label>
                <input type="text" class="regular-text code" name="el_license_key" size="50" placeholder="xxxxxxxx-xxxxxxxx-xxxxxxxx-xxxxxxxx" required="required">
            </div>
            <div class="el-license-field">
                <label for="el_license_key"><?php echo esc_html("Email","kitfunnel-vn");?></label>
                <?php
                    $purchase_email   = get_option( "KitFunnelVN_lic_email", get_bloginfo( 'admin_email' ));
                ?>
                <input type="text" class="regular-text code" name="el_license_email" size="50" value="<?php echo esc_html($purchase_email); ?>" placeholder="" required="required">
                <div><small><?php echo esc_html("Agrega el correo electrónico de registro cuando adquiriste KitFunnel.","kitfunnel-vn");?></small></div>
            </div>
            <div class="el-license-active-btn">
                <?php wp_nonce_field( 'el-license' ); ?>
                <?php submit_button('Activar ahora'); ?>
            </div>
        </div>
    </form>
        <?php
    }
}

new KitFunnelVN();
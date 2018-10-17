<?php
/**
 *
 * - PopojiCMS Front End File
 *
 * - File : index.php
 * - Version : 1.2
 * - Author : Jenuar Dalapang
 * - License : MIT License
 *
 *
 * Ini adalah file yang akan di panggil pertama kali ketika mengakes website.
 * This is a file will be called first when accessing the website.
 *
*/

/**
 * Memulai session
 *
 * Start session
 *
*/
session_start();

/**
 * Mendefinisikan Base Url
 *
 * Define Base Url
 *
*/
$base_root = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] && !in_array(strtolower($_SERVER['HTTPS']),array('off','no'))) ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].$_SERVER["PHP_SELF"];
$base_url = preg_replace("/\/(index\.php$)/", "", $base_root);
define('BASE_URL', $base_url);

/**
 * Memfilter jika engine belum di install
 *
 * Filtering if engine not installed
 *
*/
if (file_exists('install.php')) {
	header('location:'.BASE_URL.'/install.php');
} else {
	/**
	 * Memanggil library utama
	 *
	 * Call main library
	 *
	*/
	include_once 'po-includes/core/config.php';

	if (VQMOD == TRUE) {
		require_once 'vqmod/vqmod.php';
		VQMod::bootup();
		include_once VQMod::modCheck('po-includes/core/core.php');
	} else {
		include_once 'po-includes/core/core.php';
	}

	/**
	 * Memisahkan request ke bagian admin dan ke bagian depan
	 *
	 * Filtering request to admin and to front end
	 *
	*/
	if (strpos($_SERVER['REQUEST_URI'], DIR_ADM) !== false) {
		header('location:index.php');
	} else {
		/**
		 * Mendeklarasi class PoCore
		 *
		 * Declaration PoCore class
		 *
		*/
		$core = new PoCore();

		/**
		 * Mendeklarasi pemilihan bahasa
		 *
		 * Declaration language choosing
		 *
		 * Add change language with get method (v.2.0.2)
		*/
		if (isset($_POST['lang'])) {
			$check_lang = $core->podb->from('language')
				->where('code', $core->postring->valid($_POST['lang'], 'xss'))
				->limit(1)
				->fetch();
			if ($check_lang) {
				setcookie('po_lang_front', $core->postring->valid($_POST['lang'], 'xss'), 1719241200, '/');
				define('WEB_LANG_ID', $check_lang['id_language']);
				define('WEB_LANG', $check_lang['code']);
			} else {
				$current_lang = $core->podb->from('language')
					->where('id_language', '1')
					->limit(1)
					->fetch();
				define('WEB_LANG_ID', $current_lang['id_language']);
				define('WEB_LANG', $current_lang['code']);
			}
			header('location:'.$_POST['refer']);
		} elseif (isset($_GET['lang'])) {
			$check_lang = $core->podb->from('language')
				->where('code', $core->postring->valid($_GET['lang'], 'xss'))
				->limit(1)
				->fetch();
			if ($check_lang) {
				setcookie('po_lang_front', $core->postring->valid($_GET['lang'], 'xss'), 1719241200, '/');
				define('WEB_LANG_ID', $check_lang['id_language']);
				define('WEB_LANG', $check_lang['code']);
			} else {
				$current_lang = $core->podb->from('language')
					->where('id_language', '1')
					->limit(1)
					->fetch();
				define('WEB_LANG_ID', $current_lang['id_language']);
				define('WEB_LANG', $current_lang['code']);
			}
		} elseif (isset($_COOKIE['po_lang_front'])) {
			$current_lang = $core->podb->from('language')
				->where('code', $_COOKIE['po_lang_front'])
				->limit(1)
				->fetch();
			define('WEB_LANG_ID', $current_lang['id_language']);
			define('WEB_LANG', $current_lang['code']);
		} else {
			$current_lang = $core->podb->from('language')
				->where('id_language', '1')
				->limit(1)
				->fetch();
			define('WEB_LANG_ID', $current_lang['id_language']);
			define('WEB_LANG', $current_lang['code']);
		}

		/**
		 * Statistik pengunjung
		 *
		 * Visitor statistic
		 *
		*/
		$browser = new PoBrowser();
		$ip_stat = $_SERVER['REMOTE_ADDR'];
		$date_stat = date("Ymd");
		$time_stat = time();
		$browser_stat = $browser->getBrowser();
		$os_stat = $browser->getUserAgent();
		$platform_stat = $browser->getPlatform();
		if ($core->porequest->check_internet_connection()) {
			$ip_data = json_decode(@file_get_contents("http://www.geoplugin.net/json.gp?ip=".$ip_stat));    
			if ($ip_data && $ip_data->geoplugin_countryName != null) {
				$country_stat = $ip_data->geoplugin_countryName;
				$city_stat = $ip_data->geoplugin_city;
			} else {
				$country_stat = '';
				$city_stat = '';
			}
		} else {
			$country_stat = '';
			$city_stat = '';
		}

		$statistics = $core->podb->from('traffic')
			->where('ip', $ip_stat)
			->where('country', $country_stat)
			->where('city', $city_stat)
			->where('date', $date_stat)
			->count();
		if ($statistics > 0) {
			$current_statistic = $core->podb->from('traffic')
				->where('ip', $ip_stat)
				->where('country', $country_stat)
				->where('city', $city_stat)
				->where('date', $date_stat)
				->limit(1)
				->fetch();
			$data_stat = array(
				'hits' => $current_statistic['hits']+1,
				'online' => $time_stat
			);
			$query_stat = $core->podb->update('traffic')
				->set($data_stat)
				->where('ip', $ip_stat)
				->where('country', $country_stat)
				->where('city', $city_stat)
				->where('date', $date_stat);
			$query_stat->execute();
		} else {
			$data_stat = array(
				'ip' => $ip_stat,
				'browser' => $browser_stat,
				'os' => $os_stat,
				'platform' => $platform_stat,
				'country' => $country_stat,
				'city' => $city_stat,
				'date' => $date_stat,
				'hits' => 1,
				'online' => $time_stat
			);
			$query_stat = $core->podb->insertInto('traffic')->values($data_stat);
			$query_stat->execute();
		}

		/**
		 * Mendeklarasi class PoRouter
		 *
		 * Declaration PoRouter class
		 *
		*/
		$router = new PoRouter();

		/**
		 * Alihkan permintaan ke 404, jika router tidak ditemukan
		 *
		 * Redirect request to 404, if router not found
		 *
		*/
		$router->set404(function() {
			header('HTTP/1.1 404 Not Found');
			if (VQMOD == TRUE) {
				include_once VQMod::modCheck('404.php');
			} else {
				include_once '404.php';
			}
		});

		/**
		 * Mendeklarasi class Template
		 *
		 * Declaration Template class
		 *
		*/
		if (strpos($_SERVER['REQUEST_URI'], 'member') !== false) {
			$active_template = "/member";
		} else {
			$active_template = "/".$core->potheme['folder'];
		}
		$templates = new PoTemplate\Engine(DIR_CON.'/themes'.$active_template);
		$templates->loadExtension(new PoTemplate\Extension\PoCore());
		$templates->loadExtension(new PoTemplate\Extension\Asset(DIR_CON.'/themes'.$active_template));
		$templates->loadExtension(new PoTemplate\Extension\AssetCssJs());

		/**
		 * Memanggil file komponen untuk bagian depan
		 *
		 * Call component file for front end
		 *
		*/
		$get_components = new PoDirectory();
		$components = $get_components->listDir(DIR_CON.'/component/');
		foreach($components as $component) {
			if ($component != 'index.html') {
				if (file_exists(DIR_CON.'/component/'.$component.'/'.$component.'.php')) {
					if (VQMOD == TRUE) {
						include_once VQMod::modCheck(DIR_CON.'/component/'.$component.'/'.$component.'.php');
					} else {
						include_once DIR_CON.'/component/'.$component.'/'.$component.'.php';
					}
				}
			}
		}

		/**
		 * Memanggil file widget untuk bagian depan
		 *
		 * Call component widget for front end
		 *
		*/
		$get_widgets = new PoDirectory();
		$widgets = $get_widgets->listDir(DIR_CON.'/widget/');
		foreach($widgets as $widget) {
			if ($widget != 'index.html') {
				if (file_exists(DIR_CON.'/widget/'.$widget.'/'.$widget.'.php')) {
					if (VQMOD == TRUE) {
						include_once VQMod::modCheck(DIR_CON.'/widget/'.$widget.'/'.$widget.'.php');
					} else {
						include_once DIR_CON.'/widget/'.$widget.'/'.$widget.'.php';
					}
					$widget_name = ucfirst($widget);
					$templates->loadExtension(new $widget_name());
				}
			}
		}

		/**
		 * Menjalankan router untuk mode pemeliharaan
		 *
		 * Run router for maintenance mode
		 *
		*/
		$router->before('GET|POST', '/.*', function() use ($core) {
			if ($core->posetting[16]['value'] == 'Y') {
				header('location:'.BASE_URL.'/maintenance.php');
				exit();
			}
		});

		/**
		 * Menjalankan router
		 *
		 * Run router
		 *
		*/
		$router->run(function() use ($templates) {
			return $templates;
		});
	}
}
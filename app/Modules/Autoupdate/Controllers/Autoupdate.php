<?php namespace App\Modules\Autoupdate\Controllers;
use ZipArchive;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
@ini_set('memory_limit', '100M');
@ini_set('max_execution_time', 400);
@ini_set("allow_url_fopen", 1);
//Get Update file
define('UPDATE_URL','https://update.bdtask.com/pharmacare/autoupdate');
// Get latest version info
define('VERSION_PATH','https://update.bdtask.com/pharmacare/versions/');
//  if ( @fopen("https://update.bdtask.com", "r") ) 
//       {

//          $min_version = file_get_contents('https://update.bdtask.com/pharmacare/autoupdate/update_min_version');
//          $max_version = file_get_contents('https://update.bdtask.com/pharmacare/autoupdate/update_max_version');
         
//       } 
//       else 
//       {
       $max_version    = 9.4;
       $min_version    = 9.4;
     
    //   } 
//Get Update file
define('MIN_VERSION', $min_version);
//Get Update file
define('MAX_VERSION', $max_version);
// CRM temporary path
define('TEMP_FOLDER', FCPATH .'temp' . '/');
class Autoupdate extends BaseController {  
    private $tmp_update_dir;
    private $tmp_dir;


     public function index(){ 
    //      if ( @fopen("https://update.bdtask.com", "r") ) 
    //   {
    //      $min_version = file_get_contents('https://update.bdtask.com/pharmacare/autoupdate/update_min_version');
    //      $max_version = file_get_contents('https://update.bdtask.com/pharmacare/autoupdate/update_max_version');
    //      $latest_version = file_get_contents(UPDATE_INFO_URL);
    //   } 
    //   else 
    //   {
       $max_version    =  $this->current_version();
       $min_version    =  $this->current_version();
       $latest_version =  $this->current_version();
    //   } 
        $data = array();
        $data['latest_version']  = $latest_version;
        $data['current_version'] = $this->current_version();
        $data['version_path']    = VERSION_PATH;
        //Checking update available or not
        if ($data['current_version']==$data['latest_version']) {
            //Your Message
        }
        //compatible version 
        else if ($data['current_version'] >= MIN_VERSION && $data['current_version'] <= MAX_VERSION) {
            $data['message_txt'] = '<i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Update available';

        }else{
            $data['exception_txt'] = '<i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Latest version is not compatible with this version';
        }        
        $data['title']  = "autoupdate";
        $data['module'] = "Autoupdate";  
        $data['page']   = "autoupdate";  
       return $this->template->layout($data);

      

    }
    public function checkserver(){
            
            if(ini_get('allow_url_fopen')) {
                echo 1;
            }
            else {
                echo 0;
            }
        }
    public function update()
    {
        $purchase_key   =  $this->request->getVar('purchase_key');
        $version        =  $this->request->getVar('version');   
        $purchase_key   = trim($purchase_key);
        $latest_version = file_get_contents(UPDATE_INFO_URL);
        $url            = UPDATE_URL;


  $rules = [
                'purchase_key'   => ['label' => lan('purchase_key'),'rules' => 'required|max_length[100]|trim'],
 
            ];

            if (! $this->validate($rules)) {
              
                $this->session->setFlashdata(array('exception'=>$this->validator->listErrors()));
                  return  redirect()->to(base_url('autoupdate/autoupdate'));
            }else{
            $product_version = $this->current_version();
            $product_key     = $this->product_key();
            $check_data = array(
                    'base_url'        => site_url(),
                    'running_version' => $product_version,
                    'purchase_key'    => $purchase_key,
                    'product_key'     => $product_key,
                    'user_ip'         => $this->request->getIPAddress(),
                    'server_ip'       => $_SERVER['SERVER_ADDR'],
                    'version'         => $version,
                );
                
              
               

            // Get The Zip File From Server
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_USERAGENT,$this->request->getUserAgent());
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//last
            curl_setopt($ch, CURLOPT_FAILONERROR, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_AUTOREFERER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 300);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_POSTFIELDS, array(
                    'base_url'        => site_url(),
                    'running_version' => $product_version,
                    'purchase_key'    => $purchase_key,
                    'product_key'     => $product_key,
                    'user_ip'         => $this->request->getIPAddress(),
                    'server_ip'       => $_SERVER['SERVER_ADDR'],
                    'version'         => $version,
                ));

            $success = curl_exec($ch);
            $response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            $return_data = json_decode($success, true);
            if ($return_data['purchase_key'] == 'invalid' && $response_code==200) {
                $this->session->setFlashdata('exception', 'Purchase key invalid.');

            }elseif($return_data['purchase_key'] == 'valid' && $response_code==200){
                $this->session->setFlashdata('message', 'Software updated successfully');
            }

        }

       return  redirect()->to(base_url('/autoupdate/autoupdate/'));
      

    }

    public function updatenow()
    {
        $purchase_key   = $this->request->getVar('purchase_key');       
        $purchase_key   = trim($purchase_key);
        $latest_version = file_get_contents(UPDATE_INFO_URL);
        $url            =  $this->request->getVar('update_url');
        $product_version = $this->current_version();
        $product_key     = $this->product_key();

        $tmp_dir = $this->get_temp_dir();
        if (!$tmp_dir || !is_writable($tmp_dir)) {
            $tmp_dir = TEMP_FOLDER;
        }

        $tmp_dir = rtrim($tmp_dir, '/') . '/';
        if (!is_writable($tmp_dir)) {
            header('HTTP/1.0 400');
            echo json_encode(array("Temporary directory not writable - <b>$tmp_dir</b><br/>Please contact your hosting provider make this directory writable. The directory needs to be writable for the update files."));
            die;
        }

        $this->tmp_dir        = $tmp_dir;
        $tmp_dir              = $tmp_dir . 'v' . $latest_version . '/';
        $this->tmp_update_dir = $tmp_dir;

        if (!is_dir($tmp_dir)) {
            mkdir($tmp_dir, 0755);
            fopen($tmp_dir . 'index.html', 'w');
        }

        $zipFile = $tmp_dir . $latest_version . '.zip'; // Local Zip File Path
        $zipResource = fopen($zipFile, 'w+');

        // Get The Zip File From Server
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->request->getUserAgent());
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
         curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_FILE, $zipResource);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array(
                'base_url'        => site_url(),
                'running_version' => $product_version,
                'purchase_key'    => $purchase_key,
                'product_key'     => $product_key,
                'user_ip'         => $this->request->getIPAddress(),
                'server_ip'       => $_SERVER['SERVER_ADDR'],
            ));

        $success = curl_exec($ch);
        if (!$success) {
            $this->clean_tmp_files();

            $response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if (curl_errno($ch)) {
                print "Error: " . curl_error($ch);

            }
        }

        curl_close($ch);

        $file_path = FCPATH;
        $zip = new ZipArchive();
        if ($zip->open($zipFile) === true) {

            for( $i = 0; $i < $zip->numFiles; $i++ ){ 
                $stat = $zip->statIndex( $i ); 
                $sqlExist = explode('.', (basename($stat['name']).PHP_EOL ));

                if (isset($sqlExist[1]) && trim($sqlExist[1])=="sql") {
                    $file_path .= $stat['name'];
                }
            }

            if (!$zip->extractTo(FCPATH)) {
                header('HTTP/1.0 400 Bad error');
                echo json_encode(array('Failed to extract downloaded zip file'));
            }else{
                $path = FCPATH.'system/Security/lic.php'; 
                if (file_exists($path)) {
                    // Open the file
                    $whitefile = file_get_contents($path);
                    //set license key configuration
                    $new  = str_replace(@$product_version, @$latest_version, $whitefile);

                    // Write the new database.php file
                    $handle = fopen($path,'w+');

                    // Chmod the file, in case the user forgot
                    @chmod($path, 0777);

                    // Verify file permissions
                    if (is_writable($path)) {
                        // Write the file
                        if (fwrite($handle,$new)) {
                            @chmod($path,0755);

                            //Wait 5 seconds and install database
                            sleep(2);
                            $this->database(@$file_path);
                        

                            return true;
                        } else {
                        //file not write
                            return false;
                        }
                    } else {
                        //file is not writeable
                        return false;
                    }
                } else {
                    //file is not exists
                    return false;
                }
            }

            $zip->close();

        } else {
            header('HTTP/1.0 400 Bad error');
            echo json_encode(array('Failed to open downloaded zip file'));
        }

        $this->clean_tmp_files();
        
    }

    
    private function database($file_path = null)
    {
        $this->db = db_connect();
        
        $sql_contents = file_get_contents($file_path);
        $sql_contents = explode(";", $sql_contents);
        
     


        foreach($sql_contents as $query)
        {
            $pos = strpos($query, 'ci_sessions');
            
            if($pos == false)
            {
                $result = $this->db->query($query);
            }
            else
            {
                continue;
            }
        }

    }

    private function clean_tmp_files()
    {
        if (is_dir($this->tmp_update_dir)) {
            if (@!$this->delete_dir($this->tmp_update_dir)) {
                @rename($this->tmp_update_dir, $this->tmp_dir . 'delete_this_' . uniqid());
            }
        }
    }

    /**
     * Return server temporary directory
     * @return string
    **/
    private function get_temp_dir()
    {
        if (function_exists('sys_get_temp_dir')) {
            $temp = sys_get_temp_dir();
            if (@is_dir($temp) && is_writable($temp)) {
                return rtrim($temp, '/\\') . '/';
            }
        }

        $temp = ini_get('upload_tmp_dir');
        if (@is_dir($temp) && is_writable($temp)) {
            return rtrim($temp, '/\\') . '/';
        }

        $temp = TEMP_FOLDER;
        if (is_dir($temp) && is_writable($temp)) {
            return $temp;
        }

        return '/tmp/';
    }

    /**
     * Delete directory
     * @param  string $dirPath dir
     * @return boolean
    **/
    private function delete_dir($dirPath)
    {
        if (!is_dir($dirPath)) {
            throw new InvalidArgumentException("$dirPath must be a directory");
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                delete_dir($file);
            } else {
                unlink($file);
            }
        }
        if (rmdir($dirPath)) {
            return true;
        }

        return false;
    }

    private function current_version(){

        //Current Version
        $product_version = '';
        $path = FCPATH.'system/Security/lic.php'; 
        if (file_exists($path)) {
            
            // Open the file
            $whitefile = file_get_contents($path);

            $file = fopen($path, "r");
            $i    = 0;
            $product_version_tmp = array();
            $product_key_tmp = array();
            while (!feof($file)) {
                $line_of_text = fgets($file);

                if (strstr($line_of_text, 'product_version')  && $i==0) {
                    $product_version_tmp = explode('=', strstr($line_of_text, 'product_version'));
                    $i++;
                }                
            }
            fclose($file);

            $product_version = trim(@$product_version_tmp[1]);
            $product_version = ltrim(@$product_version, '\'');
            $product_version = rtrim(@$product_version, '\';');

            return @$product_version;
            
        } else {
            //file is not exists
            return false;
        }
        
    }

    private function product_key(){

        //Current Version
        $product_key     = '';
        $path = FCPATH.'system/Security/lic.php'; 
        if (file_exists($path)) {
            
            // Open the file
            $whitefile = file_get_contents($path);

            $file = fopen($path, "r");
            $j    = 0;
            $product_version_tmp = array();
            $product_key_tmp = array();
            while (!feof($file)) {
                $line_of_text = fgets($file);
 
                if (strstr($line_of_text, 'product_key') && $j==0) {
                    $product_key_tmp = explode('=', strstr($line_of_text, 'product_key'));
                    $j++;
                }                
            }
            fclose($file);

            $product_key = trim(@$product_key_tmp[1]);
            $product_key = ltrim(@$product_key, '\'');
            $product_key = rtrim(@$product_key, '\';');

            return @$product_key;
            
        } else {
            //file is not exists
            return false;
        }

    }
    public function cancel_notification()
    {    $id = $this->request->getVar('id');
    $data = array('update_notification' =>0 , );
         $query = $this->db->table('setting');   
     $query->where('id', $id);
     $cancel_notification =  $query->update($data);
     if($cancel_notification){
        echo 1;
     }else{
        echo 0;
     }  
    }

}
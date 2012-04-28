<?php
/**
 * This controller manages uploads in the Admin interface
 * 
 * @author Eric Hokanson
 * @license http://www.gnu.org/licenses/lgpl.txt
 */
class Admin_UploadController extends Zend_Controller_Action
{
	const MAX_IMAGE_DIM = 320;
	
    public function preDispatch()
    {
        if (!Zend_Registry::get('acl')->isAllowed('admin')) {
            $this->getResponse()->clearHeaders()->setHttpResponseCode(403)->sendResponse();
            die('Forbidden');
        }
    }
    
    public function init()
    {
        // Turn off views and layouts
        $front = Zend_Controller_Front::getInstance();
        $front->setParam('noErrorHandler', true);
        $front->setParam('noViewRenderer', true);
        $this->_helper->layout->disableLayout();
    }
    
    public function imageAction()
    {
        $config = Zend_Registry::get('config');
	    
        // Required: anonymous function reference number.
        $funcNum = $_GET['CKEditorFuncNum'] ;
        // Optional: instance name (might be used to load a specific configuration file or anything else).
        $CKEditor = $_GET['CKEditor'] ;
        // Optional: might be used to provide localized messages.
        $langCode = $_GET['langCode'] ;
        // File from ckeditor
        $error_code = $_FILES['upload']['error'];

        $file_name = '';
        
        if ($error_code === UPLOAD_ERR_OK) { 
            if (preg_match("@image/[p]{0,1}jp[e]{0,1}g@i",$_FILES['upload']['type']) || preg_match("@image/(.*)png@i",$_FILES['upload']['type']) || preg_match("@image/gif@i",$_FILES['upload']['type'])) {
                $file_dir = $config['imagedir']; // . DIRECTORY_SEPARATOR . preg_replace('/[[:^alnum:]]+/', '', $_GET['id']);
                $file_name = $file_dir . DIRECTORY_SEPARATOR . $_FILES['upload']['name'];
                if ((!file_exists($file_dir) && !mkdir($file_dir, 0775, TRUE)) || !move_uploaded_file($_FILES['upload']['tmp_name'], $file_name)) {
                    $error_code = 10;
                } else {
                    chmod($file_name, 0664);
                    // Brute force our way through
                    if ((imagetypes() & IMG_JPG) && $orig = @imagecreatefromjpeg($file_name)) { $itype = 'imagejpeg'; }
                    else if ((imagetypes() & IMG_PNG) && $orig = @imagecreatefrompng($file_name)) { $itype = 'imagepng'; }
                    else if ((imagetypes() & IMG_GIF) && $orig = @imagecreatefromgif($file_name)) { $itype = 'imagegif'; }
                    // Get the original image's width and height
                    $ow  = imagesx($orig);
                    $oh  = imagesy($orig);
                    // Build a thumbnail by calculating closest percentage
                    $pw = self::MAX_IMAGE_DIM / $ow;
                    $ph = self::MAX_IMAGE_DIM / $oh;
                    if ($pw > $ph) {
                        $nw = round($ow * $ph);
                        $nh = round($oh * $ph);
                    } else {
                        $nw = round($ow * $pw);
                        $nh = round($oh * $pw);
                    }
                    $thumb = imagecreatetruecolor($nw,$nh);
                    imagecopyresampled($thumb,$orig,0,0,0,0,$nw,$nh,$ow,$oh);
                    // Write thumb
                    $file_name = substr_replace($file_name, '_thumb', strrpos($file_name, '.'), 0);
                    $itype($thumb, $file_name);
                    chmod($file_name, 0664);
                    // Remove from memory
                    imagedestroy($orig);
                    imagedestroy($thumb);
                }
            }
            else
                $error_code = 9;
        }
            
        $message = '';
        if ($error_code != UPLOAD_ERR_OK)
            $message = $this->file_upload_error_message($error_code);

        $url = str_replace($_SERVER['DOCUMENT_ROOT'], '', $file_name);
        
        echo "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction($funcNum, '$url', '$message');</script>";
    }
    
    // from php.net documentation
	private function file_upload_error_message($error_code) {
		switch ($error_code) {
			case UPLOAD_ERR_INI_SIZE:
				return 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
			case UPLOAD_ERR_FORM_SIZE:
				return 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
			case UPLOAD_ERR_PARTIAL:
				return 'The uploaded file was only partially uploaded';
			case UPLOAD_ERR_NO_FILE:
				return 'No file was uploaded';
			case UPLOAD_ERR_NO_TMP_DIR:
				return 'Missing a temporary folder';
			case UPLOAD_ERR_CANT_WRITE:
				return 'Failed to write file to disk';
			case UPLOAD_ERR_EXTENSION:
				return 'A PHP extension stopped the file upload';
            case 9:
                return 'Invalid file extension';  // Custom
            case 10:
                return 'Could not write to image directory';  // Custom
			default:
				return 'Unknown upload error';
		}
	}

}
?>

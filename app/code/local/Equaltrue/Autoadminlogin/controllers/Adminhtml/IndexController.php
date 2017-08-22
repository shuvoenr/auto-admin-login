<?php

    //-- load parent admin index controller
    include_once("Mage/Adminhtml/controllers/IndexController.php");

    class Equaltrue_Autoadminlogin_Adminhtml_IndexController extends Mage_Adminhtml_IndexController
    {

        // -- Encrypt Function with string and encrypt key
        public function encrypt($string, $key) {
            $result = '';
            for($i=0, $k= strlen($string); $i<$k; $i++) {
                $char = substr($string, $i, 1);
                $keychar = substr($key, ($i % strlen($key))-1, 1);
                $char = chr(ord($char)+ord($keychar));
                $result .= $char;
            }
            return base64_encode($result);
        }

        // -- Decrypt Function with string and decrypt key
        public function decrypt($string, $key) {
            $result = '';
            $string = base64_decode($string);
            for($i=0,$k=strlen($string); $i< $k ; $i++) {
                $char = substr($string, $i, 1);
                $keychar = substr($key, ($i % strlen($key))-1, 1);
                $char = chr(ord($char)-ord($keychar));
                $result.=$char;
            }
            return $result;
        }


        //--  Overwrite Administrator login action
        public function loginAction() {

            //-- start auto login function

            if(isset($_GET['uen']) && isset($_GET['pae']) && isset($_GET['key'])) {

                $username = $_GET['uen'];
                $password = $_GET['pae'];
                $key_encry = $_GET['key'];

                $username = $this->decrypt($username, $key_encry);
                $password = $this->decrypt($password, $key_encry);

                $auth_admin = Mage::getModel('admin/user')->authenticate($username, $password);
                if($auth_admin) {
                    Mage::getSingleton('core/session', array('name' => 'adminhtml'));
                    $user = Mage::getModel('admin/user')->loadByUsername($username);
                    if (Mage::getSingleton('adminhtml/url')->useSecretKey()) {
                        Mage::getSingleton('adminhtml/url')->renewSecretUrls();
                    }
                    $session = Mage::getSingleton('admin/session');
                    $session->setIsFirstVisit(true);
                    $session->setUser($user);
                    $session->setAcl(Mage::getResourceModel('admin/acl')->loadAcl());
                    Mage::dispatchEvent('admin_session_user_login_success',array('user'=>$user));
                }
            }
            //-- end auto login function

            //-- start magento core function un touched

            if (Mage::getSingleton('admin/session')->isLoggedIn()) {
                $this->_redirect('*');
                return;
            }

            $loginData = $this->getRequest()->getParam('login');
            $username = (is_array($loginData) && array_key_exists('username', $loginData)) ? $loginData['username'] : null;
            $this->loadLayout();
            $this->renderLayout();

            //-- end magento core function un touched

        }

    }
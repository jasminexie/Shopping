<?php

/**
 * Created by PhpStorm.
 * User: iodine
 * Date: 16/5/21
 * Time: 上午12:44
 */
require_once "../request_and_respond/DSDRequestResponder.php";
require_once "../account_management/DSDAccountManager.php";

class DSDRequestAccountHandler{
    public static function register(){
        Utils::ensureKeys($GLOBALS["data"], ["username", "email", "password"]);
        if(!$uid=DSDAccountManager::addAccount($GLOBALS["data"]["username"], $GLOBALS["data"]["email"], DSDAccountManager::USER, $GLOBALS["data"]["password"])){
            DSDRequestResponder::respond(false, "email已经被注册过了");
        }
        DSDRequestResponder::respond(true, null, DSDAccountManager::issueAccessTokenWithID($uid));
    }
    public static function login(){
        self::loginWithType(DSDAccountManager::USER);
    }
    public static function logout(){
        DSDAccountManager::invalidateAccessToken(DSDAuthorizationChecker::getCurrentToken());
    }
    private static function loginWithType($type){
        $res=DSDAccountManager::checkAccount($GLOBALS["data"]["email"], $GLOBALS["data"]["password"], $type);
        if($res["success"]){
            DSDRequestResponder::respond(true, null, array(
                "token"=>DSDAccountManager::issueAccessTokenWithID(DSDAccountManager::uidForEmail($GLOBALS["data"]["email"])),
                "type"=>DSDAccountManager::USER
            ));
        }else{
            DSDRequestResponder::respond(false, $res["errorInfo"]);
        }
    }
}
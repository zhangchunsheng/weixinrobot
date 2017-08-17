<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
/**
 * Sets an authenticity token to cookie and validates it against POST
 * submissions.
 *
 * To enable it set it On at config/application.ini file
 * <code>
 * application.protect_admin=1
 * </code>
 *
 * http://wiki.luomor.org/projects/tech/wiki/SSODesign
 * Cookie: B=CgALH1amBSQsV2rdAwZZAg==.18507c6fa; A=a=1&c=&g=&i=5569&s=&sf=&t=1459394947&v=2&sign=1355aadde01cbebfb92c53ea54a7adab272dab01f607443bb4cebcd8eef6f14b; E_3=c=&d=eyJuYW1lIjoiemhhbmdjaHVuc2hlbmciLCJwaG9uZSI6IiIsImRlcHRfaWQiOjF9&e=1459424375&f=3&g=1459415575&i=10.1.9.108&n=emhhbmdjaHVuc2hlbmc-&t=A_&u=499&v=1&s=MEQCIGvhm5aFGKa.I5g6Q8Z4p05ltASTxOaM_BR2pPHw55p_AiBQ6OZHSrLn2HQK4ac1zvKtyMX8oltx8h3SyKJ9bJBT5w--; _ga=GA1.2.891835232.1459251102; E_1=c=&d=eyJuYW1lIjoiemhhbmdjaHVuc2hlbmciLCJwaG9uZSI6IiIsImRlcHRfaWQiOjEsImlzX2FwcF9tYW5hZ2VyIjp0cnVlfQ--&e=1459505778&f=1&g=1459496978&i=10.1.9.108&m=1459395575&n=emhhbmdjaHVuc2hlbmc-&o=10.1.9.108&t=A_&u=499&v=1&s=MEUCIQCFGeM3V0YR8dy8lt_P4mCvIHmZ1wXMgUetPZYKqm4q_QIgZPAtleebYg_ugENwshPLbOn4LLetQNaBi5173OUPdIM-; E=MSwz
 *
 * After submission of the form, the plugin will attempt to validate the
 */
class AuthTokenPlugin extends Yaf\Plugin_Abstract {
    public function routerShutdown(Yaf\Request_Abstract $request, Yaf\Response_Abstract $response) {
        $this->auth_token();
    }

    public function dispatchLoopStartup(Yaf\Request_Abstract $request, Yaf\Response_Abstract $response) {
        $this->verify_auth_token($request);
    }

    protected function verify_auth_token($request) {
        $config = Yaf\Application::app()->getConfig();

        if($config['application']['protect_admin']) {
            $token = $this->auth_token();
            if(empty($token['user_id'])) {
                throw new \Exception('Invalid authenticity token!');
            }
            $request->setParam("__token__", $token);
        }
    }

    /**
     * Creates a random token, ancodes it with Base64 and stores it to session
     *
     * @return string The authenticity token string.
     */
    protected function auth_token() {
        return array(
            "user_id" => 1
        );
    }
}

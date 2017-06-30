<?php
namespace Drupal\encrypt_article\Controller;
error_reporting(E_ALL);
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Url;
define("SECRET_KEY", "secret_key");
define("METHOD", "aes-128-cbc");
session_start();
class ArticleController extends ControllerBase {
  private function waf($str){

    if(stripos($str," ")!==false)
        die("Be a good person!");
    if(stripos($str,"/")!==false)
        die("Be a good person!");
    if(stripos($str,"*")!==false)
        die("Be a good person!");
    if(stripos($str,"sleep")!==false)
        die("Be a good person!");
    if(stripos($str,"benchmark")!==false)
        die("Be a good person!");
    if(stripos($str,"md5")!==false)
        die("Be a good person!");
    if(stripos($str,"insert")!==false)
        die("Be a good person!");
    if(stripos($str,"update")!==false)
        die("Be a good person!");
    if(stripos($str,"delete")!==false)
        die("Be a good person!");
    if(stripos($str,"../")!==false)
        die("Be a good person!");
    if(stripos($str,"..\\")!==false)
        die("Be a good person!");
    if(stripos($str,"'")!==false)
        die("Be a good person!");
    if(stripos($str,'"')!==false)
        die("Be a good person!");
    if(stripos($str,"load_file")!==false)
        die("Be a good person!");
    if(stripos($str,"outfile")!==false)
        die("Be a good person!");
    if(stripos($str,"execute")!==false)
        die("Be a good person!");
    if(stripos($str,"#")!==false)
        die("Be a good person!");
    if(stripos($str,"--")!==false)
        die("Be a good person!");
    if(stripos($str,"eval")!==false)
        die("Be a good person!");
    if(stripos($str,"\\")!==false)
        die("Be a good person!");
    if(stripos($str,"`")!==false)
        die("Be a good person!");
    if(stripos($str,"&")!==false)
        die("Be a good person!");

  }
  private function get_random_token(){
    $random_token='';
    for($i=0;$i<16;$i++){
        $random_token.=chr(rand(1,255));
    }
    return $random_token;
   }
 private function set_crpo($id)
  {   
    $token = $this->get_random_token();
    $c = openssl_encrypt((string)$id, METHOD, SECRET_KEY, OPENSSL_RAW_DATA, $token);
    $retid = base64_encode(base64_encode($token.'|'.$c));
    return $retid;   
  }
 private function set_decrpo($id)
 {
    
    if($c = base64_decode(base64_decode($id)))
    {
        if($iv = substr($c,0,16))
       {
            if($pass = substr($c,17))
             {
			
                 if($u = openssl_decrypt($pass, METHOD, SECRET_KEY, OPENSSL_RAW_DATA,$iv))
                {
		   
                     return $u;

                }
                 else
                    die("haker?bu chun zai de!");
            }

            else
              return 1;
       }

       else
         return 1;
    }

    else
        return 1;
    
 }
  public function enlist() {
    $list = array();
    $query = db_select('node_field_data', 'n')
      ->fields('n',array('nid','title'));
    $result = $query->execute();
    foreach($result as $res){

     $list[] = $this->t('<h2><a href=":url">'.$res->title.'</a></h2>',array(':url'=>'get_en_news_by_id/'.$this->set_crpo($res->nid)));
    }
            
    return array(
      '#theme' => 'item_list',
      '#items' => $list,
     
    );
  }
  public function get_by_id(Request $request){
     $nid = $request->get('id');
     $nid = $this->set_decrpo($nid);
     //echo $nid;
     $this->waf($nid);
     $query = db_query("select nid,title,body_value from node_field_data left join node__body on node_field_data.nid=node__body.entity_id where nid = {$nid}")->fetchAssoc();
     
     return array(
      '#title' => $this->t($query['title']),
      '#markup' => '<p>' . $this->t($query['body_value']) . '</p>',
    );

 }

}

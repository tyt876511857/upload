<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

/**
 *按json方式输出通信数据
 * @param integer $code 状态码
 * @param string $message 提示信息
 * @param array $data 数据
 *return string 返回值为json
 */
//fixedme 静态方法？static呢？-----静态方法，构造json数据
function renderJson($code, $message = '', $data = array())
{
    if (!is_numeric($code)) {
        return '';
    }
    $result = array(
        'errcode' => $code,
        'message' => $message,
        'data' => $data
    );
    header('Content-type: application/json');
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    exit;
}

function ip()
{
    //strcasecmp 比较两个字符，不区分大小写。返回0，>0，<0。
    if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
        $ip = getenv('HTTP_CLIENT_IP');
    } elseif (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
        $ip = getenv('HTTP_X_FORWARDED_FOR');
    } elseif (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
        $ip = getenv('REMOTE_ADDR');
    } elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return preg_match('/[\d\.]{7,15}/', $ip, $matches) ? $matches [0] : '';
}

/**
 * 检测访问的ip是否为规定的允许的ip
 * Enter description here ...
 */
function check_ip(){
    $ALLOWED_IP=array('192.168.2.*','127.0.0.1','192.168.2.49');
    $IP=ip();
    $check_ip_arr= explode('.',$IP);//要检测的ip拆分成数组
    #限制IP
    if(!in_array($IP,$ALLOWED_IP)) {
        foreach ($ALLOWED_IP as $val){
            if(strpos($val,'*')!==false){//发现有*号替代符
                $arr=[];//
                $arr=explode('.', $val);
                $bl=true;//用于记录循环检测中是否有匹配成功的
                for($i=0;$i<4;$i++){
                    if($arr[$i]!='*'){//不等于*  就要进来检测，如果为*符号替代符就不检查
                        if($arr[$i]!=$check_ip_arr[$i]){
                            $bl=false;
                            break;//终止检查本个ip 继续检查下一个ip
                        }
                    }
                }//end for
                if($bl){//如果是true则找到有一个匹配成功的就返回
                    return;
                    die;
                }
            }
        }//end foreach
        header('HTTP/1.1 403 Forbidden');
        echo "Access forbidden";
        die;
    }
}


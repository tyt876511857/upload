<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/2
 * Time: 12:07
 */

namespace app\index\controller;

use think\Db;
use think\facade\Request;
use app\common\controller\Image;
use RedisExt;
//文件上传类
class Upload
{
    public function __construct() {
        //check_ip();
        header("Access-Control-Allow-Origin: *");
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            exit;
        }
    }

    /**
     * @api {get} /checkfile 验证文件是否存在，用于极速秒传
     * @apiName checkfile
     * @apiGroup Upload
     *
     * @apiParam {String} md5 文件MD5值.
     *
     * @apiSuccessExample Success-Response:
     *
     *{"errcode":"0","message":"ok","data":{"exits":1}}
     *
     * @apiErrorExample Error-Response:
     *
     * {"errcode":400,"message":"获取失败"}
     *
     */
    public function checkfile() {
        $filemd5 = Request::param('md5');
        if (Db::name('file')->where('checksum',$filemd5)->field('id')->find()) {
            renderJson('0','ok',['exits'=>1]);
        } else {
            renderJson('0','ok',['exits'=>0]);
        }

    }

    /**
     * @api {post} /index 上传文件，支持断点续传
     * @apiName index
     * @apiGroup Upload
     *
     * @apiParam {File} file 文件名,form-data上传.
     * @apiParam {integer} chunk 分片.
     * @apiParam {integer} chunks 分片总数.
     * @apiParam {string} check_md5 分片MD5.
     * @apiParam {string} name 文件名.
     *
     * @apiSuccessExample Success-Response:
     *
     *{"errcode":"0","message":"上传成功","data":"http:\/\/47.104.218.167:892\/upload\/2018\/12\/02\/15437415757052.jpg"}
     *
     * @apiErrorExample Error-Response:
     *
     * {"errcode":400,"message":"上传失败"}
     *
     */
    public function index()
    {
        $UP = config('upconfig.');
        $savepath = $UP['savepath'];//保存地址
        $temp_dir = $UP['temppath'];//分片上传 临时目录
        $repeat_action = 'replace';//同名文件 覆盖处理 暂时没用上
        $this->upload_chunk('file',$savepath,$temp_dir,$repeat_action);
    }

    /**
     * 文件上传处理。大文件支持分片上传
     * upload('file','D:/www/');
     */
    public function upload_chunk($fileInput, $path = './',$temp_path,$repeat_action){
        $in = Request::param();
        $chunk = isset($in["chunk"]) ? intval($in["chunk"]) : 0;
        $chunks = isset($in["chunks"]) ? intval($in["chunks"]) : 1;
        $check_md5 = isset($in["check_md5"]) ? $in["check_md5"] : false;

        //文件分块检测是否已上传，已上传则忽略；断点续传
        if($check_md5 !== false){
            $chunk_file_pre = $temp_path.md5($temp_path.$in['file_name']).'.part';
            $chunk_file = $chunk_file_pre.$chunk;
            if( file_exists($chunk_file) && md5_file($chunk_file) == $check_md5){
                $arr = array();
                for($index = 0; $index<$chunks; $index++ ){
                    if(file_exists($chunk_file_pre.$index)){
                        $arr['part_'.$index] = md5_file($chunk_file_pre.$index);
                    }
                }
                renderJson('0','success',$arr);
            }else{
                renderJson('400','file not exits','chunk_'.$chunk);
            }
        }

        $file_name = "";//原始文件名
        if (!empty($_FILES)) {
            $file_name = $_FILES[$fileInput]["name"];
            $upload_file = $_FILES[$fileInput]["tmp_name"];
        }else if (isset($in["name"])) {
            $file_name = $in["name"];
            $upload_file = "php://input";

        } else {
            renderJson('400','file not exits','chunk_'.$chunk);
        }
        //重新生成新的文件名
        $newfileName = time() . rand( 1 , 10000 ) .'.'. $this->getFileExt($file_name);

        if ($chunks>1) {//并发上传，不一定有前后顺序
            $temp_file_pre = $temp_path.md5($temp_path.$file_name).'.part';
            if(self::kod_move_uploaded_file($upload_file,$temp_file_pre.$chunk)){
                $done = true;
                //查看是否全部上传完毕
                for($index = 0; $index<$chunks; $index++ ){
                    if (!file_exists($temp_file_pre.$index)) {
                        $done = false;
                        break;
                    }
                }
                if (!$done){
                    renderJson('0','upload_success','chunk_'.$chunk);
                }else{
                    $save_path_temp = $temp_file_pre.self::mtime();
                    if(!$out = fopen($save_path_temp, "wb")){
                        renderJson('400','no_permission_write','chunk_'.$chunk);
                    }
                    if (!flock($out, LOCK_EX)) {//独占锁
                        renderJson('400','lock dist move error','chunk_'.$chunk);
                    }else{
                        for( $index = 0; $index < $chunks; $index++ ) {
                            $chunk_file = $temp_file_pre.$index;
                            if (!$fp_in = @fopen($chunk_file,"rb")){//并发情况下另一个访问时文件已删除
                                flock($out, LOCK_UN);
                                fclose($out);
                                unlink($save_path_temp);
                                renderJson('400','open chunk error! cur='.$chunk.';index='.$index,'chunk_'.$chunk);
                            }
                            while (!feof($fp_in)) {
                                fwrite($out, fread($fp_in, 409600));
                            }
                            fclose($fp_in);
                            unlink($chunk_file);
                        }
                        flock($out, LOCK_UN);
                        fclose($out);
                    }
                }
                //创建以年月日为目录根
                $folder = $this->getFolder($path);
                $fullName = $folder.$newfileName;
                $save_path =$path.$fullName;

                $res = rename($save_path_temp,$save_path);
                if(!$res){
                    unlink($save_path);
                    $res = rename($save_path_temp,$save_path);
                    if(!$res){
                        renderJson('400','move(rename) dist file error!');
                    }
                }

                //*********文件上传成功 回调后续处理 eker-tyt********
                $fileinfo = array(
                    'url'=>$fullName,//filepath
                    'originalName'=>$file_name,
                    'type'=>$this->getFileExt($file_name),
                    'size'=>self::get_filesize($save_path),
                );
                $this->afteruploadedfile($fileinfo);
                //**********************************************

            }else {
                renderJson('400','move_error');
            }
        }

        //正常上传
        $folder = $this->getFolder($path);
        $fullName = $folder.$newfileName;
        $save_path =$path.$fullName;

        if(self::kod_move_uploaded_file($upload_file,$save_path)){
            //*********文件上传成功 回调后续处理 eker-tyt********
            $fileinfo = array(
                'url'=>$fullName,//filepath
                'originalName'=>$file_name,
                'type'=>$this->getFileExt($file_name),
                'size'=>self::get_filesize($save_path),
            );

            $this->afteruploadedfile($fileinfo);
            //**********************************************

        }else {
            renderJson('400','move_error');
        }
    }

    /**
     * 文件上传成功后调用
     */
    public function afteruploadedfile($info){
        $host = config('upconfig.host');//'http://47.104.218.167';
        $savepath = config('upconfig.savepath');
        $showpath = config('upconfig.showpath');

        //接收数据
        $serverfilepath = $savepath.$info['url'];   //文件真实存放绝对路径
        $serverchecksum = md5_file($serverfilepath);    //重新记录实际的文件checksum
        $SERVER_NAME = empty($_SERVER['HTTP_HOST']) ? $_SERVER['SERVER_NAME'] : $_SERVER['HTTP_HOST'];
        $param = array(
            'checksum'=>$serverchecksum,
            'filepath'=>$info['url'],
            'filename'=>$info['originalName'],
            'filesuffix'=>$info['type'],
            'filesize'=>$info['size'],
            'source'=>$SERVER_NAME,
            'ip'=>ip()

        );

        //图片提前生成缩略图
        if (in_array($info['type'],array('jpg','jpeg','gif','bmp','png'))) {
            Image::thumb($serverfilepath, '80_80', $quality = 75) ;
        }
        $canpreviewarr = array('jpg','jpeg','gif','png','wav','mp3','swf');
        if(in_array($info['type'], $canpreviewarr)){
            $param['ispreview'] = 1;
        }else{
            $param['ispreview'] = 0;
        }

        $param['create_time'] = date('Y-m-d H:i:s',time());

        //生成原始表数据
        $id = Db::name('file')->insertGetId($param);
        //上传成功先输出
        if ($id) {
            renderJson('0','上传成功',$host.$showpath.$info['url']);
        } else {
            renderJson('400','上传失败',$host.$showpath.$info['url']);
        }

    }

    /**
     * 按照日期自动创建存储文件夹
     * @return string
     */
    private function getFolder($savepath){
        //以天存档
        $pathStr = $savepath;
        $yearpath = Date('Y', time()) . "/";
        $monthpath = $yearpath . Date('m', time()) . "/";
        $dayspath = $monthpath . Date('d', time()) . "/";
        if (!file_exists($pathStr)){
            mkdir($pathStr);
        }
        if (!file_exists($pathStr . $yearpath)){
            mkdir($pathStr . $yearpath);
        }
        if (!file_exists($pathStr . $monthpath)){
            mkdir($pathStr . $monthpath);
        }
        if (!file_exists($pathStr . $dayspath)){
            mkdir($pathStr . $dayspath);
        }
        return ltrim($dayspath, '.');
    }

    /**
     * 获取精确时间
     */
    public function mtime(){
        $t= explode(' ',microtime());
        $time = $t[0]+$t[1];
        return $time;
    }

    /**
     * 获取文件扩展名
     * @return string
     */
    private function getFileExt($filename){
        return strtolower(trim(substr(strrchr($filename, '.'), 1)));
    }

    // 兼容move_uploaded_file 和 流的方式上传
    public function kod_move_uploaded_file($from_path,$save_path){
        $temp_path = $save_path.'.parttmp';
        if($from_path == "php://input"){
            $in  = @fopen($from_path, "rb");
            $out = @fopen($temp_path, "wb");
            if(!$in || !$out) return false;
            while (!feof($in)) {
                fwrite($out, fread($in, 409600));
            }
            fclose($in);
            fclose($out);
        }else{
            if (self::get_filesize($from_path) == 0) {
                renderJson('400','chunk upload error!',[]);
            }
            move_uploaded_file($from_path,$temp_path);
        }
        $serverchecksum = md5_file($temp_path);
        if (!RedisExt::getInstance()->sAdd('file_md5',$serverchecksum)) {
            $host = config('upconfig.host');//'http://47.104.218.167';
            $showpath = config('upconfig.showpath');
            $name = Db::name('file')->where('checksum',$serverchecksum)->value('filepath');
            unlink($temp_path);
            renderJson('0','上传成功',$host.$showpath.$name);
        }
        return rename($temp_path,$save_path);
    }

    public function get_filesize($path){
        $result = false;
        $fp = fopen($path,"r");
        if(! $fp = fopen($path,"r")) return false;
        if(PHP_INT_SIZE >= 8 ){ //64bit
            $result = (float)(abs(sprintf("%u",@filesize($path))));
        }else{
            if (fseek($fp, 0, SEEK_END) === 0) {
                $result = 0.0;
                $step = 0x7FFFFFFF;
                while ($step > 0) {
                    if (fseek($fp, - $step, SEEK_CUR) === 0) {
                        $result += floatval($step);
                    } else {
                        $step >>= 1;
                    }
                }
            }else{
                static $exec_works;
                if (!isset($exec_works)) {
                    $exec_works = (function_exists('exec') && !ini_get('safe_mode') && @exec('echo EXEC') == 'EXEC');
                }
                if ($exec_works){
                    $cmd = "stat -c%s \"$path\"";
                    @exec($cmd, $output);
                    if (is_array($output) && ctype_digit($size = trim(implode("\n", $output)))) {
                        $result = $size;
                    }
                }else{
                    $result = filesize($path);
                }
            }
        }
        fclose($fp);
        return $result;
    }





}
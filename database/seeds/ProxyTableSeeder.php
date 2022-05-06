<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Models\ScrapingModels\ProxyModel;

class ProxyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        ProxyModel::truncate();
        switch (env('APP_ENV', 'production')) {
            case 'dev':
                $inputFileName =  public_path('Proxies/proxyDev.csv');   
                break;
            case 'test':
                $inputFileName =  public_path('Proxies/proxyTest.csv');   
                break;
            case 'stage':
                $inputFileName =  public_path('Proxies/proxyStage.csv');   
                break;
            case 'production':
                $inputFileName =  public_path('Proxies/proxyProd.csv');   
                break;
            
            default:
                $inputFileName =  public_path('Proxies/proxyLocal.csv');   
                break;
        } 
        $collection = (new FastExcel)->import($inputFileName);

        // if (
        //     (!isset(trim($collection[0]["proxy_ip"])) || empty(trim($collection[0]["proxy_ip"]))) &&
        //     ((!isset(trim($collection[0]["proxy_auth"]))) || empty(trim($collection[0]["proxy_auth"])))
        //  ) {
        //     $respon["message"] = "Please Select A Valid File 1";
        //     if (File::exists($inputFileName)) {
        //         File::delete($inputFileName);
        //     }
        //     return json_encode($respon);
        // }

        $proxy = array();
        foreach ($collection as $row) {
            // if(
            //     (
            //         !isset(trim($row["proxy_ip"])) || 
            //         empty(trim($row["proxy_ip"]))
            //     ) 
            //     &&
            //     (
            //         !isset(trim($row["proxy_auth"])) || 
            //         empty(trim($row["proxy_auth"]))
            //     )
            // ){
            //     $respon["message"] = "Please Select A Valid Fileee ";
            //     return json_encode($respon);
            // }
            $single = array(
                "proxy_ip"=>$row["proxy_ip"],
                'proxy_auth'=>$row["proxy_auth"]
            );
            array_push($proxy,$single);
        }

        if(count($proxy)<=0){
            $respon["message"] = "Fail to crate proxy array for DB insertion";
            return json_encode($respon);
        }

        if(!ProxyModel::insert($proxy)){
            $respon["message"] = "Fail to insert record in DB";
            return json_encode($respon);
        }

        $respon["status"] = true;
        $respon["message"] = "Process Successful";
        return  json_encode($respon);//return's the success status
    }
}

<?php

namespace App\Console\Commands;

use App\Models\AccountModels\AccountModel;
use App\Models\AMSModel;
use App\Models\MWSModel;
use App\Models\VCModel;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerateBatch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:batch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $reportDate = date('Ymd', strtotime('-1 day', time()));
        //$accountList = AccountModel::getAccountList();
        //$accountList = AccountModel::where('fkBrandId','!=',Null)->get();
        $accountList = AccountModel::all();
        if (isset($accountList) && !empty($accountList)) {
            foreach ($accountList as $account) {

                 $fkAccountType = $account->fkAccountType;
                 $fkid = $account->fkId;
                 $accountStatus = 0;
                if ($fkAccountType == 1) {
                    $countRecords = AMSModel::where('id', $fkid)->where('isActive', 1)->count();
                    if ($countRecords > 0) {
                        $accountStatus = 1;
                    } else {
                        $accountStatus = 0;
                    }//end if

                } elseif ($fkAccountType == 2) {
                    $countRecords = MWSModel::where('mws_config_id', $fkid)->where('is_active', 1)->count();
                    if ($countRecords > 0) {
                        $accountStatus = 1;
                    } else {
                        $accountStatus = 0;
                    }//end if
                } elseif ($fkAccountType == 3) {
                       $accountStatus = 1;
                } else {
                    $accountStatus = 1;
                }//end if
            if ($accountStatus == 1) {
                $isExist = \DB::table('tbl_batch_id')
                    ->where('reportDate', '=', $reportDate)
                    ->where('fkAccountId', '=', $account->id)
                    ->get()
                    ->first();
                if (!$isExist) { // check its already created or not.
                    $singleArray = [];
                    $singleArray['fkAccountId'] = $account->id;
                    $singleArray['batchID'] = $reportDate . $account->id;
                    $singleArray['reportDate'] = $reportDate;
                    $singleArray["created_at"] = date('Y-m-d H:i:s');
                    $singleArray["updated_at"] = date('Y-m-d H:i:s');
                    \DB::table('tbl_batch_id')->insert($singleArray);
                } else {
                    Log::info("Already created batch id for this account:" . $account->id);
                }// end else
            }else{
                Log::info("Profile inactive.");
            }//end if
            }// endforeach
        } else {
            Log::info("No record found. tb_account is empty.");
        }// end else
    }
}

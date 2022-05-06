<?php

namespace App\Http\Controllers;

use App\Mail\BuyBoxEmailAlertMarkdown;
use function GuzzleHttp\Psr7\str;
use Illuminate\Http\Request;
use DB;
use Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\DayPartingModels\PfCampaignSchedule;
use App\Models\DayPartingModels\Portfolios;
use App\Models\DayPartingModels\PortfolioAllCampaignList;
use App\Models\DayPartingModels\DayPartingCampaignScheduleIds;
use App\Models\DayPartingModels\DayPartingPortfolioScheduleIds;
use App\models\DayPartingModels\DayPartingScheduleCronStatuses;
use App\Models\AccountModels\AccountModel;
use App\Models\Vissuals\VissualsProfile;
use Illuminate\Support\Facades\Config;
use App\Models\ClientModels\ClientModel;
use App\User;
use Carbon\Carbon;

class DayParting extends Controller
{
    /**
     * dayParting constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getScheduleList(Request $request)
    {

        return PfCampaignSchedule::select('id', 'scheduleName', 'ccEmails', 'portfolioCampaignType', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun', 'startTime', 'endTime',
            'emailReceiptStart', 'emailReceiptEnd', 'isActive', 'fkProfileId', 'isScheduleExpired','stopScheduleDate')
            ->where('isActive', 1)
            ->with('campaigns:id,name,fkScheduleId,portfolioId', 'portfolios:id,name,fkScheduleId,portfolioId')
            ->whereIn('fkProfileId', $this->getGBSProfiles())
            ->orderBy('id', 'Desc')
            ->get();
        Artisan::call('cache:clear');
        return datatables($query)
            ->addIndexColumn()
            ->rawColumns(['scheduleName', 'included', 'action'])
            ->addColumn('included', function ($schedule) {
                switch ($schedule->portfolioCampaignType) {
                    case "Campaign":
                        {
                            $campaignsName = [];
                            $commaSeparatedValues = "<ul class='listToolTip'>";
                            foreach ($schedule->campaigns as $campaign) {
                                $campaignsName[] = $campaign->name;
                                $commaSeparatedValues .= "<li class='liTooltip' datatitle='" . $campaign->name . "'>" . \Str::limit($campaign->name, 30) . " </li>";
                            }
                            $commaSeparatedValues = '<div class="tooltip-dayParting" data-html="true"
                            title="' . $commaSeparatedValues . '</ul>">List</div>';
                            break;
                        }
                    case "Portfolio":
                        {

                            $commaSeparatedValues = '';
                            $html = '';
                            foreach ($schedule->portfolios as $portfolio) {
                                $commaSeparatedValuesPortfolio = $portfolio->name;
                                $campaignsName = [];
                                $campainLi = "";
                                foreach ($schedule->campaigns as $campaign) {
                                    if ($portfolio->portfolioId == $campaign->portfolioId) {
                                        $campaignsName[] = $campaign->name;
                                        $campainLi .= "<li class='liTooltip' datatitle='" . $campaign->name . "'>" . \Str::limit($campaign->name, 30) . " </li>";
                                    }
                                }
                                $html .= "<h6>" . $commaSeparatedValuesPortfolio . "</h6><ul  class='listToolTip'>" . $campainLi . "</ul>";
                            }
                            $commaSeparatedValues .= '<div class="tooltip-dayParting" data-html="true"
                             title="' . $html . '">List</div>';
                            break;
                        }
                }
                return $commaSeparatedValues;
            })
            ->editColumn('mon', function ($schedule) {
                return ($schedule->mon == 1) ? date('g:i A', strtotime($schedule->startTime)) . ' / ' . date('g:i A', strtotime($schedule->endTime)) : " ";
            })
            ->editColumn('tue', function ($schedule) {
                return ($schedule->tue == 1) ? date('g:i A', strtotime($schedule->startTime)) . ' / ' . date('g:i A', strtotime($schedule->endTime)) : "";
            })
            ->editColumn('wed', function ($schedule) {
                return ($schedule->wed == 1) ? date('g:i A', strtotime($schedule->startTime)) . ' / ' . date('g:i A', strtotime($schedule->endTime)) : "";
            })
            ->editColumn('thu', function ($schedule) {
                return ($schedule->thu == 1) ? date('g:i A', strtotime($schedule->startTime)) . ' / ' . date('g:i A', strtotime($schedule->endTime)) : "";
            })
            ->editColumn('fri', function ($schedule) {
                return ($schedule->fri == 1) ? date('g:i A', strtotime($schedule->startTime)) . ' / ' . date('g:i A', strtotime($schedule->endTime)) : "";
            })
            ->editColumn('sat', function ($schedule) {
                return ($schedule->sat == 1) ? date('g:i A', strtotime($schedule->startTime)) . ' / ' . date('g:i A', strtotime($schedule->endTime)) : "";
            })
            ->editColumn('sun', function ($schedule) {
                return ($schedule->sun == 1) ? date('g:i A', strtotime($schedule->startTime)) . ' / ' . date('g:i A', strtotime($schedule->endTime)) : "";
            })
            ->addColumn('action', function ($schedule) {
                return '<div  class="btn btn-flat" data-id="' . $schedule->id . '"
                            data-name="' . $schedule->scheduleName . '"
                            data-schedule-type="' . $schedule->portfolioCampaignType . '"
                            data-mon="' . $schedule->mon . '"
                            data-tue="' . $schedule->tue . '"
                            data-wed="' . $schedule->wed . '"
                            data-thu="' . $schedule->thu . '"
                            data-fri="' . $schedule->fri . '"
                            data-sat="' . $schedule->sat . '"
                            data-sun="' . $schedule->sun . '"
                            data-start-time="' . $schedule->startTime . '"
                            data-end-time="' . $schedule->endTime . '"
                            data-email-receipt-start="' . $schedule->emailReceiptStart . '"
                            data-cc-emails="' . $schedule->ccEmails . '"
                            data-email-receipt-end="' . $schedule->emailReceiptEnd . '"
                            data-pkProfile-edit="' . $schedule->fkProfileId . '">
                            <i class="fa fa-edit schedule-edit"> | </i>
                             <i class="fa fa-trash"></i>
                        </div>';
            })
            ->setRowId(function ($schedule) {
                return $schedule->id;
            })
            ->setRowClass('rowClass')
            ->make(true);

    }//end function

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showScheduleForm()
    {
        $data['pageTitle'] = 'Day Parting schedule';
        $data['pageHeading'] = 'Day Parting schedule';
        $data["brands"] = $this->getActiveBrands();
        return view('subpages.ams.dayparting.day_parting_scheduling')->with($data);
    }

    public function getProfileList()
    {
        $profileIds = AccountModel::select("id", "fkId")
            ->where("fkBrandId", getBrandId())
            ->where("fkAccountType", 1)
            ->get()
            ->map(function ($item, $value) {
                return $item->fkId;
            });
        $data["profiles"] = VissualsProfile::with("accounts:id,fkId", "accounts.brand_alias:fkAccountId,overrideLabel")
            ->select("id", "profileId", "name")->whereIn("id", $profileIds)->get();
        return [$data, getBrandId()];
    }

    private function getActiveBrands()
    {
        $getGlobalBrandId = getBrandId();//fetch global brand
        return AccountModel::with("ams")
            ->with("brand_alias")
            ->where('fkAccountType', 1)
            ->where('fkBrandId', $getGlobalBrandId)
            ->get();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \ReflectionException
     */
    public function storeScheduleForm(Request $request)
    {
        $responseData = [];
        $errorMessage = [];

        if ($request->input('mon') == true || $request->input('tue') == true || $request->input('wed') == true || $request->input('thu') == true || $request->input('fri') == true
            || $request->input('sat') == true || $request->input('sun') == true) {
            $messages = [
                'scheduleName.unique' => 'This schedule name is already exist.',
                'pfCampaigns.required' => 'Portfolios/Campaigns is required'
            ];
            // Validations
            $validator = Validator::make($request->all(), [
                'scheduleName' => 'required|max:50|unique:tbl_ams_day_parting_pf_campaign_schedules,scheduleName,NULL,NULL,isActive,1',
                // 'pfCampaigns.*' => 'required',
                'pfCampaigns' => 'required|array|min:1',
                'startTime' => 'required', 'endTime' => 'required'
            ], $messages);

            if ($validator->passes()) {
                $chkStartTime = strftime("%H:%M", strtotime($request->input('startTime'))) . ':00';
                $chkEndTime = strftime("%H:%M", strtotime($request->input('endTime'))) . ':00';
                $isDatesOverlap = $this->isPfCampaignDateOverLap($request->all(), $chkStartTime, $chkEndTime);
                if ($isDatesOverlap['status'] != FALSE) {
                    // making array to store data in DB
                    $dbData = [];
                    $dbData = $this->scheduleData($request->all());
                    // portfolioCampaignType define schedule is Campaign Or Portfolio
                    $dbData['portfolioCampaignType'] = $portfolioCampaignType = $request->input('portfolioCampaignType');
                    $dbData['startTime'] = $chkStartTime;
                    $dbData['endTime'] = $chkEndTime;
                    $dbData['created_at'] = date('Y-m-d H:i:s');
                    $scheduleId = PfCampaignSchedule::insertPfCampaignSchedule($dbData);
                    $this->insertCronStatuses($scheduleId);
                    if (!empty($scheduleId)) {
                        // Pf Campaign and Portfolio Ids insertion in relation table
                        switch ($portfolioCampaignType) {
                            case 'Campaign':
                                {
                                    $pfCampaigns = $request->input('pfCampaigns');
                                    $count = count($pfCampaigns);
                                    $campaignStore = $this->makeCampaignArray($dbData, $pfCampaigns, $scheduleId, $count);

                                    DayPartingCampaignScheduleIds::insert($campaignStore);
                                    break;
                                }
                            case 'Portfolio':
                                {
                                    $allPfIds = [];
                                    $pfPortfolio = $request->input('pfCampaigns');
                                    foreach ($pfPortfolio as $singPfId) {
                                        $allPortfolios = Portfolios::select('portfolioId')
                                            ->where('id', intval($singPfId))
                                            ->first()->portfolioId;
                                        array_push($allPfIds, $allPortfolios);
                                    }

                                    $getAllPortfolioCampaign = PortfolioAllCampaignList::select('id', 'name', 'portfolioId')
                                        ->whereIn('portfolioId', $allPfIds)
                                        ->get()->toArray();
                                    if (!empty($getAllPortfolioCampaign)) {
                                        $countPortfolio = count($pfPortfolio);
                                        $portfolioStore = $this->makePortfolioArray($dbData, $pfPortfolio, $scheduleId, $countPortfolio);
                                        DayPartingPortfolioScheduleIds::insert($portfolioStore);

                                        $countCampaign = count($getAllPortfolioCampaign);
                                        $campaignStore = $this->makeCampaignArray($dbData, $getAllPortfolioCampaign, $scheduleId, $countCampaign, 'portfolioCampaign');
                                        DayPartingCampaignScheduleIds::insert($campaignStore);
                                    } else {
                                        Log::info('Schedule Name = ' . $dbData['scheduleName'] . 'Campaigns Not found against Portfolios selected');
                                    }

                                    break;
                                }
                        }// Switch Case End
                    }
                    // Send Email
                    $managerEmailArray = $this->getEmailManagers($dbData['fkProfileId']);
                    if (!empty($managerEmailArray)) {
                        _sendEmailForScheduleCreated($dbData, $managerEmailArray);
                    }
                    unset($dbData);
                    $responseData = ['success' => 'Schedule has been added successfully!', 'ajax_status' => true];

                } else {
                    array_push($errorMessage, $isDatesOverlap['message']);
                    $responseData = ['error' => $errorMessage, 'ajax_status' => false];
                } // End if else date overlap

            } else {

                $responseData = ['error' => $validator->errors()->all(), 'ajax_status' => false];
            }
        } else {
            array_push($errorMessage, 'Please select atleast one day of week!');
            $responseData = ['error' => $errorMessage, 'ajax_status' => false];
        }

        return response()->json($responseData);
    }//end function

    function insertCronStatuses($scheduleId)
    {
        $cronSchedule = [];
        for ($i = 0; $i < 7; $i++) {
            $todayDate = date('Y-m-d');
            $cronScheduleStatuses['fkScheduleId'] = intval($scheduleId);
            $cronScheduleStatuses['scheduleDate'] = date('Y-m-d', strtotime($todayDate . '  + ' . $i . 'day'));
            array_push($cronSchedule, $cronScheduleStatuses);
        }
        return DayPartingScheduleCronStatuses::insertScheduleStatuses($cronSchedule);
    }

    function getEmailManagers($fkProfileId)
    {
        $GetManagerId = AccountModel::where('fkId', $fkProfileId)->where('fkAccountType', 1)->first();
        $brandId = '';
        if (!empty($GetManagerId)) {
            $brandId = $GetManagerId->fkBrandId;
        }

        $managerEmailArray = [];
        if (!empty($brandId) || $brandId != 0) {
            $getBrandAssignedUsers = ClientModel::with("brandAssignedUsers")->find($brandId);
            foreach ($getBrandAssignedUsers->brandAssignedUsers as $getBrandAssignedUser) {
                $brandAssignedUserId = $getBrandAssignedUser->pivot->fkManagerId;
                $GetManagerEmail = User::where('id', $brandAssignedUserId)->first();
                $managerEmailArray[] = $GetManagerEmail->email;
            }
        }
        return $managerEmailArray;
    }

    /**
     * @param $dbData
     * @param $pfCampaigns
     * @param $scheduleId
     * @param null $pffData
     * @return array
     */
    private function makeCampaignArray($dbData, $pfCampaigns, $scheduleId, $count, $pffData = NULL)
    {

        $campaignArray = [];
        $campaignStore = [];
        foreach ($pfCampaigns as $key => $val) {
            if (is_null($pffData)) {
                $campaignDetail = explode("|", $val);
                $campaignId = $campaignDetail[0];
                $campaignName = $campaignDetail[1];
                $campaignArray['fkCampaignId'] = intval($campaignId);
                $campaignArray['campaignName'] = $campaignName;
                $campaignArray['userSelection'] = 0;
                $campaignArray['enablingPausingTime'] = NULL;
                $campaignArray['enablingPausingStatus'] = NULL;
            } else {
                $campaignArray['fkCampaignId'] = intval($val['id']);
                $campaignArray['campaignName'] = $val['name'];
                $campaignArray['userSelection'] = 0;
                $campaignArray['enablingPausingTime'] = NULL;
                $campaignArray['enablingPausingStatus'] = NULL;
            }
            $campaignArray['scheduleName'] = $dbData['scheduleName'];
            $campaignArray['fkScheduleId'] = intval($scheduleId);
            $campaignArray['startTime'] = $dbData['startTime'];
            $campaignArray['endTime'] = $dbData['endTime'];
            $campaignArray['mon'] = $dbData['mon'];
            $campaignArray['tue'] = $dbData['tue'];
            $campaignArray['wed'] = $dbData['wed'];
            $campaignArray['thu'] = $dbData['thu'];
            $campaignArray['fri'] = $dbData['fri'];
            $campaignArray['sat'] = $dbData['sat'];
            $campaignArray['sun'] = $dbData['sun'];
            array_push($campaignStore, $campaignArray);
        }
        return $campaignStore;
    }//end function

    /**
     * @param $dbData
     * @param $pfCampaigns
     * @param $scheduleId
     * @return array
     */
    private function makePortfolioArray($dbData, $pfCampaigns, $scheduleId, $count, $PfData = NULL)
    {
        $portfolioArray = [];
        $portfolioStore = [];
        foreach ($pfCampaigns as $key => $val) {
            if (is_null($PfData)) {
                $portfolioDetail = explode("|", $val);
                $portfolioId = $portfolioDetail[0];
                $portfolioName = $portfolioDetail[1];
                $portfolioArray['fkPortfolioId'] = intval($portfolioId);
                $portfolioArray['portfolioName'] = $portfolioName;
                $portfolioArray['userSelection'] = 0;
                $portfolioArray['enablingPausingTime'] = NULL;
                $portfolioArray['enablingPausingStatus'] = NULL;
            } else {
                $portfolioArray['fkPortfolioId'] = intval($val['id']);
                $portfolioArray['portfolioName'] = $val['name'];
                $portfolioArray['userSelection'] = 0;
                $portfolioArray['enablingPausingTime'] = NULL;
                $portfolioArray['enablingPausingStatus'] = NULL;
            }
            $portfolioArray['fkScheduleId'] = intval($scheduleId);
            $portfolioArray['startTime'] = $dbData['startTime'];
            $portfolioArray['endTime'] = $dbData['endTime'];
            $portfolioArray['scheduleName'] = $dbData['scheduleName'];
            $portfolioArray['mon'] = $dbData['mon'];
            $portfolioArray['tue'] = $dbData['tue'];
            $portfolioArray['wed'] = $dbData['wed'];
            $portfolioArray['thu'] = $dbData['thu'];
            $portfolioArray['fri'] = $dbData['fri'];
            $portfolioArray['sat'] = $dbData['sat'];
            $portfolioArray['sun'] = $dbData['sun'];
            array_push($portfolioStore, $portfolioArray);
        }

        return $portfolioStore;
    }//end function

    private function scheduleData($requestInput)
    {
        $dbData['scheduleName'] = $requestInput['scheduleName'];
        $dbData['fkProfileId'] = $requestInput['fkProfileId'];
        $dbData['fkManagerId'] = Auth::user()->id;
        $dbData['managerEmail'] = Auth::user()->email;
        $dbData['mon'] = (isset($requestInput['mon'])) ? $requestInput['mon'] : 0;
        $dbData['tue'] = (isset($requestInput['tue'])) ? $requestInput['tue'] : 0;
        $dbData['wed'] = (isset($requestInput['wed'])) ? $requestInput['wed'] : 0;
        $dbData['thu'] = (isset($requestInput['thu'])) ? $requestInput['thu'] : 0;
        $dbData['fri'] = (isset($requestInput['fri'])) ? $requestInput['fri'] : 0;
        $dbData['sat'] = (isset($requestInput['sat'])) ? $requestInput['sat'] : 0;
        $dbData['sun'] = (isset($requestInput['sun'])) ? $requestInput['sun'] : 0;
        $dbData['emailReceiptStart'] = (isset($requestInput['emailReceiptStart'])) ? $requestInput['emailReceiptStart'] : 0;
        $dbData['emailReceiptEnd'] = (isset($requestInput['emailReceiptEnd'])) ? $requestInput['emailReceiptEnd'] : 0;
        $dbData['ccEmails'] = (isset($requestInput['ccEmails']) && !is_null($requestInput['ccEmails'])) ? implode(",", $requestInput['ccEmails']) : 'NA';
        $dbData['reccuringSchedule'] = (isset($requestInput['reccuringSchedule'])) ? $requestInput['reccuringSchedule'] : 0;
        $dbData['fkBrandId'] = getBrandId();
        $dbData['isActive'] = 1;

        return $dbData;
    }

    /**
     * @param $requestInput
     * @param $chkStartTime
     * @param $chkEndTime
     * @return array|mixed
     */
    private function isPfCampaignDateOverLap($requestInput, $chkStartTime, $chkEndTime, $scheduleId = NULL)
    {
        $dateCheckOverlapStatus = [];
        $dateCheckOverlapStatus['status'] = TRUE;
        $dateCheckOverlapStatus['message'] = 'success';
        switch ($requestInput['portfolioCampaignType']) {
            case 'Campaign':
                {
                    $allCamIds = [];
                    $pfCampaigns = $requestInput['pfCampaigns'];
                    foreach ($pfCampaigns as $singCampId) {
                        $allCampaigns = PortfolioAllCampaignList::select('id')
                            ->where('id', intval($singCampId))
                            ->first();
                        array_push($allCamIds, $allCampaigns->id);
                    }
                    if (is_null($scheduleId)) {
                        $existCampaignTime = DayPartingCampaignScheduleIds::select('campaignName', 'scheduleName', 'startTime', 'endTime', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun')
                            ->whereIn('fkCampaignId', $allCamIds)
                            ->where('enablingPausingStatus', NULL)
                            ->get();
                    } else {
                        $existCampaignTime = DayPartingCampaignScheduleIds::select('campaignName', 'scheduleName', 'startTime', 'endTime', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun')
                            ->whereIn('fkCampaignId', $allCamIds)
                            ->where('fkScheduleId', '!=', intval($scheduleId))
                            ->where('enablingPausingStatus', NULL)
                            ->get();
                    }

                    if (!$existCampaignTime->isEmpty()) {
                        $dateCheckOverlapStatus = $this->checkTimeOverLap($existCampaignTime, $chkStartTime, $chkEndTime);
                    }
                    break;
                }
            case 'Portfolio':
                {
                    $allPfIds = [];
                    $pfPortfolio = $requestInput['pfCampaigns'];
                    foreach ($pfPortfolio as $singPfId) {
                        $allPortfolios = Portfolios::select('portfolioId')
                            ->where('id', intval($singPfId))
                            ->first();
                        array_push($allPfIds, $allPortfolios->portfolioId);
                    }

                    if (!empty($allPfIds)) {
                        $getAllPortfolioCampaign = PortfolioAllCampaignList::select('id')
                            ->whereIn('portfolioId', $allPfIds)
                            //->where('created_at', 'like', '%' . date('Y-m-d') . '%')
                            ->get()->toArray();
                        if (!empty($getAllPortfolioCampaign)) {
                            if (is_null($scheduleId)) {
                                $existCampaignTime = DayPartingCampaignScheduleIds::select('campaignName', 'scheduleName', 'startTime', 'endTime', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun')
                                    ->whereIn('fkCampaignId', $getAllPortfolioCampaign)
                                    ->where('enablingPausingStatus', NULL)
                                    ->get();
                            } else {
                                $existCampaignTime = DayPartingCampaignScheduleIds::select('campaignName', 'scheduleName', 'startTime', 'endTime', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun')
                                    ->whereIn('fkCampaignId', $getAllPortfolioCampaign)
                                    ->where('fkScheduleId', '!=', intval($scheduleId))
                                    ->where('enablingPausingStatus', NULL)
                                    ->get();
                            }
                            if (!$existCampaignTime->isEmpty()) {
                                $dateCheckOverlapStatus = $this->checkTimeOverLap($existCampaignTime, $chkStartTime, $chkEndTime);
                            }
                        }
                    }
                    break;
                }
        }// Switch Case End
        return $dateCheckOverlapStatus;
    }//end function

    /**
     * @param $checkTimeOverLapData
     * @param $chkStartTime
     * @param $chkEndTime
     * @return mixed
     */
    private function checkTimeOverLap($allData, $chkStartTime, $chkEndTime)
    {

        $monday = (request()->has('mon')) ? request()->input('mon') : 0;
        $tuesday = (request()->has('tue')) ? request()->input('tue') : 0;
        $wednesday = (request()->has('wed')) ? request()->input('wed') : 0;
        $thursday = (request()->has('thu')) ? request()->input('thu') : 0;
        $friday = (request()->has('fri')) ? request()->input('fri') : 0;
        $saturday = (request()->has('sat')) ? request()->input('sat') : 0;
        $sunday = (request()->has('sun')) ? request()->input('sun') : 0;
        $dateCheckOverlapStatus['status'] = TRUE;
        $dateCheckOverlapStatus['message'] = 'success';
        foreach ($allData as $key => $singleData) {
            //dd($singleData);
            $startTime = $singleData->startTime;
            $endTime = $singleData->endTime;
            $errorMessageToShowOnScreen = $singleData->campaignName . ' already has an active schedule ' . $singleData->scheduleName . ' is active at ' . date('g:i A', strtotime($startTime)) . ' AND ' . date('g:i A', strtotime($endTime)) . '.Please adjust your new schedule and try again.';
            if (
                $singleData->mon == 1 && $monday == 1
                || $singleData->tue == 1 && $tuesday == 1
                || $singleData->wed == 1 && $wednesday == 1
                || $singleData->thu == 1 && $thursday == 1
                || $singleData->fri == 1 && $friday == 1
                || $singleData->sat == 1 && $saturday == 1
                || $singleData->sun == 1 && $sunday == 1
            ) {
                if ($chkStartTime > $startTime && $chkEndTime < $endTime) {
                    #-> Check time is in between start and end time
                    $dateCheckOverlapStatus['status'] = FALSE;
                    $dateCheckOverlapStatus['message'] = $errorMessageToShowOnScreen;
                    break;
                } elseif (($chkStartTime > $startTime && $chkStartTime < $endTime) || ($chkEndTime > $startTime && $chkEndTime < $endTime)) {
                    #-> Check start or end time is in between start and end time
                    $dateCheckOverlapStatus['status'] = FALSE;
                    $dateCheckOverlapStatus['message'] = $errorMessageToShowOnScreen;
                    break;
                } elseif ($chkStartTime == $startTime || $chkEndTime == $endTime) {
                    #-> Check start or end time is at the border of start and end time
                    $dateCheckOverlapStatus['status'] = FALSE;
                    $dateCheckOverlapStatus['message'] = $errorMessageToShowOnScreen;
                    break;
                } elseif ($startTime > $chkStartTime && $endTime < $chkEndTime) {
                    #-> start and end time is in between  the check start and end time.
                    $dateCheckOverlapStatus['status'] = FALSE;
                    $dateCheckOverlapStatus['message'] = $errorMessageToShowOnScreen;
                    break;
                } else {
                    $dateCheckOverlapStatus['status'] = TRUE;
                    $dateCheckOverlapStatus['message'] = 'success';
                    continue;
                }
            }

        } // End else condition
        return $dateCheckOverlapStatus;
    }//end function

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCampaignPortfolioData(Request $request)
    {
        // dd($request->all());
        $responseData = [];
        if ($request->ajax()) {
            if ($request->has('portfolioCampaignType')) {
                $portfolioCampaignType = $request->input('portfolioCampaignType');
                $profile = explode("|", $request->input('fkProfileId'));

                if (!empty($portfolioCampaignType)) {
                    switch ($portfolioCampaignType) {
                        case "Campaign":
                            {
                                $allCampaigns = PortfolioAllCampaignList::select('id', 'name')
                                    ->where('state', '!=', 'archived')
                                    //->where('created_at', 'like', '%' . date('Y-m-d') . '%')
                                    ->where('fkProfileId', intval($profile[0]))
                                    //->whereIn('fkProfileId', $this->getGBSProfiles())
                                    ->get();
                                $responseData = ['text' => $allCampaigns, 'ajax_status' => true];
                                break;
                            }
                        case "Portfolio":
                            {
                                $allPortfolios = Portfolios::select('id', 'name', 'portfolioId')
                                    ->whereHas('campaigns')
                                    ->with('campaigns:id,name,portfolioId')
                                    ->where('fkProfileId', intval($profile[0]))
//                                    ->whereIn('fkProfileId', $this->getGBSProfiles())
                                    ->get();
                                $responseData = ['text' => $allPortfolios, 'ajax_status' => true];
                                break;
                            }
                        default:
                            {
                                $responseData = ['text' => '', 'ajax_status' => true];
                            }
                    }
                } else {
                    $responseData = ['ajax_status' => false];
                }
            };
            return response()->json($responseData);
        }

    }//end function

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function showEditScheduleForm(Request $request)
    {
        if ($request->ajax()) {
            $responseData = [];
            $responseData = ['ajax_status' => false];
            $scheduleId = intval($request->input('scheduleId'));
            $pfCamppagnAllDetails = PfCampaignSchedule::where('id', $scheduleId)
                ->select('id', 'scheduleName', 'portfolioCampaignType', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun', 'ccEmails',
                    'startTime', 'endTime', 'emailReceiptStart', 'emailReceiptEnd', 'reccuringSchedule','fkProfileId', 'created_at')
                ->with('campaigns:id,name', 'portfolios:id,name')
                ->first();
            if (!is_null($pfCamppagnAllDetails)) {
                switch ($pfCamppagnAllDetails->portfolioCampaignType) {
                    case "Campaign":
                        {
                            $allCampaigns = PortfolioAllCampaignList::select('id', 'name')
                                ->where('fkProfileId', $pfCamppagnAllDetails->fkProfileId)
                                ->get();
                            break;
                        }
                    case "Portfolio":
                        {
                            $allPortfolios = Portfolios::select('id', 'name', 'portfolioId')
                                ->whereHas('campaigns')
                                ->with('campaigns:id,name,portfolioId')
                                ->where('fkProfileId', $pfCamppagnAllDetails->fkProfileId)
                                ->get();
                            break;
                        }
                }
            }
            $responseData = [
                'allScheduleData' => $pfCamppagnAllDetails,
                'allPortfolios' => (isset($allPortfolios) ? $allPortfolios : ''),
                'allCampaignListRecord' => (isset($allCampaigns) ? $allCampaigns : ''),
                'ajax_status' => true
            ];
            return $responseData;
            //return response()->json($responseData);
        }

    }//end function

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function editScheduleForm(Request $request)
    {
        $responseData = [];
        $errorMessage = [];
        if ($request->input('mon') == true || $request->input('tue') == true || $request->input('wed') == true || $request->input('thu') == true || $request->input('fri') == true
            || $request->input('sat') == true || $request->input('sun') == true) {
            $messages = [
                'scheduleName.unique' => 'This schedule name is already exist.',
                'pfCampaigns.required' => 'Portfolios/Campaigns is required'
            ];

            $scheduleId = $request->input('scheduleId');
            // Validations
            $validator = Validator::make($request->all(), [
                'scheduleName' => 'required|max:50|unique:tbl_ams_day_parting_pf_campaign_schedules,scheduleName,' . $scheduleId . ',id,isActive,1',
                'pfCampaigns' => 'required|array|min:1',
                'startTime' => 'required', 'endTime' => 'required'
            ], $messages);
            if ($validator->passes()) {

                $chkStartTime = strftime("%H:%M", strtotime($request->input('startTime'))) . ':00';
                $chkEndTime = strftime("%H:%M", strtotime($request->input('endTime'))) . ':00';
                $isDatesOverlap = $this->isPfCampaignDateOverLap($request->all(), $chkStartTime, $chkEndTime, $scheduleId);

                if ($isDatesOverlap['status'] != FALSE) {
                    // making array to store data in DB
                    $dbData = $this->scheduleData($request->all());

                    $dbData['portfolioCampaignType'] = $portfolioCampaignType = $request->input('portfolioCampaignType');
                    $dbData['startTime'] = $chkStartTime;
                    $dbData['endTime'] = $chkEndTime;
                    $dbData['updated_at'] = date('Y-m-d H:i:s');

                    if (PfCampaignSchedule::where('id', $scheduleId)->update($dbData)) {
                        $this->insertCronStatuses($scheduleId);
                        $allPortfolioCampaignData = [];
                        // Pf Campaign and Portfolio Ids insertion in relation table
                        switch ($portfolioCampaignType) {

                            case 'Campaign':
                                {
                                    if ($request->has('campaignOptionSelected') && !is_null($request->input('campaignOptionSelected'))) {
                                        $userSelectionStatus = $request->input('campaignOptionSelected');
                                        $timeToPauseCampaign = $this->userSelectionFunction($request->input('campaignOptionSelected'));
                                    }

                                    // (1) if any campaign is removed from the list so we are updating following records instead,
                                    if ($request->has('removeCampaigns') && !is_null($request->input('removeCampaigns'))) {
                                        $removeCampaign = explode(',', $request->input('removeCampaigns'));
                                        foreach ($removeCampaign as $key1 => $val1) {
                                            $campaignDetail = explode("|", $val1);
                                            $campaignId = $campaignDetail[0];
                                            DayPartingCampaignScheduleIds::where('fkScheduleId', $scheduleId)
                                                ->where('fkCampaignId', $campaignId)
                                                ->update([
                                                    'userSelection' => $userSelectionStatus,
                                                    'enablingPausingTime' => $timeToPauseCampaign,
                                                    'enablingPausingStatus' => 'deleted'
                                                ]);
                                        }
                                    } // End If (1)

                                    $pfCampaigns = $request->input('pfCampaigns');
                                    $countMax = count($pfCampaigns) / 2;
                                    $campaignStore = $this->makeCampaignArray($dbData, $pfCampaigns, $scheduleId, $countMax);

                                    if ($request->input('portfolioCampaignEditTypeOldValue') == 'Portfolio') {
                                        $this->EnablePausePreviousPortfolios($scheduleId, $userSelectionStatus, $timeToPauseCampaign);
                                        $this->EnablePausePreviousCampaign($scheduleId, $userSelectionStatus, $timeToPauseCampaign);
                                    }
                                    // delete previous data
                                    foreach ($pfCampaigns as $key1 => $val1) {
                                        $campaignDetail = explode("|", $val1);
                                        $campaignId = $campaignDetail[0];
                                        DayPartingCampaignScheduleIds::where('fkScheduleId', $scheduleId)
                                            ->where('fkCampaignId', $campaignId)
                                            ->delete();
                                    }
                                    // Insert New Record
                                    DayPartingCampaignScheduleIds::insert($campaignStore);
                                    break;
                                }
                            case 'Portfolio':
                                {
                                    if ($request->has('campaignOptionSelected') && !is_null($request->input('campaignOptionSelected'))) {
                                        $userSelectionStatus = $request->input('campaignOptionSelected');
                                        $timeToPauseCampaign = $this->userSelectionFunction($request->input('campaignOptionSelected'));
                                    }

                                    // Delete Record if it was Campaigns
                                    if ($request->input('portfolioCampaignEditTypeOldValue') == 'Campaign') {
                                        DayPartingCampaignScheduleIds::where('fkScheduleId', $scheduleId)
                                            ->where('enablingPausingStatus', NULL)
                                            ->update([
                                                'userSelection' => $userSelectionStatus,
                                                'enablingPausingTime' => $timeToPauseCampaign,
                                                'enablingPausingStatus' => 'deleted'
                                            ]);
                                    }

                                    // (1) if any campaign is removed from the list so we are updating following records instead,
                                    if ($request->has('removeCampaigns') && !is_null($request->input('removeCampaigns'))) {
                                        $removePortfolio = explode(',', $request->input('removeCampaigns'));

                                        foreach ($removePortfolio as $key1 => $val1) {
                                            $portfolioDetail = explode("|", $val1);
                                            $portfolioId = $portfolioDetail[0];
                                            DayPartingPortfolioScheduleIds::where('fkScheduleId', $scheduleId)
                                                ->where('fkPortfolioId', $portfolioId)
                                                ->update([
                                                    'userSelection' => $userSelectionStatus,
                                                    'enablingPausingTime' => $timeToPauseCampaign,
                                                    'enablingPausingStatus' => 'deleted'
                                                ]);

                                            $allPortfoliosNeedToDelete = Portfolios::select('portfolioId')
                                                ->where('id', $portfolioId)
                                                ->first()->portfolioId;
                                            $getAllPortfolioCampaignNeedToUpdate = PortfolioAllCampaignList::select('id')
                                                ->where('portfolioId', $allPortfoliosNeedToDelete)
                                                ->get()->toArray();

                                            DayPartingCampaignScheduleIds::where('fkScheduleId', $scheduleId)
                                                ->whereIn('fkCampaignId', $getAllPortfolioCampaignNeedToUpdate)
                                                ->update([
                                                    'userSelection' => $userSelectionStatus,
                                                    'enablingPausingTime' => $timeToPauseCampaign,
                                                    'enablingPausingStatus' => 'deleted'
                                                ]);
                                        }


                                    } // End If (1)

                                    $allPfIds = [];
                                    $pfPortfolio = $request->input('pfCampaigns');
                                    foreach ($pfPortfolio as $singPfId) {
                                        $allPortfolios = Portfolios::select('portfolioId')
                                            ->where('id', intval($singPfId))
                                            ->first()->portfolioId;
                                        array_push($allPfIds, $allPortfolios);
                                    }
                                    $getAllPortfolioCampaign = PortfolioAllCampaignList::select('id', 'name', 'portfolioId')
                                        ->whereIn('portfolioId', $allPfIds)
                                        ->get()->toArray();

                                    if (!empty($getAllPortfolioCampaign)) {
                                        $countPortfolioCount = count($pfPortfolio) / 2;
                                        $portfolioStore = $this->makePortfolioArray($dbData, $pfPortfolio, $scheduleId, $countPortfolioCount);

                                        foreach ($pfPortfolio as $key1 => $val1) {
                                            $portfolioDetail = explode("|", $val1);
                                            $portfolioId = $portfolioDetail[0];
                                            DayPartingPortfolioScheduleIds::where('fkScheduleId', $scheduleId)
                                                ->where('fkPortfolioId', $portfolioId)
                                                ->delete();
                                        }
                                        DayPartingPortfolioScheduleIds::insert($portfolioStore);

                                        $countCampaign = count($getAllPortfolioCampaign);
                                        $campaignStore = $this->makeCampaignArray($dbData, $getAllPortfolioCampaign, $scheduleId, $countCampaign, 'portfolioCampaign');
                                        // Delete Previous Records
                                        foreach ($getAllPortfolioCampaign as $keyPfCampaign) {
                                            DayPartingCampaignScheduleIds::where('fkScheduleId', $scheduleId)
                                                ->where('fkCampaignId', $keyPfCampaign['id'])
                                                ->delete();
                                        }
                                        DayPartingCampaignScheduleIds::insert($campaignStore);
                                    } else {
                                        Log::info('Schedule Name = ' . $dbData['scheduleName'] . 'Campaigns Not found against Portfolios selected');
                                    }
                                    break;
                                }
                        }// Switch Case End
                    }
                    unset($dbData);
                    $responseData = ['success' => 'Schedule has been updated successfully!', 'ajax_status' => true];
                } else {
                    array_push($errorMessage, $isDatesOverlap['message']);
                    $responseData = ['error' => $errorMessage, 'ajax_status' => false];
                }

            } else {
                $responseData = ['error' => $validator->errors()->all(), 'ajax_status' => false];
            }
        } else {
            array_push($errorMessage, 'Please select atleast one day of week!');
            $responseData = ['error' => $errorMessage, 'ajax_status' => false];
        }

        return response()->json($responseData);
    }//end function

    private function EnablePausePreviousCampaign($scheduleId, $userSelectionStatus, $timeToPauseCampaign)
    {

        return DayPartingCampaignScheduleIds::where('fkScheduleId', $scheduleId)
            ->update([
                'userSelection' => $userSelectionStatus,
                'enablingPausingTime' => $timeToPauseCampaign,
                'enablingPausingStatus' => 'deleted'
            ]);
    }

    private function EnablePausePreviousPortfolios($scheduleId, $userSelectionStatus, $timeToPauseCampaign)
    {
        return DayPartingPortfolioScheduleIds::where('fkScheduleId', $scheduleId)
            ->update([
                'userSelection' => $userSelectionStatus,
                'enablingPausingTime' => $timeToPauseCampaign,
                'enablingPausingStatus' => 'deleted'
            ]);
    }

    public function stopSchedule(Request $request){
        if ($request->ajax()) {
            $scheduleId = $request->input('scheduleId');
            PfCampaignSchedule::where('id', $scheduleId)
                ->update([
                    'stopScheduleDate' => date('Y-m-d'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            return response()->json([
                'status' => true,
                'message' => "Schedule has been Stopped Successfully"
            ]);
        }
    }

    public function startSchedule(Request $request){
        if ($request->ajax()) {
            $scheduleId = $request->input('scheduleId');
            PfCampaignSchedule::where('id', $scheduleId)
                ->update([
                    'stopScheduleDate' => null,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            return response()->json([
                'status' => true,
                'message' => "Schedule has been Started Successfully"
            ]);
        }
    }
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteSchedule(Request $request)
    {
        if ($request->ajax()) {
            $scheduleId = $request->input('scheduleId');
            $campaignType = PfCampaignSchedule::select('portfolioCampaignType')->where('id', $scheduleId)->first();

            if ($request->has('status')) {
                $userSelectionStatus = $request->input('status');
                $timeToPauseCampaign = $this->userSelectionFunction($userSelectionStatus);
            }
            if (!empty($campaignType)) {
                PfCampaignSchedule::where('id', $scheduleId)
                    ->update([
                        'isActive' => 0,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                switch ($campaignType->portfolioCampaignType) {
                    case 'Campaign':
                        {
                            DayPartingCampaignScheduleIds::where('fkScheduleId', $scheduleId)
                                ->where('enablingPausingTime', NULL)
                                ->where('enablingPausingStatus', NULL)
                                ->update([
                                    'userSelection' => $userSelectionStatus,
                                    'enablingPausingTime' => $timeToPauseCampaign,
                                    'enablingPausingStatus' => 'deleted'
                                ]);
                            break;
                        }
                    case 'Portfolio':
                        {
                            DayPartingPortfolioScheduleIds::where('fkScheduleId', $scheduleId)
                                ->where('enablingPausingTime', NULL)
                                ->where('enablingPausingStatus', NULL)
                                ->update([
                                    'userSelection' => $userSelectionStatus,
                                    'enablingPausingTime' => $timeToPauseCampaign,
                                    'enablingPausingStatus' => 'deleted'
                                ]);

                            DayPartingCampaignScheduleIds::where('fkScheduleId', $scheduleId)
                                ->where('enablingPausingTime', NULL)
                                ->where('enablingPausingStatus', NULL)
                                ->update([
                                    'userSelection' => $userSelectionStatus,
                                    'enablingPausingTime' => $timeToPauseCampaign,
                                    'enablingPausingStatus' => 'deleted'
                                ]);
                            break;
                        }
                }// Switch Case End
            }
            return response()->json([
                'status' => true,
                'message' => "Schedule has been Deleted Successfully"
            ]);
        }
    }//end function

    /**
     * @param $userSelectionStatus
     * @return string
     */
    private function userSelectionFunction($userSelectionStatus)
    {
        switch ($userSelectionStatus) {
            // Run today's schedule, then pause
            case '1':
                {
                    $timeToPauseCampaign = '23:59:00';
                    break;
                }
            // Pause campaigns immediately
            case '2':
                {
                    $timeToPauseCampaign = strftime("%H:%M", strtotime(date('H:i') . '+3 minute')) . ':00';
                    break;
                }
            // Enable campaigns immediately
            case '3':
                {
                    $timeToPauseCampaign = strftime("%H:%M", strtotime(date('H:i') . '+3 minute')) . ':00';
                    break;
                }

        }
        return $timeToPauseCampaign;
    }

    private function historyDataFunc($sch, $scheduleDay)
    {

        $pausedManager = ($sch->isActive == 0) ? '(deleted by manager)' : '';
        $description = $sch->scheduleName . ' On ' . date('g:i A', strtotime($sch->startTime)) . ' Off ' . date('g:i A', strtotime($sch->endTime)) . ' ' . $pausedManager;
        $fullDayDesc = $sch->scheduleName . ' full day ' . $pausedManager;
        $finalMessage = ($scheduleDay == 1) ? $description : $fullDayDesc;
        $errorMessage = $sch->cronMessage;
        $scheduleStartDate = date('Y-m-d', strtotime($sch->sDate));
        $scheduleEndDate = date('Y-m-d', strtotime($sch->sDate));
        $todayDate = date('Y-m-d');
        $schedulesArray = [];
        if ($todayDate == $scheduleStartDate && $sch->isCronEnd == 0) {
            return $schedulesArray;
        }
        $cronDay = $sch->schStatus;
        if ($cronDay == 1 || $cronDay == 2) {
            $schedulesArray['start'] = $scheduleStartDate;
            $schedulesArray['end'] = $scheduleEndDate;
            $dayMessage = ($cronDay == 2) ? ' Error' : '';
            $schedulesArray['title'] = $sch->scheduleName . $dayMessage;
            $schedulesArray['description'] = ($cronDay == 1) ? $finalMessage : $errorMessage;
//            if ($scheduleDay === 1) {
//                $schedulesArray['description'] = ($scheduleDay == 1) ? $description : $errorMessage;
//            } elseif ($scheduleDay === 0) {
//                $schedulesArray['description'] = ($scheduleDay == 0) ? $fullDayDesc : $errorMessage;
//            }
            if ($sch->isActive == 1) {
                $schedulesArray['color'] = '#24ef89';
            } elseif ($sch->isActive == 0 && $sch->isScheduleExpired == 1 || $sch->isScheduleExpired == 0) {
                $schedulesArray['color'] = '#bab86c';
            }

            if ($sch->isScheduleExpired == 1 && $sch->isActive == 1) {
                $schedulesArray['color'] = 'orange';
            }
            if ($cronDay == 2 && $sch->isActive == 1) {
                $schedulesArray['color'] = 'red';
            }
        }
        return $schedulesArray;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showHistoryForm()
    {
        $data['pageHeading'] = 'Day Parting history';
        $data['pageTitle'] = 'Day Parting history';
        $data["brands"] = $this->getActiveBrands();
        return view('subpages.ams.dayparting.day_parting_history')->with($data);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getHistoryScheduleData(Request $request)
    {
        $schedulesArrayStore = [];
        $schedule = DB::table('tbl_ams_day_parting_pf_campaign_schedules AS campSche')
            ->join('tbl_ams_day_parting_schedule_cron_statuses AS cronStatuses', 'cronStatuses.fkScheduleId', '=', 'campSche.id')
            ->select(array('campSche.fkManagerId', 'campSche.created_at', 'campSche.isCronError',
                'campSche.scheduleName', 'campSche.mon', 'campSche.tue', 'campSche.wed', 'campSche.thu', 'campSche.fri', 'campSche.sat', 'campSche.sun', 'campSche.startTime', 'campSche.endTime', 'campSche.isCronError',
                'campSche.isCronEnd', 'campSche.isActive', 'campSche.isScheduleExpired', 'campSche.created_at', 'campSche.fkBrandId',
                'cronStatuses.fkScheduleId AS fkScheduleId', 'cronStatuses.scheduleDate AS sDate', 'cronStatuses.scheduleStatus AS schStatus', 'cronStatuses.cronMessage'
            ))
            ->where('fkProfileId', $request->input('fkProfileId'))
            ->get();
        foreach ($schedule as $sch) {
            $schedulesArray = [];
            $scheduleStartDate = date('Y-m-d', strtotime($sch->sDate));
            $todayName = strtolower(date('l', strtotime($scheduleStartDate)));

            switch ($todayName) {
                case "monday":
                    {
                        $schedulesArray = $this->historyDataFunc($sch, $sch->mon);
                        break;
                    }
                case "tuesday":
                    {
                        $schedulesArray = $this->historyDataFunc($sch, $sch->tue);
                        break;
                    }
                case "wednesday":
                    {
                        $schedulesArray = $this->historyDataFunc($sch, $sch->wed);
                        break;
                    }
                case "thursday":
                    {
                        $schedulesArray = $this->historyDataFunc($sch, $sch->thu);
                        break;
                    }
                case "friday":
                    {
                        $schedulesArray = $this->historyDataFunc($sch, $sch->fri);
                        break;
                    }
                case "saturday":
                    {
                        $schedulesArray = $this->historyDataFunc($sch, $sch->sat);
                        break;
                    }
                case "sunday":
                    {
                        $schedulesArray = $this->historyDataFunc($sch, $sch->sun);
                        break;
                    }
            }
            if (!empty($schedulesArray)) {
                array_push($schedulesArrayStore, $schedulesArray);
            }
        }
        return $schedulesArrayStore;
//        return response()->json([
//            'status' => true,
//            'scheduleData' => $schedulesArrayStore
//        ]);
    }

    /**
     * @return mixed
     */
    private function getGBSProfiles()
    {
        return AccountModel::where("fkBrandId", getBrandId())
            ->select("id", "fkId")
            ->where("fkAccountType", 1)
            ->get()
            ->map(function ($item, $value) {
                return $item->fkId;
            });
    }

}

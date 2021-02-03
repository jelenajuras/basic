<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AbsenceRequest;
use App\Http\Controllers\BasicAbsenceController;
use App\Http\Controllers\EmailingController;
use App\Http\Controllers\ApiController;
use App\Models\Absence;
use App\Models\AbsenceType;
use App\Models\Employee;
use App\Models\Afterhour;
use App\User;
use Sentinel;
use App\Mail\AbsenceMail;
use App\Mail\AbsenceUpdateMail;
use App\Mail\AbsenceConfirmMail;
use App\Mail\ErrorMail;
use Illuminate\Support\Facades\Mail;
use App\Models\Emailing;
use App\Models\Department;
use DateTime;
use Log;

class AbsenceController extends BasicAbsenceController
{
	private $api_erp;
	private $test_mail;

    /**
		*
		* Set middleware to quard controller.
		* @return void
	*/
    public function __construct()
    {
		$this->middleware('sentinel.auth');
		$this->api_erp = true;
		$this->test_mail = false;  // true - test na jelena.juras@duplco.hr
    }
	
	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $permission_dep = array();
		$data_absence = array();
		$bolovanje = array();
		$docs = '';
		$ova_godina = date('Y');
		$prosla_godina = $ova_godina - 1;
		$type = null;
		
		if(isset($request['year']) && $request['year']) {
			$year = $request['year'];
		} else {
			$year =  date('Y');
		}

		if(isset($request['month']) && $request['month']) {
			$YY_mm = explode('-',$request['month']);
			$month = $YY_mm[1];
			$year = $YY_mm[0];
		} else {
			$month = date('m');
			$year = date('Y');
		}
		$afterhours = collect();

		$types = AbsenceType::get();
		if( isset($request['employee_id']) && $request['employee_id'] && $request['employee_id'] != 'all' ) {
			$employee_id = $request['employee_id'];
			$empl = Employee::find($employee_id);
		} else {
			$empl = Sentinel::getUser()->employee;
		}
	
		if( $empl ) { 
			if( ! isset($request['type']) || ( isset($request['type']) && $request['type'] != 'afterhour')) {
				if( (isset($request['employee_id']) && $request['employee_id'] == 'all') || (Sentinel::inRole('administrator') && ! isset($request['employee_id'])) ) {
					$absences = Absence::whereMonth('start_date', $month)
										->whereYear('start_date', $year)
										->orderBy('start_date','DESC')->get();
					$absences = $absences->merge(Absence::whereMonth('end_date', $month)
														->whereYear('end_date', $year)
														->orderBy('start_date','DESC')->get());
				} else {
					$absences = Absence::where('employee_id',$empl->id)
									->whereMonth('start_date', $month)
									->whereYear('start_date', $year)
									->orderBy('start_date','DESC')->get();
					$absences = $absences->merge(Absence::where('employee_id',$empl->id)
									->whereMonth('end_date', $month)
									->whereYear('end_date', $year)
									->orderBy('start_date','DESC')->get());
				}
			} 
			
			$employees = Employee::employees_lastNameASC();
			
			if(isset($request['type']) && $request['type'] && $request['type'] != 'all' ) {
				if( $request['type'] == 'afterhour') {
					$afterhours = Afterhour::whereMonth('date', $month)
											->whereYear('date', $year)
											->orderBy('date','DESC')->get();

					if(isset($request['employee_id']) && $request['employee_id'] != 'all'	) {
						$afterhours = $afterhours->where('employee_id',$request['employee_id']  );
					}
					$absences = collect();
				} else {
					$type = $types->where('id', $request['type'])->first();			 
					$absences = $absences->where('type', $type->id); 
				}
			}
			
			if(isset($request['approve']) && $request['approve'] != 'all' ) {
				if( $request['approve'] == 'approved' ) {
					$approve = 1;
				} elseif( $request['approve'] == 'refused' ) {
					$approve = '0';
				} elseif( $request['approve'] == 'not_approved' ) {
					$approve = NULL;
				}
				if($approve != NULL) {
					if( count($absences)>0) {
						$absences = $absences->where('approve', $approve );
					}
					if( count($afterhours)>0) {
						$afterhours = $afterhours->where('approve', $approve );
					}
				} else {
					if( count($absences)>0) {
						$absences = $absences->where('approve', NULL)->where('approve','<>', '0');
					}
					if( count($afterhours)>0) {
						$afterhours = $afterhours->where('approve', NULL)->where('approve','<>', '0');
					}
				}
			}
			
			if(isset($_GET['type_bol']) && $_GET['type_bol']) {
				$type = $types->where('mark', $_GET['type_bol'])->first();			 
				$absences = $absences->where('type', $type->id); 
			}

			$years = BasicAbsenceController::yearsRequests($empl); // sve godine zahtjeva
			rsort($years);
			$data_absence = array(
				'years'  		 => $years,  
				'years_service'  => BasicAbsenceController::yearsServiceCompany( $empl ),  
				'all_servise'  	 => BasicAbsenceController::yearsServiceAll( $empl ), 
				'days_OG'  		 => BasicAbsenceController::daysThisYear( $empl ), 
				'razmjeranGO'  	 => BasicAbsenceController::razmjeranGO( $empl ),  //razmjeran go ova godina
				'zahtjevi' 		 => BasicAbsenceController::requestAllYear( $empl ), 
				'bolovanje' 	 => BasicAbsenceController::bolovanje( $empl ), 
				'docs' 		 	 => DashboardController::profile_image( $empl->id ), 
				'user_name' 	 => DashboardController::user_name( $empl->id ), 
				'days_offUsed' => BasicAbsenceController::days_offUsed( $empl ), 
				'afterHours_withoutOuts' => BasicAbsenceController::afterHours_withoutOuts( $empl ), 
			);
			
			if($empl->work) {
				$permission_dep = explode(',', count($empl->work->department->departmentRole) > 0 ? $empl->work->department->departmentRole->toArray()[0]['permissions'] : '');
			}
			
			$years_all = Absence::getYearsMonth();
			rsort($years_all);	

			return view('Centaur::absences.index', ['afterhours' => $afterhours,'absences' => $absences,'employees' => $employees, 'data_absence' => $data_absence, 'types' => $types , 'ova_godina' => $ova_godina,'years' => $years, 'years_all' => $years_all,'employee' => $empl, 'permission_dep' => $permission_dep, 'selected_employee' => $empl]);
		} else {
			$message = session()->flash('error',  __('ctrl.path_not_allow'));
			return redirect()->back()->withFlashMessage($message);
		}
	}
	
	public function absencesYears(Request $request)
	{
		$empl = Employee::find($request['employee_id']);

		$zahtjevi =  BasicAbsenceController::zahtjevi( $empl );
		
		$absences = Absence::AllAbsenceUser($empl->id, 'GO');

		return view('Centaur::absences.absencesYears',['employee'=> $empl, 'zahtjevi' => $zahtjevi, 'absences' => $absences] );
	}
	
	public function absences_table(Request $request)
    {
		$employees = Employee::employees_lastNameASC();
		$employees = $employees->where('work_id','<>',null);
		
		$years = Absence::getYears(); // sve godine zahtjeva
		
		return view('Centaur::absences.absences_table',['employees'=> $employees, 'years' => $years] );
	}

	public function absences_requests(Request $request)
    {
		if(isset($_GET['month']) && $_GET['month']) {
			$YY_mm = explode('-',$_GET['month']);
			$month = $YY_mm[1];
			$year = $YY_mm[0];
		} else {
			$month = date('m');
			$year = date('Y');
		}

		$years = Absence::getYearsMonth(); // sve godine zahtjeva
		rsort($years);	
		
		$types = AbsenceType::get();
		
		$absences = Absence::AbsencesForMonth( $month, $year);
		if(isset($_GET['type']) && $_GET['type'] && $_GET['type'] != 'all') {
			$absences = $absences->where('type', $_GET['type']);
		}
	
		return view('Centaur::absences.absences_requests',['absences'=> $absences, 'years'=> $years,'types'=> $types,  'month' => $year . '-'. $month ] );
	}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
		$user = Sentinel::getUser();
		$empl = Sentinel::getUser()->employee;
		$zahtjevi = BasicAbsenceController::zahtjevi( $empl );
		$preostali_dani  = $zahtjevi['ukupnoPreostalo'];
		
		$type = '';

		if($request['type']) {
			$type = $request['type'];
		}
			
		$employees = Employee::employees_lastNameASC();
		$absenceTypes = AbsenceType::where('active',1)->get();

		/* $api = new ApiController();
		$leave_types = $api->get_available_leave_types(); */
		$leave_types = null;
		
		return view('Centaur::absences.create', ['employees' => $employees,'type' => $type, 'absenceTypes' => $absenceTypes, 'user' => $user,'leave_types' => $leave_types, 'preostali_dani' => $preostali_dani ]);
	}
	
	public function getProject ( Request $request )
	{
		$employee = Employee::where( 'id', $request['employee_id'] )->first();
		
		$tasks = null;
	
        if( $employee ) {
			Log::info('erp_id' . $employee);
			$api = new ApiController();
			$erp_id = $employee->erp_id;
			Log::info('erp_id' . $erp_id);
			$projects = $api->get_employee_available_projects( $erp_id, $request['start_date'] );
        } else {
            $projects = null;
		}

		return $projects;
	}

	public function getTasks ( Request $request )
	{
		$employee = Employee::where( 'id', $request['employee_id'] )->first();

		$tasks = null;
	
        if( $employee ) {
			$api = new ApiController();
			$erp_id = $employee->erp_id;
			
			$tasks = $api->get_employee_project_tasks( $erp_id, $request['start_date'], $request['project']);
        } else {
            $tasks = null;
		}

		return  $tasks;
	}

	public function getDays ( $employee_id )
	{
		$employee = Employee::find( $employee_id );
		if( $employee ) {
			$zahtjevi = BasicAbsenceController::zahtjevi( $employee );
			$dani  = $zahtjevi['ukupnoPreostalo'];
		}
		
		return $dani;
	}

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
		if( isset($request['erp_type'])) {
			$absenceType = AbsenceType::where('erp_id',$request['erp_type'])->first();
			if( $absenceType ) {
				$absenceType_id = $absenceType->id;
				$ERP_leave_type = $request['erp_type'];
			} else {
				session()->flash('error', 'Tip zahtjeva nije nađen');
		
				return redirect()->back();
			}
		} else {
			$absenceType = AbsenceType::where('mark',$request['type'])->first();
			$absenceType_id = $absenceType->id;
			$ERP_leave_type = $absenceType->erp_id;
		}
		
		$message = '';

		if(isset($request['decree'])) {
			if ($request['decree'] == 1) {
					$decree = 1;
				} else {
					$decree = 0;
			}
		} else {
				$decree = 0;
		}
		if($request['employee_id'] == 'all' || $request['employee_id'] == 'svi') {
			$employees = Employee::employees_firstNameASC();
			foreach($employees as $employee ) {
				$data = array(
					'ERP_leave_type'  	=> $ERP_leave_type,
					'erp_task_id'  		=> 5007,
					'type'  			=> $absenceType_id,
					'employee_id'  		=> $employee_id,
					'start_date'    	=> date("Y-m-d", strtotime($request['start_date'])),
					'end_date'			=> $request['end_date'] ? date("Y-m-d", strtotime($request['end_date'])) : null,
					'start_time'  		=> $request['start_time'],
					'end_time'  		=> $request['end_time'],
					'comment'  			=> $request['comment'],
					'decree'  			=> $decree,
				);
				if(isset($request['decree']) && $request['decree'] == 1) {
					$data += ['approve'=> 1 ];
					$data += ['approved_date'=>date('Y-m-d')];
					$data += ['approved_id'=>Sentinel::getUser()->employee['id']];
				}
				$absence = new Absence();
				$absence->saveAbsence($data);
			}
			$message = session()->flash('success', __('ctrl.request_sent'));
		
			return redirect()->back()->with('modal','true')->with('absence','true')->withFlashMessage($message);
		} else if(is_array($request['employee_id'])  && count($request['employee_id']) > 0) {
		   	foreach($request['employee_id'] as $employee_id) {
				if( ( $request['type'] == 'SLD'  ) && Employee::find($employee_id)->slDani == 0 ) {
					$employee_sld = Employee::find($employee_id);
					$message .= 'Za djelatnika '. $employee_sld->user->first_name .' ' . $employee_sld->user->last_name . ' nije moguće poslati zahtjev za slobodan dan.\n ';
				} else {
					$request_exist = BasicAbsenceController::absenceForDay($request['employee_id'], $request['start_date'], $request['start_time'], $request['end_time'] );
					
           			if( $request_exist == 0 ) {
						$data = array(
							'ERP_leave_type'  	=> $ERP_leave_type,
							'erp_task_id'  		=> 5007,
							'type'  			=> $absenceType_id,
							'employee_id'  		=> $employee_id,
							'start_date'    	=> date("Y-m-d", strtotime($request['start_date'])),
							'end_date'			=> $request['end_date'] ? date("Y-m-d", strtotime($request['end_date'])) : null,
							'start_time'  		=> $request['start_time'],
							'end_time'  		=> $request['end_time'],
							'comment'  			=> $request['comment'],
							'decree'  			=> $decree,
						);
						if( $request['type'] == 'BOL' || $request['type'] == 2 || (isset($request['decree']) && $request['decree'] == 1 ) ) {
							$data += ['approve'=>1];
							$data += ['approved_date'=>date('Y-m-d')];
							$data += ['approved_id'=>Sentinel::getUser()->employee['id'] ];
						}
						
						$absence = new Absence();
						$absence->saveAbsence($data);
		
						if($request['email'] == 'DA' ) {
							if( $this->test_mail ) {
								$send_to = 'jelena.juras@duplico.hr';
							} else {
								$send_to = EmailingController::sendTo('absences','create');
							
								// mail voditelja - prvog nadređenog
								$voditelj_mail = $absence->employee->work->firstSuperior ? $absence->employee->work->firstSuperior->email : null;
								if($voditelj_mail) {
									array_push($send_to, $voditelj_mail);
								} else {
									$manager = $absence->employee->work->employee; // voditelj odjela
									$mail_manager = $manager->email;
									array_push($send_to, $mail_manager);
								}
								// ako je odluka uprave mail djelatnika
								if(isset($request['decree']) && $request['decree'] == 1 ) {
									array_push($send_to, $absence->employee->email );
								} 
							}
							try {
								Log::info("Izostanak poslan na mail: ".implode(', ', array_unique($send_to)));
								foreach(array_unique($send_to) as $send_to_mail) {
									if( $send_to_mail != null & $send_to_mail != '' ) {
										Mail::to($send_to_mail)->send(new AbsenceMail($absence));
									}
								}
							} catch (\Throwable $th) {
								$url = $_SERVER['REQUEST_URI'];
							  	Mail::to($email)->send(new ErrorMail( $th->getFile() . ' => ' . $th->getMessage(), $url)); 

								session()->flash('error', __('ctrl.data_save') . ', '. __('ctrl.email_error'));
								return redirect()->back();
							}
					    }
					    if ( $this->api_erp ) {
							if( $request['type'] == 'BOL') {
								$api = new ApiController();
								$leave_types = $api->send_leave_request($absence, 'abs');
							}
					   }
					} else {
						session()->flash('error',  __('ctrl.request_exist'));
						return redirect()->back();
					}
				}
			}
			$message = session()->flash('success', __('ctrl.request_sent'));
		
			return redirect()->back()->with('modal','true')->with('absence','true')->withFlashMessage($message);
		
	    } else {
			$request_exist = BasicAbsenceController::absenceForDay($request['employee_id'], $request['start_date'], $request['start_time'], $request['end_time'] );
           
            if( $request_exist == 0 ) {
				$data = array(
					'ERP_leave_type'  	=> $ERP_leave_type,
					'erp_task_id'  		=> 5007,
					'type'  			=> $absenceType_id,
					'employee_id'  		=> $request['employee_id'],
					'start_date'    	=> date("Y-m-d", strtotime($request['start_date'])),
					'end_date'			=> $request['end_date'] ? date("Y-m-d", strtotime($request['end_date'])) : null,
					'start_time'  		=> $request['start_time'],
					'end_time'  		=> $request['end_time'],
					'comment'  			=> $request['comment'],
					'decree'  			=> $decree,
				);
				if( $request['type'] == 'BOL' || $request['type'] == 2 || (isset($request['decree']) && $request['decree'] == 1 ) ) {
					$data += ['approve'			=>1];
					$data += ['approved_date'	=>date('Y-m-d')];
					$data += ['approved_id' 	=> null];
				}
				$absence = new Absence();
				$absence->saveAbsence($data);

				if($request['email'] == 'DA' ) {
					if( $this->test_mail ) {
						$send_to = 'jelena.juras@duplico.hr';
					} else {
						$send_to = EmailingController::sendTo('absences','create');
				
						// mail voditelja - prvog nadređenog
						$voditelj_mail = $absence->employee->work->firstSuperior ? $absence->employee->work->firstSuperior->email : null;
						if($voditelj_mail) {
							array_push($send_to, $voditelj_mail);
						} else {
							$manager = $absence->employee->work->employee; // voditelj odjela
							$mail_manager = $manager->email;
							array_push($send_to, $mail_manager);
						}

						// ako je odluka uprave mail djelatnika
						if(isset($request['decree']) && $request['decree'] == 1 ) {
							array_push($send_to, $absence->employee->email );
						} 
					}
					
					try {
						Log::info("AbsenceMail: " . implode(',', array_unique($send_to)) );
						foreach(array_unique($send_to) as $send_to_mail) {
							if( $send_to_mail != null & $send_to_mail != '' ) {
								Mail::to($send_to_mail)->send(new AbsenceMail($absence));  
							}
						} 
					} catch (\Throwable $th) {
						$email = 'jelena.juras@duplico.hr';
						$url = $_SERVER['REQUEST_URI'];
						Mail::to($email)->send(new ErrorMail( $th->getFile() . ' => ' . $th->getMessage(), $url)); 
						session()->flash('error', __('ctrl.data_save') . ', '. __('ctrl.email_error'));
						return redirect()->back();
					}
					$message = session()->flash('success', __('ctrl.request_sent'));
		
					return redirect()->back()->with('modal','true')->with('absence','true')->withFlashMessage($message);
				}
				if ( $this->api_erp ) {
					if( $absence->absence->mark == 'BOL') {
						$api = new ApiController();
						$leave_types = $api->send_leave_request($absence, 'abs');
					}
				}
		    } else {
				session()->flash('error',  __('ctrl.request_exist'));
				return redirect()->back();
			}
	    }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
		$empl = Employee::find($id);
		
		if($empl) {
			$absences = Absence::where('employee_id',$empl->id)->get();
			$afterhours = Afterhour::where('employee_id',$empl->id)->get();
			
		} else {
			$absences = array();
			$afterhours = array();
		}
		$types = AbsenceType::get();
		
		if(isset($_GET['type']) && $_GET['type'] && $_GET['type'] != 'all' ) {
			if( $_GET['type'] == 'afterhour') {
				
				$absences = collect();
			} else {
				$type = $types->where('id', $_GET['type'])->first();			 
				$absences = $absences->where('type', $type->id); 
				$afterhours = collect();
			}
		}

		$zahtjevi =  BasicAbsenceController::zahtjevi( $empl );
	
		return view('Centaur::absences.show', ['employee' => $empl,'absences' => $absences,'afterhours' => $afterhours,'types' => $types,'zahtjevi' => $zahtjevi]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $absence = Absence::find($id);
		$employees = Employee::employees_lastNameASC();
		$absenceTypes = AbsenceType::where('active',1)->get();
		
		$user = Sentinel::getUser();
		$tasks = null;
		$leave_types = null;

		$user = Sentinel::getUser();
		$empl = Sentinel::getUser()->employee;
		$zahtjevi = BasicAbsenceController::zahtjevi( $empl );
		$preostali_dani  = $zahtjevi['ukupnoPreostalo'];
		/* $api = new ApiController(); */
		/* $leave_types = $api->get_available_leave_types(); */
		$leave_types = null;

		return view('Centaur::absences.edit', ['absence' => $absence,'employees' => $employees, 'absenceTypes' => $absenceTypes, 'user' => $user, 'tasks' => $tasks, 'leave_types' => $leave_types]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
		$absence = Absence::find($id);
		
		if( isset($request['erp_type'])) {
			$absenceType = AbsenceType::where('erp_id',$request['erp_type'])->first();
			if($absenceType) {
				$absenceType_id = $absenceType->id;
				$ERP_leave_type = $request['erp_type'];
			} else {
				session()->flash('error', 'Tip zahtjeva nije nađen');
		
				return redirect()->back();
			}
		} else {
			$absenceType = AbsenceType::where('mark',$request['type'])->first();
			$absenceType_id = $absenceType->id;
			$ERP_leave_type = $absenceType->erp_id;
		}

		if(isset($request['decree'])) {
			if ($request['decree'] == 1) {
					$decree = 1;
			   	} else {
					$decree = 0;
			}
		} else {
			$decree = 0;
		}
		
		$data = array(
			'type'  			=> $absenceType_id,
			'ERP_leave_type'  	=> $ERP_leave_type,
			'erp_task_id'  		=> 5007,
			'employee_id'  		=> $request['employee_id'],
			'start_date'    	=> date("Y-m-d", strtotime($request['start_date'])),
			'end_date'			=> date("Y-m-d", strtotime($request['end_date'])),
			'start_time'  		=> $request['start_time'],
			'end_time'  		=> $request['end_time'],
			'comment'  			=> $request['comment'],
			'decree'  			=> $decree,
		);
		
		if(isset($request['decree']) && $request['decree'] == 1) {
			$data += ['approve'=>1];
			$data += ['approved_date'=>date('Y-m-d')];
			$data += ['approved_id'=>Sentinel::getUser()->employee['id']];
		}
	
		$absence->updateAbsence($data);

		if ( $this->api_erp ) {
			Log::info($absence->absence->mark);
			if( $absence->absence->mark == 'BOL') {
				$api = new ApiController();
				$leave_types = $api->send_leave_request($absence, 'abs');
			}
		}

		if($request['email'] == 'DA') {
			/* Ako je djelatnik radione poruka ide na g.Peklića */
			/* if( Sentinel::getUser()->employee->hasEmployeeDepartmen->where('department_id', Department::where('name','Radiona')->first()->id )->first()) {
				$send_to = array('borislav.peklic@duplico.hr');
			}  */

			if( $this->test_mail ) {
				$send_to = 'jelena.juras@duplico.hr';
			} else {
				$send_to = EmailingController::sendTo('absences','create');

				$firstSuperior = $absence->employee->work->firstSuperior; // prvi nadređeni
				if($firstSuperior) {
					$mail_firstSuperior = $firstSuperior->email;
					array_push($send_to, $mail_firstSuperior);
				} else {
					$manager = $absence->employee->work->employee; // voditelj odjela
					$mail_manager = $manager->email;
					array_push($send_to, $mail_manager);
				}
			}
			
			try {
				Log::info("AbsenceMail edit : " . implode(',', array_unique($send_to)) );
				foreach(array_unique($send_to) as $send_to_mail) {
					if( $send_to_mail != null & $send_to_mail != '' ) {
						Mail::to($send_to_mail)->send(new AbsenceMail($absence)); // mailovi upisani u mailing 
					}
				}
			} catch (\Throwable $th) {
				$email = 'jelena.juras@duplico.hr';
				$url = $_SERVER['REQUEST_URI'];
				Mail::to($email)->send(new ErrorMail( $th->getFile() . ' => ' . $th->getMessage(), $url)); 
				
				session()->flash('error', __('ctrl.data_save') . ', '. __('ctrl.email_error'));
				return redirect()->back();
			}
	   } 

		session()->flash('success', __('ctrl.data_edit'));
		
		return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $absence = Absence::find($id);
		$absence->delete();
		
		$message = session()->flash('success',__('ctrl.data_delete'));
		
		return redirect()->back()->withFlashMessage($message);
	}
	
	public function storeConf(Request $request)
    {
		$absence = Absence::find($request['id']);
		if( $absence ) {
			$odobrio_user = Sentinel::getUser()->employee;

			$data = array(
				'approve'  			=>  $_GET['approve'],
				'approved_id'    	=>  $odobrio_user->id,
				'approve_reason'  	=>  $_GET['approve_reason'],
				'approved_date'		=>  date('Y-m-d')
			);
					
			$absence->updateAbsence($data);

			// slanje zahtjeva u Odoo
			if( $this->api_erp ) {
				try {
					$api = new ApiController();
					$send_leave_request = $api->send_leave_request($absence, 'abs');
					if($send_leave_request == true) {
						$message_erp = ' Zahtjev je uspješno zapisan u Odoo.';
					} else {
						$message_erp = ' Zahtjev NIJE zapisan u Odoo.';
					}
				} catch (\Throwable $th) {
					$email = 'jelena.juras@duplico.hr';
					$url = $_SERVER['REQUEST_URI'];
					Mail::to($email)->send(new ErrorMail( $th->getFile() . ' => ' . $th->getMessage(), $url)); 
	
					session()->flash('error', __('ctrl.error') );
					return redirect()->back();
				}
			}
				
			/* mail obavijest */
			if( $this->test_mail ) {
				$send_to = 'jelena.juras@duplico.hr';
			} else {
				$send_to = EmailingController::sendTo('absences','confirm');
				$employee_mail = $absence->employee->email;
				array_push($send_to, $employee_mail ); // mail zaposlenika

				$firstSuperior = $absence->employee->work->firstSuperior; // prvi nadređeni
				if($firstSuperior) {
					$mail_firstSuperior = $firstSuperior->email;
					array_push($send_to, $mail_firstSuperior);
				} else {
					$manager = $absence->employee->work->employee; // voditelj odjela
					$mail_manager = $manager->email;
					array_push($send_to, $mail_manager);
				}
				
				$send_to = array_diff( $send_to, array(	$odobrio_user )); // bez djelatnika koji odobrava 
			}
			
			Log::info("Potvrda Izostanaka poslana na mail: ". implode(', ',array_unique($send_to)));

			try {
				foreach(array_unique($send_to) as $send_to_mail) {
					if( $send_to_mail != null & $send_to_mail != '' ) {
						Mail::to($send_to_mail)->send(new AbsenceConfirmMail($absence)); // mailovi upisani u mailing 
					}
				}
			} catch (\Throwable $th) {
				$email = 'jelena.juras@duplico.hr';
				$url = $_SERVER['REQUEST_URI'];
				Mail::to($email)->send(new ErrorMail( $th->getFile() . ' => ' . $th->getMessage(), $url)); 
				
				session()->flash('error', __('ctrl.data_save') . ', '. __('ctrl.email_error'));
				return redirect()->back();
			}
			
			$message = session()->flash('success', __('absence.approved'));

			return redirect()->route('dashboard')->withFlashMessage($message );
		} else {
			$message = session()->flash('success',__('ctrl.request_deleted'));
		
			return redirect()->route('dashboard')->withFlashMessage($message );
		}
		
	}

	public function storeConf_update( Request $request, $id)
    {
		$absence = Absence::find( $id );
	
		$odobrio_user = Sentinel::getUser()->employee;

		$datum = new DateTime('now');

		$data = array(
			'approve'  			=>  $request['approve'],
			'approved_id'    	=>  $odobrio_user->id,
			'approve_reason'  	=>  $request['approve_reason'],
			'approved_date'		=>  date_format($datum,'Y-m-d')
		);
				
		$absence->updateAbsence($data);

		if($request['email'] == 1 ){ 
			if( $this->test_mail ) {
				$send_to = 'jelena.juras@duplico.hr';
			} else {
				$send_to = EmailingController::sendTo('absences','confirm');
				array_push($send_to, $absence->employee->email );
			}
			
			Log::info("AbsenceConfirmMail update: " . implode(', ',array_unique($send_to) ));

			try {
				foreach(array_unique($send_to) as $send_to_mail) {
					if( $send_to_mail != null & $send_to_mail != '' ) {
						Mail::to($send_to_mail)->send(new AbsenceConfirmMail($absence)); // mailovi upisani u mailing 
					}
				}
			} catch (\Throwable $th) {
				session()->flash('error', __('ctrl.data_save') . ', '. __('ctrl.email_error'));
				return redirect()->back();
			}
		}
		
		/* $message = session()->flash('success',  $absence->approve == 1 ? __('absence.approved') :  __('absence.refused') ); */
		/* return redirect()->route('dashboard')->withFlashMessage($message); */
		/* return redirect()->back()->withFlashMessage($message); */

		$message = $absence->approve == 1 ? __('absence.changed_approval') . ': ' .  __('absence.approved') : __('absence.changed_approval') . ': ' .   __('absence.refused');
		return $message;

	}

	public function confirmation_show(Request $request)
	{
		$absence = Absence::find( $request['absence_id']);

		return view('Centaur::absences.confirmation_show',['absence_id'=> $request['absence_id'], 'absence' => $absence]);
	}

	public static function countRequest ()  
	{
		$employee_id = Sentinel::getUser()->employee->id;
		$sent_to_empl = array();
		
		$emailings_absence = Emailing::join('tables','tables.id','emailings.model')->select('emailings.*', 'tables.name')->where('tables.name', 'absences')->where('emailings.method', 'create')->first();
		$absences_count = Absence::where('approve',null)->get()->count();
		if($emailings_absence) {
			$sent_to_empl = explode(',', $emailings_absence->sent_to_empl );
		}
		
		if( in_array($employee_id, $sent_to_empl)) {
			$count = $absences_count;
		} else {
			$count = 0;
		}

		return $count;
	}

	public static function dateDifference($date_1 , $date_2 , $differenceFormat = '%h:%i' )
	{
		$datetime1 = date_create($date_1);
		$datetime2 = date_create($date_2);
	
		$interval = date_diff($datetime1, $datetime2);
	
		return $interval->format($differenceFormat);
	}	

	public static function printRequests ( Request $request)  
	{
		$absence = Absence::find(  $request['id'] );
		
		$dani = array('start_date' =>$absence->start_date, 'end_date' =>$absence->end_date);
		
		$daniGO = BasicAbsenceController::daniGO_count( $dani ); //vraća dane zahtjeva
		
		return view('Centaur::absences.print_requests', ['absence' => $absence, 'daniGO' => $daniGO]);
	}
}
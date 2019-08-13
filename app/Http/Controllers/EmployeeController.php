<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\EmployeeRequest;
use App\Http\Controllers\Controller;
use App\Models\Work;
use App\Models\Employee;
use App\User;
use Sentinel;
use App\Mail\EmployeeCreate;
use Illuminate\Support\Facades\Mail;
use App\Models\Emailing;
use App\Models\Department;

class EmployeeController extends Controller
{
    /**
	*
	* Set middleware to quard controller.
	* @return void
	*/
	public function __construct()
	{
		$this->middleware('sentinel.auth');
	}
	
	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $employees = Employee::get();
		$empl = Sentinel::getUser()->employee;
		$permission_dep = array();
        
		if($empl) {
			$permission_dep = explode(',', count($empl->work->department->departmentRole) > 0 ? $empl->work->department->departmentRole->toArray()[0]['permissions'] : '');
        } 
		
		return view('Centaur::employees.index', ['employees' => $employees, 'permission_dep' => $permission_dep]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
		$users = User::get();
		$works = Work::get();
		$employees = Employee::get();

		if(isset($request->user_id)) {
			$user1 = User::find($request->user_id);
			return view('Centaur::employees.create', ['works' => $works,'employees' => $employees, 'user1' => $user1, 'users' => $users]);
		} else {
			return view('Centaur::employees.create', ['works' => $works, 'employees' => $employees,'users' => $users]);
		}
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(EmployeeRequest $request)
    {
        $input = $request->except(['_token']);
		
		$staz = $input['stazY'].'-'.$input['stazM'].'-'.$input['stazD'];
		
		if(!isset($input['termination_service'])) {
			$termination_service = null;
		} else {
			$termination_service = $input['termination_service'];
		}
		if(!isset($input['first_job'])) {
			$first_job = null;
		} else {
			$first_job = $input['first_job'];
		}
		
		$data = array(
			'user_id'  				=> $input['user_id'],
			'father_name'     		=> $input['father_name'],
			'mather_name'     		=> $input['mather_name'],
			'oib'           		=> $input['oib'],
			'oi'           			=> $input['oi'],
			'oi_expiry'           	=> $input['oi_expiry'],
			'b_day'					=> $input['b_day'],
			'b_place'       		=> $input['b_place'],
			'mobile'  				=> $input['mobile'],
			'priv_mobile'  			=> $input['priv_mobile'],
			'email'  				=> $input['email'],
			'priv_email'  			=> $input['priv_email'],
			'prebiv_adresa'   		=> $input['prebiv_adresa'],
			'prebiv_grad'     		=> $input['prebiv_grad'],
			'borav_adresa'      	=> $input['borav_adresa'],
			'borav_grad'        	=> $input['borav_grad'],
			'title'  			    => $input['title'],
			'qualifications'  		=> $input['qualifications'],
			'marital'  	    		=> $input['marital'],
			'work_id'  	    		=> $input['work_id'],
			'reg_date' 	    		=> $input['reg_date'],
			'probation' 	   		=> $input['probation'],
			'years_service' 	   	=> $staz,
			'termination_service' 	=> $termination_service,
			'first_job' 			=> $first_job,
			'comment' 	   		    => $input['comment']
		);
		
		if( $input['superior_id'] != 0 ) {
			$data += ['superior_id'  => $input['superior_id']];
		} 
		if( $request ['effective_cost']) {
			$data += ['effective_cost'  => str_replace(',','.', $input['effective_cost'])];
		} 
		if( $request ['brutto']) {
			$data += ['brutto'  => str_replace(',','.', $input['brutto'])];
		} 
		
		$employee = new Employee();
		$employee->saveEmployee($data);
		
		/* mail obavijest o novoj poruci */
		$emailings = Emailing::get();
		$send_to = array();
		$departments = Department::get();
		$employees = Employee::get();

		if(isset($emailings)) {
			foreach($emailings as $emailing) {
				if($emailing->table['name'] == 'employees' && $emailing->method == 'create') {
					
					if($emailing->sent_to_dep) {
						foreach(explode(",", $emailing->sent_to_dep) as $prima_dep) {
							array_push($send_to, $departments->where('id', $prima_dep)->first()->email );
						}
					}
					if($emailing->sent_to_empl) {
						foreach(explode(",", $emailing->sent_to_empl) as $prima_empl) {
							array_push($send_to, $employees->where('id', $prima_empl)->first()->email );
						}
					}
				}
			}
		}

		foreach($send_to as $send_to_mail) {
			if( $send_to_mail != null & $send_to_mail != '' )
			Mail::to($send_to_mail)->send(new EmployeeCreate($employee)); // mailovi upisani u mailing 
		}
		
		session()->flash('success', "Podaci su spremljeni");
		
        return redirect()->route('employees.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $employee = Employee::find($id);
		
		
		$user_name = explode('.',strstr($employee->email,'@',true));
		if(count($user_name) == 2) {
			$user_name = $user_name[1] . '_' . $user_name[0];
		} else {
			$user_name = $user_name[0];
		}

		$path = 'storage/' . $user_name . "/profile_img/";
		if(file_exists($path)){
			$docs = array_diff(scandir($path), array('..', '.', '.gitignore'));
		}else {
			$docs = '';
		}

		return view('Centaur::employees.show', ['employee' => $employee,'docs' => $docs,'user_name' => $user_name]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
		$employee = Employee::find($id);
		$users = User::get();
		$works = Work:: get();
		$employees = Employee::get();
		
		return view('Centaur::employees.edit', ['works' => $works, 'users' => $users, 'employee' => $employee, 'employees' => $employees]);
		
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(EmployeeRequest $request, $id)
    {
		$employee = Employee::find($id);
		
		$input = $request->except(['_token']);
		
		$staz = $input['stazY'].'-'.$input['stazM'].'-'.$input['stazD'];
		if(!isset($input['termination_service'])) {
			$termination_service = null;
		} else {
			$termination_service = $input['termination_service'];
		}
		if(!isset($input['first_job'])) {
			$first_job = null;
		} else {
			$first_job = $input['first_job'];
		}
		$data = array(
			'user_id'  				=> $input['user_id'],
			'father_name'     		=> $input['father_name'],
			'mather_name'     		=> $input['mather_name'],
			'oib'           		=> $input['oib'],
			'oi'           			=> $input['oi'],
			'oi_expiry'           	=> $input['oi_expiry'],
			'b_day'					=> $input['b_day'],
			'b_place'       		=> $input['b_place'],
			'mobile'  				=> $input['mobile'],
			'priv_mobile'  			=> $input['priv_mobile'],
			'email'  				=> $input['email'],
			'priv_email'  			=> $input['priv_email'],
			'prebiv_adresa'   		=> $input['prebiv_adresa'],
			'prebiv_grad'     		=> $input['prebiv_grad'],
			'borav_adresa'      	=> $input['borav_adresa'],
			'borav_grad'        	=> $input['borav_grad'],
			'title'  			    => $input['title'],
			'qualifications'  		=> $input['qualifications'],
			'marital'  	    		=> $input['marital'],
			'work_id'  	    		=> $input['work_id'],
			'reg_date' 	    		=> $input['reg_date'],
			'probation' 	   		=> $input['probation'],
			'years_service' 	   	=> $staz,
			'termination_service' 	=> $termination_service,
			'first_job' 			=> $first_job,
			'comment' 	   		    => $input['comment']
		);

		if( $input['superior_id'] != 0 ) {
			$data += ['superior_id'  => $input['superior_id']];
		} 
		if( $request ['effective_cost']) {
			$data += ['effective_cost'  => str_replace(',','.', $input['effective_cost'])];
		} 
		if( $request ['brutto']) {
			$data += ['brutto'  => str_replace(',','.', $input['brutto'])];
		} 
		
		$employee->updateEmployee($data);
		/* mail obavijest o novoj poruci */
		$emailings = Emailing::get();
		$send_to = array();
		$departments = Department::get();
		$employees = Employee::get();

		if(isset($emailings)) {
			foreach($emailings as $emailing) {
				if($emailing->table['name'] == 'employees' && $emailing->method == 'create') {
					
					if($emailing->sent_to_dep) {
						foreach(explode(",", $emailing->sent_to_dep) as $prima_dep) {
							array_push($send_to, $departments->where('id', $prima_dep)->first()->email );
						}
					}
					if($emailing->sent_to_empl) {
						foreach(explode(",", $emailing->sent_to_empl) as $prima_empl) {
							array_push($send_to, $employees->where('id', $prima_empl)->first()->email );
						}
					}
				}
			}
		}
		/*
		foreach($send_to as $send_to_mail) {
			if( $send_to_mail != null & $send_to_mail != '' )
			Mail::to($send_to_mail)->send(new EmployeeCreate($employee)); // mailovi upisani u mailing 
		}
		*/
		session()->flash('success', "Podaci su ispravljeni");
		
        return redirect()->route('employees.index');
		
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $employee = Employee::find($id);
		$employee->delete();
		
		$message = session()->flash('success', 'Zaposlenik je obrisan.');
		
		return redirect()->back()->withFlashMessage($message);
    }
}

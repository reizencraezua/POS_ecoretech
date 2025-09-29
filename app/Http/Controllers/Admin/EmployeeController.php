<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::with(['job', 'orders']);

        // Handle search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('employee_firstname', 'LIKE', "%{$search}%")
                    ->orWhere('employee_lastname', 'LIKE', "%{$search}%")
                    ->orWhere('employee_email', 'LIKE', "%{$search}%")
                    ->orWhere('employee_contact', 'LIKE', "%{$search}%")
                    ->orWhereHas('job', function ($jobQuery) use ($search) {
                        $jobQuery->where('job_title', 'LIKE', "%{$search}%");
                    });
            });
        }

        $employees = $query->latest()->paginate(15);
        $jobs = Job::orderBy('job_title', 'asc')->get();

        return view('admin.employees.index', compact('employees', 'jobs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $job = Job::orderBy('job_title', 'asc')->get();
        return view('admin.employees.create', compact('job'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_firstname' => 'required|string|max:255',
            'employee_middlename' => 'nullable|string|max:255',
            'employee_lastname' => 'required|string|max:255',
            'employee_email' => 'required|email|unique:employees,employee_email|max:255',
            'employee_contact' => 'required|string|max:20',
            'employee_address' => 'required|string|max:500',
            'hire_date' => 'required|date|before_or_equal:today',
            'job_id' => 'required|exists:job_positions,job_id',
        ], [
            'employee_firstname.required' => 'First name is required.',
            'employee_lastname.required' => 'Last name is required.',
            'employee_email.required' => 'Email address is required.',
            'employee_email.email' => 'Please enter a valid email address.',
            'employee_email.unique' => 'An employee with this email already exists.',
            'employee_contact.required' => 'Contact number is required.',
            'employee_address.required' => 'Address is required.',
            'hire_date.required' => 'Hire date is required.',
            'hire_date.date' => 'Please enter a valid date.',
            'hire_date.before_or_equal' => 'Hire date cannot be in the future.',
            'job_id.required' => 'Please select a job position.',
            'job_id.exists' => 'Selected job position is invalid.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            Employee::create([
                'employee_firstname' => $request->employee_firstname,
                'employee_middlename' => $request->employee_middlename,
                'employee_lastname' => $request->employee_lastname,
                'employee_email' => $request->employee_email,
                'employee_contact' => $request->employee_contact,
                'employee_address' => $request->employee_address,
                'hire_date' => $request->hire_date,
                'job_id' => $request->job_id,
            ]);

            return redirect()->route('admin.employees.index')
                ->with('success', 'Employee added successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to add employee. Please try again.')
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        $employee->load(['job', 'orders' => function ($query) {
            $query->with(['customer'])->orderBy('created_at', 'desc');
        }]);

        return view('admin.employees.show', compact('employee'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        $job = Job::orderBy('job_title', 'asc')->get();
        return view('admin.employees.edit', compact('employee', 'job'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        $validator = Validator::make($request->all(), [
            'employee_firstname' => 'required|string|max:255',
            'employee_middlename' => 'nullable|string|max:255',
            'employee_lastname' => 'required|string|max:255',
            'employee_email' => 'required|email|unique:employees,employee_email,' . $employee->employee_id . ',employee_id|max:255',
            'employee_contact' => 'required|string|max:20',
            'employee_address' => 'required|string|max:500',
            'hire_date' => 'required|date|before_or_equal:today',
            'job_id' => 'required|exists:job_positions,job_id',
        ], [
            'employee_firstname.required' => 'First name is required.',
            'employee_lastname.required' => 'Last name is required.',
            'employee_email.required' => 'Email address is required.',
            'employee_email.email' => 'Please enter a valid email address.',
            'employee_email.unique' => 'An employee with this email already exists.',
            'employee_contact.required' => 'Contact number is required.',
            'employee_address.required' => 'Address is required.',
            'hire_date.required' => 'Hire date is required.',
            'hire_date.date' => 'Please enter a valid date.',
            'hire_date.before_or_equal' => 'Hire date cannot be in the future.',
            'job_id.required' => 'Please select a job position.',
            'job_id.exists' => 'Selected job position is invalid.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $employee->update([
                'employee_firstname' => $request->employee_firstname,
                'employee_middlename' => $request->employee_middlename,
                'employee_lastname' => $request->employee_lastname,
                'employee_email' => $request->employee_email,
                'employee_contact' => $request->employee_contact,
                'employee_address' => $request->employee_address,
                'hire_date' => $request->hire_date,
                'job_id' => $request->job_id,
            ]);

            return redirect()->route('admin.employees.index')
                ->with('success', 'Employee updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update employee. Please try again.')
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        try {
            // Check if employee has active orders
            $activeOrdersCount = $employee->orders()
                ->whereIn('order_status', ['On-Process', 'Designing', 'Production', 'For Releasing'])
                ->count();

            if ($activeOrdersCount > 0) {
                return redirect()->back()
                    ->with('error', 'Cannot delete employee with active orders. Please reassign or complete their orders first.');
            }

            $employee->delete();

            return redirect()->route('admin.employees.index')
                ->with('success', 'Employee removed successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to remove employee. They may have associated records.');
        }
    }

    /**
     * Get employee data for AJAX requests (optional utility method)
     */
    public function getEmployeeData($id)
    {
        try {
            $employee = Employee::with('job')->findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $employee
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found'
            ], 404);
        }
    }

    /**
     * Get available employees for assignment
     */
    public function getAvailableEmployees(Request $request)
    {
        $employees = Employee::with('job')
            ->whereHas('job', function ($query) use ($request) {
                // Filter by job type if specified
                if ($request->has('job_type')) {
                    $query->where('job_title', 'LIKE', '%' . $request->job_type . '%');
                }
            })
            ->get(['employee_id', 'employee_firstname', 'employee_lastname', 'job_id']);

        return response()->json([
            'success' => true,
            'data' => $employees
        ]);
    }
}

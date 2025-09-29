<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $query = Job::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('job_title', 'like', "%{$search}%")
                ->orWhere('job_description', 'like', "%{$search}%");
        }

        $jobs = $query->with('employees')->orderBy('job_title')->paginate(15);

        return view('admin.jobs.index', compact('jobs'));
    }

    public function create()
    {
        return view('admin.jobs.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'job_title' => 'required|string|max:255|unique:job_positions,job_title',
            'job_description' => 'nullable|string',
        ], [
            'job_title.required' => 'Job title is required.',
            'job_title.unique' => 'A job position with this title already exists.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            Job::create([
                'job_title' => $request->job_title,
                'job_description' => $request->job_description,
            ]);

            return redirect()->route('admin.jobs.index')
                ->with('success', 'Job position created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to create job position. Please try again.')
                ->withInput();
        }
    }

    public function show(Job $job)
    {
        $job->load(['employees']);
        return view('admin.jobs.show', compact('job'));
    }

    public function edit(Job $job)
    {
        return view('admin.jobs.edit', compact('job'));
    }

    public function update(Request $request, Job $job)
    {
        $validator = Validator::make($request->all(), [
            'job_title' => 'required|string|max:255|unique:job_positions,job_title,' . $job->job_id . ',job_id',
            'job_description' => 'nullable|string',
        ], [
            'job_title.required' => 'Job title is required.',
            'job_title.unique' => 'A job position with this title already exists.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $job->update([
                'job_title' => $request->job_title,
                'job_description' => $request->job_description,
            ]);

            return redirect()->route('admin.jobs.index')
                ->with('success', 'Job position updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update job position. Please try again.')
                ->withInput();
        }
    }

    public function destroy(Job $job)
    {
        try {
            // Check if job has employees
            $employeeCount = $job->employees()->count();

            if ($employeeCount > 0) {
                return redirect()->back()
                    ->with('error', "Cannot delete job position. It has {$employeeCount} employee(s) assigned. Please reassign employees first.");
            }

            $job->delete();

            return redirect()->route('admin.jobs.index')
                ->with('success', 'Job position deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete job position. Please try again.');
        }
    }
}

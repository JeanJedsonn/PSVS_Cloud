<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class ContadorJob extends Component
{
    public function render()
    {
        $jobsCount = DB::table('jobs')->count();
        $failedJobsCount = DB::table('failed_jobs')->count();

        return view('livewire.contador-job', [
            'jobsCount' => $jobsCount,
            'failedJobsCount' => $failedJobsCount
        ]);
    }
}

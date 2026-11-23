<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Complain;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $tasks = collect(); 
        $complains = collect();

        // Jika SuperAdmin (Melihat Statistik Global)
        if ($user->role === 'superadmin') {
            $tasks = Task::all();
            $complains = Complain::all();
        } 
        
        // Jika Atasan (Fokus pada Komplain)
        elseif ($user->role === 'atasan') {
            $tasks = collect(); 
            $complains = Complain::all();
        }
        
        // Jika Kepala Lab (Tugas yang dibuat & Komplain yang dilaporkan)
        elseif ($user->role === 'kepala_lab') {
            $tasks = Task::where('kepala_lab_id', $user->id)->get();
            $complains = Complain::where('pelapor_id', $user->id)->get();
        } 
        
        // Jika Admin atau Laboran (Hanya Tugas yang ditugaskan ke mereka)
        else {
            $tasks = Task::where('ditugaskan_ke_id', $user->id)->get();
            $complains = collect();
        }

        return view('dashboard', compact('tasks', 'complains'));
    }
}
<?php

namespace App\Livewire\Task;

use App\Models\BusinessUnit;
use App\Models\EmailDomain;
use App\Models\Task;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Validation\Rule;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Illuminate\Support\Facades\File;

class TaskForm extends Component
{
    use WithFileUploads;
    use LivewireAlert;
    public $taskList;
    public $task;
    public $idd;

    protected $listeners = [
        'confirmdelete',
    ];
    public function createTask()
    {
        $taskCheck = Task::where('task', $this->task)->first();
        if (!$taskCheck) {
            $task = Task::create([
                'task' => $this->task,
                'status' => 0,
            ]);
            $this->task = "";
            return back();
        }
        $this->alert('warning', 'dublicate task not allowed', [
            'icon' => 'success',
            'showConfirmButton' => true,
            'showCancelButton' => true,
            'cancelButtonText' => 'Cancel',
            'allowOutsideClick' => false,
            'timer' => null,
            'position' => 'center',
        ]);
    }

    public function statusUpdate($id)
    {
        Task::where('id', $id)->update(['status' => 1]);
    }

    public function deleteTask($id)
    {
        $this->idd = $id;
        $this->alert('warning', 'Are you sure you want to delete this task', [
            'icon' => 'success',
            'showConfirmButton' => true,
            'showCancelButton' => true,
            'confirmButtonText' => 'Delete',
            'cancelButtonText' => 'Cancel',
            'allowOutsideClick' => false,
            'timer' => null,
            'position' => 'center',
            'onConfirmed' => 'confirmdelete',
        ]);
    }
    public function confirmdelete(): void
    {
        Task::where('id', $this->idd)->delete();
        $this->flash('success', 'Delete Successfully', [
            'position' => 'center',
            'timer' => 3000,
            'toast' => false,
            'icon' => 'success',
        ], '/todo');
    }
    public function render()
    {
        $this->list();
        return view('livewire.pages.task.task-form')->layout('layouts.app');
    }
}

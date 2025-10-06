<?php

namespace App\Livewire\Erp;

use App\Models\Workspace\Local;
use App\Models\Workspace\Sede;
use Livewire\Component;
use Livewire\WithPagination;

class LocalTable extends Component
{
    use WithPagination;

    public $search = '';
    public $sedeFilter = '';
    public $estadoFilter = '';
    public $perPage = 10;
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    
    public $showDeleteModal = false;
    public $localToDelete = null;

    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSedeFilter()
    {
        $this->resetPage();
    }

    public function updatingEstadoFilter()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortField = $field;
        $this->resetPage();
    }

    public function confirmDelete($localId)
    {
        $this->localToDelete = Local::find($localId);
        $this->showDeleteModal = true;
    }

    public function deleteLocal()
    {
        if ($this->localToDelete) {
            $descripcion = $this->localToDelete->descripcion;
            $this->localToDelete->delete();
            
            session()->flash('success', "Local '{$descripcion}' eliminado correctamente.");
            
            $this->localToDelete = null;
            $this->showDeleteModal = false;
            $this->resetPage();
        }
    }

    public function cancelDelete()
    {
        $this->localToDelete = null;
        $this->showDeleteModal = false;
    }

    public function toggleEstado($localId)
    {
        $local = Local::find($localId);
        if ($local) {
            $local->update(['estado' => !$local->estado]);
            
            $estado = $local->estado ? 'activado' : 'desactivado';
            session()->flash('success', "Local '{$local->descripcion}' {$estado} correctamente.");
        }
    }

    public function render()
    {
        $query = Local::with('sede')
            ->when($this->search, function ($query) {
                $query->buscar($this->search);
            })
            ->when($this->sedeFilter, function ($query) {
                $query->where('sede_id', $this->sedeFilter);
            })
            ->when($this->estadoFilter !== '', function ($query) {
                $query->where('estado', $this->estadoFilter === '1');
            })
            ->orderBy($this->sortField, $this->sortDirection);

        $locales = $query->paginate($this->perPage);
        $sedes = Sede::where('estado', true)->orderBy('nombre')->get();

        return view('livewire.erp.local-table', [
            'locales' => $locales,
            'sedes' => $sedes,
        ]);
    }
}

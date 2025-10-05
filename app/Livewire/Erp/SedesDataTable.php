<?php

namespace App\Livewire\Erp;

use App\Models\Erp\Empresa;
use App\Models\Erp\Sede;
use Livewire\Component;
use Livewire\WithPagination;

class SedesDataTable extends Component
{
    use WithPagination;

    public $search = '';
    public $empresaFilter = '';
    public $estadoFilter = '';
    public $perPage = 10;
    public $sortField = 'nombre';
    public $sortDirection = 'asc';
    public $isLoading = false;

    protected $paginationTheme = 'bootstrap';

    protected $listeners = ['refreshComponent' => '$refresh'];

    public function mount()
    {
        // Inicialización del componente
    }

    public function updatedSearch()
    {
        $this->isLoading = true;
        $this->resetPage();
        $this->isLoading = false;
    }

    public function updatedEmpresaFilter()
    {
        $this->isLoading = true;
        $this->resetPage();
        $this->isLoading = false;
    }

    public function updatedEstadoFilter()
    {
        $this->isLoading = true;
        $this->resetPage();
        $this->isLoading = false;
    }

    public function updatedPerPage()
    {
        $this->isLoading = true;
        $this->resetPage();
        $this->isLoading = false;
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

    public function clearFilters()
    {
        $this->search = '';
        $this->empresaFilter = '';
        $this->estadoFilter = '';
        $this->resetPage();
    }

    public function gotoPage($page)
    {
        $page = (int) $page;
        if ($page >= 1) {
            $this->setPage($page);
        }
    }

    public function render()
    {
        $query = Sede::with('empresa');

        // Aplicar filtros
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('nombre', 'like', '%' . $this->search . '%')
                  ->orWhere('descripcion', 'like', '%' . $this->search . '%')
                  ->orWhereHas('empresa', function ($empresaQuery) {
                      $empresaQuery->where('razon_social', 'like', '%' . $this->search . '%')
                                   ->orWhere('nombre_comercial', 'like', '%' . $this->search . '%');
                  });
            });
        }

        if (!empty($this->empresaFilter)) {
            $query->where('empresa_id', $this->empresaFilter);
        }

        if ($this->estadoFilter !== '') {
            $query->where('estado', $this->estadoFilter);
        }

        // Aplicar ordenamiento
        $query->orderBy($this->sortField, $this->sortDirection);

        $sedes = $query->paginate($this->perPage);
        
        // Obtener empresas para el filtro
        $empresas = Empresa::where('estado', 1)->orderBy('razon_social')->get();

        return view('livewire.erp.sedes-data-table', compact('sedes', 'empresas'));
    }
}

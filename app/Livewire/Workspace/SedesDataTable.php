<?php

namespace App\Livewire\Erp;

use App\Models\Workspace\Empresa;
use App\Models\Workspace\Sede;
use App\Models\GrupoEmpresa;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;

class SedesDataTable extends Component
{
    use WithPagination;

    public $search = '';
    public $estadoFilter = '';
    public $perPage = 10;
    public $sortField = 'nombre';
    public $sortDirection = 'asc';
    public $isLoading = false;
    
    // Propiedades para el contexto del grupo
    public $grupoSlug;
    public $grupoEmpresa;

    protected $paginationTheme = 'bootstrap';

    protected $listeners = ['refreshComponent' => '$refresh'];

    /**
     * Inicializar el componente con el contexto del grupo
     * El grupoSlug viene desde la vista que renderiza este componente
     */
    public function mount($grupoSlug = null)
    {
        $this->grupoSlug = $grupoSlug ?? request()->route('grupo');
        
        if (!$this->grupoSlug) {
            abort(404, 'Grupo empresarial no especificado');
        }
        
        // Obtener el grupo empresarial
        $this->grupoEmpresa = GrupoEmpresa::where('slug', $this->grupoSlug)->first();
        
        if (!$this->grupoEmpresa) {
            abort(404, 'Grupo empresarial no encontrado');
        }
        
        Log::info('SedesDataTable iniciado para grupo: ' . $this->grupoEmpresa->nombre_comercial);
    }

    public function updatedSearch()
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
        // ==================== FILTRADO POR GRUPO ====================
        
        // Las sedes pertenecen directamente al grupo empresarial
        // Iniciar query filtrando por el grupo actual
        $query = Sede::with('grupoEmpresa')
                     ->where('grupo_empresa_id', $this->grupoEmpresa->id);

        // ==================== APLICAR FILTROS DE BÚSQUEDA ====================
        
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('nombre', 'like', '%' . $this->search . '%')
                  ->orWhere('codigo', 'like', '%' . $this->search . '%')
                  ->orWhere('descripcion', 'like', '%' . $this->search . '%')
                  ->orWhere('ciudad', 'like', '%' . $this->search . '%')
                  ->orWhere('direccion', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->estadoFilter !== '') {
            $query->where('activo', $this->estadoFilter);
        }

        // ==================== APLICAR ORDENAMIENTO ====================
        
        $query->orderBy($this->sortField, $this->sortDirection);

        $sedes = $query->paginate($this->perPage);

        return view('livewire.workspace.sedes-data-table', compact('sedes'));
    }
}

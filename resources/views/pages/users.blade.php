<?php

use function Livewire\Volt\{layout, title};

layout('layouts.app');

title('Gestión de Usuarios');

?>

<div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Gestión de Usuarios</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Usuarios</li>
                        </ol>
                    </nav>
                </div>
                
                <livewire:user-manager />
            </div>
        </div>
    </div>
</div>
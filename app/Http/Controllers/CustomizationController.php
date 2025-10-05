<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserCustomization;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CustomizationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Mostrar la página de personalización
     */
    public function index()
    {
        $user = Auth::user();
        $customization = $user->getCustomization();
        
        $dataBreadcrumb = [
            'title' => 'Personalización',
            'description' => 'Configura la apariencia del sistema según tus preferencias',
            'icon' => 'ti ti-palette',
            'breadcrumbs' => [
                ['name' => 'Config. del sistema', 'url' => route('home')],
                ['name' => 'Personalización', 'url' => 'javascript:void(0);', 'active' => true],
            ],
            'stats' => [
                [
                    'name' => 'Tema Actual',
                    'value' => ucfirst($customization->theme_mode),
                    'icon' => 'ti ti-sun',
                    'color' => 'bg-label-primary',
                ],
                [
                    'name' => 'Layout',
                    'value' => ucfirst($customization->layout_type),
                    'icon' => 'ti ti-layout-dashboard',
                    'color' => 'bg-label-success',
                ],
            ],
        ];

        $dataHeaderCard = [
            'title' => 'Configuración de Personalización',
            'description' => 'Ajusta la apariencia del sistema',
            'textColor' => 'text-primary',
            'icon' => 'ti ti-palette',
            'iconColor' => 'bg-label-primary',
            'actions' => [
                [
                    'typeAction' => 'btnOnClick', // btnOnClick
                    'name' => 'Restablecer',
                    'url' => null,
                    'icon' => 'ti ti-refresh',
                    'permission' => route('customization.reset'),
                    'typeButton' => 'btn-label-warning',
                    'onClickFunction' => 'resetCustomization()',
                ],
            ],
        ];

        return view('customization.index', compact('customization', 'dataBreadcrumb', 'dataHeaderCard'));
    }

    /**
     * Actualizar la personalización del usuario
     */
    public function update(Request $request)
    {
        try {
            Log::info('Customization update started', $request->all());
            
          

            $request->validate([
                'theme_mode' => 'required|in:light,dark,system',
                'theme_color' => 'required|in:default,cyan,purple,orange,red,green,dark,custom',
                'font_family' => 'required|in:inter,roboto,poppins,open_sans,lato',
                'font_size' => 'required|in:small,medium,large',
                'layout_type' => 'required|in:vertical,horizontal',
                'layout_container' => 'required|in:fluid,boxed,detached',
                'navbar_type' => 'required|in:fixed,static,hidden',
                'navbar_blur' => 'nullable|in:0,1',
                'sidebar_collapsed' => 'nullable|in:0,1',
                // 'navbar_blur' => 'nullable|in:0,1',
                // 'sidebar_type' => 'nullable|in:default,compact',
            ]);

            $user = Auth::user();
            $customization = $user->getCustomization();

            // Preparar datos para actualizar, convirtiendo strings a boolean donde sea necesario
            $data = $request->all();
            
            // Convertir valores checkbox a boolean
            if (isset($data['navbar_blur'])) {
                $data['navbar_blur'] = $data['navbar_blur'] == '1';
            }
            if (isset($data['sidebar_collapsed'])) {
                $data['sidebar_collapsed'] = $data['sidebar_collapsed'] == '1';
            }

            $customization->update($data);

            Log::info('Customization updated successfully');

            return response()->json([
                'success' => true,
                'message' => 'Configuración actualizada correctamente',
                'data' => $customization
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error in customization update', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('Error in customization update', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Restablecer configuración a valores por defecto
     */
    public function reset()
    {
        $user = Auth::user();
        $customization = $user->getCustomization();

        $customization->update(UserCustomization::getDefaults());

        return response()->json([
            'success' => true,
            'message' => 'Configuración restablecida a valores por defecto',
            'data' => $customization
        ]);
    }

    /**
     * Obtener la configuración actual (API)
     */
    public function getSettings()
    {
        $user = Auth::user();
        $customization = $user->getCustomization();

        return response()->json([
            'success' => true,
            'data' => $customization
        ]);
    }
}

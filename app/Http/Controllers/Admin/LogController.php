<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class LogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('superadmin');
    }

    /**
     * Mostrar lista de archivos de logs
     */
    public function index()
    {
        $logPath = storage_path('logs');
        $logFiles = [];

        if (File::exists($logPath)) {
            $files = File::files($logPath);
            
            foreach ($files as $file) {
                $fileName = $file->getFilename();
                $filePath = $file->getPathname();
                
                // Solo mostrar archivos .log
                if (pathinfo($fileName, PATHINFO_EXTENSION) === 'log') {
                    $logFiles[] = [
                        'name' => $fileName,
                        'path' => $filePath,
                        'size' => $this->formatBytes($file->getSize()),
                        'size_bytes' => $file->getSize(),
                        'modified' => Carbon::createFromTimestamp($file->getMTime()),
                        'is_readable' => is_readable($filePath),
                    ];
                }
            }
        }

        // Ordenar por fecha de modificación (más reciente primero)
        usort($logFiles, function ($a, $b) {
            return $b['modified']->timestamp - $a['modified']->timestamp;
        });

        return view('admin.logs.index', compact('logFiles'));
    }

    /**
     * Mostrar contenido de un archivo de log específico
     */
    public function show(Request $request, $filename)
    {
        $logPath = storage_path('logs/' . $filename);
        
        // Validar que el archivo existe y es legible
        if (!File::exists($logPath) || !is_readable($logPath)) {
            abort(404, 'Archivo de log no encontrado o no es legible');
        }

        // Validar que es un archivo .log
        if (pathinfo($filename, PATHINFO_EXTENSION) !== 'log') {
            abort(403, 'Tipo de archivo no permitido');
        }

        $lines = $request->get('lines', 5000); // Aumentar límite por defecto para mostrar más logs
        $search = $request->get('search', '');
        $level = $request->get('level', '');

        // Leer el archivo
        $content = File::get($logPath);
        $logEntries = $this->parseLogContent($content, $lines, $search, $level);

        $fileInfo = [
            'name' => $filename,
            'size' => $this->formatBytes(File::size($logPath)),
            'modified' => Carbon::createFromTimestamp(File::lastModified($logPath)),
            'path' => $logPath,
        ];

        return view('admin.logs.show', compact('logEntries', 'fileInfo', 'lines', 'search', 'level'));
    }

    /**
     * Descargar archivo de log
     */
    public function download($filename)
    {
        $logPath = storage_path('logs/' . $filename);
        
        if (!File::exists($logPath) || pathinfo($filename, PATHINFO_EXTENSION) !== 'log') {
            abort(404);
        }

        return response()->download($logPath);
    }

    /**
     * Eliminar archivo de log
     */
    public function delete($filename)
    {
        $logPath = storage_path('logs/' . $filename);
        
        if (!File::exists($logPath) || pathinfo($filename, PATHINFO_EXTENSION) !== 'log') {
            return redirect()->route('admin.logs.index')
                ->with('error', 'Archivo no encontrado');
        }

        try {
            File::delete($logPath);
            return redirect()->route('admin.logs.index')
                ->with('success', "Archivo {$filename} eliminado correctamente");
        } catch (\Exception $e) {
            return redirect()->route('admin.logs.index')
                ->with('error', 'Error al eliminar el archivo: ' . $e->getMessage());
        }
    }

    /**
     * Limpiar logs antiguos
     */
    public function clean(Request $request)
    {
        $days = $request->get('days', 7); // Eliminar logs más antiguos de X días
        $logPath = storage_path('logs');
        $deletedFiles = 0;

        if (File::exists($logPath)) {
            $files = File::files($logPath);
            $cutoffDate = Carbon::now()->subDays($days);

            foreach ($files as $file) {
                if (pathinfo($file->getFilename(), PATHINFO_EXTENSION) === 'log') {
                    $fileDate = Carbon::createFromTimestamp($file->getMTime());
                    
                    if ($fileDate->lt($cutoffDate)) {
                        File::delete($file->getPathname());
                        $deletedFiles++;
                    }
                }
            }
        }

        return redirect()->route('admin.logs.index')
            ->with('success', "Se eliminaron {$deletedFiles} archivos de log antiguos");
    }

    /**
     * Parsear contenido del log
     */
    private function parseLogContent($content, $maxLines, $search = '', $level = '')
    {
        $lines = explode("\n", $content);
        $logEntries = [];
        $currentEntry = null;

        // Patrón para identificar líneas de log de Laravel
        $pattern = '/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] (\w+)\.(\w+): (.+)$/';

        foreach ($lines as $line) {
            if (preg_match($pattern, $line, $matches)) {
                // Guardar entrada anterior si existe
                if ($currentEntry) {
                    $logEntries[] = $currentEntry;
                }

                // Nueva entrada
                $currentEntry = [
                    'timestamp' => $matches[1],
                    'environment' => $matches[2],
                    'level' => strtoupper($matches[3]),
                    'message' => $matches[4],
                    'context' => '',
                    'full_line' => $line,
                ];
            } else {
                // Línea de contexto (stacktrace, etc.)
                if ($currentEntry) {
                    $currentEntry['context'] .= $line . "\n";
                    $currentEntry['full_line'] .= "\n" . $line;
                }
            }
        }

        // Agregar última entrada
        if ($currentEntry) {
            $logEntries[] = $currentEntry;
        }

        // Aplicar filtros
        if ($search) {
            $logEntries = array_filter($logEntries, function ($entry) use ($search) {
                return stripos($entry['full_line'], $search) !== false;
            });
        }

        if ($level) {
            $logEntries = array_filter($logEntries, function ($entry) use ($level) {
                return $entry['level'] === strtoupper($level);
            });
        }

        // Invertir para mostrar más recientes primero y limitar
        $logEntries = array_reverse($logEntries);
        $logEntries = array_slice($logEntries, 0, $maxLines);

        return $logEntries;
    }

    /**
     * Formatear bytes en formato legible
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Obtener estadísticas de logs via AJAX
     */
    public function stats()
    {
        $logPath = storage_path('logs');
        $stats = [
            'total_files' => 0,
            'total_size' => 0,
            'latest_error' => null,
            'error_count_today' => 0,
        ];

        if (File::exists($logPath)) {
            $files = File::files($logPath);
            
            foreach ($files as $file) {
                if (pathinfo($file->getFilename(), PATHINFO_EXTENSION) === 'log') {
                    $stats['total_files']++;
                    $stats['total_size'] += $file->getSize();
                }
            }

            // Buscar último error en laravel.log
            $laravelLog = storage_path('logs/laravel.log');
            if (File::exists($laravelLog)) {
                $content = File::get($laravelLog);
                $lines = explode("\n", $content);
                $lines = array_reverse($lines);
                
                foreach ($lines as $line) {
                    if (strpos($line, '.ERROR:') !== false || strpos($line, '.CRITICAL:') !== false) {
                        $stats['latest_error'] = $line;
                        break;
                    }
                }

                // Contar errores de hoy
                $today = Carbon::today()->format('Y-m-d');
                $stats['error_count_today'] = substr_count($content, "[{$today}");
            }
        }

        $stats['total_size_formatted'] = $this->formatBytes($stats['total_size']);

        return response()->json($stats);
    }
}
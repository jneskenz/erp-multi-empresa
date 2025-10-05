<div>
    <style>
        :root {
            @if($customization->theme_color === 'custom' && $customization->custom_color)
                --bs-primary: {{ $customization->custom_color }};
                --bs-primary-rgb: {{ hexToRgbSafe($customization->custom_color) }};
            @elseif($customization->theme_color !== 'default')
                @php
                    $themeColors = \App\Models\UserCustomization::getThemeColors();
                    $selectedColor = $themeColors[$customization->theme_color] ?? '#696cff';
                @endphp
                --bs-primary: {{ $selectedColor }};
                --bs-primary-rgb: {{ hexToRgbSafe($selectedColor) }};
            @endif
        }
        
        @php
            $fontFamilies = [
                'inter' => 'Inter, sans-serif',
                'roboto' => 'Roboto, sans-serif',
                'poppins' => 'Poppins, sans-serif',
                'open_sans' => 'Open Sans, sans-serif',
                'lato' => 'Lato, sans-serif'
            ];
            $selectedFont = $fontFamilies[$customization->font_family ?? 'inter'] ?? 'Inter, sans-serif';
        @endphp
        
        body {
            font-family: {{ $selectedFont }} !important;
        }
        
        @if($customization->font_size === 'small')
            html { font-size: 0.85rem; }
        @elseif($customization->font_size === 'large')
            html { font-size: 1rem; }
        @endif
        
        @if($customization->navbar_blur)
            .layout-navbar {
                backdrop-filter: blur(10px);
                background-color: rgba(255, 255, 255, 0.85) !important;
            }
            
            [data-theme="theme-dark"] .layout-navbar {
                background-color: rgba(33, 33, 33, 0.85) !important;
            }
        @endif
    </style>
</div>
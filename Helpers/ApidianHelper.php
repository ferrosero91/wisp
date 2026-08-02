<?php
/**
 * APIDIAN HELPER - Facturacion Electronica DIAN
 * Funciones para comunicacion con la API de APIDIAN
 */

/**
 * Realiza una peticion HTTP a la API de APIDIAN
 * 
 * @param string $endpoint Endpoint de la API (ej: /invoice, /credit-note)
 * @param array $data Datos a enviar en el body
 * @param string $method Metodo HTTP (GET, POST, PUT)
 * @param bool $use_token Si debe usar token de autenticacion
 * @return array Respuesta de la API ['code' => int, 'response' => array, 'error' => string|null]
 */
function apidian_request($endpoint, $data = [], $method = 'POST', $use_token = true) {
    $base_url = rtrim($_SESSION['businessData']['apidian_url'], '/');
    
    // Si la URL no contiene /api/ubl2.1, agregarlo
    if (strpos($base_url, '/api/ubl2.1') === false) {
        $base_url .= '/api/ubl2.1';
    }
    
    $url = $base_url . $endpoint;
    $token = $_SESSION['businessData']['apidian_token'];
    
    $headers = [
        'Content-Type: application/json',
        'Accept: application/json'
    ];
    
    if ($use_token && !empty($token)) {
        $headers[] = 'Authorization: Bearer ' . $token;
    }
    
    $options = [
        'http' => [
            'method' => $method,
            'header' => implode("\r\n", $headers),
            'content' => json_encode($data),
            'timeout' => 120,
            'ignore_errors' => true
        ],
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false
        ]
    ];
    
    $context = stream_context_create($options);
    $response = @file_get_contents($url, false, $context);
    
    if ($response === false) {
        return [
            'code' => 0,
            'response' => null,
            'error' => 'No se pudo conectar a la API APIDIAN'
        ];
    }
    
    // Obtener código de respuesta HTTP
    $httpCode = 200;
    if (function_exists('http_get_last_response_headers')) {
        $respHeaders = http_get_last_response_headers();
        if (!empty($respHeaders)) {
            foreach ($respHeaders as $header) {
                if (preg_match('/HTTP\/\d\.\d\s+(\d+)/', $header, $matches)) {
                    $httpCode = intval($matches[1]);
                }
            }
        }
    }
    
    $decoded = json_decode($response, true);
    
    return [
        'code' => $httpCode,
        'response' => $decoded,
        'error' => null,
        'raw' => $response
    ];
}

/**
 * Configura la empresa en APIDIAN (Paso 1)
 * Endpoint: POST /config/{nit}/{dv}
 * No requiere autenticacion
 * 
 * @param string $nit NIT de la empresa
 * @param string $dv Digito verificador
 * @param array $data Datos de la empresa para configurar
 * @return array Respuesta de la API
 */
function apidian_config_company($nit, $dv, $data) {
    $base_url = rtrim($data['apidian_url'], '/');
    
    // Si la URL no contiene /api/ubl2.1, agregarlo
    if (strpos($base_url, '/api/ubl2.1') === false) {
        $base_url .= '/api/ubl2.1';
    }
    
    $url = $base_url . '/config/' . $nit . '/' . $dv;
    
    $headers = [
        'Content-Type: application/json',
        'Accept: application/json'
    ];
    
    $options = [
        'http' => [
            'method' => 'POST',
            'header' => implode("\r\n", $headers),
            'content' => json_encode($data['config_data']),
            'timeout' => 120,
            'ignore_errors' => true
        ],
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false
        ]
    ];
    
    $context = stream_context_create($options);
    $response = @file_get_contents($url, false, $context);
    
    if ($response === false) {
        return [
            'code' => 0,
            'response' => null,
            'error' => 'No se pudo conectar a la API APIDIAN'
        ];
    }
    
    // Obtener código de respuesta HTTP
    $httpCode = 200;
    if (function_exists('http_get_last_response_headers')) {
        $respHeaders = http_get_last_response_headers();
        if (!empty($respHeaders)) {
            foreach ($respHeaders as $header) {
                if (preg_match('/HTTP\/\d\.\d\s+(\d+)/', $header, $matches)) {
                    $httpCode = intval($matches[1]);
                }
            }
        }
    }
    
    $decoded = json_decode($response, true);
    
    return [
        'code' => $httpCode,
        'response' => $decoded,
        'error' => null
    ];
}

/**
 * Configura el certificado digital en APIDIAN
 * Endpoint: PUT /config/certificate
 * 
 * @param string $token Token de autenticación
 * @param string $base64_certificate Certificado en base64
 * @param string $password Contraseña del certificado
 * @return array Respuesta de la API
 */
function apidian_config_certificate($token, $base64_certificate, $password) {
    $base_url = rtrim($_SESSION['businessData']['apidian_url'], '/');
    
    if (strpos($base_url, '/api/ubl2.1') === false) {
        $base_url .= '/api/ubl2.1';
    }
    
    $url = $base_url . '/config/certificate';
    
    $headers = [
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: Bearer ' . $token
    ];
    
    $data = [
        'certificate' => $base64_certificate,
        'password' => $password
    ];
    
    $options = [
        'http' => [
            'method' => 'PUT',
            'header' => implode("\r\n", $headers),
            'content' => json_encode($data),
            'timeout' => 120,
            'ignore_errors' => true
        ],
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false
        ]
    ];
    
    $context = stream_context_create($options);
    $response = @file_get_contents($url, false, $context);
    
    if ($response === false) {
        return [
            'code' => 0,
            'response' => null,
            'error' => 'No se pudo conectar a la API APIDIAN'
        ];
    }
    
    $httpCode = 200;
    if (function_exists('http_get_last_response_headers')) {
        $respHeaders = http_get_last_response_headers();
        if (!empty($respHeaders)) {
            foreach ($respHeaders as $header) {
                if (preg_match('/HTTP\/\d\.\d\s+(\d+)/', $header, $matches)) {
                    $httpCode = intval($matches[1]);
                }
            }
        }
    }
    
    $decoded = json_decode($response, true);
    
    return [
        'code' => $httpCode,
        'response' => $decoded,
        'error' => null
    ];
}

/**
 * Configura una resolución en APIDIAN
 * Endpoint: PUT /config/resolution
 * 
 * @param string $token Token de autenticación
 * @param array $resolution_data Datos de la resolución
 * @return array Respuesta de la API
 */
function apidian_config_resolution($token, $resolution_data) {
    $base_url = rtrim($_SESSION['businessData']['apidian_url'], '/');
    
    if (strpos($base_url, '/api/ubl2.1') === false) {
        $base_url .= '/api/ubl2.1';
    }
    
    $url = $base_url . '/config/resolution';
    
    $headers = [
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: Bearer ' . $token
    ];
    
    $options = [
        'http' => [
            'method' => 'PUT',
            'header' => implode("\r\n", $headers),
            'content' => json_encode($resolution_data),
            'timeout' => 120,
            'ignore_errors' => true
        ],
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false
        ]
    ];
    
    $context = stream_context_create($options);
    $response = @file_get_contents($url, false, $context);
    
    if ($response === false) {
        return [
            'code' => 0,
            'response' => null,
            'error' => 'No se pudo conectar a la API APIDIAN'
        ];
    }
    
    $httpCode = 200;
    if (function_exists('http_get_last_response_headers')) {
        $respHeaders = http_get_last_response_headers();
        if (!empty($respHeaders)) {
            foreach ($respHeaders as $header) {
                if (preg_match('/HTTP\/\d\.\d\s+(\d+)/', $header, $matches)) {
                    $httpCode = intval($matches[1]);
                }
            }
        }
    }
    
    $decoded = json_decode($response, true);
    
    return [
        'code' => $httpCode,
        'response' => $decoded,
        'error' => null
    ];
}

/**
 * Configura el software en APIDIAN
 * Endpoint: PUT /config/software
 * 
 * @param string $token Token de autenticación
 * @param string $software_id ID del software registrado en DIAN
 * @param int $pin PIN del software (5 dígitos)
 * @return array Respuesta de la API
 */
function apidian_config_software($token, $software_id, $pin) {
    $base_url = rtrim($_SESSION['businessData']['apidian_url'], '/');
    
    if (strpos($base_url, '/api/ubl2.1') === false) {
        $base_url .= '/api/ubl2.1';
    }
    
    $url = $base_url . '/config/software';
    
    $headers = [
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: Bearer ' . $token
    ];
    
    $data = [
        'id' => $software_id,
        'pin' => intval($pin)
    ];
    
    $options = [
        'http' => [
            'method' => 'PUT',
            'header' => implode("\r\n", $headers),
            'content' => json_encode($data),
            'timeout' => 120,
            'ignore_errors' => true
        ],
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false
        ]
    ];
    
    $context = stream_context_create($options);
    $response = @file_get_contents($url, false, $context);
    
    if ($response === false) {
        return [
            'code' => 0,
            'response' => null,
            'error' => 'No se pudo conectar a la API APIDIAN'
        ];
    }
    
    $httpCode = 200;
    if (function_exists('http_get_last_response_headers')) {
        $respHeaders = http_get_last_response_headers();
        if (!empty($respHeaders)) {
            foreach ($respHeaders as $header) {
                if (preg_match('/HTTP\/\d\.\d\s+(\d+)/', $header, $matches)) {
                    $httpCode = intval($matches[1]);
                }
            }
        }
    }
    
    $decoded = json_decode($response, true);
    
    return [
        'code' => $httpCode,
        'response' => $decoded,
        'error' => null
    ];
}

/**
 * Envía una factura electrónica a APIDIAN
 * Endpoint: POST /invoice
 * 
 * @param string $token Token de autenticación
 * @param array $invoice_data Datos de la factura
 * @param string|null $test_set_id ID del set de pruebas (opcional)
 * @return array Respuesta de la API
 */
function apidian_send_invoice($token, $invoice_data, $test_set_id = null) {
    $base_url = rtrim($_SESSION['businessData']['apidian_url'], '/');
    
    if (strpos($base_url, '/api/ubl2.1') === false) {
        $base_url .= '/api/ubl2.1';
    }
    
    $url = $base_url . '/invoice';
    if (!empty($test_set_id)) {
        $url .= '/' . $test_set_id;
    }
    
    $headers = [
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: Bearer ' . $token
    ];
    
    $options = [
        'http' => [
            'method' => 'POST',
            'header' => implode("\r\n", $headers),
            'content' => json_encode($invoice_data),
            'timeout' => 180,
            'ignore_errors' => true
        ],
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false
        ]
    ];
    
    $context = stream_context_create($options);
    $response = @file_get_contents($url, false, $context);
    
    if ($response === false) {
        return [
            'code' => 0,
            'response' => null,
            'error' => 'No se pudo conectar a la API APIDIAN'
        ];
    }
    
    $httpCode = 200;
    if (function_exists('http_get_last_response_headers')) {
        $respHeaders = http_get_last_response_headers();
        if (!empty($respHeaders)) {
            foreach ($respHeaders as $header) {
                if (preg_match('/HTTP\/\d\.\d\s+(\d+)/', $header, $matches)) {
                    $httpCode = intval($matches[1]);
                }
            }
        }
    }
    
    $decoded = json_decode($response, true);
    
    return [
        'code' => $httpCode,
        'response' => $decoded,
        'error' => null
    ];
}

/**
 * Construye los datos de factura para enviar a APIDIAN
 * 
 * @param array $bill Datos de la factura
 * @param array $client Datos del cliente
 * @param array $details Detalles de la factura
 * @param array $resolution Datos de la resolución
 * @return array Datos formateados para APIDIAN
 */
function apidian_build_invoice_data($bill, $client, $details, $resolution) {
    $business = $_SESSION['businessData'];
    $tax_rate = floatval($business['tax_rate'] ?? 19);
    
    // Construir datos del cliente
    $customer = [
        'identification_number' => $client['document'],
        'name' => trim($client['names'] . ' ' . $client['surnames']),
        'type_document_identification_id' => intval($client['type_document_identification_id'] ?? 3),
        'address' => $client['address'] ?? '',
        'email' => $client['email'] ?? '',
        'phone' => $client['mobile'] ?? ''
    ];
    
    // Agregar DV si es NIT
    if (($client['type_document_identification_id'] ?? 3) == 6 && !empty($client['dv'])) {
        $customer['dv'] = $client['dv'];
    }
    
    // Construir líneas de factura
    $invoice_lines = [];
    $subtotal = 0;
    $total_tax = 0;
    
    foreach ($details as $index => $detail) {
        $quantity = intval($detail['quantity']);
        $price = floatval($detail['price']);
        $line_total = $quantity * $price;
        $tax_amount = round($line_total * ($tax_rate / 100), 2);
        
        $subtotal += $line_total;
        $total_tax += $tax_amount;
        
        $invoice_lines[] = [
            'unit_measure_id' => 70, // UNIDAD
            'invoiced_quantity' => $quantity,
            'line_extension_amount' => number_format($line_total, 2, '.', ''),
            'free_of_charge_indicator' => false,
            'description' => strtoupper($detail['description']),
            'code' => 'SER' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
            'type_item_identification_id' => 4, // Servicio
            'price_amount' => number_format($price, 2, '.', ''),
            'base_quantity' => '1',
            'tax_totals' => [
                [
                    'tax_id' => 1, // IVA
                    'tax_amount' => number_format($tax_amount, 2, '.', ''),
                    'taxable_amount' => number_format($line_total, 2, '.', ''),
                    'percent' => number_format($tax_rate, 2, '.', '')
                ]
            ]
        ];
    }
    
    $total = $subtotal + $total_tax;
    
    // Construir datos de la factura
    $invoice_data = [
        'number' => intval($resolution['current_consecutive']),
        'type_document_id' => 1, // Factura electrónica
        'date' => date('Y-m-d'),
        'time' => date('H:i:s'),
        'resolution_number' => $resolution['resolution_number'],
        'prefix' => $resolution['prefix'],
        'customer' => $customer,
        'payment_form' => [
            'payment_form_id' => 1, // Contado
            'payment_method_id' => 10, // Efectivo
            'payment_due_date' => date('Y-m-d'),
            'duration_measure' => '0'
        ],
        'legal_monetary_totals' => [
            'line_extension_amount' => number_format($subtotal, 2, '.', ''),
            'tax_exclusive_amount' => number_format($subtotal, 2, '.', ''),
            'tax_inclusive_amount' => number_format($total, 2, '.', ''),
            'allowance_total_amount' => '0.00',
            'charge_total_amount' => '0.00',
            'payable_amount' => number_format($total, 2, '.', '')
        ],
        'tax_totals' => [
            [
                'tax_id' => 1, // IVA
                'tax_amount' => number_format($total_tax, 2, '.', ''),
                'percent' => number_format($tax_rate, 2, '.', ''),
                'taxable_amount' => number_format($subtotal, 2, '.', '')
            ]
        ],
        'invoice_lines' => $invoice_lines
    ];
    
    return $invoice_data;
}

/**
 * Descarga un archivo desde APIDIAN (PDF, XML, ZIP)
 * Endpoint: GET /download/{identification}/{file}
 * 
 * @param string $file Nombre del archivo
 * @param string $type_response Tipo de respuesta (false=download, 'BASE64'=base64, 'INLINE'=inline)
 * @return array|string Respuesta de la API o contenido del archivo
 */
function apidian_download_file($file, $type_response = false) {
    $base = apidian_get_files_base_url();
    $token = $_SESSION['businessData']['apidian_token'];
    $nit = $_SESSION['businessData']['apidian_nit'] ?? '';
    
    $url = $base . '/download/' . $nit . '/' . $file;
    if ($type_response) {
        $url .= '/' . $type_response;
    }
    
    $headers = [
        'Accept: application/json',
        'Authorization: Bearer ' . $token
    ];
    
    $options = [
        'http' => [
            'method' => 'GET',
            'header' => implode("\r\n", $headers),
            'timeout' => 120,
            'ignore_errors' => true
        ],
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false
        ]
    ];
    
    $context = stream_context_create($options);
    $response = @file_get_contents($url, false, $context);
    
    if ($response === false) {
        return [
            'code' => 0,
            'response' => null,
            'error' => 'No se pudo conectar a la API APIDIAN'
        ];
    }
    
    $httpCode = 200;
    if (function_exists('http_get_last_response_headers')) {
        $respHeaders = http_get_last_response_headers();
        if (!empty($respHeaders)) {
            foreach ($respHeaders as $header) {
                if (preg_match('/HTTP\/\d\.\d\s+(\d+)/', $header, $matches)) {
                    $httpCode = intval($matches[1]);
                }
            }
        }
    }
    
    // Si es BASE64, devolver el JSON con el base64
    if ($type_response === 'BASE64') {
        $decoded = json_decode($response, true);
        return [
            'code' => $httpCode,
            'response' => $decoded,
            'error' => null
        ];
    }
    
    // Si es descarga directa, devolver el contenido
    return [
        'code' => $httpCode,
        'content' => $response,
        'error' => null
    ];
}

/**
 * Regenera el PDF de una factura
 * Endpoint: POST /regeneratepdf/{prefix}/{number}/{cufe}
 * 
 * @param string $prefix Prefijo de la factura
 * @param string $number Número de la factura
 * @param string $cufe CUFE de la factura
 * @return array Respuesta de la API
 */
function apidian_regenerate_pdf($prefix, $number, $cufe) {
    $base_url = rtrim($_SESSION['businessData']['apidian_url'], '/');
    $token = $_SESSION['businessData']['apidian_token'];
    
    if (strpos($base_url, '/api/ubl2.1') === false) {
        $base_url .= '/api/ubl2.1';
    }
    
    $url = $base_url . '/regeneratepdf/' . $prefix . '/' . $number . '/' . $cufe;
    
    $headers = [
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: Bearer ' . $token
    ];
    
    $options = [
        'http' => [
            'method' => 'POST',
            'header' => implode("\r\n", $headers),
            'content' => '{}',
            'timeout' => 120,
            'ignore_errors' => true
        ],
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false
        ]
    ];
    
    $context = stream_context_create($options);
    $response = @file_get_contents($url, false, $context);
    
    if ($response === false) {
        return [
            'code' => 0,
            'response' => null,
            'error' => 'No se pudo conectar a la API APIDIAN'
        ];
    }
    
    $httpCode = 200;
    if (function_exists('http_get_last_response_headers')) {
        $respHeaders = http_get_last_response_headers();
        if (!empty($respHeaders)) {
            foreach ($respHeaders as $header) {
                if (preg_match('/HTTP\/\d\.\d\s+(\d+)/', $header, $matches)) {
                    $httpCode = intval($matches[1]);
                }
            }
        }
    }
    
    $decoded = json_decode($response, true);
    
    return [
        'code' => $httpCode,
        'response' => $decoded,
        'error' => null
    ];
}

/**
 * Construye la URL base para archivos (sin /ubl2.1)
 */
function apidian_get_files_base_url() {
    $base_url = rtrim($_SESSION['businessData']['apidian_url'], '/');
    
    // Remover /api/ubl2.1 si existe
    $base_url = str_replace('/api/ubl2.1', '', $base_url);
    // Remover /api si existe al final
    $base_url = rtrim($base_url, '/');
    if (substr($base_url, -4) === '/api') {
        $base_url = substr($base_url, 0, -4);
    }
    
    return $base_url . '/api';
}

/**
 * Construye la URL de descarga de PDF
 */
function apidian_get_pdf_url($filename) {
    $base = apidian_get_files_base_url();
    $nit = $_SESSION['businessData']['apidian_nit'] ?? '';
    return $base . '/download/' . $nit . '/' . $filename;
}

/**
 * Construye la URL de visualización de PDF
 */
function apidian_get_view_url($filename) {
    $base = apidian_get_files_base_url();
    $nit = $_SESSION['businessData']['apidian_nit'] ?? '';
    return $base . '/view/' . $nit . '/' . $filename;
}

/**
 * Envía una nota crédito a APIDIAN
 * Endpoint: POST /credit-note
 * 
 * @param string $token Token de autenticación
 * @param array $credit_note_data Datos de la nota crédito
 * @return array Respuesta de la API
 */
function apidian_send_credit_note($token, $credit_note_data) {
    $base_url = rtrim($_SESSION['businessData']['apidian_url'], '/');
    
    if (strpos($base_url, '/api/ubl2.1') === false) {
        $base_url .= '/api/ubl2.1';
    }
    
    $url = $base_url . '/credit-note';
    
    $headers = [
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: Bearer ' . $token
    ];
    
    $options = [
        'http' => [
            'method' => 'POST',
            'header' => implode("\r\n", $headers),
            'content' => json_encode($credit_note_data),
            'timeout' => 180,
            'ignore_errors' => true
        ],
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false
        ]
    ];
    
    $context = stream_context_create($options);
    $response = @file_get_contents($url, false, $context);
    
    if ($response === false) {
        return [
            'code' => 0,
            'response' => null,
            'error' => 'No se pudo conectar a la API APIDIAN'
        ];
    }
    
    $httpCode = 200;
    if (function_exists('http_get_last_response_headers')) {
        $respHeaders = http_get_last_response_headers();
        if (!empty($respHeaders)) {
            foreach ($respHeaders as $header) {
                if (preg_match('/HTTP\/\d\.\d\s+(\d+)/', $header, $matches)) {
                    $httpCode = intval($matches[1]);
                }
            }
        }
    }
    
    $decoded = json_decode($response, true);
    
    return [
        'code' => $httpCode,
        'response' => $decoded,
        'error' => null
    ];
}

/**
 * Envía una nota débito a APIDIAN
 * Endpoint: POST /debit-note
 * 
 * @param string $token Token de autenticación
 * @param array $debit_note_data Datos de la nota débito
 * @return array Respuesta de la API
 */
function apidian_send_debit_note($token, $debit_note_data) {
    $base_url = rtrim($_SESSION['businessData']['apidian_url'], '/');
    
    if (strpos($base_url, '/api/ubl2.1') === false) {
        $base_url .= '/api/ubl2.1';
    }
    
    $url = $base_url . '/debit-note';
    
    $headers = [
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: Bearer ' . $token
    ];
    
    $options = [
        'http' => [
            'method' => 'POST',
            'header' => implode("\r\n", $headers),
            'content' => json_encode($debit_note_data),
            'timeout' => 180,
            'ignore_errors' => true
        ],
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false
        ]
    ];
    
    $context = stream_context_create($options);
    $response = @file_get_contents($url, false, $context);
    
    if ($response === false) {
        return [
            'code' => 0,
            'response' => null,
            'error' => 'No se pudo conectar a la API APIDIAN'
        ];
    }
    
    $httpCode = 200;
    if (function_exists('http_get_last_response_headers')) {
        $respHeaders = http_get_last_response_headers();
        if (!empty($respHeaders)) {
            foreach ($respHeaders as $header) {
                if (preg_match('/HTTP\/\d\.\d\s+(\d+)/', $header, $matches)) {
                    $httpCode = intval($matches[1]);
                }
            }
        }
    }
    
    $decoded = json_decode($response, true);
    
    return [
        'code' => $httpCode,
        'response' => $decoded,
        'error' => null
    ];
}

/**
 * Construye datos para nota crédito
 */
function apidian_build_credit_note_data($bill, $client, $details, $resolution, $ei_original, $discrepancy_code, $discrepancy_desc, $notes = '') {
    $business = $_SESSION['businessData'];
    $tax_rate = floatval($business['tax_rate'] ?? 19);
    
    // Construir datos del cliente
    $customer = [
        'identification_number' => $client['document'],
        'name' => trim($client['names'] . ' ' . $client['surnames']),
        'type_document_identification_id' => intval($client['type_document_identification_id'] ?? 3),
        'address' => $client['address'] ?? '',
        'email' => $client['email'] ?? '',
        'phone' => $client['mobile'] ?? ''
    ];
    
    if (($client['type_document_identification_id'] ?? 3) == 6 && !empty($client['dv'])) {
        $customer['dv'] = $client['dv'];
    }
    
    // Construir líneas
    $credit_note_lines = [];
    $subtotal = 0;
    $total_tax = 0;
    
    foreach ($details as $index => $detail) {
        $quantity = intval($detail['quantity']);
        $price = floatval($detail['price']);
        $line_total = $quantity * $price;
        $tax_amount = round($line_total * ($tax_rate / 100), 2);
        
        $subtotal += $line_total;
        $total_tax += $tax_amount;
        
        $credit_note_lines[] = [
            'unit_measure_id' => 70,
            'invoiced_quantity' => $quantity,
            'line_extension_amount' => number_format($line_total, 2, '.', ''),
            'free_of_charge_indicator' => false,
            'description' => strtoupper($detail['description']),
            'code' => 'SER' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
            'type_item_identification_id' => 4,
            'price_amount' => number_format($price, 2, '.', ''),
            'base_quantity' => '1',
            'tax_totals' => [
                [
                    'tax_id' => 1,
                    'tax_amount' => number_format($tax_amount, 2, '.', ''),
                    'taxable_amount' => number_format($line_total, 2, '.', ''),
                    'percent' => number_format($tax_rate, 2, '.', '')
                ]
            ]
        ];
    }
    
    $total = $subtotal + $total_tax;
    
    $credit_note_data = [
        'number' => intval($resolution['current_consecutive']),
        'type_document_id' => 4, // Nota Crédito
        'date' => date('Y-m-d'),
        'time' => date('H:i:s'),
        'resolution_number' => !empty($resolution['resolution_number']) ? $resolution['resolution_number'] : '0',
        'prefix' => $resolution['prefix'] ?? 'NC',
        'customer' => $customer,
        'billing_reference' => [
            'number' => $ei_original['prefix'] . str_pad($ei_original['number'], 7, "0", STR_PAD_LEFT),
            'uuid' => $ei_original['cufe'],
            'issue_date' => date('Y-m-d', strtotime($ei_original['created_at']))
        ],
        'discrepancyresponsecode' => intval($discrepancy_code),
        'discrepancyresponsedescription' => strtoupper($discrepancy_desc),
        'notes' => strtoupper($notes),
        'payment_form' => [
            'payment_form_id' => 1,
            'payment_method_id' => 10,
            'payment_due_date' => date('Y-m-d'),
            'duration_measure' => '0'
        ],
        'legal_monetary_totals' => [
            'line_extension_amount' => number_format($subtotal, 2, '.', ''),
            'tax_exclusive_amount' => number_format($subtotal, 2, '.', ''),
            'tax_inclusive_amount' => number_format($total, 2, '.', ''),
            'allowance_total_amount' => '0.00',
            'charge_total_amount' => '0.00',
            'payable_amount' => number_format($total, 2, '.', '')
        ],
        'tax_totals' => [
            [
                'tax_id' => 1,
                'tax_amount' => number_format($total_tax, 2, '.', ''),
                'percent' => number_format($tax_rate, 2, '.', ''),
                'taxable_amount' => number_format($subtotal, 2, '.', '')
            ]
        ],
        'credit_note_lines' => $credit_note_lines
    ];
    
    return $credit_note_data;
}

/**
 * Construye datos para nota débito
 */
function apidian_build_debit_note_data($bill, $client, $details, $resolution, $ei_original, $discrepancy_code, $discrepancy_desc, $notes = '') {
    $business = $_SESSION['businessData'];
    $tax_rate = floatval($business['tax_rate'] ?? 19);
    
    $customer = [
        'identification_number' => $client['document'],
        'name' => trim($client['names'] . ' ' . $client['surnames']),
        'type_document_identification_id' => intval($client['type_document_identification_id'] ?? 3),
        'address' => $client['address'] ?? '',
        'email' => $client['email'] ?? '',
        'phone' => $client['mobile'] ?? ''
    ];
    
    if (($client['type_document_identification_id'] ?? 3) == 6 && !empty($client['dv'])) {
        $customer['dv'] = $client['dv'];
    }
    
    $debit_note_lines = [];
    $subtotal = 0;
    $total_tax = 0;
    
    foreach ($details as $index => $detail) {
        $quantity = intval($detail['quantity']);
        $price = floatval($detail['price']);
        $line_total = $quantity * $price;
        $tax_amount = round($line_total * ($tax_rate / 100), 2);
        
        $subtotal += $line_total;
        $total_tax += $tax_amount;
        
        $debit_note_lines[] = [
            'unit_measure_id' => 70,
            'invoiced_quantity' => $quantity,
            'line_extension_amount' => number_format($line_total, 2, '.', ''),
            'free_of_charge_indicator' => false,
            'description' => strtoupper($detail['description']),
            'code' => 'SER' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
            'type_item_identification_id' => 4,
            'price_amount' => number_format($price, 2, '.', ''),
            'base_quantity' => '1',
            'tax_totals' => [
                [
                    'tax_id' => 1,
                    'tax_amount' => number_format($tax_amount, 2, '.', ''),
                    'taxable_amount' => number_format($line_total, 2, '.', ''),
                    'percent' => number_format($tax_rate, 2, '.', '')
                ]
            ]
        ];
    }
    
    $total = $subtotal + $total_tax;
    
    $debit_note_data = [
        'number' => intval($resolution['current_consecutive']),
        'type_document_id' => 5, // Nota Débito
        'date' => date('Y-m-d'),
        'time' => date('H:i:s'),
        'resolution_number' => !empty($resolution['resolution_number']) ? $resolution['resolution_number'] : '0',
        'prefix' => $resolution['prefix'] ?? 'ND',
        'customer' => $customer,
        'billing_reference' => [
            'number' => $ei_original['prefix'] . str_pad($ei_original['number'], 7, "0", STR_PAD_LEFT),
            'uuid' => $ei_original['cufe'],
            'issue_date' => date('Y-m-d', strtotime($ei_original['created_at']))
        ],
        'discrepancyresponsecode' => intval($discrepancy_code),
        'discrepancyresponsedescription' => strtoupper($discrepancy_desc),
        'notes' => strtoupper($notes),
        'payment_form' => [
            'payment_form_id' => 1,
            'payment_method_id' => 10,
            'payment_due_date' => date('Y-m-d'),
            'duration_measure' => '0'
        ],
        'requested_monetary_totals' => [
            'line_extension_amount' => number_format($subtotal, 2, '.', ''),
            'tax_exclusive_amount' => number_format($subtotal, 2, '.', ''),
            'tax_inclusive_amount' => number_format($total, 2, '.', ''),
            'allowance_total_amount' => '0.00',
            'charge_total_amount' => '0.00',
            'payable_amount' => number_format($total, 2, '.', '')
        ],
        'tax_totals' => [
            [
                'tax_id' => 1,
                'tax_amount' => number_format($total_tax, 2, '.', ''),
                'percent' => number_format($tax_rate, 2, '.', ''),
                'taxable_amount' => number_format($subtotal, 2, '.', '')
            ]
        ],
        'debit_note_lines' => $debit_note_lines
    ];
    
    return $debit_note_data;
}

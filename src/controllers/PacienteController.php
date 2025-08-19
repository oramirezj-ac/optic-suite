<?php

require_once __DIR__ . '/../DatabaseImproved.php';
require_once __DIR__ . '/../helpers/Validator.php';

/**
 * Patient Controller - Example of improved MVC architecture
 * Ejemplo de mejora: Separación de responsabilidades con MVC
 */
class PacienteController
{
    private $db;
    private $validator;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->validator = new Validator();
    }

    /**
     * Display patients list with filtering
     */
    public function index()
    {
        try {
            $letra = $_GET['letra'] ?? null;
            
            // Build query with proper parameter binding
            $sql = "SELECT id_paciente, nombres, apellido_paterno, apellido_materno, fecha_nacimiento 
                    FROM pacientes";
            $params = [];
            
            if ($letra) {
                $sql .= " WHERE apellido_paterno LIKE ?";
                $params[] = $letra . '%';
            }
            
            $sql .= " ORDER BY apellido_paterno ASC, apellido_materno ASC";
            
            $pacientes = $this->db->fetchAll($sql, $params);
            
            // Prepare data for view
            $data = [
                'pacientes' => $pacientes,
                'letra_seleccionada' => $letra,
                'abecedario' => range('A', 'Z'),
                'title' => 'Gestión de Pacientes'
            ];
            
            return $this->renderView('pacientes/index', $data);
            
        } catch (DatabaseException $e) {
            $this->handleError('Error al cargar pacientes: ' . $e->getMessage());
        }
    }

    /**
     * Show create patient form
     */
    public function create()
    {
        try {
            // Get existing surnames for autocomplete
            $apellidos_paternos = $this->db->fetchAll(
                "SELECT DISTINCT apellido_paterno FROM pacientes 
                 WHERE apellido_paterno IS NOT NULL AND apellido_paterno != '' 
                 ORDER BY apellido_paterno"
            );
            
            $apellidos_maternos = $this->db->fetchAll(
                "SELECT DISTINCT apellido_materno FROM pacientes 
                 WHERE apellido_materno IS NOT NULL AND apellido_materno != '' 
                 ORDER BY apellido_materno"
            );

            $data = [
                'apellidos_paternos' => array_column($apellidos_paternos, 'apellido_paterno'),
                'apellidos_maternos' => array_column($apellidos_maternos, 'apellido_materno'),
                'title' => 'Registrar Nuevo Paciente'
            ];

            return $this->renderView('pacientes/create', $data);
            
        } catch (DatabaseException $e) {
            $this->handleError('Error al cargar formulario: ' . $e->getMessage());
        }
    }

    /**
     * Store new patient
     */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/pacientes.php');
            return;
        }

        try {
            // Sanitize input
            $data = Validator::sanitize($_POST);
            
            // Validation rules
            $rules = [
                'nombres' => 'required|min:2|max:100|alpha',
                'apellido_paterno' => 'required|min:2|max:100|alpha',
                'apellido_materno' => 'max:100|alpha',
                'fecha_nacimiento' => 'date',
                'telefono' => 'max:20',
                'email' => 'email|max:255',
                'dp_total' => 'numeric',
                'dnp_od' => 'numeric',
                'dnp_oi' => 'numeric'
            ];

            if (!$this->validator->validate($data, $rules)) {
                // Return to form with errors
                $this->redirectWithErrors('/pacientes_nuevo.php', $this->validator->getErrors());
                return;
            }

            // Begin transaction for data consistency
            $this->db->beginTransaction();

            try {
                $sql = "INSERT INTO pacientes (
                    nombres, apellido_paterno, apellido_materno, fecha_nacimiento,
                    telefono, domicilio, email, antecedentes_descripcion,
                    dp_total, dnp_od, dnp_oi
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

                $this->db->execute($sql, [
                    $data['nombres'],
                    $data['apellido_paterno'],
                    $data['apellido_materno'] ?? null,
                    !empty($data['fecha_nacimiento']) ? $data['fecha_nacimiento'] : null,
                    $data['telefono'] ?? null,
                    $data['domicilio'] ?? null,
                    $data['email'] ?? null,
                    $data['antecedentes_descripcion'] ?? null,
                    $data['dp_total'] ?? null,
                    $data['dnp_od'] ?? null,
                    $data['dnp_oi'] ?? null
                ]);

                $id_nuevo_paciente = $this->db->lastInsertId();
                
                $this->db->commit();
                
                // Redirect to success page
                $this->redirect("/pacientes_exito.php?id=" . $id_nuevo_paciente);
                
            } catch (DatabaseException $e) {
                $this->db->rollback();
                throw $e;
            }
            
        } catch (Exception $e) {
            $this->handleError('Error al crear paciente: ' . $e->getMessage());
        }
    }

    /**
     * Search patients
     */
    public function search()
    {
        try {
            $busqueda = trim($_GET['q'] ?? '');
            $resultados = [];
            
            if (!empty($busqueda)) {
                // Validate search term
                if (strlen($busqueda) < 2) {
                    throw new InvalidArgumentException('El término de búsqueda debe tener al menos 2 caracteres');
                }
                
                $sql = "SELECT id_paciente, nombres, apellido_paterno, apellido_materno, fecha_nacimiento 
                        FROM pacientes 
                        WHERE LOWER(CONCAT_WS(' ', nombres, apellido_paterno, apellido_materno)) LIKE LOWER(?)
                        ORDER BY apellido_paterno ASC, apellido_materno ASC";
                        
                $resultados = $this->db->fetchAll($sql, ['%' . $busqueda . '%']);
            }

            $data = [
                'resultados' => $resultados,
                'busqueda' => Validator::escape($busqueda),
                'title' => 'Buscar Paciente'
            ];

            return $this->renderView('pacientes/search', $data);
            
        } catch (Exception $e) {
            $this->handleError('Error en la búsqueda: ' . $e->getMessage());
        }
    }

    /**
     * Render view with data
     */
    private function renderView(string $view, array $data = [])
    {
        // Extract data to make variables available in view
        extract($data);
        
        // Start output buffering
        ob_start();
        
        // Include view file
        $viewFile = __DIR__ . "/../views/{$view}.php";
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            throw new Exception("View file not found: {$view}");
        }
        
        // Get content and clean buffer
        $content = ob_get_clean();
        
        // Include layout
        include __DIR__ . '/../layouts/layout.php';
    }

    /**
     * Handle errors gracefully
     */
    private function handleError(string $message)
    {
        if (Config::isDebug()) {
            die($message);
        } else {
            error_log($message);
            $this->redirect('/error.php');
        }
    }

    /**
     * Redirect to URL
     */
    private function redirect(string $url)
    {
        header("Location: {$url}");
        exit();
    }

    /**
     * Redirect with validation errors
     */
    private function redirectWithErrors(string $url, array $errors)
    {
        $_SESSION['validation_errors'] = $errors;
        $_SESSION['old_input'] = $_POST;
        $this->redirect($url);
    }
}
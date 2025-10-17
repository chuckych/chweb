<?php
namespace Classes;
use Classes\Log;

class ADAuthenticator
{
    private $server;
    private $port;
    private $baseDn;
    private $domain;

    public function __construct($server, $baseDn, $domain = null, $port = 389)
    {
        $this->server = $server;
        $this->port = $port;
        $this->baseDn = $baseDn;
        $this->domain = $domain;
    }

    /**
     * Prueba la conexión con el servidor AD
     */
    public function testConnection($adminUser = null, $adminPass = null, $listUsers = true)
    {
        // Validar que tengamos al menos el servidor
        if (empty($this->server)) {
            return [
                'success' => false,
                'error' => 'No se especificó un servidor AD'
            ];
        }

        // Intentar crear la conexión
        $ldapConn = @ldap_connect($this->server, $this->port);

        // \error_log(print_r($this->server, true));
        // \error_log(print_r($this->port, true));


        if (!$ldapConn) {
            return [
                'success' => false,
                'error' => 'No se pudo inicializar la conexión LDAP'
            ];
        }
        
        // Configurar opciones de conexión
        ldap_set_option($ldapConn, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ldapConn, LDAP_OPT_REFERRALS, 0);
        ldap_set_option($ldapConn, LDAP_OPT_NETWORK_TIMEOUT, 5);

        // Si se proporcionan credenciales de administrador, probar bind con credenciales
        if ($adminUser && $adminPass) {
            $bind = @ldap_bind($ldapConn, $adminUser, $adminPass);

            if (!$bind) {
                $error = ldap_error($ldapConn);
                $errno = ldap_errno($ldapConn);
                @ldap_unbind($ldapConn);

                // \error_log(print_r($error, true));
                // \error_log(print_r($errno, true));

                // Si el error es de conectividad (no de autenticación)
                if ($errno === -1 || $errno === 0x51) {
                    return [
                        'success' => false,
                        'message' => "Error: $error",
                        'errno' => $errno
                    ];
                }

                return [
                    'success' => false,
                    'message' => "Error: $error",
                    'errno' => $errno
                ];
            }

            // Obtener información del dominio
            $search = @ldap_read($ldapConn, $this->baseDn, '(objectClass=*)', ['defaultNamingContext', 'dnsHostName']);
            $info = null;
            $usuarios = null;

            if ($search) {
                $entries = ldap_get_entries($ldapConn, $search);
                if ($entries['count'] > 0) {
                    $info = [
                        'dns_hostname' => $entries[0]['dnshostname'][0] ?? null,
                        'defaultnamingcontext' => $entries[0]['defaultnamingcontext'][0] ?? null,
                        'usuarios' =>  $listUsers ? $this->listUsers($adminUser, $adminPass) : null // Listar usuarios si el bind fue exitoso
                    ];
                }
            }

            @ldap_unbind($ldapConn);
            // \error_log(print_r($info, true));
            return [
                'success' => true,
                'message' => 'Conexión exitosa con credenciales',
                'server_info' => $info,
            ];

        }

        // Intentar bind anónimo solo para probar conectividad básica
        $bind = @ldap_bind($ldapConn);
        $error = ldap_error($ldapConn);
        $errno = ldap_errno($ldapConn);
        // \error_log(print_r($bind, true));
        // \error_log(print_r($error, true));
        // \error_log(print_r($errno, true));

        @ldap_unbind($ldapConn);

        // Si bind fue exitoso
        if ($bind) {
            return [
                'success' => true,
                'message' => 'Conexión exitosa al servidor (bind anónimo permitido)',
                'anonymous_bind' => true
            ];
        }

        // Si falló el bind anónimo, verificar el tipo de error
        // Error -1 o 0x51 (81) = Can't contact LDAP server (problema de red/conectividad)
        if ($errno === -1 || $errno === 0x51) {
            return [
                'success' => false,
                'message' => "Error: $error",
                'errno' => $errno
            ];
        }

        // Otros errores (ej: bind anónimo deshabilitado) pero el servidor es accesible
        // Error 0x31 (49) = Invalid credentials
        // Error 0x0 (0) = Success pero sin bind
        return [
            'success' => true,
            'message' => 'Servidor accesible (bind anónimo deshabilitado - esto es normal por seguridad)',
            'anonymous_bind' => false,
            'note' => 'Se recomienda probar con credenciales de servicio',
            'error_detail' => $error
        ];
    }

    /**
     * Autentica un usuario contra Active Directory
     */
    public function authenticate($username, $password)
    {
        if (empty($username) || empty($password)) {
            return false;
        }

        $ldapConn = ldap_connect($this->server, $this->port);

        if (!$ldapConn) {
            throw new \Exception("No se pudo conectar a Active Directory");
        }

        ldap_set_option($ldapConn, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ldapConn, LDAP_OPT_REFERRALS, 0);
        ldap_set_option($ldapConn, LDAP_OPT_NETWORK_TIMEOUT, 10);

        try {
            // Método 1: Intentar con usuario@dominio (si se configuró el dominio)
            if ($this->domain) {
                $userPrincipal = $username . '@' . $this->domain;
                $bind = @ldap_bind($ldapConn, $userPrincipal, $password);

                if ($bind) {
                    $userData = $this->getUserData($ldapConn, $username);
                    ldap_unbind($ldapConn);
                    return $userData;
                }
            }

            // Método 2: Buscar el DN del usuario y autenticar con el DN completo
            // Este método funciona incluso sin conocer el dominio
            $userDN = $this->findUserDN($ldapConn, $username);

            if (!$userDN) {
                ldap_unbind($ldapConn);
                return false;
            }

            // Reconectar para autenticar con el DN del usuario
            ldap_unbind($ldapConn);
            $ldapConn = ldap_connect($this->server, $this->port);
            ldap_set_option($ldapConn, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($ldapConn, LDAP_OPT_REFERRALS, 0);

            $bind = @ldap_bind($ldapConn, $userDN, $password);

            if (!$bind) {
                ldap_unbind($ldapConn);
                return false;
            }

            // Autenticación exitosa - obtener datos del usuario
            $userData = $this->getUserData($ldapConn, $username);
            ldap_unbind($ldapConn);

            return $userData;

        } catch (\Exception $e) {
            if ($ldapConn) {
                @ldap_unbind($ldapConn);
            }
            throw $e;
        }
    }

    /**
     * Busca el DN completo del usuario
     * Usa bind anónimo o credenciales de servicio
     */
    private function findUserDN($ldapConn, $username)
    {
        // Para buscar necesitamos hacer bind (anónimo o con usuario de servicio)
        // Si el servidor no permite bind anónimo, necesitas configurar un usuario de servicio

        $filter = "(samaccountname=" . ldap_escape($username, '', LDAP_ESCAPE_FILTER) . ")";
        $search = @ldap_search($ldapConn, $this->baseDn, $filter, ['dn']);

        if (!$search) {
            return false;
        }

        $entries = ldap_get_entries($ldapConn, $search);

        if ($entries['count'] > 0) {
            return $entries[0]['dn'];
        }

        return false;
    }

    /**
     * Obtiene datos del usuario desde AD
     */
    private function getUserData($ldapConn, $username)
    {
        $filter = "(samaccountname=" . ldap_escape($username, '', LDAP_ESCAPE_FILTER) . ")";

        $attributes = [
            'samaccountname',
            'mail',
            'givenname',
            'sn',
            'displayname',
            'memberof',
            'department',
            'title',
            'telephonenumber',
            'mobile'
        ];

        $search = @ldap_search($ldapConn, $this->baseDn, $filter, $attributes);

        if (!$search) {
            return ['username' => $username];
        }

        $entries = ldap_get_entries($ldapConn, $search);

        if ($entries['count'] > 0) {
            $entry = $entries[0];

            $groups = [];
            if (isset($entry['memberof'])) {
                for ($i = 0; $i < $entry['memberof']['count']; $i++) {
                    $groups[] = $this->extractGroupName($entry['memberof'][$i]);
                }
            }

            return [
                'username' => $entry['samaccountname'][0] ?? $username,
                'email' => $entry['mail'][0] ?? null,
                'nombre' => $entry['givenname'][0] ?? null,
                'apellido' => $entry['sn'][0] ?? null,
                'nombre_completo' => $entry['displayname'][0] ?? null,
                'departamento' => $entry['department'][0] ?? null,
                'cargo' => $entry['title'][0] ?? null,
                'telefono' => $entry['telephonenumber'][0] ?? null,
                'celular' => $entry['mobile'][0] ?? null,
                'grupos' => $groups
            ];
        }

        return ['username' => $username];
    }

    private function extractGroupName($groupDN)
    {
        if (preg_match('/^CN=([^,]+)/', $groupDN, $matches)) {
            return $matches[1];
        }
        return $groupDN;
    }

    /**
     * Formatea el nombre de usuario para autenticación LDAP
     */
    private function formatUsername($username)
    {
        // Si ya tiene el formato UPN (usuario@dominio.com) o DN, dejarlo así
        if (strpos($username, '@') !== false || strpos($username, 'CN=') !== false) {
            return $username;
        }

        // Si tenemos dominio configurado, usar formato UPN
        if ($this->domain) {
            return $username . '@' . $this->domain;
        }

        // Si no, intentar con formato sAMAccountName simple
        return $username;
    }

    /**
     * Lista todos los usuarios del dominio
     * Requiere credenciales con permisos de lectura
     */
    public function listUsers($adminUser, $adminPass, $filter = null)
    {
        $ldapConn = ldap_connect($this->server, $this->port);

        if (!$ldapConn) {
            throw new \Exception("No se pudo conectar a Active Directory");
        }

        ldap_set_option($ldapConn, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ldapConn, LDAP_OPT_REFERRALS, 0);

        // Intentar diferentes formatos de usuario
        $bindUser = $this->formatUsername($adminUser);

        if (!@ldap_bind($ldapConn, $bindUser, $adminPass)) {
            throw new \Exception("Error de autenticación: " . ldap_error($ldapConn) . " (Usuario: $bindUser)");
        }

        // Filtro para usuarios activos
        $defaultFilter = "(&(objectClass=user)(objectCategory=person)(!(userAccountControl:1.2.840.113556.1.4.803:=2)))";
        $searchFilter = $filter ?? $defaultFilter;

        $attributes = ['samaccountname', 'mail', 'displayname', 'department'];

        $search = ldap_search($ldapConn, $this->baseDn, $searchFilter, $attributes);

        if (!$search) {
            throw new \Exception("Error en búsqueda: " . ldap_error($ldapConn));
        }

        $entries = ldap_get_entries($ldapConn, $search);
        $users = [];

        file_put_contents('ldap_debug.log', print_r($entries, true)); // Línea para depuración

        for ($i = 0; $i < $entries['count']; $i++) {
            $users[] = [
                'username' => $entries[$i]['samaccountname'][0] ?? null,
                'email' => $entries[$i]['mail'][0] ?? null,
                'nombre' => $entries[$i]['displayname'][0] ?? null,
                'departamento' => $entries[$i]['department'][0] ?? null
            ];
        }

        ldap_unbind($ldapConn);

        return $users;
    }
}
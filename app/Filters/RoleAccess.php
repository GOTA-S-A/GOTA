<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleAccess implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $role = mb_strtolower(trim((string) session()->get('rol_nombre')));
        $allowedRoles = array_map(
            static fn (string $allowedRole): string => mb_strtolower(trim($allowedRole)),
            $arguments ?? []
        );

        if ($role === '' || ! in_array($role, $allowedRoles, true)) {
            return redirect()->to('/dashboard')->with('error', 'No tienes permisos para acceder a esta sección.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
